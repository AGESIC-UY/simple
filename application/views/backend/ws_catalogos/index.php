<ul class="breadcrumb">
    <li>
        Catálogos de Servicios
    </li>
</ul>
<a href="#" class="btn btn-ayuda btn-secundary" id="ayuda_contextual_servicios"><span class="icon-white icon-question-sign"></span> Ayuda</a>
<h2 id="accion-servicios">Catálogos de Servicios</h2>
<div class="acciones-generales">
  <a class="btn btn-success" id="accion-nuevo-servicio" href="<?=site_url('backend/ws_catalogos/crear/')?>"><span class="icon-file"></span> Nuevo</a>
</div>
<table class="table">
  <caption class="hide-text">Catálogos de Servicios</caption>
    <thead>
        <tr>
            <th id="accion-lista-servicios">Nombre</th>
            <th>Tipo</th>
            <th>Activo?</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($catalogos as $c): ?>
        <tr>
            <td><?=$c->nombre?></td>
            <td><?=$c->tipo?></td>
            <td><?= ($c->activo == true) ? 'Si' : 'No' ?></td>
            <td class="actions">
                <a class="btn btn-primary" href="<?=site_url('backend/ws_catalogos/'.$c->id.'/operaciones')?>"><span class="icon-white icon-zoom-in"></span> Ver Operaciones<span class="hide-text"> <?= $c->nombre ?><?= $c->id ?></span></a>
                <a class="btn btn-primary" href="<?=site_url('backend/ws_catalogos/editar/'.$c->id)?>"><span class="icon-white icon-edit"></span> Editar<span class="hide-text"> <?= $c->nombre ?><?= $c->id ?></span></a>
                <a class="btn btn-danger" href="<?=site_url('backend/ws_catalogos/eliminar/'.$c->id)?>" onclick="return confirm('¿Esta seguro que desea eliminar?')"><span class="icon-white icon-trash"></span> Eliminar<span class="hide-text"> <?= $c->nombre ?><?= $c->id ?></span></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
