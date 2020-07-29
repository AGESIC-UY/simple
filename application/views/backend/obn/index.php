<ul class="breadcrumb">
    <li>
        Objetos de Negocios
    </li>
</ul>
<!--<a href="#" class="btn btn-ayuda btn-secundary" id="ayuda_contextual_bloques"><span class="icon-white icon-question-sign"></span> Ayuda</a-->
<h2 id="accion-bloques">Objetos de Negocios</h2>
<div class="acciones-generales">
    <a class="btn btn-success" id="accion-nuevo-obn" href="<?= site_url('backend/obns/crear/') ?>"><span class="icon-file"></span> Nuevo</a>
    <a class="btn btn-default" id="accion-importar-obn" href="#modalImportar" data-toggle="modal" ><span class="icon-upload icon"></span> Importar</a>
</div>
<?php
if (isset($mensajes)) {
    echo $mensajes;
}
?>
<table class="table">
    <caption class="hide-text">Objetos de Negocios</caption>
    <thead>
        <tr id="accion-lista-bloques">
            <th>Identificador</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($obn as $b): ?>
            <tr>
                <td><?= $b->identificador ?></td>
                <td class="actions">
                    <a class="btn btn-primary" href="<?= site_url('backend/obns/editar/' . $b->id) ?>"><span class="icon-white icon-edit"></span> Editar<span class="hide-text"> <?= $b->identificador ?></span></a>
                    <a class="btn btn-primary" href="<?= site_url('backend/obns/exportar/' . $b->id) ?>"><span class="icon-white icon-share"></span> Exportar<span class="hide-text"> <?= $b->identificador ?></span></a>
                    <?php if (canDeleteOBN($b)): ?>
                        <a class="btn btn-danger" href="<?= site_url('backend/obns/eliminar/' . $b->id) ?>" onclick="return confirm('¿Esta seguro que desea eliminar?')"><span class="icon-white icon-trash"></span> Eliminar<span class="hide-text"> <?= $b->identificador ?></span></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div id="modalImportar" class="modal hide fade">
    <form method="POST" enctype="multipart/form-data" action="<?= site_url('backend/obns/importar') ?>">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Importar OBN</h3>
        </div>
        <div class="modal-body">
            <label for="file">Cargue a continuación el archivo <strong>.obn</strong>.</label>
            <input type="file" name="archivo" id="file"/>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Importar</button>
            <button class="btn btn-link" data-dismiss="modal" aria-hidden="true">Cerrar</button>
        </div>
    </form>
</div>
<div id="modal" class="modal hide fade"></div>