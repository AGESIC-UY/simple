<h2>Etapas sin asignar</h2>

<?php if (count($etapas) > 0): ?>

<table id="mainTable" class="table">
  <caption class="hide-text">Etapas sin asignar</caption>
    <thead>
        <tr>
            <th>Id</th>
            <th>Nombre</th>
            <th>Etapa</th>
            <th>Modificación</th>
            <th>Vencimiento</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($etapas as $e): ?>
            <tr <?=$e->getPrevisualizacion()?'data-toggle="popover" data-html="true" data-title="<h4>Previsualización</h4>" data-content="'.htmlspecialchars($e->getPrevisualizacion()).'" data-trigger="hover" data-placement="bottom"':''?>>
                <td><?=$e->Tramite->id?></td>
                <td class="name"><?= $e->Tramite->Proceso->nombre ?></td>
                <td><?=$e->Tarea->nombre ?></td>
                <td class="time"><?= strftime('%d.%b.%Y',mysql_to_unix($e->updated_at))?><br /><?= strftime('%H:%M:%S',mysql_to_unix($e->updated_at))?></td>
                <td><?=$e->vencimiento_at?strftime('%c',strtotime($e->vencimiento_at)):'N/A'?></td>
                <td class="actions"><a href="<?=site_url('etapas/asignar/'.$e->id)?>" class="btn btn-primary"><span class="icon-check icon-white"></span> Asignármelo <span class="hide-text">Etapa <?=$e->Tramite->id?></span></a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p>No hay trámites para ser asignados.</p>
<?php endif; ?>
