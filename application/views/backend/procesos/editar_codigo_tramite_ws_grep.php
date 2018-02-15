<ul class="breadcrumb">
  <li>
      <a href="<?= site_url('backend/procesos') ?>">Listado de Procesos</a> <span class="divider">/</span>
  </li>
  <li class="active"><?= $proceso->nombre ?></li>
</ul>
<h2><?=$proceso->nombre?></h2>
<ul class="nav nav-tabs">
    <li><a href="<?=site_url('backend/procesos/editar/'.$proceso->id)?>">Diseñador</a></li>
    <li><a href="<?=site_url('backend/formularios/listar/'.$proceso->id)?>">Formularios</a></li>
    <li><a href="<?= site_url('backend/documentos/listar/' . $proceso->id) ?>">Documentos</a></li>
    <li><a href="<?=site_url('backend/acciones/listar/'.$proceso->id)?>">Acciones</a></li>
    <li><a href="<?= site_url('backend/trazabilidad/listar/' . $proceso->id) ?>">Trazabilidad</a></li>
    <li  class="active"><a href="<?= site_url('backend/procesos/editar_codigo_tramite_ws_grep/' . $proceso->id) ?>">Código tramites.gub.uy</a></li>
</ul>
<div class="titulo-form">
  <h3>Código tramites.gub.uy</h3>
</div>
<form class="ajaxForm" action="<?=site_url('backend/procesos/editar_form_codigo_tramite_ws_grep/'.$proceso->id)?>" method="POST">
  <div class="validacion validacion-error"></div>
  <div class="form-horizontal">
    <div class="control-group">
      <label for="organismo" class="control-label">Código</label>
      <div class="controls">
        <input class="input-xlarge" id="codigo_tramite_ws_grep" type="text" value="<?= $proceso->ProcesoTrazabilidad->proceso_externo_id ?>" name="codigo_tramite_ws_grep" />
      </div>
    </div>

    <div class="control-group">
      <span class="control-label"></span>
      <div class="controls">
        <input class="btn btn-primary" type="submit" value="Guardar" />
      </div>
    </div>
  </div>
</form>
