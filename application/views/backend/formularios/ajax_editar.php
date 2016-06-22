<div class="modal-header">
    <button class="close" data-dismiss="modal">×</button>
    <h3>Edición de Formulario</h3>
</div>
<form id="formEditarCampo" class="ajaxForm form-horizontal" method="POST" action="<?=site_url('backend/formularios/editar_form/'.$formulario->id)?>">
  <div class="modal-body">
        <div class="validacion validacion-error"></div>
        <div class="control-group">
          <label class="control-label" for="nombre">Nombre</label>
          <div class="controls">
            <input type="text" id="nombre" name="nombre" value="<?=$formulario->nombre?>" />
          </div>
        </div>
        <div class="control-group">
          <div class="controls">
            <label class="checkbox">
              <input type="checkbox" id="contenedor" name="contenedor" value="<?=($formulario->contenedor == 1 ? 1 : 0)?>" <?=($formulario->contenedor == 1 ? 'checked' : '')?> />
              Mostrar fieldset contenedor
            </label>
          </div>
        </div>
        <div class="control-group <?=($formulario->contenedor == 1 ? '' : 'hidden')?>" id="leyenda_contenedor">
          <label class="control-label" for="leyenda">Leyenda de fieldset</label>
          <div class="controls">
            <input type="text" id="leyenda" name="leyenda" value="<?=$formulario->leyenda?>" />
          </div>
        </div>
  </div>
  <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Guardar</button>
      <a href="#" data-dismiss="modal" class="btn btn-link">Cerrar</a>
  </div>
</form>
