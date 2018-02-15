<script>
$(document).ready(function() {
  $("#btn_siguiente").click(function(event) {
    event.preventDefault();
    window.location.href = "<?= site_url('etapas/ver/' . $etapa->id . '/' . ($secuencia + 1)) ?>";
  });
});
</script>
<input type="hidden" id="info_tramite_id_tramite" value="<?=$etapa->tramite_id?>" />
<input type="hidden" id="info_tramite_id_interaccion" value="<?=str_replace(' ', '_', $etapa->Tarea->nombre)?>" />
<input type="hidden" id="info_tramite_nro_paso" value="<?php if($step_position == 0) {echo 'paso_inicio';} else{echo 'paso_' . ($step_position+1);} ?>" />

<h1><?=$paso->Formulario->Proceso->nombre?>
  <?php if(isset($funcionario_actuando_como_ciudadano) && $funcionario_actuando_como_ciudadano):?>
    <b class="blue"> - Ejecutando como ciudadano: <?php echo $usuario_nombres;?></b>
  <?php endif; ?>
</h1>
<h2><?=$paso->nombre?></h2> 
<form class="form-horizontal dynaForm">
    <?=($paso->Formulario->contenedor == 1 ? '<fieldset>' : '<div>') ?>
        <?=($paso->Formulario->contenedor == 1 ? '<legend>'.$paso->Formulario->leyenda.'</legend>' : '') ?>
        <?php foreach ($paso->Formulario->Campos as $c): ?>
            <div class="control-group campo" data-id="<?= $c->id ?>" <?= $c->dependiente_campo ? 'data-dependiente-campo="' . $c->dependiente_campo.'" data-dependiente-valor="' . $c->dependiente_valor .'" data-dependiente-tipo="' . $c->dependiente_tipo.'" data-dependiente-relacion="'.$c->dependiente_relacion.'"' : '' ?> data-readonly="<?=$paso->modo=='visualizacion' || $c->readonly?>" >
                <?= $c->displayConDatoSeguimiento($etapa->id, 'visualizacion') ?>
            </div>
        <?php endforeach ?>

        <?=($paso->Formulario->contenedor == 1 ? '</fieldset>' : '</div>') ?>

        <ul class="form-action-buttons">
            <li class="action-buttons-primary">
                <ul>
                    <li>
                      <?php if ($secuencia + 1 < count($etapa->getPasosEjecutables())): ?>
                        <button id="btn_siguiente" class="btn btn-primary btn-lg" type="submit">Siguiente
                          <span class="icon-chevron-right icon-white"></span>
                        </button>
                      <?php endif; ?>
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
</form>
