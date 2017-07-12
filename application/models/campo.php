<?php

class Campo extends Doctrine_Record {

    public $requiere_datos=true;    //Indica si requiere datos seleccionables. Como las opciones de un checkbox, select, etc.
    public $estatico=false; //Indica si es un campo estatico, es decir que no es un input con informacion. Ej: Parrafos, titulos, etc.
    public $etiqueta_tamano='large'; //Indica el tamaño default que tendra el campo de etiqueta. Puede ser large o xxlarge.
    public $requiere_nombre=true;    //Indica si requiere que se le ingrese un nombre (Es decir, no generarlo aleatoriamente)
    public $requiere_validacion = true; // Indica si se requiere validacion para el campo.
    public $sin_etiqueta=false; // Indica que no se debe mostrar la etiqueta.
    public $valor_default_tamano='small'; // Indica si el campo valor_default debe ser mas grande y se debe quitar el campo etiqueta. Se utilza en dialogos.
    public $dialogo=false; // Indica si el campo es de tipo DIALOGO.

    public static function factory($tipo){
        if($tipo=='text')
            $campo=new CampoText();
        if($tipo=='dialogo')
            $campo=new CampoDialogo();
        if($tipo=='error')
            $campo=new CampoError(); // -- Deprecado
        if($tipo=='fieldset')
            $campo=new CampoFieldset();
        if($tipo=='encuesta')
            $campo=new CampoEncuesta();
        else if($tipo=='textarea')
            $campo=new CampoTextArea();
        else if($tipo=='select')
            $campo=new CampoSelect();
        else if($tipo=='radio')
            $campo=new CampoRadio();
        else if($tipo=='checkbox')
            $campo=new CampoCheckbox();
        else if($tipo=='file')
            $campo=new CampoFile();
        else if($tipo=='date')
            $campo=new CampoDate();
        else if($tipo=='bloque')
            $campo=new CampoBloque();
        else if($tipo=='instituciones_gob')
            $campo=new CampoInstitucionesGob();
        else if($tipo=='comunas')
            $campo=new CampoComunas();
        else if($tipo=='paises')
            $campo=new CampoPaises();
        else if($tipo=='moneda')
            $campo=new CampoMoneda();
        else if($tipo=='title')
            $campo=new CampoTitle();
        else if($tipo=='subtitle')
            $campo=new CampoSubtitle();
        else if($tipo=='paragraph')
            $campo=new CampoParagraph();
        else if($tipo=='documento')
            $campo=new CampoDocumento();
        else if($tipo=='javascript')
            $campo=new CampoJavascript();
        else if($tipo=='grid')
            $campo=new CampoGrid();
        else if($tipo=='tabla-responsive')
            $campo=new CampoTablaResponsive();
        else if($tipo=='agenda')
            $campo=new CampoAgenda();
        else if($tipo=='pagos')
            $campo=new CampoPagos();
        else if($tipo=='estado_pago')
            $campo=new CampoEstadoPago();

        $campo->assignInheritanceValues();

        return $campo;
    }

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('posicion');
        $this->hasColumn('tipo');
        $this->hasColumn('formulario_id');
        $this->hasColumn('etiqueta');
        $this->hasColumn('validacion');
        $this->hasColumn('ayuda');
        $this->hasColumn('dependiente_tipo');
        $this->hasColumn('dependiente_campo');
        $this->hasColumn('dependiente_valor');
        $this->hasColumn('dependiente_relacion');
        $this->hasColumn('datos');
        $this->hasColumn('readonly');           //Indica que en este campo solo se mostrara la informacion.
        $this->hasColumn('valor_default');
        $this->hasColumn('documento_id');
        $this->hasColumn('fieldset');
        $this->hasColumn('extra');
        $this->hasColumn('ayuda_ampliada');
        $this->hasColumn('documento_tramite');
        $this->hasColumn('email_tramite');
        $this->hasColumn('pago_online');
        $this->hasColumn('requiere_agendar');
        $this->hasColumn('firma_electronica');

        $this->setSubclasses(array(
                'CampoText'  => array('tipo' => 'text'),
                'CampoError'  => array('tipo' => 'error'),
                'CampoDialogo'  => array('tipo' => 'dialogo'),
                'CampoFieldset'  => array('tipo' => 'fieldset'),
                'CampoEncuesta'  => array('tipo' => 'encuesta'),
                'CampoTextArea'  => array('tipo' => 'textarea'),
                'CampoSelect'  => array('tipo' => 'select'),
                'CampoRadio'  => array('tipo' => 'radio'),
                'CampoCheckbox'  => array('tipo' => 'checkbox'),
                'CampoFile'  => array('tipo' => 'file'),
                'CampoDate'  => array('tipo' => 'date'),
                'CampoBloque'  => array('tipo' => 'bloque'),
                'CampoInstitucionesGob'  => array('tipo' => 'instituciones_gob'),
                'CampoComunas'  => array('tipo' => 'comunas'),
                'CampoPaises'  => array('tipo' => 'paises'),
                'CampoMoneda'  => array('tipo' => 'moneda'),
                'CampoTitle'  => array('tipo' => 'title'),
                'CampoSubtitle'  => array('tipo' => 'subtitle'),
                'CampoParagraph'  => array('tipo' => 'paragraph'),
                'CampoDocumento'  => array('tipo' => 'documento'),
                'CampoJavascript'  => array('tipo' => 'javascript'),
                'CampoGrid'  => array('tipo' => 'grid'),
                'CampoTablaResponsive'  => array('tipo' => 'tabla-responsive'),
                'CampoAgenda'  => array('tipo' => 'agenda'),
                'CampoPagos'  => array('tipo' => 'pagos'),
                'CampoEstadoPago'  => array('tipo' => 'estado_pago'),
            ));
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('Formulario', array(
            'local' => 'formulario_id',
            'foreign' => 'id'
        ));

        $this->hasOne('Documento', array(
            'local' => 'documento_id',
            'foreign' => 'id'
        ));

        $this->hasOne('Reporte', array(
            'local' => 'reporte_id',
            'foreign' => 'id'
        ));
    }

    //Despliega la vista de un campo del formulario utilizando los datos de seguimiento (El dato que contenia el tramite al momento de cerrar la etapa)
    //etapa_id indica a la etapa que pertenece este campo
    //modo es visualizacion o edicion
    public function displayConDatoSeguimiento($etapa_id, $modo = 'edicion'){
        $dato = NULL;
        $dato =  Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($this->nombre,$etapa_id);
        if($this->readonly)$modo='visualizacion';

        return $this->display($modo,$dato,$etapa_id);
    }

    public function displaySinDato($modo = 'edicion') {
        if($this->readonly)$modo='visualizacion';
        return $this->display($modo,NULL,NULL);
    }


    protected function display($modo, $dato){
        return '';
    }

    //Funcion que retorna si este campo debiera poderse editar de acuerdo al input POST del usuario
    public function isEditableWithCurrentPOST(){
        $CI=& get_instance();

        $resultado=true;

        if($this->readonly){
           $resultado=false;
        }else if($this->dependiente_campo){
            $nombre_campo=preg_replace('/\[\w*\]$/', '', $this->dependiente_campo);
            $variable=$CI->input->post($nombre_campo);

            //Parche para el caso de campos dependientes con accesores. Ej: ubicacion[comuna]!='Las Condes|Santiago'
            if(preg_match('/\[(\w+)\]$/',$this->dependiente_campo,$matches))
                $variable=$variable[$matches[1]];

            if($variable===false){    //Si la variable dependiente no existe
                $resultado=false;
            }else{
                if(is_array($variable)){ //Es un arreglo
                    if($this->dependiente_tipo=='regex'){
                        foreach($variable as $x){
                            if(!preg_match('/'.$this->dependiente_valor.'/', $x))
                                $resultado = false;
                        }
                    }else{
                        if(!in_array($this->dependiente_valor, $variable))
                            $resultado = false;
                    }
                }else{
                    if($this->dependiente_tipo=='regex'){
                        if(!preg_match('/'.$this->dependiente_valor.'/', $variable))
                            $resultado = false;
                    }else{
                        if($variable!=$this->dependiente_valor)
                            $resultado = false;
                    }

                }

                if($this->dependiente_relacion=='!=')
                    $resultado=!$resultado;
            }
        }

        return $resultado;
    }

      public function esVisibleParaLaEtapaActual($etapa_id){
        $CI=& get_instance();

        $resultado=true;

        if($this->readonly){
           $resultado=false;
        }else if($this->dependiente_campo){
            $nombre_campo=preg_replace('/\[\w*\]$/', '', $this->dependiente_campo);
            $campo_datos_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre_campo, $etapa_id);
            $variable= $campo_datos_seguimiento->valor;

            //Parche para el caso de campos dependientes con accesores. Ej: ubicacion[comuna]!='Las Condes|Santiago'
            if(preg_match('/\[(\w+)\]$/',$this->dependiente_campo,$matches))
                $variable=$variable[$matches[1]];

            if($variable===false){    //Si la variable dependiente no existe
                $resultado=false;
            }else{
                if(is_array($variable)){ //Es un arreglo
                    if($this->dependiente_tipo=='regex'){
                        foreach($variable as $x){
                            if(!preg_match('/'.$this->dependiente_valor.'/', $x))
                                $resultado = false;
                        }
                    }else{
                        if(!in_array($this->dependiente_valor, $variable))
                            $resultado = false;
                    }
                }else{
                    if($this->dependiente_tipo=='regex'){
                        if(!preg_match('/'.$this->dependiente_valor.'/', $variable))
                            $resultado = false;
                    }else{
                        if($variable!=$this->dependiente_valor)
                            $resultado = false;
                    }

                }

                if($this->dependiente_relacion=='!=')
                    $resultado=!$resultado;
            }
        }

        return $resultado;
    }

    public function formValidate($etapa_id = null){
        $CI=& get_instance();

        $validacion=$this->validacion;
        if($etapa_id){
            $regla = new Regla($this->validacion);
            $validacion = $regla->getExpresionParaOutput($etapa_id);
        }

        $CI->form_validation->set_rules($this->nombre, ucfirst($this->etiqueta), implode('|', $validacion));
    }


    //Señala como se debe mostrar en el formulario de edicion del backend, cualquier field extra.
    public function backendExtraFields(){
        return;
    }

    //Validaciones adicionales que se le deben hacer a este campo en su edicion en el backend.
    public function backendExtraValidate(){

    }

    public function setValidacion($validacion){
        if($validacion)
            $this->_set('validacion',  implode ('|', $validacion));
        else
            $this->_set('validacion','');
    }

    public function getValidacion(){
        if($this->_get('validacion'))
            return explode('|',$this->_get('validacion'));
        else
            return array();
    }

    public function setDatos($datos_array) {
        if ($datos_array)
            $this->_set('datos' , json_encode($datos_array));
        else
            $this->_set('datos' , NULL);
    }

    public function getDatos() {
        return json_decode($this->_get('datos'));
    }

    public function setDocumentoId($documento_id){
        if($documento_id=='')
            $documento_id=null;

        $this->_set('documento_id',$documento_id);
    }

    public function extraForm(){
        return false;
    }

    public function setExtra($datos_array) {
        if ($datos_array)
            $this->_set('extra' , json_encode($datos_array));
        else
            $this->_set('extra' , NULL);
    }

    public function getExtra(){
        return json_decode($this->_get('extra'));
    }
}
