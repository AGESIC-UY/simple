<ul class="breadcrumb">
    <li>
        <a href="<?= site_url('backend/ws_catalogos') ?>">Catálogo de Servicios</a> <span class="divider">/</span>
    </li>
    <li class="active"><?= $catalogo->nombre ?></li>
</ul>
<h2><?= $catalogo->nombre ?></h2>
<form class="ajaxForm" action="<?=site_url('backend/ws_catalogos/editar_form/'.$catalogo->id)?>" method="post">
  <div class="validacion validacion-error"></div>
  <fieldset>
      <legend>Datos generales</legend>
      <div class="form-horizontal">
        <div class="control-group">
          <div class="controls">
            <label class="checkbox" for="servicio_activo"><input type="checkbox" id="servicio_activo" name="activo" value="<?= $catalogo->activo ?>" <?= ($catalogo->activo == 1) ? 'checked' : ''; ?> />Activo</label>
          </div>
        </div>
        <div class="control-group">
          <label for="nombre" class="control-label">Nombre*</label>
          <div class="controls">
            <input id="nombre" class="input-xlarge" type="text" value="<?= $catalogo->nombre ?>" name="nombre" />
          </div>
        </div>
        <div class="control-group">
          <label for="wsdl" class="control-label">WSDL*</label>
          <div class="controls">
            <input id="wsdl" class="input-xxlarge" type="text" value="<?= $catalogo->wsdl ?>" name="wsdl" />
          </div>
        </div>
        <div class="control-group">
          <label for="timeout" class="control-label">Timeout de conexión y respuesta (segundos)</label>
          <div class="controls">
            <input id="timeout" class="input-small" type="text" value="<?= $catalogo->conexion_timeout ?>" name="conexion_timeout" placeholder="10" />
            <label for="respuesta_timeout" class="hidden-accessible">Timeout de conexión y respuesta (segundos)</label>
            <input class="input-small" type="text" id="respuesta_timeout" value="<?= $catalogo->respuesta_timeout ?>" name="respuesta_timeout" placeholder="10" title="Timeout de respuesta" />
          </div>
        </div>
        <div class="control-group">
          <label for="endpoint_location" class="control-label">Endpoint location*</label>
          <div class="controls">
            <input id="endpoint_location" class="input-xxlarge" type="text" value="<?= $catalogo->endpoint_location ?>" name="endpoint_location" />
          </div>
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
