<?php

class Feriado extends Doctrine_Record{
    
    public function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('fecha');
    }
    
}