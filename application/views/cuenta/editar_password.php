<form method="POST" class="ajaxForm" action="<?=site_url('cuentas/editar_password_form')?>">
    <fieldset>
        <legend>Edita la información de tu cuenta</legend>
        <div class="validacion validacion-error"></div>
        <label>Contraseña antigua</label>
        <input type="password" name="password_old" value="" />
        <label>Contraseña nueva</label>
        <input type="password" name="password_new" value="" />
        <label>Confirmar contraseña nueva</label>
        <input type="password" name="password_new_confirm" value="" />
        <input type="hidden" name="redirect" value="<?=$redirect?>" />
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Guardar</button>
            <button class="btn" type="button" onclick="javascript:history.back()">Cancelar</button>
        </div>
    </fieldset>
</form>