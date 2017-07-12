<ul class="breadcrumb">
    <li>
        <a href="<?= site_url('tramites/reportes_procesos') ?>">Reportes de trámites</a> <span class="divider">/</span>
    </li>
    <li class="active"><?= $proceso->nombre ?></li>
</ul>

<h2>Ver reportes de <?= $proceso->nombre ?></h2>

<?php if (count($reportes) > 0): ?>
    <table id="mainTable" class="table">
      <caption class="hide-text">Ver reportes de <?= $proceso->nombre ?></caption>
        <thead>
            <tr>
              <th>Reporte</th>
              <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reportes as $r): ?>
                <tr>
                  <td class="name" data-title="Nombre"><?= $r->nombre ?></td>
                  <td class="actions" data-title="Acciones">
                    <a href="<?=site_url('tramites/ver_reporte/'.$r->id)?>" class="btn btn-primary"><span class="icon-eye-open icon-white"></span> Ver</span></a>
                  </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
  <p>No hay reportes disponibles para este proceso.</p>
<?php endif; ?>
