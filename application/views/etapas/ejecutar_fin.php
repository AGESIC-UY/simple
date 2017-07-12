<script type="text/javascript">

</script>
<h2>
  <?=$etapa->Tarea->Proceso->nombre?>

  <?php if(isset($funcionario_actuando_como_ciudadano) && $funcionario_actuando_como_ciudadano):?>
    <b style="color:#337ab7"> - Ejecutando como ciudadano: <?php echo $usuario_nombres;?></b>
  <?php endif; ?>
</h2>

<form method="POST" class="ajaxForm dynaForm" action="<?= site_url('etapas/ejecutar_fin_form/' . $etapa->id.($qs?'?'.$qs:'')) ?>">
    <!--<fieldset>-->
        <!--<legend class>Paso final</legend>-->
        <div class="validacion validacion-error"></div>
        <?php if ($tareas_proximas->estado == 'pendiente'): ?>
            <div class="dialogo validacion-warning">
              <h3 class="dialogos_titulo">Paso final</h3>
              <div class="alert alert-warning"><?= $etapa->Tarea->paso_final_pendiente?></div>
            </div>
            <?php foreach ($tareas_proximas->tareas as $t): ?>
              <?php if ($t->asignacion == 'manual'): ?>
                  <?php if (count($tareas_proximas->tareas)>1): ?>
                    <label>Asignar próxima etapa (<?= $t->nombre?>) a</label>
                  <?php endif; ?>
                  <?php if (count($tareas_proximas->tareas) <= 1): ?>
                    <label>Asignar próxima etapa a</label>
                  <?php endif; ?>
                  <select class="chosen" name="usuarios_a_asignar[<?= $t->id ?>]">
                      <?php foreach ($t->getUsuarios($etapa->id) as $u): ?>
                          <option value="<?= $u->id ?>"><?= $u->displayUsername(true)?></option>
                      <?php endforeach; ?>
                  </select>
              <?php endif; ?>
            <?php endforeach; ?>
        <?php elseif($tareas_proximas->estado=='standby'): ?>
          <div class="dialogo validacion-warning">
            <h3 class="dialogos_titulo">Paso final</h3>
            <div class="alert alert-warning"><?= $etapa->Tarea->paso_final_standby?></div>
          </div>
        <?php elseif($tareas_proximas->estado=='completado'):?>
          <div class="dialogo validacion-warning">
            <h3 class="dialogos_titulo">Validación previa al envío</h3>
            <div class="alert alert-warning"><?= $etapa->Tarea->paso_final_completado?></div>
          </div>
        <?php elseif($tareas_proximas->estado=='sincontinuacion'):?>
          <div class="dialogo validacion-warning">
            <h3 class="dialogos_titulo">Paso final</h3>
            <div class="alert alert-warning"><?= $etapa->Tarea->paso_final_sincontinuacion?></div>
          </div>
        <?php endif; ?>

    <!--</fieldset>-->
    <ul class="form-action-buttons">
      <li class="action-buttons-primary">
        <ul>
            <?php if($tareas_proximas->estado!='sincontinuacion'):?>
              <li>
                <button class="btn btn-primary btn-lg" type="submit">
                  <span class="icon-ok icon-white"></span>
                  <?= $etapa->Tarea->texto_boton_paso_final ?>
                </button>
              </li>
              <?php if(isset($hay_pasos_generar_pdf) && $hay_pasos_generar_pdf):?>
                <li>
                  <a href="<?php if(isset($link_pdf)) echo $link_pdf; ?>" class="btn btn-secundary btn-lg" target="_blank">
                    <span class="icon-print icon-white"></span>
                    <?php echo $etapa->Tarea->texto_boton_generar_pdf; ?>
                  </a>
                </li>
              <?php endif;?>
            <?php endif?>

            <?php if($tareas_proximas->estado =='sincontinuacion'):?>
              <?php if(isset($hay_pasos_generar_pdf) && $hay_pasos_generar_pdf):?>
                <li>
                  <a href="<?php if(isset($link_pdf)) echo $link_pdf; ?>" class="btn btn-secundary btn-lg" target="_blank">
                    <span class="icon-print icon-white"></span>
                    <?php echo $etapa->Tarea->texto_boton_generar_pdf; ?>
                  </a>
                </li>
              <?php endif;?>
            <?php endif;?>
          </ul>
        </li>
        <li class="action-buttons-second">
          <ul>
            <li class="float-left">
              <a class="btn btn-link btn-lg" href="<?= site_url('etapas/ejecutar/' . $etapa->id . '/' . (count($etapa->getPasosEjecutables()) - 1).($qs?'?'.$qs:'')) ?>"><span class="icon-chevron-left"></span> Volver</a>
            </li>
          </ul>
        </li>
      </ul>
</form>
