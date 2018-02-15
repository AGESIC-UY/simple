<?php

class Proceso extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('width');      //ancho de la grilla
        $this->hasColumn('height');     //alto de la grilla
        $this->hasColumn('cuenta_id');
        $this->hasColumn('codigo_tramite_ws_grep');
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

    //Retorna una arreglo con todos los nombres de datos usados durante el proceso
    public function getNombresDeDatosTramite($tramite_id){
      $campos=Doctrine_Query::create()
                ->select('d.nombre')
                ->from('DatoSeguimiento d, d.Etapa.Tramite t')
                ->andWhere('t.id = ?',$tramite_id)
                ->groupBy('d.nombre')
                ->execute();

        foreach($campos as $c)
            $result[]=$c->nombre;

        return $result;
    }

    //Retorna una arreglo con todos los nombres de las variables generadas por los campos que tienen reporte = true y las acciones de generar variables
    public function getNombresDeVariables(){


      //las variables que genera para el usuario
      foreach($this->Tareas as $tarea) {

        //los campos que generarn variables y aplica a reporte
        foreach($tarea->Pasos as $p) {
          foreach($p->Formulario->Campos as $campo) {
            if ($campo->reporte){
              if (!in_array($campo->nombre,$result)){
                $result[]=$campo->nombre;
              }
              if(($campo->tipo == 'radio') || ($campo->tipo == 'select') || ($campo->tipo == 'checkbox')) {
                if (!in_array($campo->nombre.'__etiqueta',$result)){
                  $result[]=$campo->nombre.'__etiqueta';
                }
              }

            }

            //si un formulario tienen pasarela de pagos de ANTEL desjamos disponibles las variables que genera
            //pasarela de pago
            if ($campo->tipo == 'pagos'){
              foreach($this->Acciones as $accion) {
                if($accion->id == $campo->valor_default) {
                  $pasarela = $accion->extra;
                  break;
                }
              }

              if(!isset($pasarela->metodo)) {
                $metodo_pasarela = 'antel';
              }
              else {
                $metodo_pasarela = $pasarela->metodo;
              }

              if ($pasarela && $metodo_pasarela == 'antel'){
                if (!in_array('Solicitud_IdSolicitud',$result)){
                  $result[]='Solicitud_IdSolicitud';
                }
                if (!in_array('Solicitud_IdEstado',$result)){
                  $result[]='Solicitud_IdEstado';
                }
                if (!in_array('Solicitud_Fecha',$result)){
                  $result[]='Solicitud_Fecha';
                }
                if (!in_array('Solicitud_Transaccion',$result)){
                  $result[]='Solicitud_Transaccion';
                }
                if (!in_array('Solicitud_Autorizacion',$result)){
                  $result[]='Solicitud_Autorizacion';
                }
                if (!in_array('Solicitud_IdFormaPago',$result)){
                  $result[]='Solicitud_IdFormaPago';
                }
                if (!in_array('Solicitud_FechaConciliacion',$result)){
                  $result[]='Solicitud_FechaConciliacion';
                }
                if (!in_array('Solicitud_ValorTasa',$result)){
                  $result[]='Solicitud_ValorTasa';
                }
                if (!in_array('Solicitud_IdTramite',$result)){
                  $result[]='Solicitud_IdTramite';
                }
                if (!in_array('Solicitud_ImporteTasa1',$result)){
                  $result[]='Solicitud_ImporteTasa1';
                }
                if (!in_array('Solicitud_ImporteTasa2',$result)){
                  $result[]='Solicitud_ImporteTasa2';
                }
                if (!in_array('Solicitud_ImporteTasa3',$result)){
                  $result[]='Solicitud_ImporteTasa3';
                }

                if (!in_array('Solicitud_FechaVto',$result)){
                  $result[]='Solicitud_FechaVto';
                }

                if (!in_array('Solicitud_CodDesglose',$result)){
                  $result[]='Solicitud_CodDesglose';
                }

                if (!in_array('Solicitud_MontoDesglose',$result)){
                  $result[]='Solicitud_MontoDesglose';
                }

                if (!in_array('Solicitud_DesRechazo',$result)){
                  $result[]='Solicitud_DesRechazo';
                }

                if (!in_array('Solicitud_Ventanilla',$result)){
                  $result[]='Solicitud_Ventanilla';
                }

                if (!in_array('Solicitud_DesError',$result)){
                  $result[]='Solicitud_DesError';
                }

                if (!in_array('Solicitud_Mensaje',$result)){
                  $result[]='Solicitud_Mensaje';
                }
              }elseif ($pasarela && $metodo_pasarela == 'generico'){
                  //se busca la accion
                  $pasarela_generica = Doctrine_Query::create()
                              ->from('PasarelaPagoGenerica pg')
                              ->where('pg.id = ?', $pasarela->pasarela_pago_generica_id)
                              ->fetchOne();

                  if ($pasarela_generica->codigo_operacion_soap){
                    $operacion_soap = Doctrine_Query::create()
                                  ->from('WsOperacion op')
                                  ->where('op.codigo = ?', $pasarela_generica->codigo_operacion_soap)
                                  ->fetchOne();

                    $respuestas = json_decode($operacion_soap->respuestas);
                    foreach($respuestas->respuestas as $resp){
                      if (!in_array($resp->key,$result)){
                        $result[]= $resp->key;
                      }
                    }
                  }

                  if ($pasarela_generica->codigo_operacion_soap_consulta){
                     $operacion_soap_consulta = Doctrine_Query::create()
                                    ->from('WsOperacion op')
                                    ->where('op.codigo = ?', $pasarela_generica->codigo_operacion_soap_consulta)
                                    ->fetchOne();

                      $respuestas = json_decode($operacion_soap_consulta->respuestas);
                      foreach($respuestas->respuestas as $resp){
                        if (!in_array($resp->key,$result)){
                          $result[]= $resp->key;
                        }
                      }
                  }
                  if ($pasarela->url_redireccion){
                    if(!filter_var($pasarela->url_redireccion, FILTER_VALIDATE_URL)) {
                        $codigo_operacion_post = $pasarela->url_redireccion;

                        $operacion_post = Doctrine_Query::create()
                          ->from('WsOperacion o')
                          ->where('o.codigo = ?', $codigo_operacion_post)
                          ->fetchOne();

                        $respuestas = json_decode($operacion_post->respuestas);
                        foreach($respuestas->respuestas as $resp){
                            if (!in_array($resp->key,$result)){
                              $result[]= $resp->key;
                            }
                        }
                    }
                  }
              }
            }
          }
        }

        //las acciones generar variable y serb service extended
        foreach($tarea->Eventos as $evento) {
          //si evento de tipo variable se deja disponible
          if ($evento->Accion->tipo == 'variable'){
            if (!in_array($evento->Accion->extra->variable,$result)){
              $result[]= $evento->Accion->extra->variable;
            }
          }
          //si evento web service dejamos las varaibles de la respuesta disponibles
          if ($evento->Accion->tipo == 'webservice_extended'){
            //recorremos las repsuestas del servicio para disponibilizar las variables
            $codigo_operacion = $evento->Accion->extra->soap_operacion;
            $operacion = Doctrine_Query::create()
                          ->from('WsOperacion o')
                          ->where('o.codigo = ?', $codigo_operacion)
                          ->execute();
            $operacion = $operacion[0];
            $respuestas = json_decode($operacion->respuestas);

            foreach($respuestas->respuestas as $resp){
              if (!in_array($resp->key,$result)){
                $result[]= $resp->key;
              }
            }
          }
        }

        //la variable que genera para almacenar el usuario de la tarea
        if ($tarea->almacenar_usuario){
          if (!in_array($tarea->almacenar_usuario_variable,$result)){
            $result[]= $tarea->almacenar_usuario_variable;
          }
        }
      }

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
