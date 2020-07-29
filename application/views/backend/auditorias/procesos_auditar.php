<div class="row-fluid">

    <div class="span12">
        <ul class="breadcrumb">
            <li>
                <a href="#">Auditoría</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/auditorias/procesos') ?>">Procesos</a> <span class="divider">/</span>
            </li>
            <li class="active">Proceso <?= ' ID : ' . $lista[0]->id ?></li>
        </ul>
        <?php $this->load->view('backend/auditorias/filtros_view') ?>
        <h2>Procesos</h2>
        <?php $this->load->view('messages') ?>
        <table class="table" id="mainTable">
            <caption class="hide-text">Procesos</caption>
            <thead>
                <tr>
                    <th>Operación</th>
                    <th>Usuario</th>
                    <th>Fecha - Hora</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Versión</th>                    
                    <th>Instanciar</th>
                    <th>Trazabilidad</th>
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
                        <td><?= $v->estado == 'public' ? 'Publicado' : ($v->estado == 'draft' ? 'Borrador' : 'Archivado') ?></td>            
                        <td><?= $v->version ?></td>       
                        <td><?= $v->instanciar_api ?></td>       
                        <td>
                            <a href="" data-toggle="modal" onclick="jsonFormat('json_code_traza_<?= ($v->id_aud); ?>')" style="font-size: 20px;display: block;text-align: center;" class="tooltip-tabla" title="Más detalles"><span class="icon-expand-alt"></span></a>
                            <input class="hidden" id="json_code_traza_<?= ($v->id_aud); ?>" value="<?= htmlspecialchars($v->traza); ?>" />
                        </td>       
                        <td >
                            <a href="" data-toggle="modal" onclick="jsonFormat('json_code_datos_<?= ($v->id_aud); ?>')" style="font-size: 20px;display: block;text-align: center;" class="tooltip-tabla" title="Más detalles"><span class="icon-expand-alt"></span></a>
                            <input class="hidden" id="json_code_datos_<?= ($v->id_aud); ?>" value="<?= htmlspecialchars($v->proceso); ?>" />
                        </td>    
                        <td class="actions">
                            <a class="btn btn-primary" href="<?= site_url('backend/auditorias/tareas/' . $v->id_aud) ?>"><span class="icon-search icon-white"></span> Tareas<span class="hidden-accessible"></span></a>
                            <a class="btn btn-primary" href="<?= site_url('backend/auditorias/formularios/' . $v->id_aud) ?>"><span class="icon-search icon-white"></span> Formularios<span class="hidden-accessible"></span></a>
                            <a class="btn btn-primary" href="<?= site_url('backend/auditorias/documentos/' . $v->id_aud) ?>"><span class="icon-search icon-white"></span> Documentos<span class="hidden-accessible"></span></a>
                            <a class="btn btn-primary" href="<?= site_url('backend/auditorias/validaciones/' . $v->id_aud) ?>"><span class="icon-search icon-white"></span> Validaciones<span class="hidden-accessible"></span></a>
                            <a class="btn btn-primary" href="<?= site_url('backend/auditorias/acciones/' . $v->id_aud) ?>"><span class="icon-search icon-white"></span> Acciones<span class="hidden-accessible"></span></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $this->load->view('backend/auditorias/modal_view') ?>
