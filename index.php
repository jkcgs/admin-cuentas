<?php include "include/init.php"; include "include/auth.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Administrador de cuentas">
    <meta name="author" content="Jonathan Gutiérrez">
    <!--link rel="icon" href="/favicon.ico"-->

    <title>Cuentas</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Custom styles for this template -->
    <link href="css/template.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="javascript:;">Cuentas y presupuesto</a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="#cuentas">Cuentas</a></li>
                    <li><a href="#deudores">Deudores</a></li>
                    <li><a href="#estadisticas">Estadísticas</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </nav>
    <div style="margin-bottom: 70px;"></div>

    <div class="loading-container text-center">
        <span class="mini-loading"></span> Cargando...
    </div>
    <div class="container page-container" id="cuentas" data-default-target="true">
        <div id="cont-cuentas"></div>
    </div>
    <div class="container page-container" id="deudores">
        <p class="text-right pull-right">
            <a type="button" class="btn btn-primary" href="javascript:deudas.mostrar_agregar();">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                Agregar deuda
            </a>
            <a type="button" class="btn btn-primary" href="javascript:deudores.mostrar_agregar();">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                Agregar deudor
            </a>
        </p>

        <div id="deudores-cont"></div>
        <hr>
        <div id="deudas"></div>
    </div>
    <div class="container page-container" id="estadisticas">
        <p>Cargando...</p>
    </div>

    <footer class="container text-center text-muted">
        <hr>
        Creado por Jonathan Gutiérrez &copy; 2016<br>
        Todos los derechos reservados.
        <br>&nbsp;
    </footer>


    <?php include "templates/mgr_cuenta.php"; ?>
    <?php include "templates/mgr_deudor.php"; ?>
    <?= file_get_contents("templates/cuentas.hbs") ?>
    <?= file_get_contents("templates/deudores.hbs") ?>
    <?= file_get_contents("templates/deudas.hbs") ?>
    <?= file_get_contents("templates/estadisticas.hbs") ?>
    <?= file_get_contents("templates/ext_tr.hbs") ?>

    <script src="https://code.jquery.com/jquery-2.2.3.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <script src="https://getbootstrap.com/assets/js/ie10-viewport-bug-workaround.js"></script>

    <script src='js/ractive.js'></script>
    <script src='js/functions.js'></script>
    <script src='js/navigation.js'></script>
    <script src='js/obj_base.js'></script>
    <script src='js/cuentas.js'></script>
    <script src='js/deudores.js'></script>
    <script src='js/deudas.js'></script>
    <script src='js/estadisticas.js'></script>
    <script src='js/ext_tr.js'></script>

  </body>
</html>
