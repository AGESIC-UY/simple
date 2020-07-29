<?php

class AudBloque extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
    }

    function setUp() {
        parent::setUp();
    }

 
    public static function auditar($obj, $usuario, $operacion) {
        $new = new AudBloque();
        $new->id = $obj->id;
        $new->nombre = $obj->nombre;

        $new->usuario_aud = $usuario;
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        $new->save();

        $formulario = Doctrine::getTable("Bloque")->find($obj->id)->Formulario;
        AudFormulario::auditar($formulario, $usuario, $operacion);
        return $new;
    }

}
