<ul class="breadcrumb">
    <li>Estadisticas <span class="divider">/</span></li>
    <li><a href="<?=site_url('manager/estadisticas/cuentas')?>">Trámites en curso</a> <span class="divider">/</span></li>
    <li><a href="<?=site_url('manager/estadisticas/cuentas/'.$proceso->Cuenta->id)?>"><?=$proceso->Cuenta->nombre?></a> <span class="divider">/</span></li>
    <li class="active"><?=$title?></li>
</ul>

<h2>Trámites del proceso <?=$title?></h2>
<p style="text-align: right; color: red;">*Estadisticas con respecto a los últimos 30 días.</p>

<table class="table">
  <caption class="hide-text">Trámites</caption>
  <thead>
      <tr>
          <th>#</th>
          <th>Etapa Actual</th>
          <th>Estado</th>
          <th>Fecha</th>
      </tr>
  </thead>
  <tbody>
      <?php foreach ($tramites as $t): ?>
          <tr>
              <td><?= $t->id ?></td>
              <td>
                  <?php
                  $etapas=$t->getEtapasActuales();
                  $etapas_arr=array();
                  foreach($etapas as $e)
                      $etapas_arr[]=$e->Tarea->nombre;
                  echo implode(', ', $etapas_arr);
                  ?>
              </td>
              <td><?= $t->pendiente ? 'Pendiente' : 'Completado' ?></td>
              <td><?= strftime('%c', strtotime($t->updated_at)) ?></td>
          </tr>
      <?php endforeach; ?>
  </tbody>
</table>
