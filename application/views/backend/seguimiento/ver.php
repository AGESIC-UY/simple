<?php if($this->config->item('js_diagram')=='gojs'):?>
<link href="<?= base_url() ?>assets/css/diagrama-procesos2.css" rel="stylesheet">
<script src="<?= base_url() ?>assets/js/go/go.js" type="text/javascript"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/js/diagrama-procesos2.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/js/seguimiento2.js"></script>
<?php else: ?>
<link href="<?= base_url() ?>assets/css/diagrama-procesos.css" property='stylesheet' rel="stylesheet">
<script src="<?= base_url() ?>assets/js/jquery.jsplumb/jquery.jsPlumb-1.3.16-all-min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/js/diagrama-procesos.js"></script>
<script type="text/javascript" src="<?= base_url() ?>assets/js/seguimiento.js"></script>
<?php endif ?>

<script type="text/javascript">
    $(document).ready(function(){
        tramiteId=<?= $tramite->id ?>;
        drawFromModel(<?= $tramite->Proceso->getJSONFromModel() ?>,"<?=$tramite->Proceso->width?>","<?=$tramite->Proceso->height?>");
        drawSeguimiento(<?= json_encode($tramite->getTareasActuales()->toArray()) ?>,<?= json_encode($tramite->getTareasCompletadas()->toArray()) ?>);
    });
</script>

<ul class="breadcrumb">
    <li><a href="<?= site_url('backend/seguimiento/index') ?>">Seguimiento de Procesos</a><span class="divider">/</span></li>
    <li><a href="<?= site_url('backend/seguimiento/index_proceso/'.$tramite->Proceso->id) ?>"><?=$tramite->Proceso->nombre?></a><span class="divider">/</span></li>
    <li class="active">Trámite # <?= $tramite->id ?></li>
</ul>


<div id="areaDibujo">
    <h2><?= $tramite->Proceso->nombre ?> - Trámite # <?= $tramite->id ?></h2>
    <div class="well dialogo-colgante">
      <h3>Registro de eventos</h3>
      <ul>
        <?php foreach ($etapas as $e): ?>
          <li>
            <h4><?= $e->Tarea->nombre ?></h4>
            <p>Estado: <?= $e->pendiente == 0 ? 'Completado' : 'Pendiente' ?></p>
            <p><?= $e->created_at ? 'Inicio: ' . strftime('%c', mysql_to_unix($e->created_at)) : '' ?></p>
            <p><?= $e->ended_at ? 'Término: ' . strftime('%c', mysql_to_unix($e->ended_at)) : '' ?></p>
            <p>Asignado a: <?= !$e->usuario_id ? 'Ninguno' : !$e->Usuario->registrado ? 'No registrado' : '<abbr class="tt" title="'.$e->Usuario->displayInfo().'">'.$e->Usuario->displayUsername().'</abbr>' ?></p>

            <?php $dato_funcionario = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('funcionario_actuando_como_ciudadano', $e->id); ?>
            <?php if($dato_funcionario) :?>
              <?php $funcionario = Doctrine::getTable('Usuario')->findOneById($dato_funcionario->valor); ?>
              <p>Funcionario actuante: <?php echo $funcionario->nombres.' '.$funcionario->apellido_paterno?></p>
            <?php endif?>
            <?php $datos_cerrado_por = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('usuario_fin_etapa_generado', $e->id); ?>
            <?php if($datos_cerrado_por && $dato_funcionario) :?>
              <?php $funcionario = Doctrine::getTable('Usuario')->findOneById($datos_cerrado_por->valor); ?>
              <p>Tarea Finalizada por: <?php echo $funcionario->nombres.' '.$funcionario->apellido_paterno?></p>
            <?php endif?>

            <?php if($e->canUsuarioRevisarDetalle(UsuarioBackendSesion::usuario())) :?>
              <p><a href="<?= site_url('backend/seguimiento/ver_etapa/' . $e->id) ?>">Revisar detalle<span class="hidden-accessible">-<?=mt_rand()?></span></a></p>
            <?php endif?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
</div>
<div id="drawWrapper"><div id="draw"></div></div>
</div>
