<?php

class AudObnQueries extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('id_obn');
        $this->hasColumn('nombre');
        $this->hasColumn('tipo');
        $this->hasColumn('consulta');
        $this->hasColumn('consulta_sql');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
    }

    function setUp() {
        parent::setUp();
    }
    
     public static function auditar($obj, $usuario, $operacion) {
        $new = new AudObnQueries();
        $conn = Doctrine_Manager::connection();
        if (is_numeric($obj->id_obn)) {
            $stmt = $conn->prepare("SELECT id_aud FROM aud_obn_structure WHERE id=$obj->id_obn ORDER BY id_aud DESC;");
            $stmt->execute();
            $obn = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } else {
            $obn[0] = null;
        }
        $new->id = $obj->id;
        $new->id_obn = $obn[0];
        $new->nombre = $obj->nombre;
        $new->tipo = $obj->tipo;
        $new->consulta = $obj->consulta;
        $new->consulta_sql = $obj->consulta_sql;
        $new->usuario_aud = $usuario;
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        $new->save();
        return $new;
    }

}
