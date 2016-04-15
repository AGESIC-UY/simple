<h2><?=$paso->Formulario->Proceso->nombre?></h2>
<form class="form-horizontal dynaForm" onsubmit="return false;">
    <fieldset>
        <div class="validacion validacion-error"></div>
        <legend><?= $paso->Formulario->nombre ?></legend>
        <?php foreach ($paso->Formulario->Campos as $c): ?>
            <div class="control-group campo" data-id="<?= $c->id ?>" <?= $c->dependiente_campo ? 'data-dependiente-campo="' . $c->dependiente_campo.'" data-dependiente-valor="' . $c->dependiente_valor .'" data-dependiente-tipo="' . $c->dependiente_tipo.'" data-dependiente-relacion="'.$c->dependiente_relacion.'"' : '' ?> data-readonly="<?=$paso->modo=='visualizacion' || $c->readonly?>" >
                <?= $c->displayConDatoSeguimiento($etapa->id, 'visualizacion') ?>
            </div>
        <?php endforeach ?>

        <ul class="form-action-buttons">
            <li class="action-buttons-primary">
                <ul>
                    <li>
                      <?php if ($secuencia + 1 < count($etapa->getPasosEjecutables())): ?><a class="btn btn-primary btn-lg" href="<?= site_url('etapas/ver/' . $etapa->id . '/' . ($secuencia + 1)) ?>">Siguiente<span class="icon-chevron-right icon-white"></span></a><?php endif; ?>
                    </li>
                </ul>
            </li>
            <li class="action-buttons-second">
                <ul>
                    <li class="float-left">
                      <?php if ($secuencia > 0): ?><a class="btn btn-link btn-lg" href="<?= site_url('etapas/ver/' . $etapa->id . '/' . ($secuencia - 1)) ?>"><span class="icon-chevron-left"></span> Volver</a><?php endif; ?>
                    </li>
                </ul>
            </li>
        </ul>
    </fieldset>
</form>
