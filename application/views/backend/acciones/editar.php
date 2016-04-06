<ul class="breadcrumb">
    <li>
        <a href="<?= site_url('backend/procesos') ?>">Listado de Procesos</a> <span class="divider">/</span>
    </li>
    <li>
        <a href="<?= site_url('backend/acciones/listar/' . $proceso->id) ?>"><?= $proceso->nombre ?></a> <span class="divider">/</span>
    </li>
    <li class="active"><?= $accion->nombre ?></li>
</ul>
<h2><?=$proceso->nombre?></h2>
<ul class="nav nav-tabs">
    <li><a href="<?= site_url('backend/procesos/editar/' . $proceso->id) ?>">Diseñador</a></li>
    <li><a href="<?= site_url('backend/formularios/listar/' . $proceso->id) ?>">Formularios</a></li>
    <li><a href="<?= site_url('backend/documentos/listar/' . $proceso->id) ?>">Documentos</a></li>
    <li class="active"><a href="<?= site_url('backend/acciones/listar/' . $proceso->id) ?>">Acciones</a></li>
    <li><a href="<?= site_url('backend/trazabilidad/listar/' . $proceso->id) ?>">Trazabilidad</a></li>
</ul>


<form class="ajaxForm" method="POST" action="<?=site_url('backend/acciones/editar_form/'.($edit?$accion->id:''))?>">
  <div class="titulo-form">
    <h3><?= $edit ? $accion->nombre : 'Acción' ?></h3>
  </div>
  <fieldset>
      <legend>Datos generales</legend>
      <div class="validacion"></div>
      <?php if(!$edit):?>
      <input type="hidden" name="proceso_id" value="<?=$proceso->id?>" />
      <input type="hidden" name="tipo" value="<?=$tipo?>" />
      <?php endif; ?>
      <div class="form-horizontal">
        <div class="control-group">
          <label for="nombre" class="control-label">Nombre de la acción</label>
          <div class="controls">
            <input id="nombre" type="text" name="nombre" value="<?=$edit?$accion->nombre:''?>" />
          </div>
        </div>
        <div class="control-group">
          <label for="tipo" class="control-label">Tipo</label>
          <div class="controls">
            <input id="tipo" type="text" readonly value="<?=$edit?$accion->tipo:$tipo?>" />
          </div>
        </div>
      </div>
  </fieldset>
  <fieldset><!-- TODO que no aparezca si no tiene otros datos (por ejemplo en pasarela de pagos) -->
      <legend>Otros datos</legend>
        <?php if(isset($operacion)): ?>
            <?=$accion->displayForm($operacion)?>
        <?php else: ?>
            <?=$accion->displayForm()?>
        <?php endif; ?>
    </fieldset>
    <ul class="form-action-buttons">
        <li class="action-buttons-primary">
            <ul>
                <li>
                  <input class="btn btn-primary" type="submit" value="Guardar" />
                </li>
            </ul>
        </li>
        <li class="action-buttons-second">
            <ul>
                <li class="float-left">
                  <a class="btn btn-link" href="<?=site_url('backend/acciones/listar/'.$proceso->id)?>">Cancelar</a>
                </li>
            </ul>
        </li>
    </ul>
</form>
