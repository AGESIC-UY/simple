<?php

class AudPasarelaPagoAntel extends Doctrine_Record {

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
        $this->hasColumn('certificado');
        $this->hasColumn('clave_certificado');
        $this->hasColumn('tema_email_inicio');
        $this->hasColumn('cuerpo_email_inicio');
        $this->hasColumn('tema_email_pendiente');
        $this->hasColumn('cuerpo_email_pendiente');
        $this->hasColumn('tema_email_ok');
        $this->hasColumn('cuerpo_email_ok');
        $this->hasColumn('tema_email_timeout');
        $this->hasColumn('cuerpo_email_timeout');
        $this->hasColumn('descripcion_pendiente_traza');
        $this->hasColumn('descripcion_iniciado_traza');
        $this->hasColumn('descripcion_token_solicita_traza');
        $this->hasColumn('descripcion_realizado_traza');
        $this->hasColumn('descripcion_error_traza');
        $this->hasColumn('descripcion_reachazado_traza');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
        $this->hasColumn('referencia_pago');
    }

    function setUp() {
        parent::setUp();
    }

    public static function auditar($obj, $usuario, $operacion,$pasarela_id) {        
        $new = new AudPasarelaPagoAntel();
        $new->id = $obj->id;
        $new->pasarela_pago_id = $pasarela_id;
        $new->id_tramite = $obj->id_tramite;
        $new->id_organismo = $obj->id_organismo;
        $new->cantidad = $obj->cantidad;
        $new->tasa_1 = $obj->tasa_1;
        $new->tasa_2 = $obj->tasa_2;
        $new->tasa_3 = $obj->tasa_3;
        $new->operacion = $obj->operacion;
        $new->vencimiento = $obj->vencimiento;
        $new->codigos_desglose = $obj->codigos_desglose;
        $new->montos_desglose = $obj->montos_desglose;
        $new->certificado = $obj->certificado;
        $new->clave_certificado = $obj->clave_certificado;
        $new->tema_email_inicio = $obj->tema_email_inicio;
        $new->cuerpo_email_inicio = $obj->cuerpo_email_inicio;
        $new->cuerpo_email_pendiente = $obj->cuerpo_email_pendiente;
        $new->tema_email_ok = $obj->tema_email_ok;
        $new->cuerpo_email_ok = $obj->cuerpo_email_ok;
        $new->tema_email_timeout = $obj->tema_email_timeout;
        $new->cuerpo_email_timeout = $obj->cuerpo_email_timeout;
        $new->descripcion_pendiente_traza = $obj->descripcion_pendiente_traza;
        $new->descripcion_iniciado_traza = $obj->descripcion_iniciado_traza;
        $new->descripcion_token_solicita_traza = $obj->descripcion_token_solicita_traza;
        $new->descripcion_realizado_traza = $obj->descripcion_realizado_traza;
        $new->descripcion_error_traza = $obj->descripcion_error_traza;
        $new->descripcion_reachazado_traza = $obj->descripcion_reachazado_traza;
        $new->referencia_pago = $obj->referencia_pago;
        $new->usuario_aud = $usuario;
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        try {
            $new->save();
        } catch (Exception $ex) {
           
        }
        
       
        return $new;
    }

}
