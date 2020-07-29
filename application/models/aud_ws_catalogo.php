<?php

class AudWsCatalogo extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('wsdl');
        $this->hasColumn('endpoint_location');
        $this->hasColumn('activo');
        $this->hasColumn('conexion_timeout');
        $this->hasColumn('respuesta_timeout');
        $this->hasColumn('url_fisica');
        $this->hasColumn('url_logica');
        $this->hasColumn('rol');
        $this->hasColumn('tipo');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
        $this->hasColumn('requiere_autenticacion');
        $this->hasColumn('requiere_autenticacion_tipo');
    }

    function setUp() {
        parent::setUp();
    }
    
   
    public static function auditar($obj,$usuario,$operacion) {
        $new = new AudWsCatalogo();        
        $new->id=$obj->id;
        $new->nombre=$obj->nombre;
        $new->wsdl=$obj->wsdl;
        $new->endpoint_location=$obj->endpoint_location;
        $new->activo=$obj->activo;
        $new->conexion_timeout=$obj->conexion_timeout;
        $new->respuesta_timeout=$obj->respuesta_timeout;
        $new->url_fisica=$obj->url_fisica;
        $new->url_logica=$obj->url_logica;
        $new->rol=$obj->rol;
        $new->tipo=$obj->tipo;
        $new->requiere_autenticacion = (isset($obj->requiere_autenticacion) ? $obj->requiere_autenticacion : 0);
        $new->requiere_autenticacion_tipo=$obj->requiere_autenticacion_tipo;        
        $new->usuario_aud=$usuario;
        $new->tipo_operacion_aud=$operacion;
        $new->fecha_aud=  date("Y-m-d H:i:s");
        $new->save();
        return $new;
    }
}
