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
        <link href="assets/css/estilos-formulario-tipo.css" rel="stylesheet">
        <!-- link href="assets/css/estilos-login.css" rel="stylesheet" -->

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
      <div class="contenedorGeneral">
        <header class="header-publico header-login">
          <div class="container-fluid">
              <div class="row-fluid">
                  <div class="span7">
                      <div id="logo">
                          <span class="nombre-app"><?= Cuenta::cuentaSegunDominio()!='localhost' ? Cuenta::cuentaSegunDominio()->nombre_largo : '' ?>  - Manager</span>
                      </div>
                  </div>
                  <div class="span5">
                    <div class="logosSecundarios">
                      <ul class="listaHorizontal">
                        <li>
                          <a href="https://www.presidencia.gub.uy/" title="Ir al sitio de Presidencia">
                            <img src="<?= base_url() ?>assets/img/logoPresidencia.png" alt="Presidencia">
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>
              </div>
            </div>
        </header>
        <div id="main" tabindex="-1">
          <div class="container-fluid">
            <h1>Ingrese a SIMPLE - Manager</h1>
            <form method="post" id="loginForm" class="ajaxForm" action="<?= site_url('manager/autenticacion/login_form') ?>">
              <fieldset>
                <legend>Ingreso de usuario</legend>

                <div id="tab1" tabindex="-1">
                  <?php $this->load->view('messages') ?>
                  <div class="validacion validacion-error"></div>
                  <?php
                    if(isset($error_login)) {
                      echo $error_login;
                    }
                  ?>
                  <input type="hidden" name="redirect" value="<?= $redirect ?>" />

                  <div class="form-horizontal">
                      <div class="control-group">
                        <label class="control-label" for="email">Usuario:</label>
                        <div class="controls">
                          <input id="usuario" name="usuario" type="text" autofocus autocomplete="off" />
                        </div>
                      </div>

                      <div class="control-group">
                        <label class="control-label" for="password">Contraseña:</label>
                        <div class="controls">
                          <input id="password" name="password" type="password" autocomplete="off" />
                        </div>
                      </div>

                      <div class="control-group">
                        <div class="controls">
                          <button type="submit" class="btn-lg btn-primario">Ingresar</button>
                        </div>
                      </div>
                  </div>
                </div>
              </fieldset>
            </form>
          </div>
        </div>
      </div>
      <footer class="row-fluid">
        <div class="area2">
          <div class="container">
            <div class="pull-right">
                <img src="<?= base_url() ?>assets/img/logoTramites.png" alt="tramites.gub.uy">
            </div>
          </div>
        </div>
      </footer>


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
