<div class="row-fluid">

    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9"  >
        <ul class="breadcrumb">
            <li>
                <a href="<?= site_url('backend/configuracion') ?>">Configuración</a> <span class="divider">/</span>
            </li>
            <li class="active"> Pasarelas Pagos</li>
        </ul>
        <h2>Monitoreo: Pasarelas Pagos</h2>
        <fieldset>
            <legend>Pasarelas de pagos activas</legend>

            <?php foreach($pasarelas as $pasarela):?>
              <p><?php echo '<strong>'.$pasarela->nombre.':</strong> '.$pasarela->metodo.''; ?></p>
            <?php endforeach; ?>
        </fieldset>

        <div class="row-fluid" id="contenido_monitoreo_pasarela">
          <div class="loader-monitoreo"></div>
          <p style="text-align:center">Verificando estado de pasarelas ...</p>
        </div>

    </div>
</div>
