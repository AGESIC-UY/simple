<ul class="breadcrumb">
    <li>
        <a href="<?= site_url('backend/catalogos') ?>">Catálogo de Servicios</a> <span class="divider">/</span>
    </li>
    <li class="active"><?= $catalogo->nombre ?></li>
</ul>

<h1>Nuevo servicio</h1>
<form action="<?=site_url('backend/catalogos/editar_form/'.$catalogo->id)?>" method="post">
    <input type="text" value="<?= $catalogo->nombre ?>" /><br />
    <input type="text" value="<?= $catalogo->wsdl ?>" /><br />
    <input type="text" value="<?= $catalogo->conexion_timeout ?>" /><input type="text" value="<?= $catalogo->respuesta_timeout ?>" /><br />
    <input type="text" value="<?= $catalogo->endpoint_location ?>" /><br />
    <input class="btn btn-primary" type="submit" value="Guardar" />
</form>
