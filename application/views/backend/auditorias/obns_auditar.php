<div class="row-fluid">

    <div class="span12">
        <ul class="breadcrumb">
            <li>
                <a href="#">Auditoría</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/auditorias/obnstr') ?>">Objetos de Negocios</a> <span class="divider">/</span>
            </li>
            <li class="active">Obn <?= ' ID : ' . $lista[0]->id ?></li>
        </ul>
        <?php $this->load->view('backend/auditorias/filtros_view') ?>
        <h2>Objetos de Negocios</h2>
        <?php $this->load->view('messages') ?>
        <table class="table" id="mainTable">
            <caption class="hide-text">Objetos de Negocios</caption>
            <thead>
                <tr>
                    <th>Operación</th>
                    <th>Usuario</th>
                    <th>Fecha - Hora</th>
                    <th>Identificador</th>
                    <th>Descripción</th>
                    <th>Estructura</th>
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
                        <td><?= $v->identificador ?></td>
                        <td><?= $v->descripcion ?></td>            
                        <td>
                            <a href="" data-toggle="modal" onclick="jsonFormat('json_code_traza_<?= ($v->id_aud); ?>')" style="font-size: 20px;display: block;text-align: center;" class="tooltip-tabla" title="Más detalles"><span class="icon-expand-alt"></span></a>
                            <input class="hidden" id="json_code_traza_<?= ($v->id_aud); ?>" value="<?= htmlspecialchars($v->json); ?>" />
                        </td>     
                        <td >
                            <a href="" data-toggle="modal" onclick="jsonFormat('json_code_datos_<?= ($v->id_aud); ?>')" style="font-size: 20px;display: block;text-align: center;" class="tooltip-tabla" title="Más detalles"><span class="icon-expand-alt"></span></a>
                            <input class="hidden" id="json_code_datos_<?= ($v->id_aud); ?>" value="<?= htmlspecialchars($v->obn); ?>" />
                        </td>    
                        <td class="actions">
                            <a class="btn btn-primary" href="<?= site_url('backend/auditorias/obnattr/' . $v->id_aud) ?>"><span class="icon-search icon-white"></span> Atributos<span class="hidden-accessible"></span></a>
                            <a class="btn btn-primary" href="<?= site_url('backend/auditorias/obnquery/' . $v->id_aud) ?>"><span class="icon-search icon-white"></span> Consultas<span class="hidden-accessible"></span></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $this->load->view('backend/auditorias/modal_view') ?>
