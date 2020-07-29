<?php

class AudWsOperacionRespuesta extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('operacion_id');
        $this->hasColumn('respuesta_id');
        $this->hasColumn('xslt');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
    }

    function setUp() {
        parent::setUp();
    }
    
   

    public static function auditar($obj, $usuario, $operacion,$wso_id) {
        $new = new AudWsOperacionRespuesta();
        $new->id = $obj->id;
        $new->operacion_id = $wso_id;
        $new->respuesta_id = $obj->respuesta_id;
        $new->xslt = $obj->xslt;   
        $new->usuario_aud = $usuario;
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        $new->save();
        return $new;
    }

}
