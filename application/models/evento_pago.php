<?php

class EventoPago extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('regla');
        $this->hasColumn('instante');
        $this->hasColumn('accion_id');
        $this->hasColumn('accion_ejecutar_id');
        $this->hasColumn('traza');
        $this->hasColumn('tipo_registro_traza');
        $this->hasColumn('descripcion_traza');
        $this->hasColumn('descripcion_error_soap');
        $this->hasColumn('variable_error_soap');
        $this->hasColumn('etiqueta_traza');
        $this->hasColumn('visible_traza');
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('Accion', array(
            'local' => 'accion_id',
            'foreign' => 'id'
        ));

        $this->hasOne('Accion as AccionEjecutar', array(
            'local' => 'accion_ejecutar_id',
            'foreign' => 'id'
        ));
    }

}
