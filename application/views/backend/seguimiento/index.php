<ul class="breadcrumb">
    <li>
        Seguimiento de Procesos
    </li>
</ul>
<h2>Seguimiento de Procesos</h2>

<table class="table">
  <caption class="hide-text">Seguimiento de Procesos</caption>
    <thead>
        <tr>
            <th>Proceso</th>
            <th>Estado</th>
            <th>Version</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($procesos as $p): ?>
        <tr>
            <td><?=$p->nombre?></td>
            <td><?=$p->estado == 'public' ? 'Publicado' : ($p->estado == 'arch' ? 'Archivado' : 'Borrador')?></td>
            <td><?=$p->version?></td>
            <td class="actions">
                <a class="btn btn-primary" href="<?=site_url('backend/seguimiento/index_proceso/'.$p->id)?>"><span class="icon-eye-open icon-white"></span> Ver seguimiento<span class="hide-text"> de <?=$p->nombre?> <?= $p->id ?></span></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>