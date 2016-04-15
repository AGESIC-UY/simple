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
        <h2>Accesos Frontend: Usuarios</h2>
        <?php $this->load->view('messages') ?>

        <div class="acciones-generales">
          <a class="btn" href="<?=site_url('backend/configuracion/usuario_editar')?>"><span class="icon-file"></span> Nuevo</a>
        </div>

        <table class="table">
          <caption class="hide-text">Usuarios</caption>
          <thead>
            <tr>
                <th>Usuario</th>
                <th>Nombres</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Pertenece a</th>
                <th>¿Fuera de oficina?</th>
                <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($usuarios as $u): ?>
            <tr>
                <td><?=$u->usuario?></td>
                <td><?=$u->nombres?></td>
                <td><?=$u->apellido_paterno?></td>
                <td><?=$u->apellido_materno?></td>
                <td>
                    <?php
                    $tmp=array();
                    foreach($u->GruposUsuarios as $g)
                        $tmp[]=$g->nombre;
                    echo implode(', ', $tmp);
                    ?>
                </td>
                <td><?=$u->vacaciones?'Si':'No'?></td>
                <td class="actions">
                    <a class="btn btn-primary" href="<?=site_url('backend/configuracion/usuario_editar/'.$u->id)?>"><span class="icon-edit icon-white"></span> Editar<span class="hidden-accessible"> <?=$u->usuario?></span></a>
                    <a class="btn btn-danger" href="<?=site_url('backend/configuracion/usuario_eliminar/'.$u->id)?>" onclick="return confirm('¿Está seguro que desea eliminar?')"><span class="icon-trash icon-white"></span> Eliminar<span class="hidden-accessible"> <?=$u->usuario?></span></a>
                </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
    </div>
</div>
