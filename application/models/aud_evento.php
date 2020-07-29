<?php

class AudEvento extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('regla');
        $this->hasColumn('instante');
        $this->hasColumn('accion_id');
        $this->hasColumn('tarea_id');
        $this->hasColumn('paso_id');
        $this->hasColumn('instanciar_api');
        $this->hasColumn('traza');
        $this->hasColumn('tipo_registro_traza');
        $this->hasColumn('descripcion_traza');
        $this->hasColumn('etiqueta_traza');
        $this->hasColumn('visible_traza');
        $this->hasColumn('descripcion_error_soap');
        $this->hasColumn('variable_error_soap');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
    }

    function setUp() {
        parent::setUp();
    }

    public static function auditar($obj, $usuario, $operacion) {
        $new = new AudEvento();
        $conn = Doctrine_Manager::connection();
        if (is_numeric($obj->accion_id)) {
            $stmt = $conn->prepare("SELECT id_aud FROM aud_accion WHERE id=$obj->accion_id ORDER BY id_aud DESC;");
            $stmt->execute();
            $accion = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } else {
            $accion[0] = null;
        }
        if (is_numeric($obj->tarea_id)) {
            $stmt = $conn->prepare("SELECT id_aud FROM aud_tarea WHERE id=$obj->tarea_id ORDER BY id_aud DESC;");
            $stmt->execute();
            $tarea = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } else {
            $tarea[0] = null;
        }
        if (is_numeric($obj->paso_id)) {
            $stmt = $conn->prepare("SELECT id_aud FROM aud_paso WHERE id=$obj->paso_id ORDER BY id_aud DESC;");
            $stmt->execute();
            $paso = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } else {
            $paso[0] = null;
        }

        $new->id = $obj->id;
        $new->regla = $obj->regla;
        $new->instante = $obj->instante;
        $new->instanciar_api = $obj->instanciar_api;
        $new->traza = $obj->traza;
        $new->tipo_registro_traza = $obj->tipo_registro_traza;
        $new->descripcion_traza = $obj->descripcion_traza;
        $new->etiqueta_traza = $obj->etiqueta_traza;
        $new->visible_traza = $obj->visible_traza;
        $new->descripcion_error_soap = $obj->descripcion_error_soap;
        $new->variable_error_soap = $obj->variable_error_soap;

        $new->accion_id = $accion[0];
        $new->tarea_id = $tarea[0];
        $new->paso_id = $paso[0];
        $new->usuario_aud = $usuario;
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        $new->save();
        return $new;
    }

}
