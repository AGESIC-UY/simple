<?php

class Trazabilidad extends Doctrine_Record {

  function setTableDefinition() {
    $this->hasColumn('id');
    $this->hasColumn('id_tramite');
    $this->hasColumn('id_etapa');
    $this->hasColumn('id_tarea');
    $this->hasColumn('num_paso');
    $this->hasColumn('num_paso_real');
    $this->hasColumn('secuencia');
    $this->hasColumn('estado');
    $this->hasColumn('enviar_correo');
  }

  function setUp() {
    parent::setUp();
  }
}
