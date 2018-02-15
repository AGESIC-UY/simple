<h1><?= $proceso->nombre ?></h1>

<div class="dialogo validacion-warning">
  <div class="alert alert-alert" id="89363">
    <?php $texto_uno_tramite = 'Existe '.count($tramites).' trámite sin finalizar';
          $texto_muchos_tramite = 'Existen '.count($tramites).' trámites sin finalizar';?>

    <span class="dialogos_titulo"><?= count($tramites)==1 ? $texto_uno_tramite:$texto_muchos_tramite ?></span>
    <div class="dialogos_contenido">Puede iniciar uno nuevo o continuar uno de los iniciados anteriormente.</div>
    <a href="<?=site_url('tramites/iniciar_f/'.$proceso->id.($qs?'?'.$qs:''));?>" class="btn btn-primary preventDoubleRequest"><span class="icon-play icon-white"></span> Iniciar Nuevo <span class="hide-text"><?= $proceso->nombre ?></span></a>
  </div>
</div>

<?php if (count($tramites) > 0): ?>
    <table id="mainTable" class="table">
      <caption class="hide-text">Existen tramites iniciados del proceso</caption>
        <thead>
            <tr>
                <th>Identificador</th>
                <th>Documento</th>
                <th>Fecha Iniciado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tramites as $t): ?>
                <tr>
                    <td data-title="Id" class="list_id_tramite"><?= $t->id ?></td>
                    <td class="list_documento"><?php
                      $c=1;
                      $documento = null;
                      foreach ($t->getTodasEtapas() as $e) {
                        if($c == 1) {
                          $documento = $e->getUsuarioInicial();
                        }

                        $c++;
                      }
                      echo $documento; ?></td>
                    <td class="time list_modificacion" data-title="Fecha Iniciado"><?= strftime('%d.%b.%Y', mysql_to_unix($t->created_at)) ?> <br /><?= strftime('%H:%M:%S', mysql_to_unix($t->created_at)) ?></td>
                    <td class="actions" data-title="Acciones">
                      <a href="<?=site_url('etapas/ejecutar/'.$t->getEtapasActuales()->get(0)->id.($qs?'?'.$qs:''));?>" class="btn btn-primary preventDoubleRequest"><span class="icon-play icon-white"></span> Continuar <span class="hide-text"><?= $proceso->nombre ?></span></a>
                      <?php if(count($e->Tramite->Etapas)==1):?><a href="<?=site_url('tramites/eliminar/'.$e->tramite_id)?>" class="btn btn-danger" onclick="return confirm('¿Esta seguro que desea eliminar este tramite?')"><span class="icon-trash"></span><span class="hide-text">Eliminar <?= $e->Tramite->Proceso->nombre ?></span></a><?php endif ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <table id="mainTable" class="table"></table>
    <p>Ud no ha participado en trámites.</p>
<?php endif; ?>
