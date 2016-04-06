<?php

class ReporteSatisfaccion extends Doctrine_Record {

    function setTableDefinition() {        
        $this->hasColumn('id');
        $this->hasColumn('usuario_id');
        $this->hasColumn('fecha');
        $this->hasColumn('reporte');
    }
}
