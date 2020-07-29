<?php

class Formulario extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('proceso_id');
        $this->hasColumn('bloque_id');
        $this->hasColumn('leyenda');
        $this->hasColumn('contenedor');
        $this->hasColumn('tipo');
    }

    function setUp() {
        parent::setUp();

        $this->hasMany('Campo as Campos',array(
            'local'=>'id',
            'foreign'=>'formulario_id',
            'orderBy'=>'posicion'
        ));

        $this->hasMany('Paso as Pasos',array(
            'local'=>'id',
            'foreign'=>'formulario_id'
        ));

        $this->hasOne('Proceso',array(
            'local'=>'proceso_id',
            'foreign'=>'id'
        ));
    }

    public function updatePosicionesCamposFromJSON($json){
        $posiciones = json_decode($json);

        Doctrine_Manager::connection()->beginTransaction();
        foreach($this->Campos as $c){
            $c->posicion=array_search($c->id, $posiciones);
            $c->save();
        }
        Doctrine_Manager::connection()->commit();
    }

    // Obtiene la ultima posicion de los campos de este formulario
    public function getUltimaPosicionCampo(){
        $max=0;
        foreach($this->Campos as $c){
            if($c->posicion>$max){
                $max=$c->posicion;
            }
        }
        return $max;
    }
}
