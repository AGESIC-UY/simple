<?php

class File extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('filename');
        $this->hasColumn('tipo');
        $this->hasColumn('llave');          //Llave para ver el documento
        $this->hasColumn('llave_copia');    //Llave para obtener la copia del documento
        $this->hasColumn('llave_firma');    //Llave para poder firmar con token el documento
        $this->hasColumn('validez');
        $this->hasColumn('validez_habiles');
        $this->hasColumn('tramite_id');
    }

    function setUp() {
        parent::setUp();
        
        $this->actAs('Timestampable');

        $this->hasOne('Tramite',array(
            'local'=>'tramite_id',
            'foreign'=>'id'
        ));

      
        

    }
    
    public function postDelete($event) {
        parent::postDelete($event);
        if($this->tipo=='documento'){
            unlink ('uploads/documentos/'.$this->filename);
            unlink ('uploads/documentos/'.preg_replace('/\.pdf$/','.copia.pdf',$this->filename));
        }
        else if($this->tipo=='dato'){
            unlink ('uploads/datos/'.$this->filename);
        }
    }

}
