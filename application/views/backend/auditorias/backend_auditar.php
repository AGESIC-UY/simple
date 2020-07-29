<div class="row-fluid">

    <div class="span12">
        <ul class="breadcrumb">
            <li>
                <a href="#">Auditoría</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/auditorias/backend') ?>">Usuarios Backend</a> <span class="divider">/</span>
            </li>
            <li class="active">Usuario <?= ' ID : ' . $usuarios[0]->id ?></li>
        </ul>
        <?php $this->load->view('backend/auditorias/filtros_view') ?>
        <h2>Usuario</h2>
        <?php $this->load->view('messages') ?>
        <table class="table" id="mainTable">
            <caption class="hide-text">Usuarios</caption>
            <thead>
                <tr>
                    <th>Operación</th>
                    <th>Usuario</th>
                    <th>Fecha - Hora</th>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Email</th>
                    <th>Procesos</th>
                    <th>Rol</th>
                    <th>Auditor</th>
                    <th>Control Total</th>
                    <th>Grupos</th>
                    <th>Reasignar</th>
                    <th>Reasignar Usuario</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= $u->tipo_operacion_aud == "insert" ? "Alta" : ($u->tipo_operacion_aud == "update" ? "Modificación" : "Baja") ?></td> 
                        <td><?= $u->usuario_aud ?></td>
                        <td><?= $u->fecha_aud ?></td>             
                        <td><?= $u->usuario ?></td>
                        <td><?= $u->nombre ?></td>
                        <td><?= $u->apellidos ?></td>
                        <td><?= $u->email ?></td>
                        <td><?= $u->procesos ?></td>
                        <td><?= $u->rol ?></td>
                        <td><?= $u->seg_alc_auditor ?></td>
                        <td><?= $u->seg_alc_control_total ?></td>
                        <td><?= $u->seg_alc_grupos_usuarios ?></td>
                        <td><?= $u->seg_reasginar ?></td>
                        <td><?= $u->seg_reasginar_usu ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
