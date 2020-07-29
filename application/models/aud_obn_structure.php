<?php

class AudObnStructure extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('descripcion');
        $this->hasColumn('identificador');
        $this->hasColumn('json');
        $this->hasColumn('id_tabla_interna');
        $this->hasColumn('cuenta_id');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
    }

    function setUp() {
        parent::setUp();
    }

    public static function auditar($obj, $usuario, $operacion) {
        $new = new AudObnStructure();
        $new->id = $obj->id;
        $new->descripcion = $obj->descripcion;
        $new->identificador = $obj->identificador;
        $new->json = json_encode(json_decode($obj->json));
        $new->id_tabla_interna = $obj->id_tabla_interna;
        $new->cuenta_id = $obj->cuenta_id;
        $new->usuario_aud = $usuario;
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        try {
            $new->save();
            $atributos = Doctrine::getTable("ObnStructure")->find($obj->id)->ObnAttributesList;
            foreach ($atributos as $cx) {
                AudObnAttributes::auditar($cx, $usuario, $operacion);
            }

            $query = Doctrine::getTable("ObnStructure")->find($obj->id)->ObnQueriesList;
            foreach ($query as $cx) {
                AudObnQueries::auditar($cx, $usuario, $operacion);
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }

        return $new;
    }

}
