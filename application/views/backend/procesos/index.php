<ul class="breadcrumb">
    <li>
        Listado de Procesos
    </li>
</ul>
<h2>Listado de Procesos</h2>
<div class="acciones-generales">
  <a class="btn btn-success" href="<?=site_url('backend/procesos/crear/')?>"><span class="icon-file"></span> Nuevo</a>
  <a class="btn btn-default" href="#modalImportar" data-toggle="modal" ><span class="icon-upload icon"></span> Importar</a>
</div>

<?php
  if(isset($mensajes)) {
    echo $mensajes;
  }
?>

<table class="table">
  <caption class="hide-text">Procesos</caption>
    <thead>
        <tr>
            <th>Proceso</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($procesos as $p): ?>
        <tr>
            <td><?=$p->nombre?></td>
            <td class="actions">
                <a class="btn btn-primary" href="<?=site_url('backend/procesos/editar/'.$p->id)?>"><span class="icon-white icon-edit"></span> Editar<span class="hide-text"> <?=$p->id?></span></a>
                <a class="btn btn-primary" href="<?=site_url('backend/procesos/exportar/'.$p->id)?>"><span class="icon-white icon-share"></span> Exportar<span class="hide-text"> <?=$p->id?></span></a>
                <a class="btn btn-danger" href="<?=site_url('backend/procesos/eliminar/'.$p->id)?>" onclick="return confirm('¿Esta seguro que desea eliminar?')"><span class="icon-white icon-trash"></span> Eliminar<span class="hide-text"> <?=$p->id?></span></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div id="modalImportar" class="modal hide fade">
    <form method="POST" enctype="multipart/form-data" action="<?=site_url('backend/procesos/importar')?>">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Importar Proceso</h3>
    </div>
    <div class="modal-body">
        <label for="file">Cargue a continuación el archivo .simple donde exportó su proceso.</label>
        <input type="file" name="archivo" id="file"/>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Importar</button>
        <button class="btn btn-link" data-dismiss="modal" aria-hidden="true">Cerrar</button>
    </div>
    </form>
</div>
