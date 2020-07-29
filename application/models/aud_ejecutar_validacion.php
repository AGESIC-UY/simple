<?php

class AudEjecutarValidacion extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('regla');
        $this->hasColumn('instante');
        $this->hasColumn('validacion_id');
        $this->hasColumn('tarea_id');
        $this->hasColumn('paso_id');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
    }

    function setUp() {
        parent::setUp();
    }
    
    public static function auditar($obj, $usuario, $operacion) {
        $new = new AudEjecutarValidacion();
        $conn = Doctrine_Manager::connection();
        if (is_numeric($obj->validacion_id)) {
            $stmt = $conn->prepare("SELECT id_aud FROM aud_validacion WHERE id=$obj->validacion_id ORDER BY id_aud DESC;");
            $stmt->execute();
            $validacion = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } else {
            $validacion[0] = null;
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

        $new->validacion_id = $validacion[0];
        $new->tarea_id = $tarea[0];
        $new->paso_id = $paso[0];
        $new->usuario_aud = $usuario;
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        $new->save();
        return $new;
    }

}
