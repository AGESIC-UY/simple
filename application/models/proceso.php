<?php

class Proceso extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('width');      //ancho de la grilla
        $this->hasColumn('height');     //alto de la grilla
        $this->hasColumn('cuenta_id');
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('Cuenta',array(
            'local'=>'cuenta_id',
            'foreign'=>'id'
        ));

        $this->hasMany('Tramite as Tramites',array(
            'local'=>'id',
            'foreign'=>'proceso_id',
        ));

        $this->hasMany('Tarea as Tareas',array(
            'local'=>'id',
            'foreign'=>'proceso_id',
        ));

        $this->hasMany('Formulario as Formularios',array(
            'local'=>'id',
            'foreign'=>'proceso_id',
            'orderBy'=>'nombre asc'
        ));

        $this->hasMany('Accion as Acciones',array(
            'local'=>'id',
            'foreign'=>'proceso_id',
            'orderBy'=>'nombre asc'
        ));

        $this->hasMany('Documento as Documentos',array(
            'local'=>'id',
            'foreign'=>'proceso_id',
            'orderBy'=>'nombre asc'
        ));

        $this->hasMany('Reporte as Reportes',array(
            'local'=>'id',
            'foreign'=>'proceso_id',
            'orderBy'=>'nombre asc'
        ));

        $this->hasOne('ProcesoTrazabilidad',array(
            'local'=>'id',
            'foreign'=>'proceso_id'
        ));
    }

    public function updateModelFromJSON($json){
        Doctrine_Manager::connection()->beginTransaction();
        $modelo = json_decode($json);

        //Agregamos los elementos nuevos y/o existentes
        foreach ($modelo->elements as $e) {
            $tarea = Doctrine::getTable('Tarea')->findOneByIdentificadorAndProcesoId($e->id, $this->id);
            $tarea->posx = $e->left;
            $tarea->posy = $e->top;
            $tarea->save();
        }

        Doctrine_Manager::connection()->commit();

    }

    public function getJSONFromModel(){
        Doctrine_Manager::connection()->beginTransaction();

        $modelo=new stdClass();
        $modelo->nombre=$this->nombre;
        $modelo->elements=array();
        $modelo->connections=array();

        $tareas=Doctrine::getTable('Tarea')->findByProcesoId($this->id);
        foreach($tareas as $t){
            $element=new stdClass();
            $element->id=$t->identificador;
            $element->name=$t->nombre;
            $element->left=$t->posx;
            $element->top=$t->posy;
            $element->start=$t->inicial;
            //$element->stop=$t->final;
            $modelo->elements[]=clone $element;
        }

        $conexiones=  Doctrine_Query::create()
                ->from('Conexion c, c.TareaOrigen.Proceso p')
                ->where('p.id = ?',$this->id)
                ->execute();
        foreach($conexiones as $c){
            //$conexion->id=$c->identificador;
            $conexion=new stdClass();
            $conexion->source=$c->TareaOrigen->identificador;
            $conexion->target=$c->TareaDestino->identificador;
            $conexion->tipo=$c->tipo;
            $modelo->connections[]=clone $conexion;
        }

        Doctrine_Manager::connection()->commit();

        return json_encode($modelo);
    }

    public function getConexiones(){
        return Doctrine_Query::create()
            ->select('c.*')
            ->from('Conexion c, c.TareaOrigen.Proceso p1, c.TareaDestino.Proceso p2')
            ->where('p1.id = ? OR p2.id = ?',array($this->id,$this->id))
            ->execute();
    }

    public function exportComplete(){
        $proceso=$this;
        $proceso->Tareas;
        foreach($proceso->Tareas as $t){
            $t->Pasos;
            $t->Eventos;
        }

        $proceso->Formularios;
        foreach ($proceso->Formularios as $f) {
            $f->Campos;
        }

        $proceso->Acciones;
        $proceso->Documentos;
        $proceso->ProcesoTrazabilidad;

        $object=$proceso->toArray();

        $object['Conexiones']=$proceso->getConexiones()->toArray();

        return json_encode($object);

    }

    /**
     * @param $input
     * @return Proceso
     */
    public static function importComplete($input){
      try {
        $json=json_decode($input);

        //Creamos el proceso
        $proceso=new Proceso();
        $proceso->cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;

        //Creamos los documentos
        if(!isset($json->Documentos)) {
          throw new Exception(false);
        }
        foreach($json->Documentos as $f){
            $proceso->Documentos[$f->id]=new Documento();
            foreach($f as $keyf => $f_attr){
                if($keyf != 'id' && $keyf != 'proceso_id' && $keyf != 'Proceso' && $keyf != 'hsm_configuracion_id'){
                    $proceso->Documentos[$f->id]->{$keyf}=$f_attr;
                }
            }
        }

        //Creamos los formularios
        if(!isset($json->Formularios)) {
          throw new Exception(false);
        }
        foreach($json->Formularios as $f){
          $proceso->Formularios[$f->id]=new Formulario();
          foreach($f as $keyf => $f_attr)
            if($keyf == 'Campos'){
              foreach($f_attr as $c){
                $campo = new Campo();
                foreach($c as $keyc => $c_attr){
                    if($keyc != 'id' && $keyc != 'formulario_id' && $keyc != 'Formulario' && $keyc != 'documento_id'){
                        $campo->{$keyc} = $c_attr;
                    }
                }
                if($c->documento_id) $campo->Documento = $proceso->Documentos[$c->documento_id];
                $proceso->Formularios[$f->id]->Campos[]=$campo;
              }
            }
            elseif($keyf != 'id' && $keyf != 'proceso_id' && $keyf != 'Proceso'){
                $proceso->Formularios[$f->id]->{$keyf}=$f_attr;
            }
        }

        //Creamos las acciones
        if(!isset($json->Acciones)) {
          throw new Exception(false);
        }
        foreach($json->Acciones as $f){
          $proceso->Acciones[$f->id]=new Accion();
          foreach($f as $keyf => $f_attr){
            if($keyf != 'id' && $keyf != 'proceso_id' && $keyf != 'Proceso'){
                $proceso->Acciones[$f->id]->{$keyf}=$f_attr;
            }
          }
        }

        //Completamos el proceso y sus tareas
        foreach($json as $keyp=>$p_attr){
            if($keyp == 'Tareas'){
                foreach($p_attr as $t){
                    $tarea=new Tarea();
                    foreach($t as $keyt=>$t_attr){
                        if($keyt == 'Pasos'){
                            foreach($t_attr as $pa){
                                $paso = new Paso();
                                foreach($pa as $keypa => $pa_attr){
                                    if($keypa != 'id' && $keypa != 'tarea_id' && $keypa != 'Tarea' && $keypa != 'formulario_id')
                                        $paso->{$keypa}=$pa_attr;
                                }
                                $paso->Formulario=$proceso->Formularios[$pa->formulario_id];
                                $tarea->Pasos[$pa->id]=$paso;
                            }
                        }elseif($keyt=='Eventos'){
                            foreach($t_attr as $ev){
                                $evento = new Evento();
                                foreach($ev as $keyev => $ev_attr){
                                    if($keyev != 'id' && $keyev != 'tarea_id' && $keyev != 'Tarea' && $keyev != 'accion_id' && $keyev != 'paso_id')
                                        $evento->{$keyev}=$ev_attr;
                                }
                                $evento->Accion=$proceso->Acciones[$ev->accion_id];
                                if($ev->paso_id)$evento->Paso=$tarea->Pasos[$ev->paso_id];
                                $tarea->Eventos[]=$evento;
                            }
                        }elseif($keyt != 'id' && $keyt != 'proceso_id' && $keyt != 'Proceso' && $keyt != 'grupos_usuarios'){
                            $tarea->{$keyt}=$t_attr;
                        }
                    }

                    $proceso->Tareas[$t->id]=$tarea;
                }
            }elseif($keyp=='Formularios' || $keyp=='Acciones' || $keyp=='Documentos' || $keyp=='Conexiones'){

            }elseif($keyp != 'id' && $keyp != 'cuenta_id' && $keyp != 'ProcesoTrazabilidad'){
                $proceso->{$keyp} = $p_attr;
            }
        }

        //Hacemos las conexiones
        if(!isset($json->Conexiones)) {
          throw new Exception(false);
        }
        foreach($json->Conexiones as $c){
            $conexion=new Conexion();
            $proceso->Tareas[$c->tarea_id_origen]->ConexionesOrigen[]=$conexion;
            if($c->tarea_id_destino) $proceso->Tareas[$c->tarea_id_destino]->ConexionesDestino[]=$conexion;
            foreach($c as $keyc => $c_attr){
                if($keyc!='id' && $keyc != 'tarea_id_origen' && $keyc != 'tarea_id_destino'){
                    $conexion->{$keyc} = $c_attr;
                }
            }
        }

        // Agregamos los datos de trazabilidad
        if(!isset($json->ProcesoTrazabilidad)) {
          throw new Exception(false);
        }
        else {
          $proceso->ProcesoTrazabilidad->organismo_id = $json->ProcesoTrazabilidad->organismo_id;
          $proceso->ProcesoTrazabilidad->proceso_externo_id = $json->ProcesoTrazabilidad->proceso_externo_id;
        }

        return $proceso;
      }
      catch(Exception $error) {
        echo '-1';
      }
    }


    //Entrega la tarea inicial del proceso. Si se entrega $usuario_id, muestra cual seria la tarea inicial para
    //ese usuario en particular.
    public function getTareaInicial($usuario_id=null){
        $tareas=Doctrine_Query::create()
                ->from('Tarea t, t.Proceso p')
                ->where('t.inicial = 1 AND p.id = ?',$this->id)
                ->orderBy('FIELD(acceso_modo, "grupos_usuarios", "claveunica", "registrados", "publico")')
                ->execute();

        if($usuario_id){
            foreach($tareas as $key=>$t)
                if ($t->canUsuarioIniciarla($usuario_id))
                    return $t;
        }

        return $tareas[0];
    }

    //Obtiene todos los campos asociados a este proceso
    public function getCampos($tipo=null,$excluir_readonly=true){
        $query= Doctrine_Query::create()
                ->from('Campo c, c.Formulario f, f.Proceso p')
                ->where('p.id = ?',$this->id);

        if($tipo)
            $query->andWhere('c.tipo = ?',$tipo);

        if($excluir_readonly)
            $query->andWhere('c.readonly = 0');

        return $query->execute();
    }

    //Obtiene todos los campos asociados a este proceso
    public function getNombresDeCampos($tipo=null,$excluir_readonly=true){
        $campos=$this->getCampos($tipo,$excluir_readonly);

        $nombres=array();
        foreach($campos as $c)
            $nombres[$c->nombre]=true;

        return array_keys($nombres);
    }

    //Retorna una arreglo con todos los nombres de datos usados durante el proceso
    public function getNombresDeDatos(){
        $campos=Doctrine_Query::create()
                ->select('d.nombre')
                ->from('DatoSeguimiento d, d.Etapa.Tramite.Proceso p')
                ->andWhere('p.id = ?',$this->id)
                ->groupBy('d.nombre')
                ->execute();

        foreach($campos as $c)
            $result[]=$c->nombre;

        return $result;
    }

    //Verifica si el usuario_id tiene permisos para iniciar este proceso como tramite.
    public function canUsuarioIniciarlo($usuario_id){

        $tareas = Doctrine_Query::create()
                ->from('Tarea t, t.Proceso p')
                ->where('p.id = ? AND t.inicial = 1',$this->id)
                ->execute();

        foreach ($tareas as $t) {
            if($t->canUsuarioIniciarla($usuario_id))
                return true;
        }


        return false;
    }

    //Verifica si el usuario_id tiene permisos para que le aparezca listado en las bandejas del frontend
    public function canUsuarioListarlo($usuario_id){
        $usuario=Doctrine::getTable('Usuario')->find($usuario_id);

        $tareas = Doctrine_Query::create()
                ->from('Tarea t, t.Proceso p')
                ->where('p.id = ? AND t.inicial = 1',$this->id)
                ->execute();

        foreach ($tareas as $t) {
            if($t->acceso_modo=='publico')
                return true;

            if ($t->acceso_modo == 'claveunica')
                return true;

            if ($t->acceso_modo == 'registrados')
                return true;

            if ($t->acceso_modo == 'grupos_usuarios') {
                $grupos_arr = explode(',', $t->grupos_usuarios);
                $u = Doctrine_Query::create()
                        ->from('Usuario u, u.GruposUsuarios g')
                        ->where('u.id = ?', $usuario->id)
                        ->andWhereIn('g.id', $grupos_arr)
                        ->fetchOne();
                if ($u)
                    return true;
            }

        }

        return false;
    }

    public function toPublicArray(){
        $publicArray=array(
            'id'=>(int)$this->id,
            'nombre'=>$this->nombre
        );

        return $publicArray;
    }
}
