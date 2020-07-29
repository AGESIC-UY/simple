<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h3 id="myModalLabel">Edición de proceso</h3>
</div>
<div class="modal-body">
    <form id="formEditarProceso" method='POST' class='ajaxForm' action="<?= site_url('backend/procesos/editar/' . $proceso->id) ?>">
        <div class='validacion'></div>
        <label>Presione editar si desea modificar el proceso publicado, de lo contrario si desea una nueva versión presione generar.</label>
    </form>
</div>
<div class="modal-footer">
    <a href="<?=site_url('backend/procesos/editar_publicado/'.$proceso->id.'/1')?>" class="btn btn-danger">Editar</a>
    <a href="<?=site_url('backend/procesos/editar_publicado/'.$proceso->id)?>" class="btn btn-primary">Generar</a>
</div>
