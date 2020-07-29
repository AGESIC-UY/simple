<div class="row-fluid">

    <div class="span12">
        <ul class="breadcrumb">
            <li>
                <a href="#">Auditoría</a> <span class="divider">/</span>
            </li>
            <li class="active">Grupos de Usuarios</li>
        </ul>
        <?php $this->load->view('backend/auditorias/filtros_view') ?>
        <h2>Grupos de Usuarios</h2>
        <?php $this->load->view('messages') ?>
        <table class="table" id="mainTable">
            <caption class="hide-text">Grupos de Usuarios</caption>
            <thead>
                <tr>
                    <th>Operación</th>
                    <th>Usuario</th>
                    <th>Fecha - Hora</th>
                    <th>Nombre</th>
                    <th>Usuarios</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($grupos as $g): ?>
                    <tr>
                        <td><?= $g['tipo_operacion_aud']=="insert"?"Alta":($u['tipo_operacion_aud']=="update"?"Modificación":"Baja") ?></td> 
                        <td><?= $g['usuario_aud'] ?></td>
                        <td><?= $g['fecha_aud'] ?></td>             
                        <td><?= $g['nombre'] ?></td>
                        <td><?= $g['usuarios_grupo'] ?></td>             
                        <td class="actions">
                            <a class="btn btn-primary" href="<?= site_url('backend/auditorias/grupos_auditar/' . $g['id']) ?>"><span class="icon-search icon-white"></span> Auditar<span class="hidden-accessible"> <?= $u->usuario ?></span></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
