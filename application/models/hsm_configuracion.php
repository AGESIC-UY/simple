<?php

class HsmConfiguracion extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('cuenta_id');
    }

    function setUp() {
        parent::setUp();
        
        $this->hasOne('Cuenta', array(
            'local' => 'cuenta_id',
            'foreign' => 'id'
        ));

        $this->hasMany('Documento as Documentos', array(
            'local' => 'id',
            'foreign' => 'hsm_configuracion_id'
        ));
    }

}
