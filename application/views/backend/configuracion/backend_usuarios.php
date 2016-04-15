<div class="row-fluid">

    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9">
        <ul class="breadcrumb">
            <li>
                <a href="<?=site_url('backend/configuracion')?>">Configuración</a> <span class="divider">/</span>
            </li>
            <li class="active">Usuarios</li>
        </ul>
        <h2>Accesos Backend: Usuarios</h2>

        <div class="acciones-generales">
          <a class="btn btn-success" href="<?=site_url('backend/configuracion/backend_usuario_editar')?>"><span class="icon-file"></span> Nuevo</a>
        </div>

        <table class="table">
          <caption class="hide-text">Usuarios</caption>
          <thead>
            <tr>
                <th>E-Mail</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($usuarios as $u): ?>
            <tr>
                <td><?=$u->email?></td>
                <td><?=$u->nombre?></td>
                <td><?=$u->apellidos?></td>
                <td><?=$u->rol?></td>
                <td class="actions">
                    <a class="btn btn-primary" href="<?=site_url('backend/configuracion/backend_usuario_editar/'.$u->id)?>"><span class="icon-edit icon-white"></span> Editar<span class="hidden-accessible"> <?=$u->email?></span></a>
                    <a class="btn btn-danger" href="<?=site_url('backend/configuracion/backend_usuario_eliminar/'.$u->id)?>" onclick="return confirm('¿Está seguro que desea eliminar?')"><span class="icon-trash icon-white"></span> Eliminar<span class="hidden-accessible"> <?=$u->email?></span></a>
                </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
    </div>
</div>
