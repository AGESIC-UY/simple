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
                <a href="<?= site_url('backend/auditorias/formularios/' . $lista[0]->proceso_id) ?>">Formularios</a> <span class="divider">/</span>
            </li>
            <li class="active">Formulario <?= ' ID : ' . $lista[0]->id ?></li>
        </ul>
        <?php $this->load->view('backend/auditorias/filtros_view') ?>
        <h2>Formulario</h2>
        <?php $this->load->view('messages') ?>
        <table class="table" id="mainTable">
            <caption class="hide-text">Formulario</caption>
            <thead>
                <tr>
                    <th>Operación</th>
                    <th>Usuario</th>
                    <th>Fecha - Hora</th>
                    <th>Nombre</th>
                    <th>Leyenda</th>       
                    <th>Datos Extra</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($lista as $v): ?>
                    <tr>
                        <td><?= $v->tipo_operacion_aud == "insert" ? "Alta" : ($v->tipo_operacion_aud == "update" ? "Modificación" : "Baja") ?></td> 
                        <td><?= $v->usuario_aud ?></td>
                        <td><?= $v->fecha_aud ?></td>             
                        <td><?= $v->nombre ?></td>            
                        <td><?= $v->leyenda ?></td> 
                        <td >
                            <a href="" data-toggle="modal" onclick="jsonFormat('json_code_datos_<?= ($v->id_aud); ?>')" style="font-size: 20px;display: block;text-align: center;" class="tooltip-tabla" title="Más detalles"><span class="icon-expand-alt"></span></a>
                            <input class="hidden" id="json_code_datos_<?= ($v->id_aud); ?>" value="<?= htmlspecialchars($v->json); ?>" />
                        </td>   
                        <td class="actions">
                            <a class="btn btn-primary" href="<?= site_url('backend/auditorias/form_campos/' . $v->id_aud) ?>"><span class="icon-search icon-white"></span> Campos<span class="hidden-accessible"></span></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $this->load->view('backend/auditorias/modal_view') ?>
