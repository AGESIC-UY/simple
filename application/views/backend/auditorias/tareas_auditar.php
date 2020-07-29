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
                <a href="<?= site_url('backend/auditorias/tareas/' . $lista[0]->proceso_id) ?>">Tareas</a> <span class="divider">/</span>
            </li>
            <li class="active">Tarea <?= ' ID : ' . $lista[0]->id ?></li>
        </ul>
        <?php $this->load->view('backend/auditorias/filtros_view') ?>
        <h2>Tarea</h2>
        <?php $this->load->view('messages') ?>
        <table class="table" id="mainTable">
            <caption class="hide-text">Tarea</caption>
            <thead>
                <tr>
                    <th>Operación</th>
                    <th>Usuario</th>
                    <th>Fecha - Hora</th>
                    <th>Nombre</th>
                    <th>Automática</th>                    
                    <th>Inicial</th>
                    <th>Final</th>
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
                        <td><?= $v->automatica ?></td>       
                        <td><?= $v->inicial ?></td>  
                        <td><?= $v->final ?></td>  
                        <td >
                            <a href="" data-toggle="modal" onclick="jsonFormat('json_code_datos_<?= ($v->id_aud); ?>')" style="font-size: 20px;display: block;text-align: center;" class="tooltip-tabla" title="Más detalles"><span class="icon-expand-alt"></span></a>
                            <input class="hidden" id="json_code_datos_<?= ($v->id_aud); ?>" value="<?= htmlspecialchars($v->tarea); ?>" />
                        </td>    
                        <td class="actions">
                            <a class="btn btn-primary" href="<?= site_url('backend/auditorias/pasos/' . $v->id_aud) ?>"><span class="icon-search icon-white"></span> Pasos<span class="hidden-accessible"></span></a>
                            <a class="btn btn-primary" href="<?= site_url('backend/auditorias/eventos/' . $v->id_aud) ?>"><span class="icon-search icon-white"></span> Eventos<span class="hidden-accessible"></span></a>
                            <a class="btn btn-primary" href="<?= site_url('backend/auditorias/validacionesjs/' . $v->id_aud) ?>"><span class="icon-search icon-white"></span> Validación<span class="hidden-accessible"></span></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $this->load->view('backend/auditorias/modal_view') ?>