<?php

class AudConexion extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('tarea_id_origen');
        $this->hasColumn('tarea_id_destino');
        $this->hasColumn('tipo');
        $this->hasColumn('regla');
        $this->hasColumn('estado_fin_trazabilidad');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
    }

    function setUp() {
        parent::setUp();
    }
    

    public static function auditar($obj, $usuario, $operacion) {
        $new = new AudConexion();
        $conn = Doctrine_Manager::connection();
        if (is_numeric($obj->tarea_id_origen)) {
            $stmt = $conn->prepare("SELECT id_aud FROM aud_tarea WHERE id=$obj->tarea_id_origen ORDER BY id_aud DESC;");
            $stmt->execute();
            $tarea_id_origen = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } else {
            $tarea_id_origen[0] = null;
        }
        if (is_numeric($obj->tarea_id_destino)) {
            $stmt = $conn->prepare("SELECT id_aud FROM aud_tarea WHERE id=$obj->tarea_id_destino ORDER BY id_aud DESC;");
            $stmt->execute();
            $tarea_id_destino = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } else {
            $tarea_id_destino[0] = null;
        }

        $new->id = $obj->id;
        $new->tipo = $obj->tipo;
        $new->regla = $obj->regla;
        $new->estado_fin_trazabilidad = $obj->estado_fin_trazabilidad;
        $new->tarea_id_origen = (isset($tarea_id_origen[0])?$tarea_id_origen[0]:"");
        $new->tarea_id_destino = (isset($tarea_id_destino[0])?$tarea_id_destino[0]:"");
        $new->usuario_aud = $usuario;
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        $new->save();
        return $new;
    }

}
