<!DOCTYPE html>
<html lang="es">
    <head>
        <?php $this->load->view('head')?>
    </head>
    <body>
      <div class="contenedorGeneral">
        <header class="header-publico">
            <div class="container">
                <div class="row-fluid">
                    <div class="span5">
                        <div id="logo">
                          <a href="<?= site_url() ?>">
                            <img src="<?= Cuenta::cuentaSegunDominio()!='localhost' ? Cuenta::cuentaSegunDominio()->logoADesplegar : base_url('assets/img/logo.svg') ?>" alt="<?= Cuenta::cuentaSegunDominio()!='localhost' ? Cuenta::cuentaSegunDominio()->nombre_largo : 'Simple' ?>" />
                          </a>
                          <span class="nombre-app"><?= Cuenta::cuentaSegunDominio()!='localhost' ? Cuenta::cuentaSegunDominio()->nombre_largo : '' ?></span>
                          <!-- p><?= Cuenta::cuentaSegunDominio()!='localhost' ? Cuenta::cuentaSegunDominio()->mensaje : '' ?></p -->
                        </div>
                    </div>
                    <div class="span7">
                      <div class="logosSecundarios">
                        <ul class="listaHorizontal">
                          <li>
                            <a href="https://www.presidencia.gub.uy/" title="Ir al sitio de Presidencia">
                              <img src="<?= base_url() ?>assets/img/logoPresidencia.png" alt="Presidencia">
                            </a>
                          </li>
                        </ul>
                      </div>
                    </div>
                </div>
            </div>
        </header>
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
        <footer>
            <?php $this->load->view('foot') ?>
        </footer>
    </body>
</html>
