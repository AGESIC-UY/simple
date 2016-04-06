<ul class="breadcrumb">
    <li>Estadisticas <span class="divider">/</span></li>
    <li class="active">Trámites en curso</li>
</ul>

<h2>Trámites en curso por <?=$title?></h2>
<p style="text-align: right; color: red;">*Estadisticas con respecto a los últimos 30 días.</p>

<table class="table">
  <caption class="hide-text">Trámites en curso</caption>
  <thead>
      <tr>
          <th>Cuenta</th>
          <th>Nº de Trámites</th>
      </tr>
  </thead>
  <tbody>
      <?php foreach($cuentas as $c): ?>
      <tr>
          <td><a href="<?=site_url('manager/estadisticas/cuentas/'.$c->id)?>"><?=$c->nombre?></a></td>
          <td><?=$c->ntramites?></td>
      </tr>
      <?php endforeach; ?>

      <tr class="success">
          <td>Total</td>
          <td><?=$ntramites?></td>
      </tr>
  </tbody>
</table>
