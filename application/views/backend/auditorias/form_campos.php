<div class="row-fluid">

    <div class="span12">

        <ul class="breadcrumb">
            <li>
                <a href="#">Auditoría</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/auditorias/procesos') ?>">Procesos</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/auditorias/procesos_auditar/' . $proceso) ?>">Proceso <?= ' ID : ' . $proceso ?></a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/auditorias/formularios/' . $proceso) ?>">Formularios</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/auditorias/formularios_auditar/' . $formulario) ?>">Formulario <?= ' ID : ' . $formulario ?></a> <span class="divider">/</span>
            </li>
            <li class="active">Campos</li>
        </ul>
        <?php $this->load->view('backend/auditorias/filtros_view') ?>
        <h2>Campos</h2>
        <?php $this->load->view('messages') ?>
        <table class="table" id="mainTable">
            <caption class="hide-text">Campos</caption>
            <thead>
                <tr>
                    <th>Operación</th>
                    <th>Usuario</th>
                    <th>Fecha - Hora</th>
                    <th>Nombre</th>
                    <th>Etiqueta</th>
                    <th>Tipo</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lista as $l): ?>
                    <tr>
                        <td><?= $l->tipo_operacion_aud == "insert" ? "Alta" : ($l->tipo_operacion_aud == "update" ? "Modificación" : "Baja") ?></td> 
                        <td><?= $l->usuario_aud ?></td>
                        <td><?= $l->fecha_aud ?></td>      
                        <td><?= $l->nombre ?></td>
                        <td><pre><?= htmlspecialchars($l->etiqueta) ?></pre></td>             
                        <td><?= $l->tipo ?></td>    
                        <td class="actions">
                            <a class="btn btn-primary" href="<?= site_url('backend/auditorias/form_campos_auditar/' . $l->id) ?>"><span class="icon-search icon-white"></span> Auditar<span class="hidden-accessible"></span></a>
                        </td>    
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
