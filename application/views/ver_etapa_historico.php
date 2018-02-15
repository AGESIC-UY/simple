<ul class="breadcrumb">
    <li><a href="<?=site_url('historico/ver/'.$etapa->tramite_id)?>">Trámite # <?= $etapa->tramite_id ?></a> <span class="divider">/</span></li>
    <li><a href="<?=site_url('historico/ver_etapa/'.$etapa->id)?>"><?=$etapa->Tarea->nombre?></a> <span class="divider">/</span></li>
    <li class="active">Paso <?=$secuencia+1?></li>
</ul>
<h2><?=$etapa->Tramite->Proceso->nombre?> - Trámite # <?= $etapa->tramite_id ?> - <?=$etapa->Tarea->nombre?></h2>
<div class="row-fluid">
    <div class="span3">
      <div class="validacion validacion-error"></div>
        <div class="well">
            <p>Estado: <?= $etapa->pendiente == 0 ? 'Completado' : 'Pendiente' ?></p>
            <p><?= $etapa->created_at ? 'Inicio: ' . strftime('%c', mysql_to_unix($etapa->created_at)) : '' ?></p>
            <p><?= $etapa->ended_at ? 'Término: ' . strftime('%c', mysql_to_unix($etapa->ended_at)) : '' ?></p>

            <p>Asignado a : <?= empty($etapa->usuario_id) ?'Ninguno': (!$etapa->Usuario->registrado?'No registrado':'<abbr class="tt" title="'.$etapa->Usuario->displayInfo().'">'.$etapa->Usuario->displayUsername().'</abbr>')?></p>
            <?php if($etapa->usuario_original_id > 0 || ($etapa->usuario_original_historico != NULL && $etapa->usuario_original_historico != '')): ?>
              <p>Usuario Original (último) :  <?=!$etapa->usuario_original_id?'Ninguno':!$etapa->UsuarioOriginal->registrado?'No registrado':'<abbr class="tt" title="'.$etapa->UsuarioOriginal->displayInfo().'">'.$etapa->UsuarioOriginal->displayUsername().'</abbr>'?>
                (<abbr class="tt tooltip-tabla" title='<?=$etapa->displayHistoricoUsuarios()?>'>Ver más</abbr>)
              </p>

            <?php endif?>
            <?php $dato_funcionario = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('funcionario_actuando_como_ciudadano', $etapa->id); ?>
            <?php if($dato_funcionario) :?>
              <?php $funcionario = Doctrine::getTable('Usuario')->findOneById($dato_funcionario->valor); ?>
              <p>Funcionario actuante: <?php echo $funcionario->nombres.' '.$funcionario->apellido_paterno ?></p>
            <?php endif?>
            <?php $dato_usuario_empresa = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('usuario_actuando_como_empresa', $etapa->id); ?>
            <?php if($dato_usuario_empresa) :?>
              <?php $usuario_empresa = Doctrine::getTable('Usuario')->findOneById($dato_usuario_empresa->valor); ?>
              <p>Usuario actuante: <?php echo $usuario_empresa->nombres.' '.$usuario_empresa->apellido_paterno ?>
                (<abbr class="tt tooltip-tabla" title='<?=$etapa->displayHistoricoEjecucionesUsuarios()?>'>Ver más</abbr>)
              </p>
            <?php endif?>
            <?php $datos_cerrado_por = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('usuario_fin_etapa_generado', $etapa->id); ?>
            <?php if($datos_cerrado_por && $dato_funcionario) :?>
              <?php $funcionario = Doctrine::getTable('Usuario')->findOneById($datos_cerrado_por->valor); ?>
              <p>Tarea Finalizada por: <?php echo $funcionario->nombres.' '.$funcionario->apellido_paterno?></p>
            <?php endif?>
        </div>
      </div>
      <div class="span9">
          <form class="form-horizontal dynaForm" onsubmit="return false;">
              <fieldset>
                <?php if(isset($paso)): ?>
                  <legend><?= $paso->Formulario->nombre ?></legend>
                  <div class="validacion validacion-error"></div>
                  <?php foreach ($paso->Formulario->Campos as $c): ?>
                      <div class="control-group campo" data-id="<?= $c->id ?>" <?= $c->dependiente_campo ? 'data-dependiente-campo="' . $c->dependiente_campo.'" data-dependiente-valor="' . $c->dependiente_valor .'" data-dependiente-tipo="' . $c->dependiente_tipo.'" data-dependiente-relacion="'.$c->dependiente_relacion.'"' : '' ?> data-readonly="<?=$paso->modo=='visualizacion' || $c->readonly?>" >
                          <?=$c->displayConDatoSeguimiento($etapa->id,$paso->modo)?>
                      </div>
                  <?php endforeach ?>
                <?php else: ?>
                  <div class="alert alert-info">La tarea no tiene pasos.</div>
                <?php endif; ?>
              </fieldset>
              <ul class="form-action-buttons">
                  <li class="action-buttons-primary">
                      <ul>
                          <li>
                            <button class="hidden-accessible" type="submit">No hace nada</button>
                            <?php if ($secuencia + 1 < count($etapa->getPasosEjecutables())): ?><a class="btn btn-primary btn-lg" href="<?= site_url('historico/ver_etapa/' . $etapa->id . '/' . ($secuencia + 1)) ?>">Siguiente <span class="icon-chevron-right icon-white"></a><?php endif; ?>
                          </li>
                      </ul>
                  </li>
                  <li class="action-buttons-second">
                      <ul>
                          <li class="float-left">
                            <?php if ($secuencia>0): ?><a class="btn btn-link btn-lg" href="<?= site_url('historico/ver_etapa/' . $etapa->id . '/' . ($secuencia - 1)) ?>"><span class="icon-chevron-left"></span> Volver</a><?php endif; ?>
                            <!-- button class="btn-lg btn-link">&lt;&lt; Volver al paso anterior</button -->
                          </li>
                      </ul>
                  </li>
              </ul>
          </form>
      </div>
  </div>
