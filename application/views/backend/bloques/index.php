<ul class="breadcrumb">
    <li>
        Catálogos de bloques
    </li>
</ul>
<h2>Catálogos de bloques</h2>
<div class="acciones-generales">
  <a class="btn btn-success" href="<?=site_url('backend/bloques/crear/')?>"><span class="icon-file"></span> Nuevo</a>
</div>
<table class="table">
  <caption class="hide-text">Catálogos de bloques</caption>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($bloques as $b): ?>
            <tr>
                <td><?= $b->nombre ?></td>
                <td class="actions">
                    <a class="btn btn-primary" href="<?=site_url('backend/bloques/editar/'.$b->id)?>"><span class="icon-white icon-edit"></span> Editar<span class="hide-text"> <?= $b->nombre ?></span></a>
                    <a class="btn btn-danger" href="<?=site_url('backend/bloques/eliminar/'.$b->id)?>" onclick="return confirm('¿Esta seguro que desea eliminar?')"><span class="icon-white icon-trash"></span> Eliminar<span class="hide-text"> <?= $b->nombre ?></span></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
