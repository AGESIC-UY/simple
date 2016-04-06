<?php

class WsOperacionRespuesta extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('operacion_id');
        $this->hasColumn('respuesta_id');
        $this->hasColumn('xslt');
    }
}
