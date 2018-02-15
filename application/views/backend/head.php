<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv='X-UA-Compatible' content='IE=edge' />

<title><?=Cuenta::cuentaSegunDominio()!='localhost'?Cuenta::cuentaSegunDominio()->nombre_largo:'SIMPLE'?> - <?= $title ?></title>

<link href="<?= base_url() ?>assets/css/bootstrap.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/css/responsive.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/css/common.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/js/handsontable/dist/handsontable.full.min.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/js/jquery.chosen/chosen.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/js/jquery.select2/dist/css/select2.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/js/file-uploader/fileuploader.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/css/jquery-ui/jquery-ui.min.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/css/jquery-ui/jquery-ui.structure.min.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/css/jquery-ui/jquery-ui.theme.min.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/css/modelador-formularios.css" rel="stylesheet">
<link href="<?= base_url() ?>assets/css/dashboard.css" rel="stylesheet">
<link href="<?= base_url() ?>assets/css/font-awesome-3.2.1.min.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/css/estilos_extendidos.css?v=1.4" rel="stylesheet" />
<link href="<?= base_url() ?>assets/css/dataTables.css" rel="stylesheet" />
<link href="<?= base_url() ?>assets/css/bootstrap-tour/bootstrap-tour.min.css" rel="stylesheet" />
<link rel="shortcut icon" href="<?= base_url() ?>assets/img/favicon.png" />
<script type="text/javascript">
    var site_url = "<?= site_url() ?>";
    var base_url = "<?= base_url() ?>";
    var denegar_remover_campos_bloques = "<?= DENEGAR_REMOVER_CAMPOS_BLOQUES ?>";
    document.Constants = {
        host: "<?=HOST_SISTEMA?>",
    };
</script>
</script>
<script src="<?= base_url() ?>assets/js/jquery/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/file-uploader/fileuploader.js?v=1.4"></script>
<script src="<?= base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.min.js"></script>
<script src="<?= base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.11.4.js"></script>
<script src="<?= base_url() ?>assets/js/datepicker_accesible.js"></script>
<script src="<?= base_url() ?>assets/js/jquery.dataTable/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/jquery.dataTable/datatables.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/jquery-blockui/jquery.blockUI.js" type="text/javascript"></script>


<!--[if lt IE 9]>
<script src="<?= base_url() ?>assets/js/chartjs/excanvas.js" type="text/javascript"></script>
<![endif]-->
<script src="<?= base_url() ?>assets/js/chartjs/chartjs.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/randomColors/randomColors.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/bootstrap-typeahead-multiple/bootstrap-typeahead-multiple.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/handsontable/dist/handsontable.full.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/jquery.chosen/chosen.jquery.min.js"></script>
<script src="<?= base_url() ?>assets/js/jquery.select2/dist/js/select2.min.js"></script>
<script src="<?= base_url() ?>assets/js/jquery.select2/dist/js/i18n/es.js"></script>
<script src="<?= base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/jquery.ui.touch-punch/jquery.ui.touch-punch.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/jquery.ui.livedraggable/jquery.ui.livedraggable.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/jquery.doubletap/jquery.doubletap.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/json-js/json2.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/jquery.base64/jquery.base64.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/bootstrap-tour/bootstrap-tour.min.js" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/common.js?v=1.4" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/backend.js?v=1.4" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/backend_extendido.js?v=1.4" type="text/javascript"></script>
<script src="<?= base_url() ?>assets/js/backend_ayuda_contextual.js?v=1.4" type="text/javascript"></script>
