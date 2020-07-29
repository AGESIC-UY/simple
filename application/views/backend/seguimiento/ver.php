<?php if ($this->config->item('js_diagram') == 'gojs'): ?>
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

<?php
$array_id_tareas_automaticas = array();
$array_id_tareas_escaladas = array();
$array_id_tareas_avanzadas_cron_pagos = array();
$array_id_tareas_avanzadas_cron_agenda = array();
$array_id_tareas_avanzadas_cron_pagos_agendas = array();


foreach ($tramite->Etapas as $etapa) {
    $tarea_ejecutada_automaticamente = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('tarea_ejecutada_automaticamente', $etapa->id);
    $tarea_escalada_automaticamente = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('escalado_automatico', $etapa->id);
    $tarea_avanzada_cron_pagos = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('tarea_avanzada_cron_pagos', $etapa->id);
    $tarea_avanzada_cron_agenda = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('tarea_avanzada_cron_agenda', $etapa->id);
    $tarea_avanzada_cron_pagos_agendas = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('tarea_avanzada_cron_pagos_agendas', $etapa->id);


    if ($tarea_ejecutada_automaticamente) {
        array_push($array_id_tareas_automaticas, $etapa->tarea_id);
    }
    if ($tarea_escalada_automaticamente) {
        array_push($array_id_tareas_escaladas, $etapa->tarea_id);
    }

    if ($tarea_avanzada_cron_pagos) {
        array_push($array_id_tareas_avanzadas_cron_pagos, $etapa->tarea_id);
    }

    if ($tarea_avanzada_cron_agenda) {
        array_push($array_id_tareas_avanzadas_cron_agenda, $etapa->tarea_id);
    }

    if ($tarea_avanzada_cron_pagos_agendas) {
        array_push($array_id_tareas_avanzadas_cron_pagos_agendas, $etapa->tarea_id);
    }
}
?>
<script type="text/javascript">
    $(document).ready(function () {
        tramiteId =<?= $tramite->id ?>;
        drawFromModel(<?= $tramite->Proceso->getJSONFromModel() ?>, "<?= $tramite->Proceso->width ?>", "<?= $tramite->Proceso->height ?>");
        drawSeguimiento(
<?= json_encode($tramite->getTareasActuales()->toArray()) ?>,
<?= json_encode($tramite->getTareasCompletadas()->toArray()) ?>,
<?= json_encode($tramite->getTareasAutomaticas($array_id_tareas_automaticas)->toArray()) ?>,
<?= json_encode($tramite->getTareasAutomaticas($array_id_tareas_escaladas)->toArray()) ?>,
<?= json_encode($tramite->getTareasAutomaticas($array_id_tareas_avanzadas_cron_pagos)->toArray()) ?>,
<?= json_encode($tramite->getTareasAutomaticas($array_id_tareas_avanzadas_cron_agenda)->toArray()) ?>,
<?= json_encode($tramite->getTareasAutomaticas($array_id_tareas_avanzadas_cron_pagos_agendas)->toArray()) ?>
        );
    });
</script>

<ul class="breadcrumb">
    <li><a href="<?= site_url('backend/seguimiento/index') ?>">Seguimiento de Procesos</a><span class="divider">/</span></li>
    <li><a href="<?= site_url('backend/seguimiento/index_proceso/' . $tramite->Proceso->id) ?>"><?= $tramite->Proceso->nombre ?></a><span class="divider">/</span></li>
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

                    <?php $tarea_ejecutada_automaticamente = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('tarea_ejecutada_automaticamente', $e->id); ?>
                    <?php if ($tarea_ejecutada_automaticamente) : ?>
                        <p>(Proceso instanciado desde API)</p>
                    <?php endif ?>

                    <?php $tarea_escalada_automaticamente = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('escalado_automatico', $e->id); ?>
                    <?php if ($tarea_escalada_automaticamente) : ?>
                        <p>(Escalada automáticamente)</p>
                    <?php endif ?>

                    <?php $tarea_avanzada_cron_pagos = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('tarea_avanzada_cron_pagos', $e->id); ?>
                    <?php if ($tarea_avanzada_cron_pagos) : ?>
                        <p>(Avanzada por cron pagos)</p>
                    <?php endif ?>

                    <?php $tarea_avanzada_cron_agenda = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('tarea_avanzada_cron_agenda', $e->id); ?>
                    <?php if ($tarea_avanzada_cron_agenda) : ?>
                        <p>(Avanzada por cron agenda)</p>
                    <?php endif ?>

                    <?php $tarea_avanzada_cron_pagos_agendas = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('tarea_avanzada_cron_pagos_agendas', $e->id); ?>
                    <?php if ($tarea_avanzada_cron_pagos_agendas) : ?>
                        <p>(Avanzada por cron pagos-agendas)</p>
                    <?php endif ?>

                    <p><?= $e->created_at ? 'Inicio: ' . strftime('%c', mysql_to_unix($e->created_at)) : '' ?></p>
                    <p><?= $e->ended_at ? 'Término: ' . strftime('%c', mysql_to_unix($e->ended_at)) : '' ?></p>
                    <p>Asignado a: <?= !$e->usuario_id ? 'Ninguno' : !$e->Usuario->registrado ? 'No registrado' : '<abbr class="tt" title="' . $e->Usuario->displayInfo() . '">' . $e->Usuario->displayUsername() . '</abbr>' ?></p>

                    <?php $dato_funcionario = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('funcionario_actuando_como_ciudadano', $e->id); ?>
                    <?php if ($dato_funcionario) : ?>
                        <?php $funcionario = Doctrine::getTable('Usuario')->findOneById($dato_funcionario->valor); ?>
                        <p>Funcionario actuante: <?php echo $funcionario->nombres . ' ' . $funcionario->apellido_paterno ?></p>
                    <?php endif ?>
                    <?php $datos_cerrado_por = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('usuario_fin_etapa_generado', $e->id); ?>
                    <?php if ($datos_cerrado_por && $dato_funcionario) : ?>
                        <?php $funcionario = Doctrine::getTable('Usuario')->findOneById($datos_cerrado_por->valor); ?>
                        <p>Tarea Finalizada por: <?php echo $funcionario->nombres . ' ' . $funcionario->apellido_paterno ?></p>
                    <?php endif ?>

                    <?php if ($e->canUsuarioRevisarDetalle(UsuarioBackendSesion::usuario())) : ?>
                        <p><a href="<?= site_url('backend/seguimiento/ver_etapa/' . $e->id) ?>">Revisar detalle<span class="hidden-accessible">-<?= mt_rand() ?></span></a></p>
                    <?php endif ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<div id="drawWrapper"><div id="draw"></div></div>
</div>
