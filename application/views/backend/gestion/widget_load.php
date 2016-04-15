<div class="front">
    <div class="cabecera">
        <h3><?=$widget->nombre?></h3>
        <a class="config" href="#" onclick="return widgetConfig(this)"><span class="icon-wrench"></span></a>
    </div>
    <div class="contenido">
        <?=$widget->display()?>
    </div>
</div>
<div class="back">
    <form class="ajaxForm" method="POST" action="<?= site_url('backend/gestion/widget_config_form/'.$widget->id) ?>" data-onsuccess="widgetConfigOk">
        <div class="cabecera">
            <h3>Configuración</h3>
            <button type="submit" class="volver btn btn-mini">ok</button>
        </div>
        <div class="contenido">
            <div class="validacion validacion-error"></div>
            <label for="nombre_<?=$widget->id?>">Nombre</label>
            <input type="text" name="nombre" id="nombre_<?=$widget->id?>" value="<?=$widget->nombre?>" />
            <?= $widget->displayForm() ?>

            <a class="btn btn-danger btn-block" href="<?=site_url('backend/gestion/widget_remove/'.$widget->id)?>" style="margin-top: 50px;" onclick="return confirm('¿Esta seguro que desea eliminar este widget?')"><span class="icon-white icon-trash"></span> Eliminar <span class="hide-text"><?=$widget->id?></span></a>
        </div>
    </form>
</div>
