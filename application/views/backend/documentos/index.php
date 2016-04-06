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
    <li class="active"><a href="<?= site_url('backend/documentos/listar/' . $proceso->id) ?>">Documentos</a></li>
    <li><a href="<?=site_url('backend/acciones/listar/'.$proceso->id)?>">Acciones</a></li>
    <li><a href="<?= site_url('backend/trazabilidad/listar/' . $proceso->id) ?>">Trazabilidad</a></li>
</ul>
<div class="acciones-generales">
<a class="btn" href="<?=site_url('backend/documentos/crear/'.$proceso->id)?>"><span class="icon-file"></span> Nuevo</a>
</div>
<table class="table">
  <caption class="hide-text">Documentos</caption>
    <thead>
        <tr>
            <th>Documento</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($documentos as $p): ?>
        <tr>
            <td><?=$p->nombre?></td>
            <td class="actions">
                <a href="<?=site_url('backend/documentos/editar/'.$p->id)?>" class="btn btn-primary"><span class="icon-white icon-edit"></span> Editar<span class="hide-text"> <?= $p->nombre ?> <?=$p->id?></span></a>
                <a href="<?=site_url('backend/documentos/previsualizar/'.$p->id)?>" class="btn btn-primary"><span class="icon-white icon-zoom-in"></span> Previsualizar<span class="hide-text"> <?= $p->nombre ?> <?=$p->id?></span></a>
                <a href="<?=site_url('backend/documentos/eliminar/'.$p->id)?>" class="btn btn-danger" onclick="return confirm('¿Esta seguro que desea eliminar?')"><span class="icon-white icon-trash"></span> Eliminar<span class="hide-text"> <?= $p->nombre ?> <?=$p->id?></span></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="modal hide fade" id="modal"></div>
