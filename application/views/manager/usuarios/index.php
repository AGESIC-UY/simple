<ul class="breadcrumb">
  <li>Administración <span class="divider">/</span></li>
  <li class="active"><?=$title?></li>
</ul>

<h2><?=$title?></h2>
<div class="acciones-generales">
  <a class="btn" href="<?=site_url('manager/usuarios/editar')?>"><span class="icon-plus"></span> Crear Usuario</a>
</div>

<table class="table">
  <caption class="hide-text">Usuarios</caption>
  <thead>
      <tr>
          <th>Correo Electrónico</th>
          <th>Nombre</th>
          <th>Apellidos</th>
          <th>Cuenta</th>
          <th>Rol</th>
          <th>Acciones</th>
      </tr>
  </thead>
  <tbody>
      <?php foreach($usuarios as $c):?>
      <tr>
          <td><?=$c->email?></td>
          <td><?=$c->nombre?></td>
          <td><?=$c->apellidos?></td>
          <td><?=$c->Cuenta->nombre?></td>
          <td><?=$c->rol?></td>
          <td class="actions">
              <a class="btn btn-primary" href="<?=site_url('manager/usuarios/editar/'.$c->id)?>"><span class="icon-edit icon-white"></span> Editar</a>
              <a class="btn btn-danger" href="<?=site_url('manager/usuarios/eliminar/'.$c->id)?>" onclick="return confirm('¿Está seguro que desea eliminar este usuario?')"><span class="icon-trash icon-white"></span> Eliminar</a>
          </td>
      </tr>
      <?php endforeach ?>
  </tbody>
</table>
