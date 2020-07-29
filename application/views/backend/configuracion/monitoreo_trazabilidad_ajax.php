<div class="row-fluid">

    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9">
        <ul class="breadcrumb">
            <li>
                <a href="<?= site_url('backend/configuracion') ?>">Configuración</a> <span class="divider">/</span>
            </li>
            <li class="active">Estado Trazabilidad</li>
        </ul>
        <h2>Monitoreo: Estado Trazabilidad</h2>

            <fieldset>
              <legend>Configuración general</legend>

              <p><strong>URL servicio cabezal:</strong> <?php echo WS_AGESIC_TRAZABLIDAD_CABEZAL; ?></p>
              <p><strong>URL servicio línea:</strong> <?php echo WS_AGESIC_TRAZABLIDAD_LINEA; ?></p>
              <p><strong>Versión modelo de trazabilidad:</strong> <?php echo WS_VERSION_MODELO_TRAZABILIDAD; ?></p>
              <p><strong>XPath GUID:</strong> <?php echo WS_XPATH_COD_TRAZABILIDAD; ?></p>
              <p><strong>Variable almacena GUID:</strong> <?php echo '@@'.WS_VARIABLE_COD_TRAZABILIDAD; ?></p>
            </fieldset>

            <div id="contenido_monitoreo_trazabilidad">
              <div class="loader-monitoreo"></div>
              <p style="text-align:center">Verificando estado de trazabilidad ... </p>
            </div>
    </div>
</div>
