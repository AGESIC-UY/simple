<?php

class PasarelaPagoAntel extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('pasarela_pago_id');
        $this->hasColumn('id_tramite');
        $this->hasColumn('cantidad');
        $this->hasColumn('tasa_1');
        $this->hasColumn('tasa_2');
        $this->hasColumn('tasa_3');
        $this->hasColumn('operacion');
        $this->hasColumn('vencimiento');
        $this->hasColumn('codigos_desglose');
        $this->hasColumn('montos_desglose');
        $this->hasColumn('clave_organismo');
        $this->hasColumn('clave_tramite');
        $this->hasColumn('certificado');
        $this->hasColumn('clave_certificado');
        $this->hasColumn('pass_clave_certificado');
    }
}
