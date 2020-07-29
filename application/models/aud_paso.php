<?php

class AudPaso extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('orden');
        $this->hasColumn('modo');
        $this->hasColumn('regla');
        $this->hasColumn('nombre');
        $this->hasColumn('formulario_id');
        $this->hasColumn('tarea_id');
        $this->hasColumn('generar_pdf');
        $this->hasColumn('enviar_traza');
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
        $new = new AudPaso();
        $conn = Doctrine_Manager::connection();
        if (is_numeric($obj->formulario_id)) {
            $stmt = $conn->prepare("SELECT id_aud FROM aud_formulario WHERE id=$obj->formulario_id ORDER BY id_aud DESC;");
            $stmt->execute();
            $formulario = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } else {
            $formulario[0] = null;
        }
        if (is_numeric($obj->tarea_id)) {
            $stmt = $conn->prepare("SELECT id_aud FROM aud_tarea WHERE id=$obj->tarea_id ORDER BY id_aud DESC;");
            $stmt->execute();
            $tarea = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } else {
            $tarea[0] = null;
        }

        $new->id = $obj->id;
        $new->orden = $obj->orden;
        $new->modo = $obj->modo;
        $new->regla = $obj->regla;
        $new->nombre = $obj->nombre;
        $new->generar_pdf = $obj->generar_pdf;
        $new->enviar_traza = $obj->enviar_traza;
        $new->etiqueta_traza = $obj->etiqueta_traza;
        $new->visible_traza = $obj->visible_traza;

        $new->formulario_id = $formulario[0];
        $new->tarea_id = $tarea[0];
        $new->usuario_aud = $usuario;
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        $new->save();
        return $new;
    }

}
