<?php

class Monitoreo extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('proceso_id');
        $this->hasColumn('url_web_service');
        $this->hasColumn('fecha');
        $this->hasColumn('tipo');
        $this->hasColumn('rol');
        $this->hasColumn('certificado');
        $this->hasColumn('error_texto');
        $this->hasColumn('error');
        $this->hasColumn('soap_peticion');
        $this->hasColumn('soap_respuesta');
        $this->hasColumn('catalogo_id');
        $this->hasColumn('seguridad');
    }

    function setUp() {
      parent::setUp();
    }

    public static function getListaEjecuciones($tipo) {
        return Doctrine_Query::create()
                        ->from('monitoreo m')
                        ->where('m.tipo = ?', $tipo)
                        ->execute();
    }

    public static function getListaOrdenadaId() {
        return Doctrine_Query::create()
                        ->from('monitoreo m')
                        ->orderBy('id desc')
                        ->execute();
    }
}
