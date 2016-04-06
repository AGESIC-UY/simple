<script src="<?= base_url() ?>assets/js/modelador-acciones.js" type="text/javascript"></script>

<ul class="breadcrumb">
    <li><a href="<?=site_url('backend/reportes')?>">Gestión</a> <span class="divider">/</span></li>
    <li><a href="<?=site_url('backend/reportes/reporte_satisfaccion')?>">Reporte de satisfacción</a> <span class="divider">/</span></li>
    <li class="active">Detalle</li>
</ul>
<h2>Reporte de satisfacción</h2>
<div class="well">
    <dl  class="dl-horizontal">
      <dt>Fecha de realización</dt>
      <dd><?=date('d/m/Y', strtotime($detalle->fecha))?></dd>
    </dl>
    <dl  class="dl-horizontal">
        <?php foreach($detalle->reporte as $k => $v) { ?>
            <dt><?=ucfirst(str_replace('_', ' ', $k))?></dt>
            <dd><?=$v?></dd>
        <?php } ?>
    </dl>
</div>

<div class="modal hide fade" id="modal"></div>
