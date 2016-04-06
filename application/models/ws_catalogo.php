<?php

class WsCatalogo extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('wsdl');
        $this->hasColumn('endpoint_location');
        $this->hasColumn('activo');
        $this->hasColumn('conexion_timeout');
        $this->hasColumn('respuesta_timeout');
    }

    function setUp() {
        parent::setUp();

        $this->hasMany('WsOperacion as WsOperaciones', array(
            'local' => 'id',
            'foreign' => 'catalogo_id'
        ));
    } 
}
