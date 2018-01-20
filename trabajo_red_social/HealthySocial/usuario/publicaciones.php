<!DOCTYPE html>
<?PHP
require_once '../functions.php';

if (isset($_SESSION['usuario'])) {

    $con = connectDB();

    if (!$con) {
        die("Conexión fallida");
    }

    $user = $_SESSION['user'];
    $db_selected = mysqli_select_db($con, "healthysocial");

    if (!$db_selected) {
        die("Conexión a base de datos fallida");
    }

//consultamos el id_usuario
    $id_usuario = mysqli_query($con, "SELECT `id_usuario` FROM `usuario` WHERE `usuario` LIKE '" . $user . "';");

    if (!$id_usuario) {
        die("Error al ejecutar la consulta: " . mysqli_error($con));
    }

    $row = mysqli_fetch_array($id_usuario);

    if (!$row) {
        die("Error al ejecutar la consulta: " . mysqli_error($con));
    }

//si queremos modificar el comentario

    if (isset($_POST['modificarDescripcion'])) {
        if (preg_match("/^([a-zA-ZÁÉÍÓÚñáéíóú0-9]*[\s]*)+$/", $_POST['nuevaDescripcion'])) {
            $row1 = mysqli_query($con, "UPDATE `contenido` SET `descripcion` = '" . $_POST['nuevaDescripcion'] . "' WHERE `contenido`.`id_contenido` = " . $_POST['modificarDescripcion'] . ";");

            if (!$row1) {
                die("Error al ejecutar la consulta: " . mysqli_error($con));
            }
        } else {
            ?>  
            <script type="text/javascript">
                alert("La descripción debe contener carácteres alfanuméricos");
            </script>
            <?PHP
        }
    }

    if (isset($_POST['modificarFoto'])) {

        $row1 = mysqli_query($con, "UPDATE `foto` SET `url` = '" . $_POST['nuevaFoto'] . "' WHERE `id_foto` = " . $_POST['modificarFoto'] . ";");

        if (!$row1) {
            die("Error al ejecutar la consulta: " . mysqli_error($con));
        }
    }

    if (isset($_POST['insertarFoto'])) {

        $row1 = mysqli_query($con, "INSERT INTO `foto` (`id_foto`, `id_usuario`, `id_contenido`, `url`) VALUES (NULL, '" . $row['id_usuario'] . "', '" . $_POST['insertarFoto'] . "', '" . $_POST['nuevaFoto'] . "');");

        if (!$row1) {
            die("Error al ejecutar la consulta: " . mysqli_error($con));
        }
    }

    if (isset($_POST['modificarComentario'])) {

        if (preg_match("/^([a-zA-ZÁÉÍÓÚñáéíóú0-9]*[\s]*)+$/", $_POST['nuevoComentario' . $_POST['modificarComentario']])) {
            $row1 = mysqli_query($con, "UPDATE `comentario` SET `texto` = '" . $_POST['nuevoComentario' . $_POST['modificarComentario']] . "' WHERE `comentario`.`id_comentario` = " . $_POST['modificarComentario'] . ";");

            if (!$row1) {
                die("Error al ejecutar la consulta: " . mysqli_error($con));
            }
        } else {
            ?>  
            <script type="text/javascript">
                alert("El comentario debe contener carácteres alfanuméricos");
            </script>
            <?PHP
        }
    }
    if (isset($_POST['eliminarComentario'])) {
        $row1 = mysqli_query($con, "DELETE FROM `comentario` WHERE `id_comentario` = '" . $_POST['eliminarComentario'] . "';");

        if (!$row1) {
            die("Error al ejecutar la consulta: " . mysqli_error($con));
        }

        if (mysqli_affected_rows($con)) {
            ?>  
            <script type="text/javascript">
                alert("El comentario se ha eliminado correctamente");
            </script>
            <?PHP
        }
    }

    if (isset($_POST['eliminarFoto'])) {
        $row1 = mysqli_query($con, "DELETE FROM `foto` WHERE `id_foto` = '" . $_POST['eliminarFoto'] . "';");

        if (!$row1) {
            die("Error al ejecutar la consulta: " . mysqli_error($con));
        }

        if (mysqli_affected_rows($con)) {
            ?>  
            <script type="text/javascript">
                alert("La foto se ha eliminado correctamente");
            </script>
            <?PHP
        }
    }

    if (isset($_POST['borrar'])) {
        $row1 = mysqli_query($con, "DELETE FROM `contenido` WHERE `id_contenido` = '" . $_POST['borrar'] . "';");

        if (!$row1) {
            die("Error al ejecutar la consulta: " . mysqli_error($con));
        }
    }



//si queremos borrar un contenido
    if (isset($_POST['like']) || isset($_POST['unlike']) || isset($_POST['comentario']) || isset($_POST['borrar'])) {

        if (isset($_POST['borrar'])) {
            $row1 = mysqli_query($con, "DELETE FROM `contenido` WHERE `id_contenido` = '" . $_POST['borrar'] . "';");

            if (!$row1) {
                die("Error al ejecutar la consulta: " . mysqli_error($con));
            }
        } else if (isset($_POST['like'])) {
            $row2 = mysqli_query($con, "SELECT * FROM `voto` WHERE `id_contenido` = " . $_POST['like'] . " and id_usuario = " . $row['id_usuario'] . ";");

            if (!$row2) {
                die("Error al ejecutar la consulta: " . mysqli_error($con));
            }
            if (mysqli_num_rows($row2) == 0) {
                $row1 = mysqli_query($con, "INSERT INTO `voto` (`id_voto`, `id_contenido`, `id_usuario`) VALUES (NULL, '" . $_POST['like'] . "', '" . $row['id_usuario'] . "');");
                if (!$row1) {
                    die("Error al ejecutar la consulta: " . mysqli_error($con));
                }
            }
        } else if (isset($_POST['unlike'])) {
            $row1 = mysqli_query($con, "DELETE FROM `voto` WHERE `id_contenido` = " . $_POST['unlike'] . " and id_usuario = " . $row['id_usuario'] . ";");

            if (!$row1) {
                die("Error al ejecutar la consulta: " . mysqli_error($con));
            }
        } else if (isset($_POST['comentario'])) {

            if (preg_match("/^([a-zA-ZÁÉÍÓÚñáéíóú0-9]*[\s]*)+$/", $_POST['comentario'])) {
                $row1 = mysqli_query($con, "INSERT INTO `comentario` (`id_comentario`, `id_usuario`, `id_contenido`, `texto`) VALUES (NULL, '" . $row['id_usuario'] . "', '" . $_POST['comentario'] . "', '" . $_POST['comment'] . "');");

                if (!$row1) {
                    die("Error al ejecutar la consulta: " . mysqli_error($con));
                }
            } else {
                ?>  
                <script type="text/javascript">
                    alert("El comentario debe contener carácteres alfanuméricos");
                </script>
                <?PHP
            }
        }
    }


    if ($_SESSION['amigo'] == TRUE) {
//obtenemos el contenido del usuario amigo
        $rowContenido = mysqli_query($con, "SELECT * FROM `contenido` c , `amigo` a WHERE c.id_usuario = a.id_usuario_amigo and a.`id_usuario` = " . $row['id_usuario'] . " ORDER BY `id_contenido` DESC");
    } else {
//obtenemos el contenido del usuario
        $rowContenido = mysqli_query($con, "SELECT * FROM `contenido` WHERE `id_usuario` = " . $row['id_usuario'] . " ORDER BY `id_contenido` DESC");
    }



    if (!$rowContenido) {
        die("Error al ejecutar la consulta: " . mysqli_error($con));
    }
    ?>

    <!--
    To change this license header, choose License Headers in Project Properties.
    To change this template file, choose Tools | Templates
    and open the template in the editor.
    -->
    <html>
        <head>
            <meta charset="UTF-8" />
            <title>SocialHealthy</title>
            <meta name="viewport" content="width=device-width, user-scalabe=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
            <link rel="stylesheet" type="text/css" href="../css/style_base.css" />
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
            <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
            <link rel="shortcut icon" type="image/x-icon" href="../images/logo2.png" />
        </head>

        <body>
            <!-- mantenemos la posición actual de la página al enviar el formulario -->
            <script>
                window.onload = function () {
                    var pos = window.name || 0;
                    window.scrollTo(0, pos);
                }
                window.onunload = function () {
                    window.name = self.pageYOffset || (document.documentElement.scrollTop + document.documentElement.scrollTop);
                }
                mostradoDescrip = true;
                function mostrarDescripcion(id) {


                    if (mostradoDescrip) {
                        document.getElementById("mostrarDescripcion" + id).setAttribute("style", "display:block");
                        mostradoDescrip = false;
                    } else {
                        document.getElementById("mostrarDescripcion" + id).setAttribute("style", "display:none");
                        mostradoDescrip = true;
                    }


                }
                mostradoFoto = true;
                function mostrarFoto(id) {


                    if (mostradoFoto) {
                        document.getElementById("mostrarFoto" + id).setAttribute("style", "display:block");
                        mostradoFoto = false;
                    } else {
                        document.getElementById("mostrarFoto" + id).setAttribute("style", "display:none");
                        mostradoFoto = true;
                    }
                }
                mostradoComentario = true;
                function mostrarComentario(id) {


                    if (mostradoComentario) {
                        document.getElementById("mostrarComentario" + id).setAttribute("style", "display:block");
                        mostradoComentario = false;
                    } else {
                        document.getElementById("mostrarComentario" + id).setAttribute("style", "display:none");
                        mostradoComentario = true;
                    }
                }

                function comprobar(campo, expr) {
                    if (!expr.test(campo.value)) {
                        campo.value = "";

                        if (campo.getAttribute('id') === "descripcion") {
                            alert('El campo ' +
                                    campo.getAttribute('id') +
                                    ' debe tener carácteres alfanuméricos');
                        }

                        if (campo.getAttribute('id') === "comentario") {
                            alert('El campo ' +
                                    campo.getAttribute('id') +
                                    ' debe tener carácteres alfanuméricos');
                        }
                    }
                }
            </script>

            <?PHP include_once '../header.php'; ?>

            <div class="contendioPrincipal">

                <?PHP include_once './menuPrincipal.php'; ?>

                <section class="sectionPaginaPersonal">
                    <?PHP
                    if ($_SESSION['amigo'] == TRUE) {
                        ?><h2>Publicaciones Amigos</h2><?PHP
                    } else {
                        ?><h2>Publicaciones Personales</h2><?PHP
                    }
                    ?>

                    <article>
                        <?PHP
                        if (mysqli_num_rows($rowContenido) == 0) {

                            if ($_SESSION['amigo'] == TRUE) {
                                ?><p>Para iniciar la actividad, debes agregar primeramente 
                                    a un amigo en la secci&oacute;n "AGREGAR" situado en el
                                    padel a la izquierda de la pantalla</p><?PHP
                            } else {
                                ?><p>Para iniciar la actividad, debes publicar primeramente 
                                    un contenido en la secci&oacute;n "PUBLICAR" situado en el
                                    padel a la izquierda de la pantalla</p><?PHP
                            }
                            ?>

                            <?PHP
                        } else {

                            while ($contenido = mysqli_fetch_array($rowContenido)) {
                                ?><form method="post"><?php
                                    //realizamo una consulta para ver si el contenido tiene una foto asociada
                                    $rowFoto = mysqli_query($con, "SELECT * FROM `foto` WHERE `id_contenido` = " . $contenido['id_contenido'] . ";");

                                    if (!$rowFoto) {
                                        die("Error al ejecutar la consulta: " . mysqli_error($con));
                                    }
                                    if ($_SESSION['amigo'] == TRUE) {
                                        $rowAmigo = mysqli_query($con, "SELECT usuario FROM `usuario` WHERE `id_usuario` = " . $contenido['id_usuario_amigo'] . ";");
                                        if (!$rowAmigo) {
                                            die("Error al ejecutar la consulta: " . mysqli_error($con));
                                        }
                                        $amigo = mysqli_fetch_array($rowAmigo);
                                        ?> 
                                        <p><b>Usuario: </b><?PHP echo $amigo['usuario']; ?></p>

                                        <?PHP
                                    }
                                    //si hay 1 fila de una foto, se mostrará por pantalla
                                    if (mysqli_num_rows($rowFoto) == 1) {
                                        $foto = mysqli_fetch_array($rowFoto);
                                        ?> 

                                        <img  class="imagenesArticulos" src="<?php echo $foto['url']; ?>">

                                        <?PHP if ($_SESSION['amigo'] == FALSE) { ?>


                                            <label onclick="mostrarFoto(<?PHP echo $foto['id_foto'] ?>)" >  -> Modificar/Eliminar Foto</label>
                                            <div id="<?PHP echo "mostrarFoto" . $foto['id_foto'] ?>" style="display:none" >
                                                <input id="nuevaFoto" type="url" name="nuevaFoto"  /> 
                                                <button type="submit" name="modificarFoto" value="<?PHP echo $foto['id_foto'] ?>" >Modificar Foto</button>
                                                <button type="submit" name="eliminarFoto" value="<?PHP echo $foto['id_foto'] ?>" >Eliminar Foto</button>
                                            </div>

                                            <?PHP
                                        }
                                    } else {
                                        if ($_SESSION['amigo'] == FALSE) {
                                            ?>


                                            <label onclick="mostrarFoto(<?PHP echo $contenido['id_contenido'] ?>)" >  -> Insertar Foto</label>
                                            <div id="<?PHP echo "mostrarFoto" . $contenido['id_contenido'] ?>" style="display:none" >
                                                <input id="nuevaFoto" type="url" name="nuevaFoto"  /> 
                                                <button type="submit" name="insertarFoto" value="<?PHP echo $contenido['id_contenido'] ?>" >Insertar Foto</button>
                                            </div>

                                            <?PHP
                                        }
                                    }
                                    ?>
                                    <p id="descripcion"><b>Descripci&oacute;n: </b><?PHP echo $contenido['descripcion']; ?></p>
                                    <?PHP if ($_SESSION['amigo'] == FALSE) { ?>
                                        <label onclick="mostrarDescripcion(<?PHP echo $contenido['id_contenido'] ?>)">  -> Modificar Descripcion</label>
                                        <div id="<?PHP echo "mostrarDescripcion" . $contenido['id_contenido'] ?>" style="display:none" >
                                            <input id="descripcion" type="text" name="nuevaDescripcion" onchange="comprobar(this, /^([a-zA-ZÁÉÍÓÚñáéíóú0-9]*[\s]*)+$/)" /> 
                                            <button type="submit" name="modificarDescripcion" value="<?PHP echo $contenido['id_contenido'] ?>" >Modificar Descripci&oacute;n</button>
                                        </div>

                                        <?PHP
                                    }
                                    //comprobamos si el contenido es de deportes,alimentación o suplementos
                                    $rowAlimentacion = mysqli_query($con, "SELECT * FROM `contenido` NATURAL JOIN `alimentacion` WHERE `id_contenido` = " . $contenido['id_contenido'] . ";");
                                    $rowDeportes = mysqli_query($con, "SELECT * FROM `contenido` NATURAL JOIN `deportes` WHERE `id_contenido` = " . $contenido['id_contenido'] . ";");
                                    $rowSuplemento = mysqli_query($con, "SELECT * FROM `contenido` NATURAL JOIN `suplemento` WHERE `id_contenido` = " . $contenido['id_contenido'] . ";");

                                    if (mysqli_num_rows($rowAlimentacion) == 1) {
                                        $alimentacion = mysqli_fetch_array($rowAlimentacion);
                                        ?>

                                        <details><summary>M&aacute;s</summary>
                                            <p><b>Dieta o estudio: </b><?PHP echo $alimentacion['dieta_estudio']; ?></p>
                                            <p><b>Tipo alimentaci&oacute;n: </b><?PHP echo $alimentacion['tipo']; ?></p>
                                            <p><b>Duraci&oacute;n: </b><?PHP echo $alimentacion['duracion']; ?></p>
                                        </details><?PHP
                                    } else if (mysqli_num_rows($rowDeportes) == 1) {
                                        $deportes = mysqli_fetch_array($rowDeportes);
                                        ?>
                                        <details><summary>M&aacute;s</summary>
                                            <p><b>Nivel: </b><?PHP echo $deportes['nivel']; ?></p>
                                            <p><b>Localicaci&oacute;n: </b><?PHP echo $deportes['localizacion']; ?></p>
                                            <p><b>Tipo deporte: </b><?PHP echo $deportes['tipo']; ?></p>
                                            <p><b>Duraci&oacute;n: </b><?PHP echo $deportes['duracion']; ?></p>
                                        </details><?PHP
                                    } else if (mysqli_num_rows($rowSuplemento) == 1) {
                                        $suplemento = mysqli_fetch_array($rowSuplemento);
                                        ?><details><summary>M&aacute;s</summary>
                                            <p><b>Dosis: </b><?PHP echo $suplemento['dosis']; ?></p>
                                            <p><b>Tipo suplemento: </b><?PHP echo $suplemento['tipo']; ?></p>
                                            <p><b>Duraci&oacute;n: </b><?PHP echo $suplemento['duracion']; ?></p>
                                        </details><?PHP
                                    }
                                    ?>

                                    <?php
                                    //consultamos el total de votos
                                    $rowVoto = mysqli_query($con, "select count(*) as \"totalVotos\" from voto where id_contenido = " . $contenido['id_contenido'] . ";");
                                    if (!$rowVoto) {
                                        die("Error al ejecutar la consulta: " . mysqli_error($con));
                                    }

                                    $votos = mysqli_fetch_array($rowVoto);


                                    $idContenido = $contenido['id_contenido'];
                                    ?>

                                    <button type="submit" value="<?PHP echo $idContenido; ?>"  name="like" class="botonesSection" style="font-size:24px"><i class="fa fa-thumbs-o-up" ></i></button>
                                    <button class="botonesSection" style="font-size:24px"><?PHP echo $votos['totalVotos']; ?> <i class="fa fa-heart"></i></button>
                                    <button type="submit" value="<?PHP echo $idContenido; ?>" name="unlike" class="botonesSection"  style="font-size:24px"><i class="fa fa-thumbs-o-down"></i></button>
                                    <button  type="submit" value="<?PHP echo $idContenido; ?>"  name="borrar"  class="botonesSection"  style="font-size:24px" /><i class="fa fa-trash"></i></button>
                                    <button type="submit" value="<?PHP echo $idContenido; ?>" name="comentario" class="botonesSection"  style="font-size:24px" ><i class="fa fa-commenting-o"></i></button>
                                    <input id="comentario" onchange="comprobar(this, /^([a-zA-ZÁÉÍÓÚñáéíóú0-9]*[\s]*)+$/)" type="text" name="comment" />
                                    <?PHP
                                    $rowComentario = mysqli_query($con, "SELECT * FROM `comentario` WHERE `id_contenido` = " . $idContenido . ";");

                                    if (!$rowComentario) {
                                        die("Error al ejecutar la consulta: " . mysqli_error($con));
                                    }

                                    if (mysqli_num_rows($rowComentario) > 0) {
                                        ?><details  id="comentario"><summary>Comentarios</summary><?php
                                        while ($comentario = mysqli_fetch_array($rowComentario)) {

                                            $rowUser = mysqli_query($con, "SELECT usuario FROM `usuario` WHERE `id_usuario` = " . $comentario['id_usuario'] . ";");

                                            if (!$rowUser) {
                                                die("Error al ejecutar la consulta: " . mysqli_error($con));
                                            }
                                            $usuario = mysqli_fetch_array($rowUser);
                                            ?><p><b><?PHP echo $usuario['usuario']; ?>: </b><?PHP echo $comentario['texto'] ?> </p>

                                                <!--Modificar comentario si eres el propietario del mismo-->
                                                <?PHP if ($row['id_usuario'] == $comentario['id_usuario']) { ?>
                                                    <label onclick="mostrarComentario(<?PHP echo $comentario['id_comentario'] ?>)" >  -> Modificar/eliminar Comentario</label>
                                                    <div id="<?PHP echo "mostrarComentario" . $comentario['id_comentario'] ?>" style="display:none" >
                                                        <input id="comentario" type="text" onchange="comprobar(this, /^([a-zA-ZÁÉÍÓÚñáéíóú0-9]*[\s]*)+$/)" name="<?PHP echo "nuevoComentario" . $comentario['id_comentario'] ?>" /> 
                                                        <button itype="submit" name="modificarComentario"  value="<?PHP echo $comentario['id_comentario'] ?>" >Modificar Comentario</button>
                                                        <button itype="submit" name="eliminarComentario"  value="<?PHP echo $comentario['id_comentario'] ?>" >Eliminar Comentario</button>
                                                    </div><?PHP
                                                }
                                            }
                                            ?></details><?php
                                    }
                                    ?>
                                </form>
                                <hr />
                                <?PHP
                            }
                        }
                        ?>
                    </article>

                </section>

                <?Php
                disconnectDB($con);
                include_once '../aside.php';
                ?>
            </div>

            <?php
            include_once '../footer.php';
        } else {
            if ($_SESSION['amigo'] == TRUE) {
                $_SESSION['url'] = "usuario/publicacionAmigos.php";
            } else {
                $_SESSION['url'] = "usuario/publicacionesPersonales.php";
            }
            $_SESSION['tipo'] = 'usuario';
            header("location:../login.php");
        }
        ?>
    </body>
</html>
