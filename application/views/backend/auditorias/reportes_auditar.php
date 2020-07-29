<div class="row-fluid">

    <div class="span12">
        <ul class="breadcrumb">
            <li>
                <a href="#">Auditoría</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/auditorias/reportes') ?>">Reportes</a> <span class="divider">/</span>
            </li>
            <li class="active">Reporte <?= ' ID : ' . $lista[0]->id ?></li>
        </ul>
        <?php $this->load->view('backend/auditorias/filtros_view') ?>
        <h2>Reporte</h2>
        <?php $this->load->view('messages') ?>
        <table class="table" id="mainTable">
            <caption class="hide-text">Usuarios</caption>
            <thead>
                <tr>
                    <th>Operación</th>
                    <th>Usuario</th>
                    <th>Fecha - Hora</th>
                    <th>Nombre</th>
                    <th>Campos</th>
                    <th>Grupos Permitidos</th>
                    <th>Usuarios Permitidos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lista as $l): ?>
                    <tr>
                        <td><?= $l->tipo_operacion_aud == "insert" ? "Alta" : ($l->tipo_operacion_aud == "update" ? "Modificación" : "Baja") ?></td> 
                        <td><?= $l->usuario_aud ?></td>
                        <td><?= $l->fecha_aud ?></td>      
                        <td><?= $l->nombre ?></td>
                        <td>
                             <a href="" data-toggle="modal" onclick="jsonFormat('json_code_campos_<?= ($l->id_aud); ?>')" style="font-size: 20px;display: block;text-align: center;" class="tooltip-tabla" title="Extra"><span class="icon-expand-alt"></span></a>
                                <input class="hidden" id="json_code_campos_<?= ($l->id_aud); ?>"  value="<?= htmlspecialchars($l->campos); ?>" />
                        </td>
                        
                        <td><?= $l->grupos_usuarios_permiso ?></td>    
                        <td><?= $l->usuarios_permiso ?></td>    
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $this->load->view('backend/auditorias/modal_view') ?>