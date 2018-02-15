<?php

class WsCatalogo extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('wsdl');
        $this->hasColumn('endpoint_location');
        $this->hasColumn('activo');
        $this->hasColumn('conexion_timeout');
        $this->hasColumn('respuesta_timeout');
        $this->hasColumn('url_fisica');
        $this->hasColumn('url_logica');
        $this->hasColumn('rol');
        $this->hasColumn('tipo');
        $this->hasColumn('requiere_autenticacion');
        $this->hasColumn('requiere_autenticacion_tipo');
        $this->hasColumn('autenticacion_basica_user');
        $this->hasColumn('autenticacion_basica_pass');
        $this->hasColumn('autenticacion_basica_cert');
        $this->hasColumn('autenticacion_basica_cert_pass');
        $this->hasColumn('autenticacion_mutua_client');
        $this->hasColumn('autenticacion_mutua_client_pass');
        $this->hasColumn('autenticacion_mutua_server');
        $this->hasColumn('autenticacion_mutua_user');
        $this->hasColumn('autenticacion_mutua_pass');
        $this->hasColumn('autenticacion_mutua_client_key');
    }

    function setUp() {
        parent::setUp();

        $this->hasMany('WsOperacion as WsOperaciones', array(
            'local' => 'id',
            'foreign' => 'catalogo_id'
        ));
    }
}
