<?php

class WsOperacion extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('codigo');
        $this->hasColumn('nombre');
        $this->hasColumn('operacion');
        $this->hasColumn('catalogo_id');
        $this->hasColumn('soap');
        $this->hasColumn('ayuda');
        $this->hasColumn('respuestas');
    }

    function setUp() {
        parent::setUp();

        $this->hasMany('WsOperacionRespuesta as WsOperacionRespuestas', array(
            'local' => 'id',
            'foreign' => 'operacion_id'
        ));
    }
}
