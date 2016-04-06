<div class="modal-header">
    <button class="close" data-dismiss="modal">×</button>
    <h3>Edición de Formulario</h3>
</div>
<form id="formEditarCampo" class="ajaxForm form-horizontal" method="POST" action="<?=site_url('backend/bloques/editar_form/'.$bloque->id)?>">
  <div class="modal-body">
        <div class="validacion"></div>
        <div class="control-group">
          <label class="control-label" for="nombre">Nombre</label>
          <div class="controls">
            <input type="text" name="nombre" value="<?=$formulario->nombre?>" />
          </div>
        </div>
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-primary">Guardar</button>
      <!--a href="#" onclick="javascript:$('#formEditarCampo').submit();return false;" class="btn btn-primary">Guardar</a-->
      <a href="#" data-dismiss="modal" class="btn btn-link">Cerrar</a>
  </div>
</form>
