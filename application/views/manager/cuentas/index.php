<ul class="breadcrumb">
  <li>Administración <span class="divider">/</span></li>
  <li class="active"><?=$title?></li>
</ul>

<h2><?=$title?></h2>
<div class="acciones-generales">
  <a class="btn" href="<?=site_url('manager/cuentas/editar')?>"><span class="icon-plus"></span> Crear Cuenta</a>
</div>

<table class="table">
  <caption class="hide-text">Cuentas</caption>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Nombre largo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($cuentas as $c):?>
        <tr>
            <td><?=$c->nombre?></td>
            <td><?=$c->nombre_largo?></td>
            <td class="actions">
                <a class="btn btn-primary" href="<?=site_url('manager/cuentas/editar/'.$c->id)?>"><span class="icon-edit icon-white"></span> Editar</a>
                <a class="btn btn-danger" href="<?=site_url('manager/cuentas/eliminar/'.$c->id)?>" onclick="return confirm('¿Está seguro que desea eliminar esta cuenta?')"><span class="icon-trash icon-white"></span> Eliminar</a>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>
