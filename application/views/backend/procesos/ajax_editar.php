<div class="modal-header">
    <button class="close" data-dismiss="modal">×</button>
    <h3>Editar Proceso</h3>
</div>
<form id="formEditarProceso" class="ajaxForm form-horizontal" method="POST" action="<?=site_url('backend/procesos/editar_form/'.$proceso->id)?>">
<div class="modal-body">
      <div class="validacion"></div>
      <div class="control-group">
        <label class="control-label" for="nombre">Nombre</label>
        <div class="controls">
          <input type="text" name="nombre" id="nombre" value="<?=$proceso->nombre?>" />
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="width">Tamaño de la Grilla</label>
        <div class="controls form-inline">
          <input type="text" name="width" id="width" value="<?=$proceso->width?>" class="input-small" /> <label for="height">X</label> <input type="text" id="height" name="height" value="<?=$proceso->height?>" class="input-small" />
        </div>
      </div>
</div>
<div class="modal-footer">
  <button type="submit" class="btn btn-primary">Guardar</button>
    <!--a href="#" onclick="javascript:$('#formEditarProceso').submit();return false;" class="btn btn-primary">Guardar</a-->
    <a href="#" data-dismiss="modal" class="btn btn-link">Cerrar</a>
</div>
</form>
