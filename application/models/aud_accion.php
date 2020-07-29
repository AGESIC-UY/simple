<?php

class AudAccion extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('tipo');
        $this->hasColumn('extra');
        $this->hasColumn('proceso_id');
        $this->hasColumn('exponer_variable');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
    }

    function setUp() {
        parent::setUp();
    }
    
    public static function auditar($obj,$usuario,$operacion) {
        $new = new AudAccion();
        $conn = Doctrine_Manager::connection();
        if (is_numeric($obj->proceso_id)) {
        $stmt = $conn->prepare("SELECT id_aud FROM aud_proceso WHERE id=$obj->proceso_id ORDER BY id_aud DESC;");
        $stmt->execute();
        $proceso = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);     
        } else {
            $proceso[0] = null;
        }
        $new->id=$obj->id;
        $new->nombre=$obj->nombre;
        $new->tipo=$obj->tipo;
        $new->extra=  json_encode($obj->extra);
        $new->exponer_variable=$obj->exponer_variable;
        $new->proceso_id=$proceso[0];
        $new->usuario_aud=$usuario;
        $new->tipo_operacion_aud=$operacion;
        $new->fecha_aud=date("Y-m-d H:i:s");
        $new->save();
        return $new;
    }
}
