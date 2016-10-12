<?php

class Pagos extends CI_Controller {

  public function __construct() {
    parent::__construct();

    if(!$this->input->is_cli_request()) {
      redirect(site_url());
    }
  }

  public function conciliacion() {
    $etapas_pendientes = Doctrine_Query::create()
        ->from('Etapa e')
        ->where('e.pendiente = ?', 1)
        ->execute();

    if(count($etapas_pendientes) > 0) {
      echo 'Iniciando proceso de conciliacion de pagos, aguarde por favor...' . PHP_EOL;

      foreach($etapas_pendientes as $etapa) {
        foreach($etapa->Tarea->Pasos as $paso) {
          foreach($paso->Formulario->Campos as $campo) {
            if($campo->tipo == 'pagos') {
              $etapa->cerrar();
            }
          }
        }
      }
    }
    else {
      echo 'No se han encontrado etapas pendientes.' . PHP_EOL;
    }
  }
}
