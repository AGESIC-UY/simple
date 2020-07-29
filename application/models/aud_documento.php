<?php

class AudDocumento extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('tipo');
        $this->hasColumn('nombre');
        $this->hasColumn('titulo');
        $this->hasColumn('subtitulo');
        $this->hasColumn('contenido');
        $this->hasColumn('servicio');
        $this->hasColumn('servicio_url');
        $this->hasColumn('validez');
        $this->hasColumn('validez_habiles');
        $this->hasColumn('firmador_nombre');
        $this->hasColumn('firmador_cargo');
        $this->hasColumn('firmador_servicio');
        $this->hasColumn('firmador_imagen');
        $this->hasColumn('proceso_id');
        $this->hasColumn('timbre');
        $this->hasColumn('logo');
        $this->hasColumn('hsm_configuracion_id');
        $this->hasColumn('tamano');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
        $this->hasColumn('imagenes');
    }

    function setUp() {
        parent::setUp();
    }
    
    public static function auditar($obj, $usuario, $operacion) {
        $new = new AudDocumento();
        $conn = Doctrine_Manager::connection();
        if (is_numeric($obj->proceso_id)) {
            $stmt = $conn->prepare("SELECT id_aud FROM aud_proceso WHERE id=$obj->proceso_id ORDER BY id_aud DESC;");
            $stmt->execute();
            $proceso = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        } else {
            $proceso[0] = null;
        }
        $new->id = $obj->id;
        $new->tipo = $obj->tipo;
        $new->nombre = $obj->nombre;
        $new->titulo = $obj->titulo;
        $new->subtitulo = $obj->subtitulo;
        $new->contenido = $obj->contenido;
        $new->servicio = $obj->servicio;
        $new->servicio_url = $obj->servicio_url;
        $new->validez = $obj->validez;
        $new->validez_habiles = $obj->validez_habiles;
        $new->firmador_nombre = $obj->firmador_nombre;
        $new->firmador_cargo = $obj->firmador_cargo;
        $new->firmador_servicio = $obj->firmador_servicio;
        $new->firmador_imagen = $obj->firmador_imagen;
        $new->timbre = $obj->timbre;
        $new->imagenes = $obj->imagenes;
        $new->logo = $obj->logo;
        $new->hsm_configuracion_id = $obj->hsm_configuracion_id;
        $new->tamano = $obj->tamano;

        $new->proceso_id = $proceso[0];
        $new->usuario_aud = $usuario;
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        $new->save();
        return $new;
    }

}
