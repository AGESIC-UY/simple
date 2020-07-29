<?php foreach($etapas as $e):?>
<ol>
    <li>
        <p>Estado: <?=$e->pendiente==0?'Completado':'Pendiente'?></p>
        <?php $tarea_ejecutada_automaticamente = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('tarea_ejecutada_automaticamente', $e->id); ?>
        <?php if($tarea_ejecutada_automaticamente) :?>
          <p>(Proceso instanciado desde API)</p>
        <?php endif?>
        <p><?=$e->created_at?'Inicio: '.strftime('%c',mysql_to_unix($e->created_at)):''?></p>
        <p><?=$e->ended_at?'Término: '.strftime('%c',mysql_to_unix($e->ended_at)):''?></p>
        <p>Asignado a: <?=!$e->usuario_id?'Ninguno':!$e->Usuario->registrado?'No registrado':'<abbr class="tt" title="'.$e->Usuario->displayInfo().'">'.$e->Usuario->displayUsername().'</abbr>'?></p>

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
            <p><a href="<?=site_url('backend/seguimiento/ver_etapa/'.$e->id)?>">Revisar detalle</a></p>
        <?php endif?>
    </li>
</ol>
<?php endforeach; ?>
