<?php

class AudUsuarioBackend extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('usuario');
        $this->hasColumn('email');
        $this->hasColumn('nombre');
        $this->hasColumn('apellidos');
        $this->hasColumn('rol');
        $this->hasColumn('cuenta_id');
        $this->hasColumn('seg_alc_control_total');
        $this->hasColumn('seg_alc_grupos_usuarios');
        $this->hasColumn('seg_reasginar');
        $this->hasColumn('seg_reasginar_usu');
        $this->hasColumn('seg_alc_auditor');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
    }

    function setUp() {
        parent::setUp();
    }
  

    public static function auditar($obj,$usuario,$operacion) {
        $new = new AudUsuarioBackend();    
        $new->id=$obj->id;
        $new->usuario=$obj->usuario;
        $new->nombre=$obj->nombre;
        $new->apellidos=$obj->apellidos;
        $new->email=$obj->email;
        $new->rol=$obj->rol;
        $new->cuenta_id=$obj->cuenta_id;
        $new->seg_alc_control_total=$obj->seg_alc_control_total;
        $new->seg_alc_grupos_usuarios=implode("|",$obj->seg_alc_grupos_usuarios);
        $new->seg_reasginar=$obj->seg_reasginar;
        $new->seg_reasginar_usu=$obj->seg_reasginar_usu;
        $new->seg_alc_auditor=$obj->seg_alc_auditor;
        $new->usuario_aud=$usuario;
        $new->tipo_operacion_aud=$operacion;
        $new->fecha_aud=  date("Y-m-d H:i:s");
        $new->save();
        return $new;
    }

}
