<div class="row-fluid">

    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9">
        <ul class="breadcrumb">
            <li>
                <a href="<?=site_url('backend/configuracion')?>">Configuración</a> <span class="divider">/</span>
            </li>
            <li class="active">Grupos de Usuarios</li>
        </ul>
        <h2>Accesos Frontend: Usuarios</h2>

        <div class="acciones-generales">
          <a class="btn" href="<?=site_url('backend/configuracion/grupo_usuarios_editar')?>"><span class="icon-file"></span> Nuevo</a>
        </div>

        <table class="table">
          <thead>
            <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Usuarios</th>
                <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($grupos_usuarios as $u): ?>
            <tr>
                <td><?=$u->id?></td>
                <td><?=$u->nombre?></td>
                <td>
                    <?php
                    $tmp=array();
                    foreach($u->Usuarios as $g)
                        $tmp[]=$g->displayUsername();
                    echo implode(', ', $tmp);
                    ?>
                </td>
                <td class="actions">
                    <a class="btn btn-primary" href="<?=site_url('backend/configuracion/grupo_usuarios_editar/'.$u->id)?>"><span class="icon-edit icon-white"></span> Editar</a>
                    <a class="btn btn-danger" href="<?=site_url('backend/configuracion/grupo_usuarios_eliminar/'.$u->id)?>" onclick="return confirm('¿Está seguro que desea eliminar?')"><span class="icon-trash icon-white"></span> Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
    </div>
</div>
