<script src="<?= base_url() ?>assets/js/modelador-acciones.js" type="text/javascript"></script>

<ul class="breadcrumb">
    <li>
        <a href="<?=site_url('backend/reportes')?>">Gestión</a> <span class="divider">/</span>
    </li>
    <li class="active"><?=$proceso->nombre?></li>
</ul>
<h2>Gestión de <?=$proceso->nombre?></h2>
<div class="acciones-generales">
  <a class="btn btn-success" href="<?=site_url('backend/reportes/crear/'.$proceso->id)?>"><span class="icon-file"></span> Nuevo</a>
</div>

<table class="table">
  <caption class="hide-text">Reportes</caption>
    <thead>
        <tr>
            <th>Reporte</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($reportes as $p): ?>
        <tr>
            <td><?=$p->nombre?></td>
            <td class="actions">
                <a href="<?=site_url('backend/reportes/ver/'.$p->id)?>" class="btn btn-primary"><span class="icon-eye-open icon-white"></span> Ver<span class="hidden-accessible"> <?=$p->nombre?></span></a>
                <a href="<?=site_url('backend/reportes/editar/'.$p->id)?>" class="btn btn-primary"><span class="icon-edit icon-white"></span> Editar<span class="hidden-accessible"> <?=$p->nombre?></span></a>
                <a href="<?=site_url('backend/reportes/eliminar/'.$p->id)?>" class="btn btn-danger" onclick="return confirm('¿Esta seguro que desea eliminar?')"><span class="icon-trash icon-white"></span> Eliminar<span class="hidden-accessible"> <?=$p->nombre?></span></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="modal hide fade" id="modal"></div>
