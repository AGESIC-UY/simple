<?php

class AudFormulario extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('proceso_id');
        $this->hasColumn('bloque_id');
        $this->hasColumn('leyenda');
        $this->hasColumn('contenedor');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
        $this->hasColumn('tipo');
    }

    function setUp() {
        parent::setUp();
    }


    public static function auditar($obj, $usuario, $operacion) {
        $new = new AudFormulario();
        $conn = Doctrine_Manager::connection();
        if (is_numeric($obj->proceso_id)) {
        $stmt = $conn->prepare("SELECT id_aud FROM aud_proceso WHERE id=$obj->proceso_id ORDER BY id_aud DESC;");
        $stmt->execute();
        $proceso_id = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } else {
            $proceso_id[0] = null;
        }
        if (is_numeric($obj->bloque_id)) {
        $stmt = $conn->prepare("SELECT id_aud FROM aud_bloque WHERE id=$obj->bloque_id ORDER BY id_aud DESC;");
        $stmt->execute();
        $bloque_id = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } else {
            $bloque_id[0] = null;
        }
        $new->id = $obj->id;
        $new->nombre = $obj->nombre;
        $new->leyenda = $obj->leyenda;
        $new->tipo = $obj->tipo;
        $new->contenedor = $obj->contenedor;
        $new->proceso_id = $proceso_id[0];
        $new->bloque_id = $bloque_id[0];
        $new->usuario_aud = $usuario;
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        $new->save();
        $campos = Doctrine::getTable("Formulario")->find($obj->id)->Campos;
        foreach ($campos as $campo) {
            AudCampo::auditar($campo,$usuario,$operacion);
        }
        return $new;
    }

}
