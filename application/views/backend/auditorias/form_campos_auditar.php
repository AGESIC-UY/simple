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
                <a href="<?= site_url('backend/auditorias/procesos_auditar/' . $proceso->id) ?>">Proceso <?= ' ID : ' . $proceso->id ?></a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/auditorias/formularios/' . $proceso->id_aud) ?>">Formularios</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/auditorias/formularios_auditar/' . $formulario->id) ?>">Formulario <?= ' ID : ' . $formulario->id ?></a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/auditorias/form_campos/' . $formulario->id_aud) ?>">Campos </a> <span class="divider">/</span>
            </li>
            <li class="active">Campo <?= ' ID : ' . $lista[0]->id ?></li>
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
                    <th>Extra</th>
                    <th>Datos Extra</th>
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
                        <td >
                            <a href="" data-toggle="modal" onclick="jsonFormat('json_code_extra_<?= ($l->id_aud); ?>')" style="font-size: 20px;display: block;text-align: center;" class="tooltip-tabla" title="Extra"><span class="icon-expand-alt"></span></a>
                            <input class="hidden" id="json_code_extra_<?= ($l->id_aud); ?>"  value="<?= htmlspecialchars($l->extra); ?>" /></td>
                        <td >
                            <a href="" data-toggle="modal" onclick="jsonFormat('json_code_datos_<?= ($l->id_aud); ?>')" style="font-size: 20px;display: block;text-align: center;" class="tooltip-tabla" title="Más detalles"><span class="icon-expand-alt"></span></a>
                            <input class="hidden" id="json_code_datos_<?= ($l->id_aud); ?>" value="<?= htmlspecialchars($l->json); ?>" />
                        </td>   
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $this->load->view('backend/auditorias/modal_view') ?>
