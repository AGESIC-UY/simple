<?php

class PasarelaPago extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('metodo');
        $this->hasColumn('activo');
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('PasarelaPagoAntel as PasarelaPagoAntel', array(
            'local' => 'id',
            'foreign' => 'pasarela_pago_id'
        ));
    } 
}
