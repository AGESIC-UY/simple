<?php

class AudReporte extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('campos');
        $this->hasColumn('tipo');
        $this->hasColumn('proceso_id');
        $this->hasColumn('grupos_usuarios_permiso');
        $this->hasColumn('usuarios_permiso');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
    }

    function setUp() {
        parent::setUp();
    }
    

    public static function auditar($obj, $usuario, $operacion) {        
        if (is_array($obj->campos))
            $campo = json_encode($obj->campos,true);
        else
            $campo = '';
        
        $new = new AudReporte();
        $new->id = $obj->id;
        $new->nombre = $obj->nombre;
        $new->campos = $campo;
        $new->tipo = $obj->tipo;
        $new->proceso_id = $obj->proceso_id;
        $new->grupos_usuarios_permiso = $obj->grupos_usuarios_permiso;
        $new->usuarios_permiso = $obj->usuarios_permiso;
        $new->usuario_aud = $usuario;
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        $new->save();
        return $new;
    }

}
