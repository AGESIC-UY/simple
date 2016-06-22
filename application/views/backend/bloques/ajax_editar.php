<div class="modal-header">
    <button class="close" data-dismiss="modal">×</button>
    <h3>Edición de Bloque</h3>
</div>
<form id="formEditarCampo" class="ajaxForm form-horizontal" method="POST" action="<?=site_url('backend/bloques/editar_form/'.$bloque->id)?>">
  <div class="modal-body">
        <div class="validacion validacion-error"></div>
        <div class="control-group">
          <label class="control-label" for="nombre">Nombre</label>
          <div class="controls">
            <input type="text" name="nombre_bloque" value="<?=$bloque->nombre?>" />
          </div>
        </div>
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-primary">Guardar</button>
      <a href="#" data-dismiss="modal" class="btn btn-link">Cerrar</a>
  </div>
</form>
