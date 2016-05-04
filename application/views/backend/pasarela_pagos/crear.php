<ul class="breadcrumb">
    <li>
        <a href="<?= site_url('backend/pasarela_pagos') ?>">Pasarela de Pagos</a> <span class="divider">/</span>
    </li>
    <li class="active"><?= $pasarela->nombre ?></li>
</ul>

<h1>Nueva pasarela</h1>
<form action="<?=site_url('backend/pasarela_pagos/editar_form/'.$pasarela->id)?>" method="post">
    <input type="text" value="<?= $pasarela->nombre ?>" /><br />

    <label>Método</label>
    <select class="input-xlarge" id="pasarela_metodo" name="metodo">
        <option selected>-- Seleccione el método --</option>
        <option value="antel" <?=($pasarela->metodo == 'antel' ? 'selected' : '')?>>Antel</option>
    </select>
    <br />

    <div id="pasarela_metodo_antel" class="well pasarela_metodo_form <?=($pasarela->metodo == 'antel' ? '' : 'hidden')?>">
        <label>Datos del método</label>
        <br />
        <label>ID de tramite</label>
        <input type="text" name="pasarela_metodo_antel_id_tramite" class="input-xlarge" placeholder="ID de tramite" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->id_tramite : '')?>" /><br />
        <input type="hidden" name="pasarela_metodo_antel_cantidad" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->cantidad : 1)?>" />
        <label>Tasa 1</label>
        <input type="text" name="pasarela_metodo_antel_tasa_1" class="input-xlarge" placeholder="Tasa 1" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->tasa_1 : '')?>" /><br />
        <label>Tasa 2</label>
        <input type="text" name="pasarela_metodo_antel_tasa_2" class="input-xlarge" placeholder="Tasa 2" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->tasa_2 : '')?>" /><br />
        <label>Tasa 3</label>
        <input type="text" name="pasarela_metodo_antel_tasa_3" class="input-xlarge" placeholder="Tasa 3" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->tasa_3 : '')?>" /><br />
        <input type="hidden" name="pasarela_metodo_antel_operacion" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->operacion : 'P')?>" />
        <label>Vencimiento</label>
         <div id="pasarela_pago_vencimiento_muestra"><span><span id="pasarela_pago_vencimiento_muestra_texto">Fecha de vencimiento</span> <a class="btn" id="pasarela_pago_vencimiento_button" href="#"><span class="icon-calendar"></span></a></span></div>
        <input type="hidden" id="pasarela_pago_vencimiento" name="pasarela_metodo_antel_vencimiento" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->vencimiento : '')?>" /><br />
        <label>Códigos de desglose</label>
        <input type="text" name="pasarela_metodo_antel_codigos_desglose" class="input-xlarge" placeholder="Códigos de desglose" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->codigos_desglose : '')?>" /><br />
        <label>Montos de desglose</label>
        <input type="text" name="pasarela_metodo_antel_montos_desglose" class="input-xlarge" placeholder="Montos de desglose" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->montos_desglose : '')?>" />
        <label>Clave de organismo</label>
        <input type="text" name="pasarela_metodo_antel_clave_organismo" class="input-xlarge" placeholder="Clave de organismo" value="<?=($pasarela->metodo == 'antel' ? $pasarela_metodo->clave_organismo : '')?>" />
    </div>

    <input class="btn btn-primary" type="submit" value="Guardar" />
</form>
