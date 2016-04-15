<div class="modal-header">
    <button class="close" data-dismiss="modal">×</button>
    <h3>Editar Catalogo</h3>
</div>
<form id="formEditarCatalogo" class="ajaxForm" method="POST" action="<?=site_url('backend/ws_catalogos/editar_form/'.$catalogo->id)?>">
<div class="modal-body">
        <div class="validacion validacion-error"></div>
        <label>Nombre</label>
        <input class="input-xlarge" type="text" name="nombre" value="<?=$catalogo->nombre?>" />
        <label>WSDL</label>
        <input class="input-xxlarge" type="text" name="wsdl" value="<?=$catalogo->wsdl?>" />
</div>
<div class="modal-footer">
  <button type="submit" class="btn btn-primary">Guardar</button>
    <!--a href="#" onclick="javascript:$('#formEditarCatalogo').submit();return false;" class="btn btn-primary">Guardar</a-->
    <a href="#" data-dismiss="modal" class="btn btn-link">Cerrar</a>
</div>
</form>
