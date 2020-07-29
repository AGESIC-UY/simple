<?php

class AudValidacion extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('contenido');
        $this->hasColumn('proceso_id');
        $this->hasColumn('filename');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
    }

    function setUp() {
        parent::setUp();
    }
    
    

    public static function auditar($obj, $usuario, $operacion) {
        $new = new AudValidacion();
        $conn = Doctrine_Manager::connection();
        if (is_numeric($obj->proceso_id)) {
        $stmt = $conn->prepare("SELECT id_aud FROM aud_proceso WHERE id=$obj->proceso_id ORDER BY id_aud DESC;");
        $stmt->execute();
        $proceso_id = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } else {
            $proceso_id[0] = null;
        }
        
        $new->id = $obj->id;
        $new->nombre = $obj->nombre;
        $new->contenido = $obj->contenido;
        $new->filename = $obj->filename;        
        $new->proceso_id = $proceso_id[0];
        $new->usuario_aud = $usuario;
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        $new->save();       
        return $new;
    }

}
