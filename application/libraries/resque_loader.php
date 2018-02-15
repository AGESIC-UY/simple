<?php

//ejecutar en /vendors/fresque --> ./fresque restart -q default
require __DIR__ . '/trazabilidad/trazabilidad_pasos_linea.php';
require __DIR__ . '/trazabilidad/traza_primer_cabezal_linea.php';
require __DIR__ . '/trazabilidad/traza_agenda_linea.php';

class resque_loader {
  public function __construct() {
  }
}
