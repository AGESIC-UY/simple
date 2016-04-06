<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Validador de Documentos</title>
        <link href="<?= base_url() ?>assets/css/bootstrap.css" rel="stylesheet">
        <link href="<?= base_url() ?>assets/css/bootstrap-responsive.css" rel="stylesheet">
    </head>

    <body>
        <div class="container">
            <div class="row-fluid" style="margin-top: 50px;">
                <div class="span4 offset3">
                    <div class="well">
                        <form method="POST" action="<?=site_url('validador/documento')?>" autocomplete="off">
                            <legend>Valide su documento</legend>
                            <div class="validacion"><?=  validation_errors()?></div>
                            <label>Folio</label>
                            <input class="span3" type="text" name="id" value="<?=set_value('id')?>" />
                            <label>Código de verificación</label>
                            <input class="span3" type="text" name="key" value="<?=set_value('key')?>" />
                            <div>
                                <button type="submit" class="btn btn-primary pull-right">Validar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </div> <!-- /container -->

    </body>
</html>
