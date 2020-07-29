<h2>Generar reportes de trámites</h2>

<?php if (count($tramites) > 0): ?>
    <table id="mainTable" class="table">
      <caption class="hide-text">Generar reportes de trámites</caption>
        <thead>
            <tr>
              <th>Id</th>
              <th>Nombre</th>
              <th>Etapa actual</th>
              <th>Fecha Modificación</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tramites as $t): ?>
                <tr>
                  <td data-title="Id"><?= $t->id ?></td>
                  <td class="name" data-title="Nombre"><?= $t->Proceso->nombre ?></td>
                  <td data-title="Etapa actual">
                      <?php
                      $etapas_array = array();
                      foreach ($t->getEtapasActuales() as $e)
                          $etapas_array[] = $e->Tarea->nombre;
                      echo implode(', ', $etapas_array);
                      ?>
                  </td>
                  <td class="time" data-title="Fecha Modificación"><?= strftime('%d.%b.%Y', mysql_to_unix($t->updated_at)) ?><br /><?= strftime('%H:%M:%S', mysql_to_unix($t->updated_at)) ?></td>
                  <td data-title="Estado"><?= $t->pendiente ? 'Pendiente' : 'Completado' ?></td>
                  <td class="actions" data-title="Acciones">
                    <a href="<?=site_url('tramites/generar_reporte/'.$t->id)?>" class="btn btn-primary"><span class="icon-file icon-white"></span> Generar</span></a>
                  </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No hay reportes creados. Puede generar uno.</p>
<?php endif; ?>
