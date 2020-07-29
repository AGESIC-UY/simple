
<div class="row-fluid">

    <div class="span12">
        <ul class="breadcrumb">
            <li>
                <a href="#">Auditoría</a> <span class="divider">/</span>
            </li>
            <li class="active">Objetos de Negocios</li>
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
                        <td class="actions">
                            <a class="btn btn-primary" href="<?= site_url('backend/auditorias/obnstr_auditar/' . $v->id) ?>"><span class="icon-search icon-white"></span> Auditar<span class="hidden-accessible"></span></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
