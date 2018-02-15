<input type="hidden" id="info_tramite_id_tramite" value="<?=$etapa->tramite_id?>" />
<input type="hidden" id="info_tramite_id_interaccion" value="<?=str_replace(' ', '_', $etapa->Tarea->nombre)?>" />
<?php
$prox_paso_traza = $etapa->getPasoEjecutable($step_position + 1);
$cierre_automatico = ($etapa->Tarea->final ||  !$etapa->Tarea->paso_confirmacion)  && $prox_paso_traza && $prox_paso_traza->getReadonly() && end($etapa->getPasosEjecutables()) == $prox_paso_traza;
?>
<input type="hidden" id="info_tramite_nro_paso" value="<?php if($cierre_automatico) { echo 'paso_fin' ; } else if($step_position == 0) { echo 'paso_inicio' ; } else { echo 'paso_' . ($step_position +1); } ?>" />

<h1><?=$paso->Formulario->Proceso->nombre?>
  <?php if(isset($funcionario_actuando_como_ciudadano) && $funcionario_actuando_como_ciudadano):?>
    <b class="blue"> - Ejecutando como ciudadano: <?php echo $usuario_nombres;?></b>
  <?php endif; ?>
  </h1>
<?php if($etapa->Tarea->vencimiento):?>
<div class="alert alert-warning">Atención. Esta etapa <?=$etapa->getFechaVencimientoAsString()?>.</div>
<?php endif ?>
<ul class="step-boxes<?= (count($etapa->getPasosEjecutables()) == 1 ? ' hidden' :'')?>">
    <?php
        $current_position = ++$step_position;
        for($i = 1; $i <= count($etapa->getPasosEjecutables()); $i++) {
            $step_title = $etapa->getPasoEjecutable($i-1)->nombre;

            ?>
            <li class="step-box<?= ($i == $current_position ? ' active' : '')?>" style="width:<?=number_format((100/count($etapa->getPasosEjecutables())), 4, '.', '') ?>%;">
                <span class="wizard-step"><?=$i?></span>
                <span class="wizard-step-description"><?=$step_title?></span>
            </li>
            <?php
        }
    ?>
</ul>
<?php if(count($etapa->getPasosEjecutables()) != 1):?>
<h2><?=$etapa->getPasoEjecutable($step_position-1)->nombre?></h2>
<?php endif ?>
<form method="POST" class="ajaxForm dynaForm form-horizontal" action="<?=site_url('etapas/ejecutar_form/'.$etapa->id.'/'.$secuencia.($qs?'?'.$qs:''))?>">
    <input type="hidden" name="_method" value="post">
    <input type="hidden" name="no_advance" id="no_advance" value="0" />
	<div class="validacion validacion-error"></div>

    <div class="aviso_campos_obligatorios">Los campos indicados con * son obligatorios.</div><br />

    <?=($paso->Formulario->contenedor == 1 ? '<fieldset>' : '<div>') ?>
        <?=($paso->Formulario->contenedor == 1 ? '<legend>'.$paso->Formulario->leyenda.'</legend>' : '') ?>

        <?php foreach($paso->Formulario->Campos as $c): ?>
            <div class="campo" data-id="<?=$c->id?>" <?= $c->dependiente_campo ? 'data-dependiente-campo="' . $c->dependiente_campo.'" data-dependiente-valor="' . $c->dependiente_valor .'" data-dependiente-tipo="' . $c->dependiente_tipo.'" data-dependiente-relacion="'.$c->dependiente_relacion.'"' : '' ?> data-readonly="<?=$paso->modo=='visualizacion' || $c->readonly?>" >
            <?=$c->displayConDatoSeguimiento($etapa->id,$paso->modo)?>
            </div>
        <?php endforeach ?>
    <?=($paso->Formulario->contenedor == 1 ? '</fieldset>' : '</div>') ?>
    <ul class="form-action-buttons">
        <li class="action-buttons-primary">
            <ul>
                <li>
                  <?php if (UsuarioSesion::usuario()->registrado): ?>

                    <?php

                      $usuario_backend_sesion = Doctrine::getTable('UsuarioBackend')->findOneByUsuarioAndCuentaId(UsuarioSesion::usuario()->usuario,UsuarioSesion::usuario()->cuenta_id);
                      if($usuario_backend_sesion && (UsuarioBackend::user_has_rol($usuario_backend_sesion->id, 'seguimiento') || UsuarioBackend::user_has_rol($usuario_backend_sesion->id, 'super'))){
                        $usuario_con_acceso = true;
                      }else{
                        $usuario_con_acceso = false;
                      }


                    ?>
                    <?php if($usuario_con_acceso) {?>
                      <a class="btn btn-info btn-lg" href="<?=site_url('historico/ver/' . $etapa->Tramite->id)?>" target="_blank"><span class="icon-search"></span> Histórico de Trámites</a>
                    <?php } ?>
                      <button class="btn btn-secundary btn-lg" type="submit" id="save_step"><span class="icon-ok icon-white"></span> Guardar y Cerrar</button>
                  <?php endif; ?>
                  <button class="btn btn-primary btn-lg" type="submit" id="btn_siguiente_ciudadano">Siguiente <span class="icon-chevron-right icon-white"></span></button>
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
