<?php

class PasarelaPago extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('metodo');
        $this->hasColumn('activo');
        $this->hasColumn('cuenta_id');
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('PasarelaPagoAntel as PasarelaPagoAntel', array(
            'local' => 'id',
            'foreign' => 'pasarela_pago_id'
        ));

        $this->hasOne('PasarelaPagoGenerica as PasarelaPagoGenerica', array(
            'local' => 'id',
            'foreign' => 'pasarela_pago_id'
        ));
    }
}
