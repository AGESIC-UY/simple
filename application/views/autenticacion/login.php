<!DOCTYPE html>
<html lang="es">
    <head>
        <?php $this->load->view('head')?>
        <link href="<?= base_url() ?>assets/css/estilos-login.css" rel="stylesheet">
    </head>

    <body>
      <ul id="skip">
        <li><a href="#tab1">Ir al contenido</a></li>
      </ul>
        <form method="post" class="ajaxForm login" id="formLogin" action="<?= site_url('autenticacion/login_form') ?>">
            <fieldset>
                <legend>Autenticación</legend>
                <div class="selector-header">
                  <h1>Ingrese a SIMPLE - trámites públicos</h1>
          			</div>
          			<div class="selector-wrap">
          				<div class="selector seleccionado usuario"></div>
          			</div>

                <div id="tab1">
                  <?php $this->load->view('messages') ?>
                  <div class="validacion validacion-error"></div>
                  <input type="hidden" name="redirect" value="<?= $redirect ?>" />

                  <h2>Ingrese su código de usuario y contraseña</h2>
                  <div class="row">
                    <label for="name">Usuario o Correo electrónico</label>
          					<span class="input-col">
                      <input name="usuario" id="name" type="text" class="input-xlarge" autofocus autocomplete="off" />
          					</span>
          				</div>

                  <div class="row">
                    <label for="password">Contraseña</label>
          					<span class="input-col">
                      <input name="password" id="password" type="password" class="input-xlarge" autocomplete="off" />
                      <input type="hidden" name="redirect" value="<?=$redirect?>" />
                      <!--
                      <div><a href="<?=site_url('autenticacion/olvido')?>">¿Olvidaste tu contraseña?</a></div>
                      <div><a href="<?= site_url('autenticacion/registrar') ?>">¿No estas registrado?</a></div>
                      -->
          					</span>
          				</div>

                  <div class="row">
          					<div>
                      <button class="btn btn-primary btn-block" type="submit">Ingresar</button>
          					</div>
          				</div>
                </div>
            </fieldset>
        </form>

    </body>
</html>
