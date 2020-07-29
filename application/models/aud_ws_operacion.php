<?php

class AudWsOperacion extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('codigo');
        $this->hasColumn('nombre');
        $this->hasColumn('operacion');
        $this->hasColumn('catalogo_id');
        $this->hasColumn('soap');
        $this->hasColumn('ayuda');
        $this->hasColumn('respuestas');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
    }

    function setUp() {
        parent::setUp();
    }
    
   
    public static function auditar($obj, $usuario, $operacion) {
        $conn = Doctrine_Manager::connection();
        if (is_numeric($obj->catalogo_id)) {
            $stmt = $conn->prepare("SELECT id_aud FROM aud_ws_catalogo WHERE id=$obj->catalogo_id ORDER BY id_aud DESC;");
            $stmt->execute();
            $catalogo = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } else {
            $catalogo[0] = null;
        }
        $catalogo_id = $catalogo[0];
        $new = new AudWsOperacion();
        $new->id = $obj->id;
        $new->codigo = $obj->codigo;
        $new->nombre = $obj->nombre;
        $new->operacion = $obj->operacion;
        $new->catalogo_id = $catalogo_id;
        $new->soap = $obj->soap;
        $new->ayuda = $obj->ayuda;
        $new->respuestas = $obj->respuestas;

        $new->usuario_aud = $usuario;
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        $new->save();

        $conn = Doctrine_Manager::connection();
        $stmt = $conn->prepare("SELECT id_aud, respuestas FROM aud_ws_operacion WHERE id=$obj->id ORDER BY id_aud DESC;");
        $stmt->execute();
        $op = $stmt->fetchAll();
        $op_id = $op[0][0];
        $op_respuesta = json_decode($op[0][1]);
        $resp = $op_respuesta->respuestas;
        foreach ($resp as $value) {

            if ($value->tipo) {
                $obj_or = Doctrine::getTable("WsOperacionRespuesta")->findOneByRespuestaId($value->id);
                if ($obj_or)
                    AudWsOperacionRespuesta::auditar($obj_or, $usuario, $operacion, $op_id);
            }
        }
        return $new;
    }

}
