<script type="text/javascript">
    <?php
        $resultados = array();
        foreach($reportes_chart as $r) {
            if(!empty($r->reporte) && ($r->reporte != "false")) {
                $r->reporte = json_decode($r->reporte);

                switch(date('n', strtotime($r->fecha))) {
                    case '1': $mes = 'Enero'; break;
                    case '2': $mes = 'Febrero'; break;
                    case '3': $mes = 'Marzo'; break;
                    case '4': $mes = 'Abril'; break;
                    case '5': $mes = 'Mayo'; break;
                    case '6': $mes = 'Junio'; break;
                    case '7': $mes = 'Julio'; break;
                    case '8': $mes = 'Agosto'; break;
                    case '9': $mes = 'Setiembre'; break;
                    case '10': $mes = 'Octubre'; break;
                    case '11': $mes = 'Noviembre'; break;
                    case '12': $mes = 'Diciembre'; break;
                }

                $resultados[$mes] = (array_key_exists($mes, $resultados) ? (int)$resultados[$mes] : 0) + (isset($r->reporte->calificacion_general) ? (int)$r->reporte->calificacion_general : 0);
            }
        }

        $labels = array();
        $values = array();
        foreach(array_reverse($resultados) as $key => $val) {
            $labels[] = $key;
            $values[] = $val;
        }
    ?>

    $(document).ready(function(){
      document.labels = <?php echo json_encode($labels); ?>;
      document.values = <?php echo json_encode($values); ?>;
      var data = {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [
            {
                label: "Valores generales",
                fillColor: "#e2e9ef",
                pointColor: "#3F8CD2",
                data: <?php echo json_encode($values); ?>
            }
        ]
      };

      var ctx = $("#reporte_satisfaccion_chart").get(0).getContext("2d");
      new Chart(ctx).Line(data, {maintainAspectRatio: false, responsive: true});
    });
</script>

<ul class="breadcrumb">
  <li>
    <a href="<?=site_url('backend/reportes')?>">Gestión</a> <span class="divider">/</span>
  </li>
  <li class="active">Reporte de satisfacción</li>
</ul>
<h2>Reporte de satisfacción</h2>

<div class="satisfaccion_wrap_chart">
  <canvas id="reporte_satisfaccion_chart"></canvas>
</div>

<table class="table margen-sup">
  <caption class="hide-text">Reportes de satisfacción</caption>
  <thead>
    <tr>
      <th>Fecha de realización</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($reportes as $r): ?>
    <tr>
      <td><?=date('d/m/Y', strtotime($r->fecha))?></td>
      <td class="actions">
          <a href="<?=site_url('backend/reportes/reporte_satisfaccion/'.$r->id)?>" class="btn btn-primary"><span class="icon-eye-open icon-white"></span> Detalle<span class="hidden-accessible"> <?=date('d/m/Y', strtotime($r->fecha))?> <?=$r->id?></span></a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?= $reportes_total[0] ?>
<div class="pagination">
  <ul>
    <?php if($pagina_actual > 1) { ?>
        <li><a href="?p=<?=$pagina_actual-1?>">&laquo; Nuevos</a></li>
    <?php } ?>

    <?php for($i = 1; $i <= ($reportes_total / $tam_pagina); $i++) { ?>
        <li class="<?=($pagina_actual == $i ? 'active' : '')?>"><a href="?p=<?=$i?>"><?=$i?></a></li>
    <?php } ?>

    <?php if(($reportes_total / $tam_pagina) > $pagina_actual) { ?>
        <li><a href="?p=<?=++$pagina_actual?>">Anteriores &raquo;</a></li>
    <?php } ?>
  </ul>
</div>

<div class="modal hide fade" id="modal"></div>
