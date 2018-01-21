<?PHP
session_start();

if (isset($_SESSION['administrador'])) {
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-
strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta charset="UTF-8" />
            <title>SocialHealthy</title>
            <meta name="viewport" content="width=device-width, user-scalabe=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
            <link rel="stylesheet" type="text/css" href="../css/style_base.css" />
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
            <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
            <link rel="shortcut icon" type="image/x-icon" href="images/logoUrl.ico" />
        </head>
        <body>

            <?PHP include_once '../header.php'; ?>

            <div class="contendioPrincipal">

                <?PHP include_once './menuPrincipalAdministrador.php'; ?>

                <section class="sectionPaginaPersonal">

                    <p><b>Bienvenido <?PHP echo $_SESSION['user']; ?></b></p>
                    <ol>Como administrador del sistema podrás realizar las siguientes funciones:
                        <li>Eliminar un usuario en concreto</li>
                        <li>Eliminar el contenido de un usuario</li>
                        <li>Ver estadísticas de los usuarios para mejorar la aplicación</li>
                    </ol>

                </section>

                <?php
                include_once '../aside.php';
                ?>
            </div>

            <?php
            include_once '../footer.php';
        } else {
            $_SESSION['url'] = "administrador/indexAdministrador.php";
            $_SESSION['tipo'] = 'administrador';
            header("location:../login.php");
        }
        ?>
    </body>
</html>
