<div class="row-fluid">

    <div class="span12">
        <ul class="breadcrumb">
            <li>
                <a href="#">Auditoría</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/auditorias/grupos') ?>">Grupos de Usuarios</a> <span class="divider">/</span>
            </li>
            <li class="active">Grupo <?= ' ID : ' . $grupos[0]->id ?></li>
        </ul>
        <?php $this->load->view('backend/auditorias/filtros_view') ?>
        <h2>Grupo</h2>
        <?php $this->load->view('messages') ?>
        <table class="table" id="mainTable">
            <caption class="hide-text">Usuarios</caption>
            <thead>
                <tr>
                    <th>Operación</th>
                    <th>Usuario</th>
                    <th>Fecha - Hora</th>
                    <th>Nombre</th>
                    <th>Usuarios</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grupos as $g): ?>
                    <tr>
                        <td><?= $g->tipo_operacion_aud == "insert" ? "Alta" : ($g->tipo_operacion_aud == "update" ? "Modificación" : "Baja") ?></td> 
                        <td><?= $g->usuario_aud ?></td>
                        <td><?= $g->fecha_aud ?></td>      
                        <td><?= $g->nombre ?></td>
                        <td><?= $g->usuarios_grupo ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
