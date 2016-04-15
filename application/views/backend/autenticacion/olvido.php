<!DOCTYPE html>
<html lang="es">
    <head>
        <?php $this->load->view('head') ?>
    </head>

    <body>

        <div class="container">
            <div class="row-fluid" style="margin-top: 100px;">
                <div class="span6 offset3">
                    <form method="post" class="well ajaxForm" action="<?= site_url('backend/autenticacion/olvido_form') ?>">
                        <fieldset>
                            <legend>¿Olvidaste tu contraseña?</legend>
                            <?php $this->load->view('messages') ?>
                            <div class="validacion validacion-error"></div>

                            <p>Al hacer click en Reestablecer se te enviara un email indicando las instrucciones para reestablecer tu contraseña.</p>

                            <label>E-Mail</label>
                            <input name="email" type="text" class="input-xlarge">


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
