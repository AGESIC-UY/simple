<ul class="breadcrumb">
    <li><a href="<?=site_url('manager')?>">Inicio</a> <span class="divider">/</span></li>
    <li><a href="<?=site_url('manager/claveunica')?>">ClaveUnica</a> <span class="divider">/</span></li>
    <li class="active"><?=$title?></li>
</ul>

<table class="table">
    <thead>
        <tr>
            <th>Cuenta</th>
            <th>Nº de Trámites</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($cuentas as $c): ?>
        <tr>
            <td><a href="<?=site_url('manager/claveunica/cuentas/'.$c->id)?>"><?=$c->nombre?></a></td>
            <td><?=$c->ntramites?></td>
        </tr>
        <?php endforeach; ?>

        <tr class="success">
            <td>Total</td>
            <td><?=$ntramites?></td>
        </tr>
    </tbody>
</table>
