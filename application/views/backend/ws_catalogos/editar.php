<ul class="breadcrumb">
    <li>
        <a href="<?= site_url('backend/ws_catalogos') ?>">Catálogo de Servicios</a> <span class="divider">/</span>
    </li>
    <li class="active"><?= $catalogo->nombre ?></li>
</ul>
<a href="#" class="btn btn-ayuda btn-secundary" id="ayuda_contextual_servicios_editar"><span class="icon-white icon-question-sign"></span> Ayuda</a>

<h2><?= $catalogo->nombre ?></h2>
<form class="ajaxForm" action="<?=site_url('backend/ws_catalogos/editar_form/'.$catalogo->id)?>" method="post">
  <div class="validacion validacion-error"></div>
  <fieldset>
      <legend>Datos generales</legend>
      <div class="form-horizontal">
        <div class="control-group">
          <div class="controls">
            <label class="checkbox tipo_ws" for="tipo"><input type="radio" id="servicio_tipo_pdi" name="tipo" value="pdi" <?= ($catalogo->tipo == 'pdi') ? 'checked' : ''; ?> />PDI</label>
            <label id="accion-tipo-servicio" class="checkbox tipo_ws" for="tipo"><input type="radio" id="servicio_tipo_soap" name="tipo" value="soap" <?= ($catalogo->tipo == 'soap') ? 'checked' : ''; ?> />SOAP</label>
          </div>
        </div>
        <div class="control-group">
          <div class="controls">
            <label class="checkbox" for="servicio_activo"><input type="checkbox" id="servicio_activo" name="activo" value="<?= $catalogo->activo ?>" <?= ($catalogo->activo == 1) ? 'checked' : ''; ?> />Activo</label>
          </div>
        </div>
        <div class="control-group">
          <label for="nombre" class="control-label">Nombre*</label>
          <div class="controls" id="accion-nombre-servicio">
            <input id="nombre" class="input-xlarge" type="text" value="<?= $catalogo->nombre ?>" name="nombre" />
          </div>
        </div>
        <div id="form_pdi">
          <div class="control-group">
            <label for="timeout" class="control-label">Tiempo de espera de conexión y respuesta (segundos)</label>
            <div class="controls">
              <input id="timeout" class="input-small" type="text" value="<?= ($catalogo->conexion_timeout == 0 ? 10 : $catalogo->conexion_timeout) ?>" name="conexion_timeout"  />
              <label for="respuesta_timeout" class="hidden-accessible">Tiempo de espera de conexión y respuesta (segundos)</label>
              <input class="input-small" type="text" id="respuesta_timeout" value="<?= ($catalogo->respuesta_timeout == 0 ? 10 : $catalogo->respuesta_timeout) ?>" name="respuesta_timeout" title="Timeout de respuesta" />
            </div>
          </div>
          <div class="control-group">
            <label for="url_fisica" class="control-label">URL física*</label>
            <div class="controls">
              <input id="url_fisica" class="input-xxlarge" type="text" value="<?= $catalogo->url_fisica ?>" name="url_fisica" />
            </div>
          </div>
          <div class="control-group">
            <label for="url_logica" class="control-label">URL lógica*</label>
            <div class="controls">
              <input id="url_logica" class="input-xxlarge" type="text" value="<?= $catalogo->url_logica ?>" name="url_logica" />
            </div>
          </div>
          <div class="control-group">
            <label for="rol" class="control-label">Rol*</label>
            <div class="controls">
              <input id="rol" class="input-xxlarge" type="text" value="<?= $catalogo->rol ?>" name="rol" />
            </div>
          </div>
        </div>
        <div id="form_soap">
          <div class="control-group">
            <label for="wsdl" class="control-label">URL del WSDL*</label>
            <div class="controls">
              <input id="wsdl" class="input-xxlarge" type="text" value="<?= $catalogo->wsdl ?>" name="wsdl" />
            </div>
          </div>
          <div class="control-group">
            <label for="timeout" class="control-label">Tiempo de espera de conexión y respuesta (segundos)</label>
            <div class="controls">
              <input id="timeout" class="input-small" type="text" value="<?= ($catalogo->conexion_timeout == 0 ? 10 : $catalogo->conexion_timeout) ?>" name="conexion_timeout" />
              <label for="respuesta_timeout" class="hidden-accessible">Tiempo de espera de conexión y respuesta (segundos)</label>
              <input class="input-small" type="text" id="respuesta_timeout" value="<?= ($catalogo->respuesta_timeout == 0 ? 10 : $catalogo->respuesta_timeout) ?>" name="respuesta_timeout" title="Timeout de respuesta" />
            </div>
          </div>
          <div class="control-group">
            <label for="endpoint_location" class="control-label">URL del servicio (endpoint)*</label>
            <div class="controls">
              <input id="endpoint_location" class="input-xxlarge" type="text" value="<?= $catalogo->endpoint_location ?>" name="endpoint_location" />
            </div>
          </div>

          <!-- AUTH -->
          <div class="control-group">
            <div class="controls">
              <label class="checkbox ws_auth" for="tipo"><input type="checkbox" id="requiere_autenticacion" name="requiere_autenticacion" <?= ($catalogo->requiere_autenticacion == 1 ? 'checked' : '') ?> /> Requiere autenticación</label>
            </div>
          </div>
          <div class="control-group autenticacion_soap tipo_autenticacion <?= ($catalogo->requiere_autenticacion == 1 ? '' : 'hidden') ?>">
            <div class="controls">
              <label class="checkbox tipo_ws" for="autenticacion_basica"><input type="radio" id="autenticacion_basica" name="requiere_autenticacion_tipo" value="autenticacion_basica" <?= ($catalogo->requiere_autenticacion == 1 ? $catalogo->requiere_autenticacion_tipo == 'autenticacion_basica' ? 'checked' : '' : '') ?> /> Autenticación básica</label>
              <label class="checkbox tipo_ws" for="autenticacion_mutua"><input type="radio" id="autenticacion_mutua" name="requiere_autenticacion_tipo" value="autenticacion_mutua" <?= ($catalogo->requiere_autenticacion == 1 ? $catalogo->requiere_autenticacion_tipo == 'autenticacion_mutua' ? 'checked' : '' : '') ?> /> Autenticación mútua</label>
              <label class="checkbox tipo_ws hidden" for="autenticacion_token"><input type="radio" id="autenticacion_token" name="requiere_autenticacion_tipo" value="autenticacion_token" <?= ($catalogo->requiere_autenticacion == 1 ? $catalogo->requiere_autenticacion_tipo == 'autenticacion_token' ? 'checked' : '' : '') ?> /> Autenticación con token</label>
            </div>
          </div>

          <!-- Basica -->
          <div class="control-group autenticacion_soap tipo_autenticacion_basica <?= ($catalogo->requiere_autenticacion == 1 ? $catalogo->requiere_autenticacion_tipo == 'autenticacion_basica' ? '' : 'hidden' : 'hidden') ?>">
            <label for="autenticacion_basica_user" class="control-label">Usuario</label>
            <div class="controls">
              <input id="autenticacion_basica_user" class="input-large" type="text" name="autenticacion_basica_user" value="<?= $catalogo->autenticacion_basica_user ?>" />
            </div>
          </div>
          <div class="control-group autenticacion_soap tipo_autenticacion_basica <?= ($catalogo->requiere_autenticacion == 1 ? $catalogo->requiere_autenticacion_tipo == 'autenticacion_basica' ? '' : 'hidden' : 'hidden') ?>">
            <label for="autenticacion_basica_pass" class="control-label">Contraseña</label>
            <div class="controls">
              <input id="autenticacion_basica_pass" class="input-large" type="password" name="autenticacion_basica_pass" value="<?= $catalogo->autenticacion_basica_pass ?>" />
            </div>
          </div>
          <div class="control-group autenticacion_soap tipo_autenticacion_basica <?= ($catalogo->requiere_autenticacion == 1 ? $catalogo->requiere_autenticacion_tipo == 'autenticacion_basica' ? '' : 'hidden' : 'hidden') ?>">
            <label for="autenticacion_basica_cert" class="control-label">Certificado del servidor (pem)</label>
            <div class="controls">
              <div id="file-uploader-certificado"></div>
              <input id="autenticacion_basica_cert" type="hidden" name="autenticacion_basica_cert" value="<?= $catalogo->autenticacion_basica_cert ?>" />
              <script>
                  var uploader = new qq.FileUploader({
                      element: document.getElementById('file-uploader-certificado'),
                      action: site_url+'backend/uploader/certificado_autenticacion_soap_basica',
                      onComplete: function(id,filename,respuesta){
                          $("input[name=autenticacion_basica_cert]").val(respuesta.file_name);
                      }
                  });
              </script>
            </div>
          </div>
          <!-- /Basica -->

          <!-- Mutua -->
          <div class="control-group autenticacion_soap tipo_autenticacion_mutua <?= ($catalogo->requiere_autenticacion == 1 ? $catalogo->requiere_autenticacion_tipo == 'autenticacion_mutua' ? '' : 'hidden' : 'hidden') ?>">
            <label for="autenticacion_mutua_client" class="control-label">Certificado del cliente (pem)</label>
            <div class="controls">
              <div id="file-uploader-mutua_client"></div>
              <input id="autenticacion_mutua_client" type="hidden" name="autenticacion_mutua_client" value="<?= $catalogo->autenticacion_mutua_client ?>" />
              <script>
                  var uploader = new qq.FileUploader({
                      element: document.getElementById('file-uploader-mutua_client'),
                      action: site_url+'backend/uploader/certificado_autenticacion_soap_mutua',
                      onComplete: function(id,filename,respuesta){
                          $("input[name=autenticacion_mutua_client]").val(respuesta.file_name);
                      }
                  });
              </script>
            </div>
          </div>
          <div class="control-group autenticacion_soap tipo_autenticacion_mutua <?= ($catalogo->requiere_autenticacion == 1 ? $catalogo->requiere_autenticacion_tipo == 'autenticacion_mutua' ? '' : 'hidden' : 'hidden') ?>">
            <label for="autenticacion_mutua_client_pass" class="control-label">Contraseña del certificado del cliente</label>
            <div class="controls">
              <input id="autenticacion_mutua_client_pass" class="input-large" type="password" name="autenticacion_mutua_client_pass" value="<?= $catalogo->autenticacion_mutua_client_pass ?>" />
            </div>
          </div>
          <div class="control-group autenticacion_soap tipo_autenticacion_mutua <?= ($catalogo->requiere_autenticacion == 1 ? $catalogo->requiere_autenticacion_tipo == 'autenticacion_mutua' ? '' : 'hidden' : 'hidden') ?>">
            <label for="autenticacion_mutua_server" class="control-label">Clave privada</label>
            <div class="controls">
              <div id="file-uploader-mutua_client_key"></div>
              <input id="autenticacion_mutua_client_key" type="hidden" name="autenticacion_mutua_client_key" value="<?= $catalogo->autenticacion_mutua_client_key ?>" />
              <script>
                  var uploader = new qq.FileUploader({
                      element: document.getElementById('file-uploader-mutua_client_key'),
                      action: site_url+'backend/uploader/certificado_autenticacion_soap_mutua',
                      onComplete: function(id,filename,respuesta){
                          $("input[name=autenticacion_mutua_client_key]").val(respuesta.file_name);
                      }
                  });
              </script>
            </div>
          </div>
          <div class="control-group autenticacion_soap tipo_autenticacion_mutua <?= ($catalogo->requiere_autenticacion == 1 ? $catalogo->requiere_autenticacion_tipo == 'autenticacion_mutua' ? '' : 'hidden' : 'hidden') ?>">
            <label for="autenticacion_mutua_server" class="control-label">Certificado del servidor (pem)</label>
            <div class="controls">
              <div id="file-uploader-mutua_server"></div>
              <input id="autenticacion_mutua_server" type="hidden" name="autenticacion_mutua_server" value="<?= $catalogo->autenticacion_mutua_server ?>" />
              <script>
                  var uploader = new qq.FileUploader({
                      element: document.getElementById('file-uploader-mutua_server'),
                      action: site_url+'backend/uploader/certificado_autenticacion_soap_mutua',
                      onComplete: function(id,filename,respuesta){
                          $("input[name=autenticacion_mutua_server]").val(respuesta.file_name);
                      }
                  });
              </script>
            </div>
          </div>
          <div class="control-group autenticacion_soap tipo_autenticacion_mutua <?= ($catalogo->requiere_autenticacion == 1 ? $catalogo->requiere_autenticacion_tipo == 'autenticacion_mutua' ? '' : 'hidden' : 'hidden') ?>">
            <p>En caso de requerir adicionalmente autenticación básica:</p>
            <label for="autenticacion_mutua_user" class="control-label">Usuario</label>
            <div class="controls">
              <input id="autenticacion_mutua_user" class="input-large" type="text" name="autenticacion_mutua_user" value="<?= $catalogo->autenticacion_mutua_user ?>" />
            </div>
          </div>
          <div class="control-group autenticacion_soap tipo_autenticacion_mutua <?= ($catalogo->requiere_autenticacion == 1 ? $catalogo->requiere_autenticacion_tipo == 'autenticacion_mutua' ? '' : 'hidden' : 'hidden') ?>">
            <label for="autenticacion_mutua_pass" class="control-label">Contraseña</label>
            <div class="controls">
              <input id="autenticacion_mutua_pass" class="input-large" type="password" name="autenticacion_mutua_pass" value="<?= $catalogo->autenticacion_mutua_pass ?>" />
            </div>
          </div>
          <!-- /Mutua -->

          <!-- /AUTH -->

        </div>
      </div>
  </fieldset>
  <ul class="form-action-buttons">
      <li class="action-buttons-primary">
          <ul>
              <li>
                <input class="btn btn-primary btn-lg" type="submit" value="Guardar" />
              </li>
          </ul>
      </li>
      <li class="action-buttons-second">
          <ul>
              <li class="float-left">
                <a class="btn btn-link btn-lg" href="<?=site_url('backend/ws_catalogos')?>">Cancelar</a>
              </li>
          </ul>
      </li>
  </ul>
</form>
