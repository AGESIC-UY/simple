<?php

class EtapaHistorialEjecuciones extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('etapa_id');
        $this->hasColumn('secuencia');
        $this->hasColumn('nombre_paso');
        //usuario real que ejecuto la etapa
        $this->hasColumn('usuario_id');
        $this->hasColumn('descripcion');
        $this->hasColumn('fecha');

    }

    function setUp() {
        parent::setUp();

        $this->hasOne('Etapa', array(
            'local' => 'etapa_id',
            'foreign' => 'id'
        ));

        $this->hasOne('Usuario', array(
            'local' => 'usuario_id',
            'foreign' => 'id'
        ));
    }
}
