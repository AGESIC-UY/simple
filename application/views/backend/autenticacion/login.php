<!DOCTYPE html>
<html lang="es">
    <head>
        <base href="<?= base_url() ?>" />
        <meta charset="utf-8">
        <title>Tramitador - Autenticación</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Le styles -->
        <link href="assets/css/bootstrap.css" rel="stylesheet">
        <link href="assets/js/bootstrap-datepicker/css/datepicker.css" rel="stylesheet">
        <link href="assets/css/common.css" rel="stylesheet">
        <link href="assets/css/estilos_extendidos.css" rel="stylesheet">
        <link href="assets/css/estilos-login.css" rel="stylesheet">

        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!-- Le fav and touch icons -->
        <link rel="shortcut icon" href="<?= base_url() ?>assets/img/favicon.png">
    </head>

    <body>
      <ul id="skip">
        <li><a href="#tab1">Ir al contenido</a></li>
      </ul>
      <form method="post" class="ajaxForm login" id="formLogin" action="<?= site_url('backend/autenticacion/login_form') ?>">
          <fieldset>
            <legend>Seleccionar el tipo de acceso</legend>

            <div class="selector-header">
              <h1>Ingrese a SIMPLE - backend</h1>
      			</div>
      			<div class="selector-wrap">
      				<div class="selector seleccionado usuario"></div>
      			</div>

            <div id="tab1" tabindex="-1">
              <?php $this->load->view('messages') ?>
              <div class="validacion"></div>
              <input type="hidden" name="redirect" value="<?= $redirect ?>" />

              <h2>Ingrese su código de usuario y contraseña</h2>
              <div class="row">
                <label for="email">Usuario:</label>
      					<span class="input-col">
                  <input id="email" name="email" type="text" >
      					</span>
      				</div>

              <div class="row">
                <label for="password">Contraseña:</label>
      					<span class="input-col">
                  <input id="password" name="password" type="password">
                  <a href="<?=site_url('backend/autenticacion/olvido')?>" class="enlace-form left">Olvidé mi contraseña</a>
      					</span>
      				</div>

              <div class="row">
      					<div>
                  <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
      					</div>
      				</div>
            </div>
          </fieldset>
      </form>




        <!-- Le javascript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="assets/js/jquery/jquery-1.8.3.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script src="assets/js/bootstrap-datepicker/js/locales/bootstrap-datepicker.es.js"></script>
        <script src="assets/js/jquery.chosen/chosen.jquery.min.js"></script> <?php //Soporte para selects con multiple choices   ?>
        <script src="assets/js/common.js"></script>
    </body>
</html>
