<!DOCTYPE html>
<html lang="es">
    <head>
        <?php $this->load->view('head')?>
    </head>
    <body>
      <div class="contenedorGeneral">
        <div id="main" tabindex="-1">
          <div class="container">
              <div class="row-fluid">
                  <div class="span12 contenido-publico">
                      <?php $this->load->view($content) ?>
                  </div>
              </div>
          </div>
        </div>
      </div>
    </body>
</html>
