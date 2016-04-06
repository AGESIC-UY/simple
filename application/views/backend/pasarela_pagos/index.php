<ul class="breadcrumb">
    <li>
        Pasarela de Pagos
    </li>
</ul>
<h2>Pasarela de Pagos</h2>
<div class="acciones-generales">
  <a class="btn btn-success" href="<?=site_url('backend/pasarela_pagos/crear/')?>"><span class="icon-file"></span> Nuevo</a>
</div>
<table class="table">
  <caption class="hide-text">Pasarela de Pagos</caption>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Activa?</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($pasarelas as $p): ?>
            <tr>
                <td><?= $p->nombre ?></td>
                <td><?= ($p->activo == true) ? 'Si' : 'No' ?></td>
                <td class="actions">
                    <a class="btn btn-primary" href="<?=site_url('backend/pasarela_pagos/editar/'.$p->id)?>"><span class="icon-white icon-edit"></span> Editar<span class="hide-text"> <?= $p->nombre ?><?= $p->id ?></span></a>
                    <a class="btn btn-danger" href="<?=site_url('backend/pasarela_pagos/eliminar/'.$p->id)?>" onclick="return confirm('¿Esta seguro que desea eliminar?')"><span class="icon-white icon-trash"></span> Eliminar<span class="hide-text"> <?= $p->nombre ?><?= $p->id ?></span></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
