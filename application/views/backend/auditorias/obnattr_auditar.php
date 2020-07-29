<div class="row-fluid">

    <div class="span12">
        <ul class="breadcrumb">
            <li>
                <a href="#">Auditoría</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/auditorias/obnstr') ?>">Objetos de Negocios</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/auditorias/obnstr_auditar/'. $obn) ?>">Obn <?= ' ID : ' . $obn ?></a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/auditorias/obnattr/' . $lista[0]->id_obn) ?>">Atributos</a> <span class="divider">/</span>
            </li>
            <li class="active">Atributo <?= ' ID : ' . $lista[0]->id ?></li>
        </ul>
        <?php $this->load->view('backend/auditorias/filtros_view') ?>
        <h2>Atributo</h2>
        <?php $this->load->view('messages') ?>
        <table class="table" id="mainTable">
            <caption class="hide-text">Atributo</caption>
            <thead>
                <tr>
                    <th>Operación</th>
                    <th>Usuario</th>
                    <th>Fecha - Hora</th>
                    <th>Nombre</th>
                    <th>Tipo</th>                    
                    <th>Clave Logica</th>
                    <th>Multiple</th>
                    <th>Datos Extra</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($lista as $v): ?>
                    <tr>
                        <td><?= $v->tipo_operacion_aud == "insert" ? "Alta" : ($v->tipo_operacion_aud == "update" ? "Modificación" : "Baja") ?></td> 
                        <td><?= $v->usuario_aud ?></td>
                        <td><?= $v->fecha_aud ?></td>             
                        <td><?= $v->nombre ?></td>            
                        <td><?= $v->tipo ?></td>       
                        <td><?= $v->clave_logica ?></td>  
                        <td><?= $v->multiple ?></td>  
                        <td >
                            <a href="" data-toggle="modal" onclick="jsonFormat('json_code_datos_<?= ($v->id_aud); ?>')" style="font-size: 20px;display: block;text-align: center;" class="tooltip-tabla" title="Más detalles"><span class="icon-expand-alt"></span></a>
                            <input class="hidden" id="json_code_datos_<?= ($v->id_aud); ?>" value="<?= htmlspecialchars($v->attributo); ?>" />
                        </td> 
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $this->load->view('backend/auditorias/modal_view') ?>