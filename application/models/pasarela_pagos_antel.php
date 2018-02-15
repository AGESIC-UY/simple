<?php

class PasarelaPagoAntel extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('pasarela_pago_id');
        $this->hasColumn('id_tramite');
        $this->hasColumn('id_organismo');
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
        $this->hasColumn('tema_email_inicio');
        $this->hasColumn('cuerpo_email_inicio');
        $this->hasColumn('tema_email_pendiente');
        $this->hasColumn('cuerpo_email_pendiente');
        $this->hasColumn('tema_email_ok');
        $this->hasColumn('cuerpo_email_ok');
        $this->hasColumn('tema_email_timeout');
        $this->hasColumn('cuerpo_email_timeout');
    }
}
