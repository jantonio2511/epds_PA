<?php 
session_start();
if(isset($_SESSION['rol'])==NULL or $_SESSION['rol']!='monitor'){
	header("Location:404.html");
}

if(isset($_POST['altaEjercicio'])){
	
	if(!isset($_POST['grupoMuscular']) or $_POST['grupoMuscular'] == ""){
		$fails[] = "El grupo muscular es incorrecto";
	}else{
		$datos['grupoMuscular'] = filter_var(filter_input(INPUT_POST, 'grupoMuscular',FILTER_SANITIZE_SPECIAL_CHARS));
	}

	if(!isset($_POST['observaciones']) or $_POST['observaciones'] == ""){
		$fails[] = "Las observaciones son incorrectas";
	}else{
		$datos['observaciones'] = filter_var(filter_input(INPUT_POST, 'observaciones', FILTER_SANITIZE_SPECIAL_CHARS));
	}

	if(!isset($_POST['numRep']) or $_POST['numRep'] == "" or !preg_match('/^[0-9]*$/', $_POST['numRep'] )){
		$fails[] = "El numero de repeticiones es incorrecto";
	}else{
		$datos['numRep'] = filter_var(filter_input(INPUT_POST, 'numRep',FILTER_SANITIZE_NUMBER_INT));
	}

	if(!isset($_POST['nombreEjercicio']) or $_POST['nombreEjercicio'] == ""){
		$fails[] = "El nombre del ejercicio es incorrecto";
	}else{
		$datos['nombreEjercicio'] = filter_var(filter_input(INPUT_POST, 'nombreEjercicio', FILTER_SANITIZE_SPECIAL_CHARS));
	}

		if(!isset($_POST['peso']) or $_POST['peso'] == "" or !preg_match('/^[0-9]*$/', $_POST['peso'] )){
		$fails[] = "El peso es incorrecto";
	}else{
		$datos['peso'] = filter_var(filter_input(INPUT_POST, 'peso',FILTER_SANITIZE_NUMBER_INT));
	}

	if(!isset($_POST['numSeries']) or $_POST['numSeries'] == "" or !preg_match('/^[0-9]*$/', $_POST['numSeries'] )){
		$fails[] = "El numero de series es incorrecto";
	}else{
		$datos['numSeries'] = filter_var(filter_input(INPUT_POST, 'numSeries',FILTER_SANITIZE_NUMBER_INT));
	}

	if(!isset($_POST['video1']) or $_POST['video1'] == "" or !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $_POST['video1'] )){
		$fails[] = "El video introducido es incorrecto";
	}else{
		$datos['video1'] = filter_var(filter_input(INPUT_POST, 'video1', FILTER_SANITIZE_SPECIAL_CHARS));
	}


if(($_FILES['image1']['error']>0 or !comprobarTipo($_FILES['image1']['type']) or $_FILES['image1']['size']>1024*1024) or ($_FILES['image2']['error']>0 or !comprobarTipo($_FILES['image2']['type']) or $_FILES['image2']['size']>1024*1024)){
		$fails[] = "Error al adjuntar las imagenes";
	}else{
		$ruta1 = "ejercicios/img1".time().$_FILES['image1']['name'];
		$ruta2 = "ejercicios/img2".time().$_FILES['image2']['name'];
		move_uploaded_file($_FILES['image1']['tmp_name'], $ruta1);
		move_uploaded_file($_FILES['image2']['tmp_name'], $ruta2);
		$datos['image1'] = $ruta1;
		$datos['image2'] = $ruta2;
	}

	if(isset($fails)){
		
	}else{
		insertar($datos);
		header("Location:listarEjercicios.php");
	}

}

function comprobarTipo($tipo){
	$tipos = array('image/jpg','image/jpeg','image/gif','image/png');
	return in_array($tipo, $tipos);
}
function insertar($datos){
	include 'conexion.php';
		$sql = "insert into ejercicio (grupo_muscular,observacion,repeticiones,nombre_ejercicio,peso,series,imagen1,imagen2,video) values ( '".$datos['grupoMuscular']."','".$datos['observaciones']."',".$datos['numRep'].",'".$datos['nombreEjercicio']."',".$datos['peso'].",".$datos['numSeries'].",'".$datos['image1']."','".$datos['image2']."','".$datos['video1']."')";
		mysql_query($sql,$conexion);
		mysql_close($conexion);
}



function morosos(){
	$cont = 0;
	include 'conexion.php';
	$sql = "SELECT t2.pago_restante FROM socios as t1 join pago as t2 where t1.id_usuario = t2.id_socio and t2.pago_restante > 0";

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
                               fotoMonitor($_SESSION['nick']);
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
                                    <a role="menuitem" tabindex="-1" href="lockPerfil.php" ><i class="fa fa-lock"></i> Bloquear Perfil</a>
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
					
                    <?php if(isset($fails)){
        echo "<div class ='alert alert-danger'>";
        echo "<ul>";
        foreach ($fails as $f ) {
            echo "<li><p class = 'm-none text-semibold h6'>".$f."</p></li>";
        }
        echo "</ul></div>";
    }?>

					<!-- start: Alta Ejercicio -->
					<?php if($_SESSION['rol'] == "monitor"){ ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <section class="panel">

                                <div class="panel-body">
                                    <form class="form-horizontal form-bordered" action="#" method="post" enctype = "multipart/form-data"> 
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="inputDefault">Grupo Muscular</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" id="inputDefault" name = "grupoMuscular">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="textareaAutosize">Observaciones</label>
                                            <div class="col-md-6">
                                                <textarea class="form-control" name = "observaciones" rows="3" id="textareaAutosize" data-plugin-textarea-autosize></textarea>
                                            </div>
                                        </div>
  										 <div class="form-group">
                                            <label class="col-md-3 control-label">Numero Repeticiones</label>
                                            <div class="col-md-6">
                                                <div data-plugin-spinner>
                                                    <div class="input-group input-small">
                                                        <input type="text" name = "numRep" class="spinner-input form-control" >
                                                        <div class="spinner-buttons input-group-btn btn-group-vertical">
                                                            <button type="button" class="btn spinner-up btn-xs btn-default">
                                                                <i class="fa fa-angle-up"></i>
                                                            </button>
                                                            <button type="button" class="btn spinner-down btn-xs btn-default">
                                                                <i class="fa fa-angle-down"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="inputDefault">Nombre</label>
                                            <div class="col-md-6">
                                                <input type="text" name = "nombreEjercicio" class="form-control" id="inputDefault">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Peso</label>
                                            <div class="col-md-6">
                                                <div class="m-md slider-primary1" data-plugin-slider data-plugin-options='{ "value": 50, "range": "min", "max": 250 }' data-plugin-slider-output="#listenSlider">
                                                    <input id="listenSlider" name = "peso" type="hidden" value="50" />
                                                </div>
                                                <p class="output">El <code>peso</code> es de: <b>50</b> kg</p>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Numero Series</label>
                                            <div class="col-md-6">
                                                <div data-plugin-spinner>
                                                    <div class="input-group input-small">
                                                        <input type="text" name = "numSeries" class="spinner-input form-control" >
                                                        <div class="spinner-buttons input-group-btn btn-group-vertical">
                                                            <button type="button" class="btn spinner-up btn-xs btn-default">
                                                                <i class="fa fa-angle-up"></i>
                                                            </button>
                                                            <button type="button" class="btn spinner-down btn-xs btn-default">
                                                                <i class="fa fa-angle-down"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                         <div class="form-group">
                                            <label class="col-md-3 control-label">Imagen 1</label>
                                            <div class="col-md-6">
                                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                                    <div class="input-append">
                                                        <div class="uneditable-input">
                                                            <i class="fa fa-file fileupload-exists"></i>
                                                            <span class="fileupload-preview"></span>
                                                        </div>
                                                        <span class="btn btn-default btn-file">
                                                        <span class="fileupload-new">Selecciona Imagen</span>
                                                            <input type="file" name = "image1" />
                                                        </span>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                         <div class="form-group">
                                            <label class="col-md-3 control-label">Imagen 2</label>
                                            <div class="col-md-6">
                                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                                    <div class="input-append">
                                                        <div class="uneditable-input">
                                                            <i class="fa fa-file fileupload-exists"></i>
                                                            <span class="fileupload-preview"></span>
                                                        </div>
                                                        <span class="btn btn-default btn-file">
                                                            <span class="fileupload-new">Selecciona Imagen</span>
                                                            <input type="file" name = "image2" />
                                                        </span>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-md-3 control-label" for="inputDefault">Video</label>
                                            <div class="col-md-6">
                                                <input type="text" name = "video1" class="form-control" id="inputDefault">
                                            </div>
                                        </div>
                                        

                                        <div class="form-group" align="center">
                                            <button type="submit" class="mb-xs mt-xs mr-xs btn btn-warning btn-lg" value="enviar" name="altaEjercicio">Enviar</button>
                                            <button type="reset" class="mb-xs mt-xs mr-xs btn btn-warning btn-lg" value="reset" name="reset">Resetear</button>

                                        </div>
                                    </form>

                                </div>

                            </section>
                        </div>
                    </div>
                    <?php }else{ ?>

                    	<div class="alert alert-danger"><p class="m-none text-semibold h6">No tienes permiso para acceder a este área</p></div>  <?php                	
                    }
                    ?>
                    <!-- end: page -->
                    
                </section>
					
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

        <!-- Specific Page Vendor -->		<script src="assets/vendor/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>		<script src="assets/vendor/jquery-ui-touch-punch/jquery.ui.touch-punch.js"></script>		<script src="assets/vendor/select2/select2.js"></script>		<script src="assets/vendor/bootstrap-multiselect/bootstrap-multiselect.js"></script>		<script src="assets/vendor/jquery-maskedinput/jquery.maskedinput.js"></script>		<script src="assets/vendor/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>		<script src="assets/vendor/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>		<script src="assets/vendor/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>		<script src="assets/vendor/fuelux/js/spinner.js"></script>		<script src="assets/vendor/dropzone/dropzone.js"></script>		<script src="assets/vendor/bootstrap-markdown/js/markdown.js"></script>		<script src="assets/vendor/bootstrap-markdown/js/to-markdown.js"></script>		<script src="assets/vendor/bootstrap-markdown/js/bootstrap-markdown.js"></script>		<script src="assets/vendor/codemirror/lib/codemirror.js"></script>		<script src="assets/vendor/codemirror/addon/selection/active-line.js"></script>		<script src="assets/vendor/codemirror/addon/edit/matchbrackets.js"></script>		<script src="assets/vendor/codemirror/mode/javascript/javascript.js"></script>		<script src="assets/vendor/codemirror/mode/xml/xml.js"></script>		<script src="assets/vendor/codemirror/mode/htmlmixed/htmlmixed.js"></script>		<script src="assets/vendor/codemirror/mode/css/css.js"></script>		<script src="assets/vendor/summernote/summernote.js"></script>		<script src="assets/vendor/bootstrap-maxlength/bootstrap-maxlength.js"></script>		<script src="assets/vendor/ios7-switch/ios7-switch.js"></script>

        <!-- Theme Base, Components and Settings -->
        <script src="assets/javascripts/theme.js"></script>

        <!-- Theme Custom -->
        <script src="assets/javascripts/theme.custom.js"></script>

        <!-- Theme Initialization Files -->
        <script src="assets/javascripts/theme.init.js"></script>
       
        <!-- Examples -->
        <script src="assets/javascripts/forms/examples.advanced.form.js" /></script>
	</body>
</html>
<!-- Localized -->