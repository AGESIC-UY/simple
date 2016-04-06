<ul class="breadcrumb">
    <li>Estadisticas <span class="divider">/</span></li>
    <li><a href="<?=site_url('manager/estadisticas/cuentas')?>">Trámites en curso</a> <span class="divider">/</span></li>
    <li class="active"><?=$title?></li>
</ul>

<h2>Trámites de la cuenta <?=$title?> por procesos</h2>
<p style="text-align: right; color: red;">*Estadisticas con respecto a los últimos 30 días.</p>

<table class="table">
  <caption class="hide-text">Procesos</caption>
    <thead>
        <tr>
            <th>Proceso</th>
            <th>Nº de Trámites</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($procesos as $p): ?>
        <tr>
            <td><a href="<?=site_url('manager/estadisticas/cuentas/'.$p->cuenta_id.'/'.$p->id)?>"><?=$p->nombre?></a></td>
            <td><?=$p->ntramites?></td>
        </tr>
        <?php endforeach; ?>

        <tr class="success">
            <td>Total</td>
            <td><?=$ntramites?></td>
        </tr>
    </tbody>
</table>
