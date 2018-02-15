<meta charset="utf-8" />
<title>
  <? if(isset($paso)) { ?>
    <?=Cuenta::cuentaSegunDominio()!='localhost'?Cuenta::cuentaSegunDominio()->nombre_largo:'SIMPLE'?> - <?=$paso->Formulario->Proceso->nombre?>
  <? }
  else { ?>
    <?=Cuenta::cuentaSegunDominio()!='localhost'?Cuenta::cuentaSegunDominio()->nombre_largo:'SIMPLE'?> - <?= $title ?>
  <? } ?>
</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv='X-UA-Compatible' content='IE=edge' />
<!-- meta http-equiv="Pragma" content="no-cache" / -->
<!-- meta http-equiv="Expires" content="-1" / -->

<link href="<?= base_url() ?>assets/css/bootstrap.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/css/responsive.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/js/handsontable/dist/handsontable.full.min.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/js/jquery.chosen/chosen.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/js/file-uploader/fileuploader.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/css/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/css/jquery-ui/jquery-ui.structure.min.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/css/jquery-ui/jquery-ui.theme.min.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/css/common.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/css/font-awesome-3.2.1.min.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/css/dataTables.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/css/estilos_extendidos.css?v=1.3" rel="stylesheet" />
<link rel="shortcut icon" href="<?= base_url() ?>assets/img/favicon.png" />
<script src="<?= base_url() ?>assets/js/jquery/jquery-1.8.3.min.js"></script>
<script src="<?= base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.11.4.js"></script>
<script src="<?= base_url() ?>assets/js/datepicker_accesible.js"></script>
<script src="<?= base_url() ?>assets/js/bootstrap.min.js"></script>
<script src="<?= base_url() ?>assets/js/handsontable/dist/handsontable.full.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/jquery.chosen/chosen.jquery.min.js"></script>
<script src="<?= base_url() ?>assets/js/file-uploader/fileuploader.js?v=1.3"></script>
<script src="<?= base_url() ?>assets/js/jquery.base64/jquery.base64.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/jquery.serializeObject/jquery.serializeObject.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/jquery.scrollTo/jquery.scrollTo.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/jquery.dataTable/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/jquery.dataTable/datatables.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/multifilter/multifilter.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/jquery-blockui/jquery.blockUI.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/jquery.bind-first/jquery.bind-first-0.2.3.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/proj4js/proj4.js" type="text/javascript"></script>

<script type="text/javascript">
    var site_url="<?= site_url() ?>";
    var base_url="<?= base_url() ?>";
</script>
<script type="text/javascript">
  document.Constants = {
    host: "<?=HOST_SISTEMA?>",
    debug: JSON.parse("<?=(empty(MODO_DEBUG) ? 'false' : 'true')?>")
  };

  //compatibilidad con internet explorer
  Number.isInteger = Number.isInteger || function(value) {
    return typeof value === "number" &&
           isFinite(value) &&
           Math.floor(value) === value;
  };

  $(document).ready(function() {
    // -- Variables para consumir desde componente Analytics.
    document.Analytics = {};
    if($('#info_tramite_id_tramite').length) {
      document.Analytics.info_tramite_id_tramite = $('#info_tramite_id_tramite').val();
    }
    if($('#info_tramite_id_interaccion').length) {
      document.Analytics.info_tramite_id_interaccion = $('#info_tramite_id_interaccion').val();
    }
    if($('#info_tramite_nro_paso').length) {
      document.Analytics.info_tramite_nro_paso = $('#info_tramite_nro_paso').val();
    }
  });
</script>
<script src="<?= base_url() ?>assets/js/common.js?v=1.3"></script>
<script src="<?= base_url() ?>assets/js/frontend_extendido.js?v=1.3"></script>

<script src="<?= base_url() ?>assets/js/ol-3.5.js?v=1.3" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/calendario-sae.js?v=1.3" type="text/javascript"></script>

<link rel="stylesheet" href="<?= base_url() ?>assets/css/ol-3.5.css?v=1.3" type="text/css">
<link rel="stylesheet" href="<?= base_url() ?>assets/css/calendario-sae.css?v=1.3" type="text/css">
