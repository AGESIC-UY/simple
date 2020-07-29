<div class="row-fluid">

    <div class="span12">
        <ul class="breadcrumb">
            <li>
                <a href="#">Auditoría</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/auditorias/usuarios') ?>">Usuarios</a> <span class="divider">/</span>
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
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Email</th>
                    <th>Acceso reportes</th>
                    <th>Vacaciones</th>
                    <th>Grupos usuario</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= $u->tipo_operacion_aud == "insert" ? "Alta" : ($u->tipo_operacion_aud == "update" ? "Modificación" : "Baja") ?></td> 
                        <td><?= $u->usuario_aud ?></td>
                        <td><?= $u->fecha_aud ?></td>           
                        <td><?= $u->usuario ?></td>
                        <td><?= $u->nombres ?></td>
                        <td><?= $u->apellidos ?></td>
                        <td><?= $u->email ?></td>
                        <td><?= $u->acceso_reportes ?></td>
                        <td><?= $u->vacaciones ?></td>
                        <td><?= $u->grupos_usuario ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>