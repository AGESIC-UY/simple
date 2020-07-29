<div class="row-fluid">

    <div class="span12">
        <ul class="breadcrumb">
            <li>
                <a href="#">Auditoría</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/auditorias/bloques') ?>">Bloques</a> <span class="divider">/</span>
            </li>
            <li class="active">Bloque <?= ' ID : ' . $lista[0]->id ?></li>
        </ul>
        <?php $this->load->view('backend/auditorias/filtros_view') ?>
        <h2>Catálogo de Bloques</h2>
        <?php $this->load->view('messages') ?>
        <table class="table" id="mainTable">
            <caption class="hide-text">Pasarela</caption>
            <thead>
                <tr>
                    <th>Operación</th>
                    <th>Usuario</th>
                    <th>Fecha - Hora</th>
                    <th>Nombre</th>
                    <th>Total de campos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lista as $l): ?>
                    <tr>
                        <td><?= $l->tipo_operacion_aud == "insert" ? "Alta" : ($l->tipo_operacion_aud == "update" ? "Modificación" : "Baja") ?></td> 
                        <td><?= $l->usuario_aud ?></td>
                        <td><?= $l->fecha_aud ?></td>      
                        <td><?= $l->nombre ?></td>
                        <td><?= $l->formulario->campos ?></td>
                        <td class="actions">
                            <?php if ($l->formulario->campos > 0): ?>
                                <a class="btn btn-primary" href="<?= site_url('backend/auditorias/campos_auditar/' . $l->formulario->id_aud) ?>"><span class="icon-search icon-white"></span> Campos<span class="hidden-accessible"></span></a>
                                <?php else: ?> 
                                <a class="btn btn-primary" href="#"><span class="icon-search icon-white"></span> Campos<span class="hidden-accessible"></span></a>
                                <?php endif ?> 
                        </td>
                    </tr>
                <?php
                endforeach;
                //document.getElementById("json").innerHTML = JSON.stringify(data, undefined, 2);
                ?>
            </tbody>
        </table>
    </div>
</div>
