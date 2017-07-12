<ul class="breadcrumb">
  <li>Administración <span class="divider">/</span></li>
  <li class="active"><?=$title?></li>
</ul>

<h2><?=$title?></h2>

<?php $output = shell_exec('crontab -l'); ?>

<?php
$pagos = 'No Configurado';
$configura_pagos = '0 2 * * * php DIRECTORIO_SIMPLE/index.php tasks/pagos conciliacion > RUTA_LOG/nombre.log';
foreach(preg_split("/((\r?\n)|(\r\n?))/", $output) as $line){
    //si tiene el plgun de pagos configurado
    if (strpos($line, 'pagos conciliacion')){
       $pagos = 'Configurado';
       $configura_pagos = $line;
    }

}

 ?>

<table class="table">
  <caption class="hide-text">Cuentas</caption>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>Estado</th>
            <th>Configuración</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <tr>
          <form method="POST" action="<?=site_url('manager/plugins/configurar_pago')?>">
            <td>Tarea de Pago (conciliacion)</td>
            <td><?=posix_getpwuid(posix_geteuid())['name']?></td>
            <td><?=$pagos ?></td>
            <?php if ($pagos == 'Configurado') { ?>
              <td><?=$configura_pagos ?></td>
            <?php } ?>
            <?php if ($pagos == 'No Configurado') { ?>
              <td><input  name="configurar_pago" id="configurar_pago" type='text' value='<?=$configura_pagos ?>'></input></td>
            <?php } ?>
            <td class="actions">
              <?php if ($pagos == 'Configurado') { ?>
                <input style='display:none' name="configurar_pago_tipo" id="configurar_pago_tipo" type='text' value='1'></input>
                <button class="btn btn-primary btn-lg" type="submit">Eliminar</button>
              <?php } ?>
              <?php if ($pagos == 'No Configurado') { ?>
                <input style='display:none' name="configurar_pago_tipo" id="configurar_pago_tipo" type='text' value='2'></input>
                <button class="btn btn-primary btn-lg" type="submit">Configurar</button>
              <?php } ?>

            </td>
          </form>
        </tr>
    </tbody>
</table>
