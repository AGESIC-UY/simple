<h2><?=$etapa->Tarea->Proceso->nombre?></h2>

<form method="POST" class="ajaxForm dynaForm" action="<?= site_url('etapas/ejecutar_fin_form/' . $etapa->id.($qs?'?'.$qs:'')) ?>">
    <fieldset>
        <legend>Paso final</legend>
        <div class="validacion validacion-error"></div>
        <?php if ($tareas_proximas->estado == 'pendiente'): ?>
            <?php foreach ($tareas_proximas->tareas as $t): ?>
                <p>Para confirmar y enviar el formulario a la siguiente etapa haga click en Finalizar.</p>
                <?php if ($t->asignacion == 'manual'): ?>
                    <label>Asignar próxima etapa a</label>
                    <select class="chosen" name="usuarios_a_asignar[<?= $t->id ?>]">
                        <?php foreach ($t->getUsuarios($etapa->id) as $u): ?>
                            <option value="<?= $u->id ?>"><?= $u->displayUsername(true)?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php elseif($tareas_proximas->estado=='standby'): ?>
            <p>Luego de hacer click en Finalizar esta etapa quedara detenida momentaneamente hasta que se completen el resto de etapas pendientes.</p>
        <?php elseif($tareas_proximas->estado=='completado'):?>
            <p>Luego de hacer click en Finalizar este trámite quedará completado.</p>
        <?php elseif($tareas_proximas->estado=='sincontinuacion'):?>
            <p>Este trámite no tiene una etapa donde continuar.</p>
        <?php endif; ?>

    </fieldset>
    <ul class="form-action-buttons">
      <li class="action-buttons-primary">
        <ul>
          <li>
            <?php if($tareas_proximas->estado!='sincontinuacion'):?><button class="btn btn-primary btn-lg" type="submit"><span class="icon-ok icon-white"></span> Finalizar</button><?php endif?>
            </li>
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
