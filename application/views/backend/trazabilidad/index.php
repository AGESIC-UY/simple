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
    <li class="active"><a href="<?= site_url('backend/trazabilidad/listar/' . $proceso->id) ?>">Trazabilidad</a></li>
</ul>
<div class="titulo-form">
  <h3>Trazabilidad</h3>
</div>
<form class="ajaxForm" action="<?=site_url('backend/trazabilidad/editar_form/'.$proceso->id)?>" method="POST">
  <div class="validacion"></div>
  <input type="hidden" value="<?= $proceso->ProcesoTrazabilidad->proceso_id ?>" name="proceso_id" />
  <div class="form-horizontal">
    <div class="control-group">
      <label for="organismo" class="control-label">ID de organismo</label>
      <div class="controls">
        <input class="input-xlarge" id="organismo" type="text" value="<?= $proceso->ProcesoTrazabilidad->organismo_id ?>" name="organismo_id" />
      </div>
    </div>
    <div class="control-group">
      <label for="proceso" class="control-label">ID de proceso</label>
      <div class="controls">
        <input class="input-xlarge" id="proceso" type="text" value="<?= $proceso->ProcesoTrazabilidad->proceso_externo_id ?>" name="proceso_externo_id" />
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
