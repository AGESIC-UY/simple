<ul class="breadcrumb">
    <li>
        <a href="<?= site_url('backend/ws_catalogos') ?>">Catálogo de Servicios</a> <span class="divider">/</span>
    </li>
    <li>
        <a href="<?= site_url('backend/ws_catalogos/editar/'.$catalogo->id.'')?>"><?= $catalogo->nombre ?></a> <span class="divider">/</span>
    </li>
    <li class="active">Operaciones</li>
</ul>
<a href="#" class="btn btn-ayuda btn-secundary" id="ayuda_contextual_servicios_operaciones"><span class="icon-white icon-question-sign"></span> Ayuda</a>
<h2 id="accion-operaciones">Operaciones</h2>
<div class="acciones-generales">
  <a id="accion-nueva-operacion" class="btn btn-success" href="<?=site_url('backend/ws_catalogos/'.$catalogo->id.'/operaciones/crear/')?>"><span class="icon-file"></span> Nueva</a>
</div>
<table class="table">
  <caption class="hide-text">Operaciones</caption>
    <thead>
        <tr>
            <th id="accion-lista-operaciones">Nombre</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($operaciones as $o): ?>
        <tr>
            <td><?=$o->nombre?></td>
            <td class="actions">
                <a class="btn btn-primary" href="<?=site_url('backend/ws_catalogos/'.$catalogo->id.'/operaciones/editar/'.$o->id)?>"><span class="icon-white icon-edit"></span> Editar<span class="hide-text"> <?=$o->nombre?></span></a>
                <a class="btn btn-danger" href="<?=site_url('backend/ws_catalogos/'.$catalogo->id.'/operaciones/eliminar/'.$o->id)?>" onclick="return confirm('¿Esta seguro que desea eliminar?')"><span class="icon-white icon-trash"></span> Eliminar<span class="hide-text"> <?=$o->nombre?></span></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
