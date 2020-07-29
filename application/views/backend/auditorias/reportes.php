<div class="row-fluid">

    <div class="span12">
        <ul class="breadcrumb">
            <li>
                <a href="#">Auditoría</a> <span class="divider">/</span>
            </li>
            <li class="active">Reportes</li>
        </ul>
        <?php $this->load->view('backend/auditorias/filtros_view') ?>
        <h2>Reportes</h2>
        <?php $this->load->view('messages') ?>
        <table class="table" id="mainTable">
            <caption class="hide-text">Reportes</caption>
            <thead>
                <tr>
                    <th>Operación</th>
                    <th>Usuario</th>
                    <th>Fecha - Hora</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Proceso ID</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($lista as $v): ?>
                    <tr>
                        <td><?= $v->tipo_operacion_aud=="insert"?"Alta":($v->tipo_operacion_aud=="update"?"Modificación":"Baja") ?></td> 
                        <td><?= $v->usuario_aud ?></td>
                        <td><?= $v->fecha_aud ?></td>             
                        <td><?= $v->nombre ?></td>
                        <td><?= $v->tipo ?></td>             
                        <td><?= $v->proceso_id ?></td>       
                        <td class="actions">
                            <a class="btn btn-primary" href="<?= site_url('backend/auditorias/reportes_auditar/' . $v->id) ?>"><span class="icon-search icon-white"></span> Auditar<span class="hidden-accessible"></span></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
