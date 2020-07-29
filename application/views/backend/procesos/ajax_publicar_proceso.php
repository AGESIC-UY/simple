<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h3 id="myModalLabel">Publicación de proceso</h3>
</div>
<div class="modal-body">
    <form id="formPublicarProceso" method='POST' class='ajaxForm' action="<?= site_url('backend/procesos/publicar/' . $proceso->id) ?>">
        <label>Esta acción dejará la versión actual del proceso disponible para los usuarios.</label>
        <label>Elija para qué trámites desea publicar este proceso:</label>
    </form>
</div>
<div class="modal-footer">
    <a href="#" onclick="javascript:$('#formPublicarProceso').submit();
            return false;" class="btn btn-primary">Nuevos trámites</a>
    <a href="#" onclick="javascript:$('#formPublicarProceso').attr('action','<?= site_url('backend/procesos/publicar/' . $proceso->id.'/1') ?>');
            $('#formPublicarProceso').submit();
            return false;" class="btn btn-primary">Todos los trámites</a>
    <button class="btn-link" data-dismiss="modal">Cerrar</button>
</div>
