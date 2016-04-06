<!DOCTYPE html>
<html lang="es">
    <head>
        <?php $this->load->view('head')?>
    </head>

    <body>

        <div class="container">
            <div class="row-fluid" style="margin-top: 100px;">
                <div class="span6 offset3">
                    <form method="post" class="ajaxForm" action="<?= site_url('autenticacion/registrar_form') ?>">
                        <fieldset>
                            <legend>Registrarse en el sistema</legend>
                            <div class="validacion"></div>
                            <label>Nombre de Usuario</label>
                            <input name="usuario" type="text" class="input-xlarge">
                            <label>Nombres</label>
                            <input name="nombres" type="text" class="input-xlarge">
                            <label>Apellido Paterno</label>
                            <input name="apellido_paterno" type="text" class="input-xlarge">
                            <label>Apellido Materno</label>
                            <input name="apellido_materno" type="text" class="input-xlarge">
                            <label>Contraseña</label>
                            <input name="password" type="password" class="input-xlarge">
                            <label>Confirmar contraseña</label>
                            <input name="password_confirm" type="password" class="input-xlarge">
                            <label>Correo electrónico</label>
                            <input type="text" name="email" class="input-xlarge" />
                            <p class="help-block">En este correo recibirá notificaciones sobre el estado de sus trámites.</p>
                            <div class="form-actions">
                                <button class="btn" onclick="history.back()" type="button">Volver</button>
                                <button class="btn btn-primary" type="submit">Ingresar</button>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>


        </div> <!-- /container -->




    </body>
</html>
