<h2><?=$paso->Formulario->Proceso->nombre?></h2>
<?php if($etapa->Tarea->vencimiento):?>
<div class="alert alert-warning">Atención. Esta etapa <?=$etapa->getFechaVencimientoAsString()?>.</div>
<?php endif ?>
<ul class="step-boxes <?= (count($etapa->getPasosEjecutables()) == 1 ? 'hidden' : '') ?>">
    <?php
        $current_position = ++$step_position;
        for($i = 1; $i <= count($etapa->getPasosEjecutables()); $i++) {
            $step_title = $etapa->getPasoEjecutable($i-1)->nombre;

            ?>
            <li class="step-box <?= ($i == $current_position ? 'active' : '') ?>" style="width:<?=(100/count($etapa->getPasosEjecutables()))?>%;">
                <span class="wizard-step"><?=$i?></span>
                <span class="wizard-step-description"><?=$step_title?></span>
            </li>
            <?php
        }
    ?>
</ul>
<form method="POST" class="ajaxForm dynaForm form-horizontal" action="<?=site_url('etapas/ejecutar_form/'.$etapa->id.'/'.$secuencia.($qs?'?'.$qs:''))?>">
    <input type="hidden" name="_method" value="post">
    <input type="hidden" name="no_advance" id="no_advance" value="0" />
    <fieldset>
        <legend><?=$paso->Formulario->nombre?></legend>

        <div class="validacion validacion-error"></div>

        <div class="aviso_campos_obligatorios">Los campos indicados con * son obligatorios.</div>
        <?php foreach($paso->Formulario->Campos as $c): ?>
            <div class="campo" data-id="<?=$c->id?>" <?= $c->dependiente_campo ? 'data-dependiente-campo="' . $c->dependiente_campo.'" data-dependiente-valor="' . $c->dependiente_valor .'" data-dependiente-tipo="' . $c->dependiente_tipo.'" data-dependiente-relacion="'.$c->dependiente_relacion.'"' : '' ?> data-readonly="<?=$paso->modo=='visualizacion' || $c->readonly?>" >
            <?=$c->displayConDatoSeguimiento($etapa->id,$paso->modo)?>
            </div>
        <?php endforeach ?>
    </fieldset>
    <ul class="form-action-buttons">
        <li class="action-buttons-primary">
            <ul>
                <li>
                  <?php if (UsuarioSesion::usuario()->registrado): ?>
                      <button class="btn btn-secundary btn-lg" type="button" id="save_step"><span class="icon-ok icon-white"></span> Guardar y Cerrar</button>
                  <?php endif; ?>
                  <button class="btn btn-primary btn-lg" type="submit">Siguiente <span class="icon-chevron-right icon-white"></span></button>
                  <!--button class="btn-lg btn-primario">Continuar al paso siguiente &gt;&gt;</button-->
                </li>
            </ul>
        </li>
        <li class="action-buttons-second">
            <ul>
                <li class="float-left">
                  <?php if ($secuencia>0): ?><a class="btn btn-link btn-lg" href="<?=site_url('etapas/ejecutar/'.$etapa->id.'/'.($secuencia-1).($qs?'?'.$qs:''))?>"><span class="icon-chevron-left"></span> Volver</a><?php endif; ?>
                  <!-- button class="btn-lg btn-link">&lt;&lt; Volver al paso anterior</button -->
                </li>
            </ul>
        </li>
    </ul>
</form>
