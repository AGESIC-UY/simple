<div class="row-fluid">

    <div class="span12">
        <ul class="breadcrumb">
            <li>
                <a href="#">Auditoría</a> <span class="divider">/</span>
            </li>
            <li class="active">Usuarios Backend</li>
        </ul>
        <?php $this->load->view('backend/auditorias/filtros_view') ?>
        <h2>Accesos Backend: Usuarios</h2>
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
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= $u['tipo_operacion_aud']=="insert"?"Alta":($u['tipo_operacion_aud']=="update"?"Modificación":"Baja") ?></td> 
                        <td><?= $u['usuario_aud'] ?></td>
                        <td><?= $u['fecha_aud'] ?></td>   
                        <td><?= $u['usuario'] ?></td>
                        <td><?= $u['nombre'] ?></td>
                        <td><?= $u['apellidos'] ?></td>                                   
                        <td class="actions">
                            <a class="btn btn-primary" href="<?= site_url('backend/auditorias/backend_auditar/' . $u['id']) ?>"><span class="icon-search icon-white"></span> Auditar<span class="hidden-accessible"> <?= $u->usuario ?></span></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
