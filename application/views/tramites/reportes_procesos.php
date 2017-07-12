<h2>Reportes de trámites</h2>

<?php if (count($procesos) > 0): ?>
    <table id="mainTable" class="table">
      <caption class="hide-text">Reportes de trámites</caption>
        <thead>
            <tr>
              <th>Proceso</th>
              <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($procesos as $p): ?>
                <tr>
                  <td class="name" data-title="Nombre"><?= $p->nombre ?></td>
                  <td class="actions" data-title="Acciones">
                    <a href="<?=site_url('tramites/ver_reportes/'.$p->id)?>" class="btn btn-primary"><span class="icon-eye-open icon-white"></span> Ver reportes</span></a>
                  </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
<?php endif; ?>
