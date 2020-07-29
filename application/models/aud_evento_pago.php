<?php

class AudEventoPago extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('regla');
        $this->hasColumn('instante');
        $this->hasColumn('accion_id');
        $this->hasColumn('accion_ejecutar_id');
        $this->hasColumn('traza');
        $this->hasColumn('tipo_registro_traza');
        $this->hasColumn('descripcion_traza');
        $this->hasColumn('descripcion_error_soap');
        $this->hasColumn('variable_error_soap');
        $this->hasColumn('etiqueta_traza');
        $this->hasColumn('visible_traza');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
    }

    function setUp() {
        parent::setUp();
    }
    
 

    public static function auditar($obj, $usuario, $operacion) {
        $new = new AudEventoPago();
        $conn = Doctrine_Manager::connection();
        if (is_numeric($obj->accion_id)) {
            $stmt = $conn->prepare("SELECT id_aud FROM aud_accion WHERE id=$obj->accion_id ORDER BY id_aud DESC;");
            $stmt->execute();
            $accion_id = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } else {
            $accion_id[0] = null;
        }
        if (is_numeric($obj->accion_ejecutar_id)) {
            $stmt = $conn->prepare("SELECT id_aud FROM aud_accion WHERE id=$obj->accion_ejecutar_id ORDER BY id_aud DESC;");
            $stmt->execute();
            $accion_ejecutar_id = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } else {
            $accion_ejecutar_id[0] = null;
        }

        $new->id = $obj->id;
        $new->regla = $obj->regla;
        $new->instante = $obj->instante;
        $new->traza = $obj->traza;
        $new->tipo_registro_traza = $obj->tipo_registro_traza;
        $new->descripcion_traza = $obj->descripcion_traza;
        $new->descripcion_error_soap = $obj->descripcion_error_soap;
        $new->descripcion_error_soap = $obj->descripcion_error_soap;
        $new->variable_error_soap = $obj->variable_error_soap;
        $new->etiqueta_traza = $obj->etiqueta_traza;
        $new->visible_traza = $obj->visible_traza;
        $new->accion_id = $accion_id[0];
        $new->accion_ejecutar_id = $accion_ejecutar_id[0];
        $new->usuario_aud = $usuario;
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        $new->save();
        return $new;
    }

}
