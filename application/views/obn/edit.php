<?php ?>
<form method="POST" class="ajaxForm dynaForm form-horizontal" action="<?= site_url('obn_consultas/alta_obn_form') ?>">
    <input type="hidden" name="_method" value="post">
    <input type="hidden" name="no_advance" id="no_advance" value="0" />
    <input type="hidden" name="redirect_form" id="redirect_form" value="<?= htmlspecialchars($redirect_form) ?>" />
    <input type="hidden" name="id" id="id" value="<?= $objeto->id ? $objeto->id : null ?>" />
    <input type="hidden" name="variable_obn" id="variable_obn" value="<?= $variable_obn ?>" />
    <input type="hidden" name="formulario_id" id="formulario_id" value="<?= $formulario->id ?>" />
    <input type="hidden" name="etapa_id" id="etapa_id" value="<?= $etapa->id ?>" />
    <div class="validacion validacion-error"></div>
    <div class="aviso_campos_obligatorios">Los campos indicados con * son obligatorios.</div><br />

    <?= ($formulario->contenedor == 1 ? '<fieldset>' : '<div>') ?>
    <?= ($formulario->contenedor == 1 ? '<legend>' . $formulario->leyenda . '</legend>' : '') ?>

    <?php foreach ($formulario->Campos as $c): ?>
        <div class="campo" data-id="<?= $c->id ?>" <?= $c->dependiente_campo ? 'data-dependiente-campo="' . $c->dependiente_campo . '" data-dependiente-valor="' . $c->dependiente_valor . '" data-dependiente-tipo="' . $c->dependiente_tipo . '" data-dependiente-relacion="' . $c->dependiente_relacion . '"' : '' ?> data-readonly="0" >
            <?php
            if ($c->tipo == "tabla_datos") {
                $atributo_obn = explode(".", $variable_obn);
                if(isset($objeto->id)) {
                    $c->variable_obn = $variable_obn . ".[" . $objeto->id . "]." . $c->nombre;
                }else if (!isset($atributo_obn[1])) {
                    $c->variable_obn = $variable_obn . "." . $c->nombre;
                } else {
                    $c->variable_obn = $variable_obn . "." . $c->nombre;
                }
            }
            ?>
            <?php if ($edicion): ?>
                <?php $nombre = $c->nombre; ?>
                <?php if (isset($objeto->$nombre)): ?>

                    <?php if (json_decode(htmlspecialchars_decode($objeto->$nombre))): ?>
                        <?= $c->displayConDatoObn($etapa->id, json_decode(htmlspecialchars_decode($objeto->$nombre))) ?>                    
                    <?php else: ?>
                        <?= $c->displayConDatoObn($etapa->id, htmlspecialchars_decode($objeto->$nombre)) ?>      
                    <?php endif; ?>  
                <?php else: ?>
                    <?= $c->displaySinDato() ?>
                <?php endif; ?>
            <?php else: ?>
                <?= $c->displaySinDatoObn($etapa->id) ?>
        <?php endif; ?>
        </div>
    <?php endforeach ?>
<?= ($formulario->contenedor == 1 ? '</fieldset>' : '</div>') ?>
    <ul class="form-action-buttons">
        <li class="action-buttons-primary">
            <ul>
                <li>
                    <button class="btn btn-secundary btn-lg" type="submit" id="save_step"><span class="icon-ok icon-white"></span> Guardar y Cerrar</button>
                </li>
            </ul>
        </li>
        <li class="action-buttons-second">
            <ul>
            </ul>
        </li>
    </ul>
</form>