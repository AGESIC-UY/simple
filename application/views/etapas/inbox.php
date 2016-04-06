<h2>Bandeja de Entrada</h2>

<?php if (count($etapas) > 0): ?>

<table id="mainTable" class="table">
  <caption class="hide-text">Trámites en bandeja de entrada</caption>
    <thead>
        <tr>
            <th><a href="<?=current_url().'?orderby=id&direction='.($direction=='asc'?'desc':'asc')?>">Id</a></th>
            <th><a href="<?=current_url().'?orderby=proceso_nombre&direction='.($direction=='asc'?'desc':'asc')?>">Nombre</a></th>
            <th><a href="<?=current_url().'?orderby=tarea_nombre&direction='.($direction=='asc'?'desc':'asc')?>">Etapa</a></th>
            <th><a href="<?=current_url().'?orderby=updated_at&direction='.($direction=='asc'?'desc':'asc')?>">Modificación</a></th>
            <th><a href="<?=current_url().'?orderby=vencimiento_at&direction='.($direction=='asc'?'desc':'asc')?>">Vencimiento</a></th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($etapas as $e): ?>
            <tr <?=$e->getPrevisualizacion()?'data-toggle="popover" data-html="true" data-title="<h4>Previsualización</h4>" data-content="'.htmlspecialchars($e->getPrevisualizacion()).'" data-trigger="hover" data-placement="bottom"':''?>>
                <td><?=$e->Tramite->id?></td>
                <td class="name"><?= $e->Tramite->Proceso->nombre ?></td>
                <td><?=$e->Tarea->nombre?></td>
                <td class="time"><?= strftime('%d.%b.%Y',mysql_to_unix($e->updated_at))?><br /><?= strftime('%H:%M:%S',mysql_to_unix($e->updated_at))?></td>
                <td><?=$e->vencimiento_at?strftime('%c',strtotime($e->vencimiento_at)):'N/A'?></td>
                <td class="actions">
                    <a href="<?=site_url('etapas/ejecutar/'.$e->id)?>" class="btn btn-primary preventDoubleRequest"><span class="icon-edit icon-white"></span> Realizar <span class="hide-text"><?= $e->Tramite->Proceso->nombre ?></span></a>
                    <?php if($e->netapas==1):?><a href="<?=site_url('tramites/eliminar/'.$e->tramite_id)?>" class="btn btn-danger" onclick="return confirm('¿Esta seguro que desea eliminar este tramite?')"><span class="icon-trash"></span><span class="hide-text">Eliminar <?= $e->Tramite->Proceso->nombre ?></span></a><?php endif ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>
<p>No hay trámites pendientes en su bandeja de entrada.</p>
<?php endif; ?>
