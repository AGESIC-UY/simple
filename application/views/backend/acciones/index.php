<script src="<?= base_url() ?>assets/js/modelador-acciones.js" type="text/javascript"></script>

<ul class="breadcrumb">
    <li>
        <a href="<?=site_url('backend/procesos')?>">Listado de Procesos</a> <span class="divider">/</span>
    </li>
    <li class="active"><?=$proceso->nombre?></li>
</ul>
<h2><?=$proceso->nombre?></h2>
<ul class="nav nav-tabs">
    <li><a href="<?=site_url('backend/procesos/editar/'.$proceso->id)?>">Diseñador</a></li>
    <li><a href="<?=site_url('backend/formularios/listar/'.$proceso->id)?>">Formularios</a></li>
    <li><a href="<?= site_url('backend/documentos/listar/' . $proceso->id) ?>">Documentos</a></li>
    <li class="active"><a href="<?=site_url('backend/acciones/listar/'.$proceso->id)?>">Acciones</a></li>
    <li><a href="<?= site_url('backend/trazabilidad/listar/' . $proceso->id) ?>">Trazabilidad</a></li>
    <li><a href="<?= site_url('backend/procesos/editar_codigo_tramite_ws_grep/' . $proceso->id) ?>">Código tramites.gub.uy</a></li>

</ul>
<div class="acciones-generales">
<a class="btn" href="#" onclick="return seleccionarAccion(<?=$proceso->id?>);"><span class="icon-file"></span> Nuevo</a>
</div>
<table class="table">
  <caption class="hide-text">Acciones</caption>
  <thead>
      <tr>
          <th>Accion</th>
          <th>Tipo</th>
          <th>Acciones</th>
      </tr>
  </thead>
  <tbody>
    <?php foreach($acciones as $p): ?>
    <tr>
      <td><?=$p->nombre?></td>
      <td><?=$p->tipo?></td>
      <td class="actions">
        <a href="<?=site_url('backend/acciones/editar/'.$p->id)?>" class="btn btn-primary"><span class="icon-edit icon-white"></span> Editar<span class="hide-text"> <?= $p->nombre ?></span></a>
        <a href="<?=site_url('backend/acciones/eliminar/'.$p->id)?>" class="btn btn-danger" onclick="return confirm('¿Esta seguro que desea eliminar?')"><span class="icon-trash icon-white"></span> Eliminar<span class="hide-text"> <?= $p->nombre ?></span></a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<div class="modal hide fade" id="modal"></div>
