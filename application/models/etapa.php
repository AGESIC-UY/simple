<?php

class Etapa extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('tarea_id');
        $this->hasColumn('tramite_id');
        $this->hasColumn('usuario_id');
        $this->hasColumn('pendiente');
        $this->hasColumn('etapa_ancestro_split_id');    //Etapa ancestro que provoco el split del flujo. (Sirve para calcular cuando se puede hacer la union del flujo)
        $this->hasColumn('vencimiento_at');
        $this->hasColumn('created_at');
        $this->hasColumn('updated_at');
        $this->hasColumn('ended_at');
    }

    function setUp() {
        parent::setUp();

        $this->actAs('Timestampable');

        $this->hasOne('Tarea', array(
            'local' => 'tarea_id',
            'foreign' => 'id'
        ));

        $this->hasOne('Tramite', array(
            'local' => 'tramite_id',
            'foreign' => 'id'
        ));

        $this->hasOne('Usuario', array(
            'local' => 'usuario_id',
            'foreign' => 'id'
        ));

        $this->hasMany('DatoSeguimiento as DatosSeguimiento', array(
            'local' => 'id',
            'foreign' => 'etapa_id'
        ));

        $this->hasOne('Etapa as EtapaAncestroSplit', array(
            'local'=>'etapa_ancestro_split_id',
            'foreign'=>'id'
        ));

        $this->hasMany('Etapa as EtapasDescendientesSplit', array(
            'local'=>'id',
            'foreign'=>'etapa_ancestro_split_id'
        ));
    }


    //verifica si el usuario del backend puede reasignar la etapa
    public function canUsuarioReasignar($usuario_backend){

      //si no es rol seguimiento entonces el comportamiento normal
      if ($usuario_backend->rol != 'seguimiento'){
        return true;
      }

      return $usuario_backend->seg_reasginar;
    }

    //verifica si el usuario del backend puede revisar el detalle de la etapa
    public function canUsuarioRevisarDetalle($usuario_backend){

      //solo el rol seguimiento otro rol si tiene acceso desde el menu ve el detalle
      if ($usuario_backend->rol != 'seguimiento'){
        return true;
      }

      if ($this->Tarea->acceso_modo == 'publico')
          return true;

      if ($this->Tarea->acceso_modo == 'claveunica' && $usuario->open_id)
          return true;

      if ($this->Tarea->acceso_modo == 'registrados' && $usuario->registrado)
          return true;

      //si control total se puede ver el detalle de todas las tareas.
      if ($usuario_backend->seg_alc_control_total){
          return true;
      }

      $grupos_permitidos = $usuario_backend->seg_alc_grupos_usuarios;

      if (in_array('todos',$grupos_permitidos)){
        //si es para tdos los grupos del usuario, se levanta el usaurio del front extends
        //y se verifica los grupos de aqui
        $usuario = Doctrine::getTable('Usuario')->findOneByUsuarioAndCuentaId($usuario_backend->usuario, $usuario_backend->cuenta_id);
        $grupos_permitidos = $usuario->GruposUsuarios;
        //obtiene los grupos de usario de la tarea
        $r=new Regla($this->Tarea->grupos_usuarios);
        $grupos_arr = explode(',', $r->getExpresionParaOutput($this->id));
        foreach($grupos_permitidos as $g){
          if(in_array($g->id,$grupos_arr))
              return true;
        }
      }else{
        //obtiene los grupos de usario de la tarea
        //show_error($this->Tarea->id);
        $r=new Regla($this->Tarea->grupos_usuarios);
        $grupos_arr = explode(',', $r->getExpresionParaOutput($this->id));
        foreach($grupos_permitidos as $g){
          if(in_array($g,$grupos_arr))
              return true;
        }

      }


    }

    //Verifica si el usuario_id tiene permisos para asignarse esta etapa del tramite.
    public function canUsuarioAsignarsela($usuario_id) {
        static $usuario;

        if(!$usuario || ($usuario->id != $usuario_id)){
            $usuario=Doctrine::getTable('Usuario')->find($usuario_id);
        }

        if ($this->Tarea->acceso_modo == 'publico')
            return true;

        if ($this->Tarea->acceso_modo == 'claveunica' && $usuario->open_id)
            return true;

        if ($this->Tarea->acceso_modo == 'registrados' && $usuario->registrado)
            return true;

        if ($this->Tarea->acceso_modo == 'grupos_usuarios') {
            $r=new Regla($this->Tarea->grupos_usuarios);
            $grupos_arr = explode(',', $r->getExpresionParaOutput($this->id));
            foreach($usuario->GruposUsuarios as $g)
                if(in_array($g->id,$grupos_arr))
                    return true;
        }

        return false;
    }

    //Avanza a la siguiente etapa.
    //Si se desea especificar el usuario a cargo de la prox etapa, se debe pasar como parametros en un array: $usuarios_a_asignar[$tarea_id]=$usuario_id.
    //Este parametro solamente es valido si la asignacion de la prox tarea es manual.
    public function avanzar($usuarios_a_asignar = null) {
        Doctrine_Manager::connection()->beginTransaction();
        // Cerramos esta etapa
        $this->cerrar();

        $tp = $this->getTareasProximas();
        if ($tp->estado != 'sincontinuacion') {
            if ($tp->estado == 'completado') {
                if ($this->Tramite->getEtapasActuales()->count() == 0)
                    $this->Tramite->cerrar();
            }
            else {
                if ($tp->estado == 'pendiente') {
                    $tareas_proximas = $tp->tareas;
                    foreach ($tareas_proximas as $tarea_proxima) {
                        $etapa = new Etapa();
                        $etapa->tramite_id = $this->Tramite->id;
                        $etapa->tarea_id = $tarea_proxima->id;
                        $etapa->pendiente = 1;
                        $etapa->save();

                        $usuario_asignado_id = NULL;
                        if ($tarea_proxima->asignacion == 'ciclica') {
                            $usuarios_asignables = $etapa->getUsuarios();
                            $usuario_asignado_id = $usuarios_asignables[0]->id;
                            $ultimo_usuario = $tarea_proxima->getUltimoUsuarioAsignado($this->Tramite->Proceso->id);
                            if ($ultimo_usuario) {
                                foreach ($usuarios_asignables as $key => $u) {
                                    if ($u->id == $ultimo_usuario->id) {
                                        $usuario_asignado_id = $usuarios_asignables[($key + 1) % $usuarios_asignables->count()]->id;
                                        break;
                                    }
                                }
                            }
                        } else if ($tarea_proxima->asignacion == 'manual') {
                            $usuario_asignado_id = $usuarios_a_asignar[$tarea_proxima->id];
                        } else if ($tarea_proxima->asignacion == 'usuario') {
                            $regla = new Regla($tarea_proxima->asignacion_usuario);
                            $u = $regla->evaluar($this->id);
                            $usuario_asignado_id = $u;
                        }

                        //Para mas adelante poder calcular como hacer las uniones
                        if($tp->conexion=='union')
                            $etapa->etapa_ancestro_split_id=null;
                        else if ($tp->conexion=='paralelo' || $tp->conexion=='paralelo_evaluacion')
                            $etapa->etapa_ancestro_split_id=$this->id;
                        else
                            $etapa->etapa_ancestro_split_id=$this->etapa_ancestro_split_id;

                        $etapa->save();
                        $etapa->vencimiento_at=$etapa->calcularVencimiento();
                        $etapa->save();

                        if($usuario_asignado_id)
                            $etapa->asignar($usuario_asignado_id);

                        $etapa->notificarTareaPendiente();
                    }
                    $this->Tramite->updated_at = date("Y-m-d H:i:s");
                    $this->Tramite->save();
                }
            }
        }
        Doctrine_Manager::connection()->commit();
    }

    //Esta funcion entrega un listado de tareas a continuar y un estado que indica como se debe proceder con esta continuacion.
    //tareas:   -Arreglo de tareas para continuar
    //estado:   -sincontinuacion: No hay reglas para continuar. No se puede avanzar de etapa.
    //          -completado: Se completa el tramite luego de esta etapa.
    //          -pendiente: Hay etapas a continuacion
    //          -standby: Hay etapas a continuacion pero no se puede avanzar todavia hasta que que se completen etapas paralelas.
    public function getTareasProximas() {
        $resultado = new stdClass();
        $resultado->tareas = null;
        $resultado->estado = 'sincontinuacion';
        $resultado->conexion=null;


        $tarea_actual = $this->Tarea;
        $conexiones = $tarea_actual->ConexionesOrigen;

        //$tareas = null;
        foreach ($conexiones as $c) {
            if ($c->evaluarRegla($this->id)) {
                //Si no hay destino es el fin del tramite.
                if (!$c->tarea_id_destino) {
                    $resultado->tareas = null;
                    $resultado->estado = 'completado';
                    $resultado->conexion=null;
                    break;
                }

                //Si no es en paralelo, retornamos con la tarea proxima.
                if ($c->tipo == 'secuencial' || $c->tipo == 'evaluacion') {
                    $resultado->tareas = array($c->TareaDestino);
                    $resultado->estado = 'pendiente';
                    $resultado->conexion=$c->tipo;
                    break;
                }
                //Si son en paralelo, vamos juntando el grupo de tareas proximas.
                else if ($c->tipo == 'paralelo' || $c->tipo == 'paralelo_evaluacion') {
                    $resultado->tareas[] = $c->TareaDestino;
                    $resultado->estado = 'pendiente';
                    $resultado->conexion=$c->tipo;
                }
                //Si es de union, chequeamos que las etapas paralelas se hayan completado antes de continuar con la proxima.
                else if ($c->tipo == 'union') {
                    if (!$this->hayEtapasParalelasPendientes()) {
                        $resultado->estado = 'pendiente';
                    } else {
                        $resultado->estado = 'standby';
                    }
                    $resultado->tareas = array($c->TareaDestino);
                    $resultado->conexion=$c->tipo;
                    break;
                }
            }
        }

        return $resultado;
    }

    public function hayEtapasParalelasPendientes() {
        if($this->etapa_ancestro_split_id){
            $n_etapas_paralelas= Doctrine_Query::create()
                    ->from('Etapa e')
                    ->where('e.etapa_ancestro_split_id = ?',$this->etapa_ancestro_split_id)
                    ->andWhere('e.pendiente = 1')
                    ->andWhere('e.id != ?',$this->id)
                    ->count();
        }else{  //Metodo antiguo (Deprecado)
            $n_etapas_paralelas = Doctrine_Query::create()
                ->from('Etapa e, e.Tarea t, t.ConexionesOrigen c, c.TareaDestino tarea_hijo, tarea_hijo.ConexionesDestino c2, c2.TareaOrigen.Etapas etapa_this')
                ->andWhere('etapa_this.id = ?', $this->id)
                ->andWhere('c.tipo = "union" AND c2.tipo="union"')
                ->andWhere('e.tramite_id = ?',$this->tramite_id)
                ->andWhere('e.pendiente = 1')
                ->andWhere('e.id != ?',$this->id)
                ->count();
        }

        return $n_etapas_paralelas?true:false;
    }

    public function asignar($usuario_id) {
        if (!$this->canUsuarioAsignarsela($usuario_id))
            return;

        $this->usuario_id = $usuario_id;
        $this->save();

        //Ejecutamos los eventos
        $eventos=Doctrine_Query::create()->from('Evento e')
                ->where('e.tarea_id = ? AND e.instante = ? AND e.paso_id IS NULL',array($this->Tarea->id,'antes'))
                ->execute();
        foreach ($eventos as $e) {
          $r = new Regla($e->regla);
          if ($r->evaluar($this->id)) {
            $e->Accion->ejecutar($this);
          }
        }
    }

    public function notificarTareaPendiente(){
        if ($this->Tarea->asignacion_notificar) {
            if($this->usuario_id)
                $usuarios = Doctrine::getTable('Usuario')->findById($this->usuario_id);
            else
                $usuarios = $this->getUsuarios();

            foreach($usuarios as $usuario){
                if ($usuario->email) {
                    $CI = & get_instance();
                    $cuenta=$this->Tramite->Proceso->Cuenta;

                    if(!$cuenta->correo_remitente) {
                      ($CI->config->item('main_domain') == '') ? $from = $cuenta->nombre.'@simple' : $from = $cuenta->nombre.'@'.$CI->config->item('main_domain');
                    }
                    else {
                      $from = $cuenta->correo_remitente;
                    }

                    $CI->email->from($from, $cuenta->nombre_largo);
                    $CI->email->to($usuario->email);
                    $CI->email->subject('SIMPLE - Tiene una tarea pendiente');

                    if($this->Tarea->asignacion_notificar_mensaje) {
                      $CI->email->message('<p>'. $this->Tarea->asignacion_notificar_mensaje .'</p><p>Enlace a la tarea: ' . ($this->usuario_id?site_url('etapas/ejecutar/' . $this->id):site_url('etapas/sinasignar')) . '</p>');
                    }
                    else {
                      $CI->email->message('<p>' . $this->Tramite->Proceso->nombre . '</p><p>Tiene una tarea pendiente por realizar: ' . $this->Tarea->nombre . '</p><p>Podra realizarla en: ' . ($this->usuario_id?site_url('etapas/ejecutar/' . $this->id):site_url('etapas/sinasignar')) . '</p>');
                    }

                    if (!$CI->email->send()){
                        log_message('ERROR', "send email notificarTareaPendiente: ".$CI->email->print_debugger());
                    }
                }
            }

        }
    }

    public function cerrar() {
        // Si ya fue cerrada, retornamos inmediatamente.
        if (!$this->pendiente)
            return;

        //si se ejecuta desde la conciliacion no se tiene session
        //nunca se debe generar esta variable
        if ($this->Tarea->almacenar_usuario && UsuarioSesion::usuario()) {
            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($this->Tarea->almacenar_usuario_variable,$this->id);
            if (!$dato)
                $dato = new DatoSeguimiento();
            $dato->nombre = $this->Tarea->almacenar_usuario_variable;
            $dato->valor = UsuarioSesion::usuario()->id;
            $dato->etapa_id = $this->id;
            $dato->save();
        }

        //Ejecutamos los eventos
        $eventos=Doctrine_Query::create()->from('Evento e')
                ->where('e.tarea_id = ? AND e.instante = ? AND e.paso_id IS NULL',array($this->Tarea->id,'despues'))
                ->execute();
        foreach ($eventos as $e) {
                $r = new Regla($e->regla);
                if ($r->evaluar($this->id)) {
                  //$e->Accion->ejecutar($this);
                  if ($e->Accion->ejecutar($this) != null && $e->Accion->ejecutar($this) != false) return;
                }
        }

        //Cerramos la etapa
        $this->pendiente = 0;
        $this->ended_at = date('Y-m-d H:i:s');
        $this->save();
    }

    //Retorna el paso correspondiente a la secuencia, dado los datos ingresados en el tramite hasta el momento.
    //Es decir, tomando en cuenta las condiciones para que se ejecute cada paso.
    public function getPasoEjecutable($secuencia) {
        $pasos = $this->getPasosEjecutables($this->tramite_id);

        if (isset($pasos[$secuencia]))
            return $pasos[$secuencia];

        return null;
    }

    //Retorna un arreglo con todos los pasos que son ejecutables dado los datos ingresados en el tramite hasta el momento.
    //Es decir, tomando en cuenta las condiciones para que se ejecute cada paso.
    public function getPasosEjecutables() {
        $pasos = array();
        foreach ($this->Tarea->Pasos as $p) {
            $r = new Regla($p->regla);
            if ($r->evaluar($this->id))
                $pasos[] = $p;
        }

        return $pasos;
    }

    //Calcula la fecha en que deberia vencer esta etapa tomando en cuenta la configuracion de la tarea.
    public function calcularVencimiento(){
        if(!$this->Tarea->vencimiento)
            return NULL;

        $fecha=NULL;
        if($this->Tarea->vencimiento_unidad=='D')
            if($this->Tarea->vencimiento_habiles){
                $fecha=add_working_days($this->created_at,$this->Tarea->vencimiento_valor);
            }else{
                $temp = new DateTime($this->created_at);
                $fecha= $temp->add(new DateInterval('P' . $this->Tarea->vencimiento_valor . 'D'))->format('Y-m-d');
            }
        else if($this->Tarea->vencimiento_unidad=='W'){
            $temp = new DateTime($this->created_at);
            $fecha= $temp->add(new DateInterval('P' . $this->Tarea->vencimiento_valor . 'W'))->format('Y-m-d');
        }else if($this->Tarea->vencimiento_unidad=='M'){
            $temp = new DateTime($this->created_at);
            $fecha= $temp->add(new DateInterval('P' . $this->Tarea->vencimiento_valor . 'M'))->format('Y-m-d');
        }

        return $fecha;
    }

    /*
    public function getFechaVencimiento() {
        if (!($this->Tarea->vencimiento && $this->Tarea->vencimiento_valor))
            return NULL;

        //return strtotime($this->Tarea->vencimiento_valor.' '.$this->Tarea->vencimiento_unidad, mysql_to_unix($this->created_at));
        $creacion = new DateTime($this->created_at);
        //$creacion->setTime(0, 0, 0);
        return $creacion->add(new DateInterval('P' . $this->Tarea->vencimiento_valor . $this->Tarea->vencimiento_unidad));
    }
     *
     */

    public function getFechaVencimientoAsString() {
        $now = new DateTime();
        $now->setTime(0,0,0);

        $exp = new DateTime($this->vencimiento_at);
        $exp->setTime(0,0,0);

        $interval = $now->diff($exp);

        if($interval->invert)
            return 'vencida';
        else
            return 'vence en '. ($interval->d) . ' días';
    }


    public function vencida() {
        if (!$this->vencimiento_at)
            return FALSE;

        $vencimiento = new DateTime($this->vencimiento_at);
        $now = new DateTime();
        $now->setTime(0,0,0);

        return $vencimiento < $now;
    }

    public function iniciarPaso(Paso $paso, $secuencia) {
        //Ejecutamos los eventos iniciales
        $eventos=Doctrine_Query::create()->from('Evento e')
                ->where('e.paso_id = ? AND e.instante = ?',array($paso->id,'antes'))
                ->execute();
        foreach ($eventos as $e) {
                $r = new Regla($e->regla);
                if ($r->evaluar($this->id))
                    $e->Accion->ejecutar($this);

        }
    }

    public function finalizarPaso(Paso $paso, $secuencia) {
        //Ejecutamos los eventos finales
        $eventos=Doctrine_Query::create()->from('Evento e')
                ->where('e.paso_id = ? AND e.instante = ?',array($paso->id,'despues'))
                ->execute();
        foreach ($eventos as $e) {
                $r = new Regla($e->regla);
                if ($r->evaluar($this->id))
                    $e->Accion->ejecutar($this, $secuencia);
        }
    }

    public function toPublicArray(){
        $publicArray=array(
            'id'=>(int)$this->id,
            'estado'=>$this->pendiente?'pendiente':'completado',
            'usuario_asignado'=>$this->usuario_id?$this->Usuario->toPublicArray():null,
            'fecha_inicio' => $this->created_at,
            'fecha_modificacion' => $this->updated_at,
            'fecha_termino' => $this->ended_at,
            'fecha_vencimiento'=>$this->vencimiento_at,
            'tarea'=>$this->Tarea->toPublicArray()
        );

        return $publicArray;
    }

    //Obtiene el listado de usuarios que tienen acceso a esta tarea y que esten disponibles (no en vacaciones).
    public function getUsuarios() {
        return $this->Tarea->getUsuarios($this->id);
    }

    //Obtiene el listado de usuarios que tienen acceso a esta tarea y que esten disponibles (no en vacaciones).
    //Ademas, deben pertenecer a alguno de los grupos de usuarios definidos en la cuenta
    public function getUsuariosFromGruposDeUsuarioDeCuenta() {
        return $this->Tarea->getUsuariosFromGruposDeUsuarioDeCuenta($this->id);
    }

    public function getPrevisualizacion(){
        if(!$this->Tarea->previsualizacion)
            return '';

        $r = new Regla($this->Tarea->previsualizacion);

        return $r->getExpresionParaOutput($this->id);
    }

    public function getUsuarioInicial() {
      $documento = null;
      $documento_tramite = Doctrine::getTable('DatoSeguimiento')->findOneByNombre('documento_tramite_inicial__e'.$this->tramite_id);
      if($documento_tramite) {
        $campo = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($documento_tramite->valor, $documento_tramite->etapa_id);
        $documento = $campo->valor;
      }

      if(!$documento) {
        $primera_etapa_del_tramite = Doctrine_Query::create()->from('Etapa e')
                ->where('e.tramite_id = ?', $this->tramite_id)
                ->orderby('id ASC')
                ->execute();

        if(isset($primera_etapa_del_tramite[0]) && $primera_etapa_del_tramite[0]->Usuario) {
          $usuario_doc = $primera_etapa_del_tramite[0]->Usuario->usuario;
          $usuario_doc_len = strlen(trim($usuario_doc));
          if($usuario_doc_len > 16 || $usuario_doc_len < 1) {
            $documento = '';
          }
          else {
            $documento = $usuario_doc;
          }
        }
      }

      return $documento;
    }
}
