<?php
session_start();
if(isset($_SESSION['rol'])==NULL or !($_SESSION['rol']!='monitor' or $_SESSION['rol']!='socio' or $_SESSION['rol']!='admin')){
	header("Location:404.html");
}
function morosos(){
	$cont = 0;
	include 'conexion.php';
	$sql = "SELECT t2.pago_restante FROM socios as t1 join pago as t2 where t1.id_usuario = t2.id_socio and t2.pago_restante > 0 ";

	$result=mysql_query($sql,$conexion);
	while($res=mysql_fetch_row($result)){
		$cont++;
	}
	mysql_close($conexion);
	return $cont;
	
}
function numUsuarios(){
	$cont=0;
	include 'conexion.php';
	$sql = "select nick,password from usuarios where rol = 'socio'";
	$result=mysql_query($sql,$conexion);
	while($res=mysql_fetch_row($result)){
		$cont++;
	}
	mysql_close($conexion);
	return $cont;

}
function numCorreos(){
	$file = fopen('correos.txt', "r");
	$cont = 0;
	while(!feof($file))
	{
	$linea = fgets($file);
	$fila = explode(',',$linea);
	if($fila[0] == $_SESSION['id']){
		$cont++;
	}
}
fclose($file);
	return $cont;
}

if(isset($_POST['send'])){
	$para = $_POST['para'];
	$enviado = $_SESSION['id'];
	$asunto = $_POST['asunto'];
	$cuerpo = $_POST['cuerpo'];
	$date = getdate();
	$hoy = $date['mday']."/".$date['mon']."/".$date['year'];
	
	if($para != -1){
	$file = fopen('correos.txt','a');

	fwrite($file,PHP_EOL."$para,$enviado,$asunto,$cuerpo,$hoy,".time());

	}else{
		echo "<div class ='alert alert-danger'>No existe el destinatario en la base de datos</div>";
	}
	fclose($file);
	header("Location:mailBox.php");
}

function getID($nick){
	include 'conexion.php';
	$id = -1;
	$result = mysql_query("select id,nick from usuarios where nick = '".$nick."'",$conexion);
	$filas = mysql_fetch_row($result);
	if(count($filas)>1){
		$id = $filas[0];
	}
	mysql_close($conexion);
	return $id;

}

function fotoMonitor($user)
{
    include 'conexion.php';
    $sql="select t1.foto from monitores as t1 join usuarios as t2 where t1.id_usuario = t2.id  and t2.id=".$_SESSION['id']."";
    $result=mysql_query($sql,$conexion);
    $r=mysql_fetch_row($result);
    if($r>1 && $r[0]!="")
    {
       
        echo  "<img src='$r[0]' alt='foto monitor' class='img-circle' data-lock-picture='$r[0]' />";
    }
    else{
         echo  "<img src='assets/images/sinFoto.jpg' alt='foto monitor' class='img-circle' data-lock-picture='assets/images/sinFoto.jpg' />";

    }

}
function fotoAdmin($user)
{
    include 'conexion.php';
    $sql="select nick,password from usuarios where rol='admin' and nick='$user'";
    $result=mysql_query($sql,$conexion);
    $r=mysql_fetch_row($result);
    if($r>1)
    {
        echo  '<img src="assets/images/admin.jpg" alt="foto admin" class="img-circle user-image" data-lock-picture="assets/images/admin.jpg" />';
    }
	mysql_close($conexion);

}
function fotoSocio($user)
{
    include 'conexion.php';
   $sql="select t1.foto_usuario from socios as t1 join usuarios as t2 where t1.id_usuario = t2.id and t2.id=".$_SESSION['id']."";
    $result=mysql_query($sql,$conexion);
    $r=mysql_fetch_row($result);
   
    if($r>1 && $r[0]!="")
    {
       
        echo  "<img src='./fotos/$r[0]' alt='foto monitor' class='img-circle user-image' data-lock-picture='./fotos/$r[0]' />";
    }
    else{
         echo  "<img src='./assets/images/sinFoto.jpg' alt='foto monitor' class='img-circle user-image' data-lock-picture='./assets/images/sinFoto.jpg' />";

    }
	mysql_close($conexion);

}

function usuariosDisponibles(){
    include 'conexion.php';
    $sql="select id, nick, rol from usuarios";
    $res=mysql_query($sql,$conexion);
    while(($fila = mysql_fetch_assoc($res))!= NULL){
        ?>  <option value="<?php echo $fila['id']; ?>"><?php echo $fila['nick']."-".$fila['rol']; ?></option> <?php

    }
	mysql_close($conexion);
}
?>

<!doctype html>
<html class="fixed">
	<head>
<title>Panel de Administración Seven Gym</title>
		<!-- Basic -->
		<meta charset="UTF-8">

		
		<meta name="description" content="Portal de administracion del gimnasio Seven Gym">
		<meta name="author" content="Equipo5PA">

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  -->
		<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800%7CShadows+Into+Light" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
		<link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css" />
		<link rel="stylesheet" href="assets/vendor/font-awesome/css/font-awesome.css" />
		<link rel="stylesheet" href="assets/vendor/magnific-popup/magnific-popup.css" />
		<link rel="stylesheet" href="assets/vendor/bootstrap-datepicker/css/datepicker3.css" />

		<!-- Specific Page Vendor CSS -->		<link rel="stylesheet" href="assets/vendor/jquery-ui/css/ui-lightness/jquery-ui-1.10.4.custom.css" />		<link rel="stylesheet" href="assets/vendor/bootstrap-multiselect/bootstrap-multiselect.css" />		<link rel="stylesheet" href="assets/vendor/morris/morris.css" />

		<!-- Theme CSS -->
		<link rel="stylesheet" href="assets/stylesheets/theme.css" />

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="assets/stylesheets/theme-custom.css">

		<!-- Head Libs -->
		<script src="assets/vendor/modernizr/modernizr.js"></script>

	</head>

	<body>
		<section class="body">

			<!-- start: header -->
			<header class="header">
				<div class="logo-container">
					<a href="../index.html" class="logo">
						<img src="assets/images/logo.png" height="35" alt="Porto Admin" />
					</a>
					<div class="visible-xs toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
						<i class="fa fa-bars" aria-label="Toggle sidebar"></i>
					</div>
				</div>
			
				<!-- start: search & user box -->
				<div class="header-right">
			
			
					<span class="separator"></span>
			
					<div id="userbox" class="userbox">
						<a href="#" data-toggle="dropdown">
							<figure class="profile-picture">
								 <?php 
                                            if($_SESSION['rol']=="admin"){
                                                 fotoAdmin($_SESSION['nick']);
                                                
                                            }
                                            else if($_SESSION['rol']=="monitor"){
                                                fotoMonitor($_SESSION['nick']);
                                              
                                            }else if($_SESSION['rol']=="socio")
                                            {
                                                fotoSocio($_SESSION['nick']);
                                               
                                            }

                                            ?>
							</figure>
							 <div class="profile-info">
                                <span class="name"><?php echo $_SESSION['nick'];?></span>
                                <span class="role"><?php echo $_SESSION['rol'];?></span>
                            </div>
			
							<i class="fa custom-caret"></i>
						</a>
			
						 <div class="dropdown-menu">
                            <ul class="list-unstyled">
                                <li class="divider"></li>
                                
                                <li>
                                    <a role="menuitem" tabindex="-1" href="lockPerfil.php"><i class="fa fa-lock"></i> Bloquear Perfil</a>
                                </li>
                                <li>
                                    <a role="menuitem" tabindex="-1" href="logout.php"><i class="fa fa-power-off"></i> Logout</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
				<!-- end: search & user box -->
			</header>
			<!-- end: header -->

			<div class="inner-wrapper">
																<!-- start: sidebar -->
                <aside id="sidebar-left" class="sidebar-left">

                    <div class="sidebar-header">
                        <div class="sidebar-title">
                            Menú
                        </div>
                        <div class="sidebar-toggle hidden-xs" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
                            <i class="fa fa-bars" aria-label="Toggle sidebar"></i>
                        </div>
                    </div>
                
                    <div class="nano">
                        <div class="nano-content">
                            <nav id="menu" class="nav-main" role="navigation">
                                <ul class="nav nav-main">
                                    <li class="nav-active">
                                        <?php
                                        if($_SESSION['rol']=='admin'){
                                            
                                            ?>
                                            <a href="indexAdmin.php"> <!-- vista de admin, monitor, socio-->
                                            <i class="fa fa-home" aria-hidden="true"></i>
                                            <span>Principal</span>
                                            </a>    
                                            <?php

                                        }else if($_SESSION['rol']=='monitor'){
                                            ?>
                                            <a href="indexMonitor.php"> <!-- vista de admin, monitor, socio-->
                                            <i class="fa fa-home" aria-hidden="true"></i>
                                            <span>Principal</span>
                                            </a>    
                                            <?php

                                        }else if($_SESSION['rol']=='socio'){
                                            ?>
                                            <a href="indexSocio.php"> <!-- vista de admin, monitor, socio-->
                                            <i class="fa fa-home" aria-hidden="true"></i>
                                            <span>Principal</span>
                                            </a>    
                                            <?php

                                        }
                                        ?>
                                        
                                    </li>
                                    <li>
                                        <a href="mailBox.php">
                                            
                                            <i class="fa fa-envelope" aria-hidden="true"></i>
                                            <span>Correo</span>
                                        </a>
                                    </li>
                                
                                <?php if($_SESSION['rol'] == 'admin'){ ?>
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-group" aria-hidden="true"></i>
                                            <span>Gestionar Socios</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="altaSocio.php">
                                                     Alta Socio
                                                </a>
                                            </li>
                                            <li>
                                                <a href="listaSocioAdmin.php">
                                                     Listar Socios
                                                </a>
                                            </li>
                                            
                                        </ul>
                                    </li>

                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-male" aria-hidden="true"></i>
                                            <span>Gestionar Monitores</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="altaMonitor.php">
                                                     Alta Monitor
                                                </a>
                                            </li>
                                            <li>
                                                <a href="listarMonitores.php">
                                                     Listar Monitores
                                                </a>
                                            </li>
                                            
                                        </ul>
                                    </li>

                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-bar-chart-o" aria-hidden="true"></i>
                                            <span>Gestionar Tarifas</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="altaTarifa.php">
                                                     Alta Tarifa
                                                </a>
                                            </li>
                                            <li>
                                                <a href="listarTarifa.php">
                                                     Listar Tarifas
                                                </a>
                                            </li>
                                            
                                        </ul>
                                    </li>
                                    <?php }?>

                                    <?php if($_SESSION['rol'] == 'monitor'){ ?>
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-pencil" aria-hidden="true"></i>
                                            <span>Mis Entrenamientos</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="listarEntrenamientos.php">
                                                     Listar Entrenamientos
                                                </a>
                                            </li>
                                            

                                            <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                            <span>Gestionar Ejercicios</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="altaEjercicio.php">
                                                     Alta Ejercicio
                                                </a>
                                            </li>
                                            <li>
                                                <a href="listarEjercicios.php">
                                                     Mostrar Ejercicios
                                                </a>
                                            </li>
                                            
                                        </ul>
                                    </li>                       
                                    
                                        </ul>
                                    </li>

                                        <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-bar-chart-o" aria-hidden="true"></i>
                                            <span>Socios Afiliados</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="listarSocios.php">
                                                     Lista Socios
                                                </a>
                                            </li>
                                        
                                            
                                        </ul>
                                    </li>

                                    <?php }?>
                                    <?php if($_SESSION['rol'] == 'admin'){ ?>
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-list-alt" aria-hidden="true"></i>
                                            <span>Gestionar Pagos</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="PrePago.php">
                                                    Realizar Pago
                                                </a>
                                            </li>
                                            <li>
                                                <a href="pagoPendiente.php">
                                                     Pagos Pendiente
                                                </a>
                                            </li>
                                            <li>
                                                <a href="pagoRealizado.php">
                                                     Pagos Realizados
                                                </a>
                                            </li>
                                            
                                        </ul>
                                    </li>
                                    <?php }?>
                                    <?php if($_SESSION['rol'] == 'socio'){ ?>

                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-list-alt" aria-hidden="true"></i>
                                            <span>Asignar Monitor</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="seleccionarMonitor.php">
                                                    Seleccionar Monitor
                                                </a>
                                            </li>
                                            
                                        </ul>
                                    </li>
                                    
                                

                                <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-bar-chart-o" aria-hidden="true"></i>
                                            <span>Mi Progreso</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="altaMedidas.php">
                                                    Actualizar Datos
                                                </a>
                                            </li>

                                           
                                            
                                        </ul>
                                    </li>

                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-heart" aria-hidden="true"></i>
                                            <span>Zona Sana</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="dietas.php">
                                                    Dietas
                                                </a>
                                            </li>

                                            <li>
                                                <a href="suplementos.php">
                                                    Suplementos
                                                </a>
                                            </li>
                                            
                                        </ul>
                                    </li>
                                    
                                </ul>


                                <?php }?>
                            </nav>
                
                            <?php if($_SESSION['rol'] == 'admin'){ ?>
                            <hr class="separator" />
                
                            <div class="sidebar-widget widget-stats">
                                <div class="widget-header">
                                    <h6>Gimnasio Seven Gym</h6>
                                    <div class="widget-toggle">+</div>
                                </div>
                                <div class="widget-content">
                                    <ul>
                                        <li>
                                            <span class="stats-title">Numero de clientes</span>
                                            <span class="stats-complete"><?php $n=numUsuarios(); echo $n ;?></span>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-primary progress-without-number" role="progressbar" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100" style="width: <?echo $n."%";?>">
                                                    
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <span class="stats-title">Numero de impagos</span>
                                            <span class="stats-complete"><?php  $m=morosos(); echo $m;?></span>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-primary progress-without-number" role="progressbar" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100" style="width: <?echo $m."%";?>">
                                                    
                                                </div>
                                            </div>
                                        </li>
                                        
                                    </ul>
                                </div>
                            </div>
                            <?php }?>
                        </div>
                
                    </div>
                
                </aside>
                <!-- end: sidebar -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Principal</h2>
					
						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.html">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><span>Principal</span></li>
							</ol>
					
							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->
					<?php if($_SESSION['rol'] == "monitor" or $_SESSION['rol'] == "socio" or $_SESSION['rol'] == "admin"){ ?>
					<section class="content-with-menu content-with-menu-has-toolbar mailbox">
						<div class="content-with-menu-container" data-mailbox data-mailbox-view="compose">
							<div class="inner-menu-toggle">
								<a href="#" class="inner-menu-expand" data-open="inner-menu">
									Mostrar Menu <i class="fa fa-chevron-right"></i>
								</a>
							</div>
							
							<menu id="content-menu" class="inner-menu" role="menu">
								<div class="nano">
									<div class="nano-content">
							
										<div class="inner-menu-toggle-inside">
											<a href="#" class="inner-menu-collapse">
												<i class="fa fa-chevron-up visible-xs-inline"></i><i class="fa fa-chevron-left hidden-xs-inline"></i> Ocultar Menu
											</a>
							
											<a href="#" class="inner-menu-expand" data-open="inner-menu">
												Mostrar Menu <i class="fa fa-chevron-down"></i>
											</a>
										</div>
							
										<div class="inner-menu-content">
											<a href="crearCorreo.php" class="btn btn-block btn-primary btn-md pt-sm pb-sm text-md">
												<i class="fa fa-envelope mr-xs"></i>
												Nuevo Email
											</a>
							
											<ul class="list-unstyled mt-xl pt-md">
												<li>
													<a href="mailBox.php" class="menu-item active">Bandeja Entrada <span class="label label-primary text-normal pull-right"><?php echo numCorreos(); ?></span></a>
												</li>
										
												<li>
													<a href="mailbox-enviados.php" class="menu-item">Enviados</a>
												</li>
												
												
											</ul>
							
											<hr class="separator" />
							
										</div>
									</div>
								</div>
							</menu>
							<div class="inner-body">
								
								<div class="mailbox-compose">
									<form class="form-horizontal form-bordered form-bordered" action = "#" method = "post" enctype = "multipart/form-data">

										<div class="inner-toolbar clearfix">
									<ul>
										<li>
											<button  type = "submit" name = "send"><i class="fa fa-send-o mr-sm"></i> Enviar</button>
										</li>
										<li>
											<button type = "reset" name = "reset"><i class="fa fa-times mr-sm"></i> Deshacer</button>
										</li>
										
									</ul>
								</div>
                               <div class="form-group form-group-invisible">
                                         <label for="cc" class="control-label-invisible">Para:</label>
                                           <div class="col-sm-offset-2 col-sm-9 col-md-offset-1 col-md-3">
                                                <select  class="form-control populate" name="para">

                                                    <optgroup label="Nombre/Apellidos">
                                                        <?php usuariosDisponibles(); ?>

                                                    </optgroup>    
                                                </select>
                                            </div>
                                        </div>

										<!--<div class="form-group form-group-invisible">
											<label for="to" class="control-label-invisible">Para:</label>
											<div class="col-sm-offset-2 col-sm-9 col-md-offset-1 col-md-10">
                                            
												<input id="to" type="text" class="form-control form-control-invisible" data-role="tagsinput" data-tag-class="label label-primary" value="" name  = "para">
											</div>
										</div>-->
							
										<div class="form-group form-group-invisible">
											<label for="cc" class="control-label-invisible">Asunto:</label>
											<div class="col-sm-offset-2 col-sm-9 col-md-offset-1 col-md-10">
												<input id="cc" type="text" class="form-control form-control-invisible" data-role="tagsinput" data-tag-class="label label-primary" value="" name = "asunto">
											</div>
										</div>
							
										<div class="form-group form-group-invisible">
											<label for="subject" class="control-label-invisible">Cuerpo:</label>
											<div class="col-sm-offset-2 col-sm-9 col-md-offset-1 col-md-10">
												<input id="subject" type="text" class="form-control form-control-invisible" value="" name = "cuerpo">
											</div>
										</div>
							
										<div class="form-group">
											<div class="compose">
												<div id="compose-field" class="compose-control">
												</div>
											</div>
										</div>

									</form>
								</div>
							</div>
						</div>
					</section>
					 <?php }else{ ?>

                    	<div class="alert alert-danger"><p class="m-none text-semibold h6">No tienes permiso para acceder a este área</p></div>  <?php                	
                    }
                    ?>
					<!-- end: page -->
				</section>
			</div>

			<aside id="sidebar-right" class="sidebar-right">
				<div class="nano">
					<div class="nano-content">
						<a href="#" class="mobile-close visible-xs">
							Collapse <i class="fa fa-chevron-right"></i>
						</a>
			
						<div class="sidebar-right-wrapper">
			
							<div class="sidebar-widget widget-calendar">
								<h6>Calendario</h6>
								<div data-plugin-datepicker data-plugin-skin="dark" ></div>
			
								<ul>
									<li>
										<time datetime="2014-04-19T00:00+00:00">04/19/2014</time>
										<span>Seven Gym</span>
									</li>
								</ul>
							</div>
			
					
			
						</div>
					</div>
				</div>
			</aside>
		</section>

		<!-- Vendor -->
		<script src="assets/vendor/jquery/jquery.js"></script>		<script src="assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>		<script src="assets/vendor/jquery-cookie/jquery.cookie.js"></script>		<script src="assets/vendor/style-switcher/style.switcher.js"></script>		<script src="assets/vendor/bootstrap/js/bootstrap.js"></script>		<script src="assets/vendor/nanoscroller/nanoscroller.js"></script>		<script src="assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>		<script src="assets/vendor/magnific-popup/magnific-popup.js"></script>		<script src="assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>
		
		<!-- Specific Page Vendor -->		<script src="assets/vendor/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>		<script src="assets/vendor/jquery-ui-touch-punch/jquery.ui.touch-punch.js"></script>		<script src="assets/vendor/jquery-appear/jquery.appear.js"></script>		<script src="assets/vendor/bootstrap-multiselect/bootstrap-multiselect.js"></script>		<script src="assets/vendor/jquery-easypiechart/jquery.easypiechart.js"></script>		<script src="assets/vendor/flot/jquery.flot.js"></script>		<script src="assets/vendor/flot-tooltip/jquery.flot.tooltip.js"></script>		<script src="assets/vendor/flot/jquery.flot.pie.js"></script>		<script src="assets/vendor/flot/jquery.flot.categories.js"></script>		<script src="assets/vendor/flot/jquery.flot.resize.js"></script>		<script src="assets/vendor/jquery-sparkline/jquery.sparkline.js"></script>		<script src="assets/vendor/raphael/raphael.js"></script>		<script src="assets/vendor/morris/morris.js"></script>		<script src="assets/vendor/gauge/gauge.js"></script>		<script src="assets/vendor/snap-svg/snap.svg.js"></script>		<script src="assets/vendor/liquid-meter/liquid.meter.js"></script>		<script src="assets/vendor/jqvmap/jquery.vmap.js"></script>		<script src="assets/vendor/jqvmap/data/jquery.vmap.sampledata.js"></script>		<script src="assets/vendor/jqvmap/maps/jquery.vmap.world.js"></script>		<script src="assets/vendor/jqvmap/maps/continents/jquery.vmap.africa.js"></script>		<script src="assets/vendor/jqvmap/maps/continents/jquery.vmap.asia.js"></script>		<script src="assets/vendor/jqvmap/maps/continents/jquery.vmap.australia.js"></script>		<script src="assets/vendor/jqvmap/maps/continents/jquery.vmap.europe.js"></script>		<script src="assets/vendor/jqvmap/maps/continents/jquery.vmap.north-america.js"></script>		<script src="assets/vendor/jqvmap/maps/continents/jquery.vmap.south-america.js"></script>
		
		<!-- Theme Base, Components and Settings -->
		<script src="assets/javascripts/theme.js"></script>
		
		<!-- Theme Custom -->
		<script src="assets/javascripts/theme.custom.js"></script>
		
		<!-- Theme Initialization Files -->
		<script src="assets/javascripts/theme.init.js"></script>
		<!-- Analytics to Track Preview Website -->		<script>		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');		  ga('create', 'UA-42715764-8', 'auto');		  ga('send', 'pageview');		</script>

		<!-- Examples -->
		<script src="assets/javascripts/dashboard/examples.dashboard.js"></script>
	</body>
</html>
<!-- Localized -->