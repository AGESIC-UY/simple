<?php

class Widget extends Doctrine_Record {

    function setTableDefinition() {        
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('tipo');
        $this->hasColumn('config');
        $this->hasColumn('posicion');
        $this->hasColumn('cuenta_id');

        
        $this->setSubclasses(array(
                'WidgetTramiteEtapas'  => array('tipo' => 'tramite_etapas'),
                'WidgetTramitesCantidad'  => array('tipo' => 'tramites_cantidad'),
                'WidgetEtapaUsuarios'  => array('tipo' => 'etapa_usuarios')
            )
        );
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('Cuenta', array(
            'local' => 'cuenta_id',
            'foreign' => 'id'
        ));
    }
    
    public function display(){
        return null;
    }
    
    public function displayForm(){
        return null;
    }
    
    public function validateForm(){
        return;
    }
    
    public function setConfig($datos_array) {
        if ($datos_array) 
            $this->_set('config' , json_encode($datos_array));
        else 
            $this->_set('config' , NULL);
    }
    
    public function getConfig(){
        return json_decode($this->_get('config'));
    }


}
