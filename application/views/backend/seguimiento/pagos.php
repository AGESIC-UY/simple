<ul class="breadcrumb">
    <li>
        Seguimiento de Pagos
    </li>
</ul>

<h2>Seguimiento de Pagos</h2>

<table class="table">
  <caption class="hide-text">Seguimiento de Pagos</caption>
    <thead>
        <tr>
            <th>ID trámite</th>
            <th>ID solicitud</th>
            <th>Pasarela</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($registros as $p): ?>
        <tr>
            <td><?=$p->id_tramite?></td>
            <td><?=$p->id_solicitud?></td>
            <td><?=$p->pasarela?></td>
            <td><span class="estado badge <?= ($p->estado == 'realizado' ? 'badge-success' : ($p->estado == 'error' ? 'badge-important' : ($p->estado == 'pendiente' ? 'badge-warning' : ($p->estado == 'rechazado' ? 'badge-important' : 'badge-secondary')))) ?>"><?=$p->estado?></span></td>
            <td class="actions">
                <a class="btn btn-primary" href="<?=site_url('backend/seguimiento/ver_pago/'.$p->id)?>"><span class="icon-eye-open icon-white"></span> Ver detalle</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
