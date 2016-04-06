<!DOCTYPE html>
<html lang="es">
    <head>
        <?php $this->load->view('head') ?>
    </head>

    <body>

        <div class="container">
            <div class="row-fluid" style="margin-top: 100px;">
                <div class="span6 offset3">
                    <form method="post" class="well ajaxForm" action="<?= site_url('autenticacion/reestablecer_form').'?'.$this->input->server('QUERY_STRING') ?>">
                        <fieldset>
                            <legend>Reestablecer contraseña</legend>
                            <?php $this->load->view('messages') ?>
                            <div class="validacion"></div>

                            <label>Usuario</label>
                            <input type="text" class="input-xlarge" value="<?=$usuario->usuario?>" disabled>

                            <label>Nueva contraseña</label>
                            <input name="password" type="password" class="input-xlarge">

                            <label>Confirmar contraseña</label>
                            <input name="password_confirm" type="password" class="input-xlarge">


                            <div class="form-actions">
                                <a class="btn" href="#" onclick="javascript:history.back();">Volver</a>
                                <button class="btn btn-primary" type="submit">Reestablecer</button>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>


        </div> <!-- /container -->




    </body>
</html>
