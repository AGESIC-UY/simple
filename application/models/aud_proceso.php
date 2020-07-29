<?php

class AudProceso extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('width');      //ancho de la grilla
        $this->hasColumn('height');     //alto de la grilla
        $this->hasColumn('cuenta_id');
        $this->hasColumn('codigo_tramite_ws_grep');
        $this->hasColumn('instanciar_api');
        $this->hasColumn('activo');
        $this->hasColumn('estado');
        $this->hasColumn('root');
        $this->hasColumn('version');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
        $this->hasColumn('traza');
    }

    function setUp() {
        parent::setUp();
    }
   

    public static function auditar($obj, $usuario, $operacion) {
        $new = new AudProceso();
        $new->id = $obj->id;
        $new->nombre = $obj->nombre;
        $new->width = $obj->width;
        $new->height = $obj->height;
        $new->cuenta_id = $obj->cuenta_id;
        $new->codigo_tramite_ws_grep = $obj->codigo_tramite_ws_grep;
        $new->instanciar_api = $obj->instanciar_api;
        $new->activo = $obj->activo;
        $new->root = $obj->root;
        $new->version = $obj->version;
        $new->estado = $obj->estado;
        $new->usuario_aud = $usuario;
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        
        $traza = Doctrine::getTable("Proceso")->find($obj->id)->ProcesoTrazabilidad;
        $new->traza = json_encode($traza->toArray());
        
        $new->save();

        $acciones = Doctrine::getTable("Proceso")->find($obj->id)->Acciones;
        foreach ($acciones as $accion) {
            AudAccion::auditar($accion, $usuario, $operacion);
        }
        $documentos = Doctrine::getTable("Proceso")->find($obj->id)->Documentos;
        foreach ($documentos as $documento) {
            AudDocumento::auditar($documento, $usuario, $operacion);
        }
        $validaciones = Doctrine::getTable("Proceso")->find($obj->id)->Validaciones;
        foreach ($validaciones as $validacion) {
            AudValidacion::auditar($validacion, $usuario, $operacion);
        }
        if ($obj->nombre != "BLOQUE") {
            $formularios = Doctrine::getTable("Proceso")->find($obj->id)->Formularios;
            foreach ($formularios as $formulario) {
                AudFormulario::auditar($formulario, $usuario, $operacion);
            }
        }
        $tareas = Doctrine::getTable("Proceso")->find($obj->id)->Tareas;
        foreach ($tareas as $tarea) {
            AudTarea::auditar($tarea, $usuario, $operacion);
        }
        return $new;
    }

}
