<ul class="breadcrumb">
    <li>
        <a href="<?=site_url('backend/procesos')?>">Listado de Procesos</a> <span class="divider">/</span>
    </li>
    <li class="active"><?=$proceso->nombre?></li>
</ul>
<?php $this->load->view('backend/proceso_descripcion') ?>
<ul class="nav nav-tabs">
    <li><a href="<?=site_url('backend/procesos/editar/'.$proceso->id)?>">Diseñador</a></li>
    <li <?= ($tipo=="comun"? 'class="active"':'')?>><a href="<?=site_url('backend/formularios/listar/comun/'.$proceso->id)?>">Formularios</a></li>
    <li <?= ($tipo=="obn"? 'class="active"':'')?>><a href="<?= site_url('backend/formularios/listar/obn/' . $proceso->id) ?>">Formularios para Tablas de Datos</a></li>
    <li><a href="<?= site_url('backend/documentos/listar/' . $proceso->id) ?>">Documentos</a></li>
    <li><a href="<?= site_url('backend/validaciones/listar/' . $proceso->id) ?>">Validaciones</a></li>
    <li><a href="<?=site_url('backend/acciones/listar/'.$proceso->id)?>">Acciones</a></li>
    <li><a href="<?= site_url('backend/trazabilidad/listar/' . $proceso->id) ?>">Trazabilidad</a></li>
    <li><a href="<?= site_url('backend/procesos/editar_codigo_tramite_ws_grep/' . $proceso->id) ?>">Código tramites.gub.uy</a></li>
    <li><a href="<?= site_url('backend/procesos/editar_api/' . $proceso->id) ?>">API</a></li>
    
</ul>
<div class="acciones-generales">
<a class="btn" href="<?=site_url('backend/formularios/crear/'.$proceso->id.($tipo=="comun"? '/comun':'/obn'))?>"><span class="icon-file"></span> Nuevo</a>
</div>

<table class="table" id="mainTable">
  <caption class="hide-text"><?= ($tipo=="comun"? 'Formularios':'Formularios para Tablas de Datos')?></caption>
    <thead>
        <tr>
            <th>Formulario</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($formularios as $p): ?>
        <tr>
            <td><?=$p->nombre?></td>
            <td class="actions">
                <a href="<?=site_url('backend/formularios/editar/'.$p->id)?>" class="btn btn-primary"><span class="icon-edit icon-white"></span> Editar<span class="hide-text"> <?= $p->nombre ?> <?=$p->id?></span></a>
                <a href="<?=site_url('backend/formularios/eliminar/'.$p->id)?>" class="btn btn-danger" onclick="return confirm('¿Esta seguro que desea eliminar?')"><span class="icon-trash icon-white"></span> Eliminar<span class="hide-text"> <?= $p->nombre ?>  <?=$p->id?></span></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
