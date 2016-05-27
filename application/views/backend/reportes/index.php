<script src="<?= base_url() ?>assets/js/modelador-acciones.js" type="text/javascript"></script>

<ul class="breadcrumb">
    <li class="active">
        Gestión
    </li>
</ul>
<h2>Gestión</h2>
<table class="table">
    <caption class="hide-text">Procesos</caption>
    <thead>
        <tr>
            <th>Proceso</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($procesos as $p): ?>
        <tr>
            <td><?=$p->nombre?></td>
            <td class="actions">
                <a href="<?=site_url('backend/reportes/listar/'.$p->id)?>" class="btn btn-primary"><span class="icon-eye-open icon-white"></span> Ver Reportes<span class="hide-text"> de <?= $p->nombre ?> <?=$p->id?></span></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Reporte de Satisfacción</h2>
<a href="<?=site_url('backend/reportes/reporte_satisfaccion/')?>" class="btn btn-primary"><span class="icon-signal icon-white"></span> Ver resultados</a>
