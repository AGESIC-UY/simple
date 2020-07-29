<?php

class Accion extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('tipo');
        $this->hasColumn('extra');
        $this->hasColumn('proceso_id');
        $this->hasColumn('exponer_variable');

        $this->setSubclasses(array(
            'AccionEnviarCorreo' => array('tipo' => 'enviar_correo'),
            'AccionWebservice' => array('tipo' => 'webservice'),
            'AccionWebserviceExtended' => array('tipo' => 'webservice_extended'),
            'AccionPasarelaPago' => array('tipo' => 'pasarela_pago'),
            'AccionVariable' => array('tipo' => 'variable'),
            'AccionArchivo' => array('tipo' => 'archivo'),
            'AccionTraza' => array('tipo' => 'traza'),
            'AccionVariableObn' => array('tipo' => 'variable_obn')
                )
        );
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('Proceso', array(
            'local' => 'proceso_id',
            'foreign' => 'id'
        ));

        $this->hasMany('Evento as Eventos', array(
            'local' => 'id',
            'foreign' => 'accion_id'
        ));

        $this->hasMany('EventoPago as EventosPagos', array(
            'local' => 'id',
            'foreign' => 'accion_id'
        ));
    }

    public function displayForm() {
        return NULL;
    }

    public function validateForm() {
        return;
    }

    //Ejecuta la regla, de acuerdo a los datos del tramite tramite_id
    public function ejecutar($tramite_id) {
        return;
    }

    public function setExtra($datos_array) {
        if ($datos_array)
            $this->_set('extra', json_encode($datos_array));
        else
            $this->_set('extra', NULL);
    }

    public function getExtra() {
        return json_decode($this->_get('extra'));
    }

    public function getEventoPago() {
        if ($this->id) {
            $eventoPago = Doctrine::getTable('EventoPago')->findByAccionId($this->id);
        } else {
            $eventoPago = array();
        }
        return $eventoPago;
    }

    public function removeEventoPago($evento) {

        $eventoPago = Doctrine::getTable('EventoPago')->find($evento->id);
        $eventoPago->delete();
        //show_error(1);
    }

}
