<?php

class Tramite extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('pendiente');
        $this->hasColumn('proceso_id');
        $this->hasColumn('created_at');
        $this->hasColumn('updated_at');
        $this->hasColumn('ended_at');
    }

    function setUp() {
        parent::setUp();

        $this->actAs('Timestampable');

        $this->hasOne('Proceso', array(
            'local' => 'proceso_id',
            'foreign' => 'id'
        ));

        $this->hasMany('Etapa as Etapas', array(
            'local' => 'id',
            'foreign' => 'tramite_id'
        ));

        $this->hasMany('File as Files', array(
            'local' => 'id',
            'foreign' => 'tramite_id'
        ));
    }

    public function iniciar($proceso_id) {
        $CI = & get_instance();
        //verifico si el usuario pertenece el grupo MesaDeEntrada y hay esta actuando como un ciudadano
        if (UsuarioSesion::usuarioMesaDeEntrada() && $CI->session->userdata('id_usuario_ciudadano')) {
            $usuario_sesion = Doctrine_Query::create()
                    ->from('Usuario u')
                    ->where('u.id = ?', $CI->session->userdata('id_usuario_ciudadano'))
                    ->fetchOne();
        } else {
            $usuario_sesion = UsuarioSesion::usuario();
        }

        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        $this->proceso_id = $proceso->id;
        $this->pendiente = 1;

        $etapa = new Etapa();
        $etapa->tarea_id = $proceso->getTareaInicial($usuario_sesion->id)->id;
        $etapa->pendiente = 1;
        $etapa->vencimiento_at = $etapa->calcularVencimiento();

        $this->Etapas[] = $etapa;

        $this->save();
        //echo 'sdfsdf';

        $etapa->asignar($usuario_sesion->id);
        //show_error($usuario_sesion->id);
    }

    public function getEtapasParticipadas($usuario_id) {
        return Doctrine_Query::create()
                        ->from('Etapa e, e.Tramite t')
                        ->where('t.id = ? AND e.usuario_id=?', array($this->id, $usuario_id))
                        ->andWhere('e.pendiente=0')
                        ->execute();
    }

    public function getEtapasActuales() {
        return Doctrine_Query::create()
                        ->from('Etapa e, e.Tramite t')
                        ->where('t.id = ? AND e.pendiente=1', $this->id)
                        ->execute();
    }

    public function getTodasEtapas() {
        return Doctrine_Query::create()
                        ->from('Etapa e, e.Tramite t')
                        ->where('t.id = ?', $this->id)
                        ->execute();
    }

    public function getUltimaEtapa() {
        return Doctrine_Query::create()
                        ->from('Etapa e, e.Tramite t')
                        ->where('t.id = ?', $this->id)
                        ->orderBy('e.id DESC')
                        ->fetchOne();
    }

    public function getTareasActuales() {
        return Doctrine_Query::create()
                        ->from('Tarea tar, tar.Etapas e, e.Tramite t')
                        ->where('t.id = ? AND e.pendiente=1', $this->id)
                        ->execute();
    }

    public function getTareasCompletadas() {
        return Doctrine_Query::create()
                        ->from('Tarea tar, tar.Etapas e, e.Tramite t')
                        ->where('t.id = ? AND e.pendiente=0', $this->id)
                        ->execute();
    }

    public function getTareasAutomaticas($array_id_tareas) {
        if (count($array_id_tareas) > 0) {
            $query = Doctrine_Query::create()
                    ->from('Tarea tar, tar.Etapas e, e.Tramite t')
                    ->where('t.id = ?', $this->id);
$consultaSQL ="";
            for ($i = 0; $i < count($array_id_tareas); $i++) {
                if ($i == 0) {
                    $consultaSQL .= 'tar.id = ' . (int) $array_id_tareas[$i];
                } else {
                    $consultaSQL .= ' or tar.id = ' . (int) $array_id_tareas[$i];
                }
            }

            $query->andWhere($consultaSQL);

            return $query->execute();
        } else {
            return Doctrine_Query::create()
                            ->from('Tarea tar, tar.Etapas e, e.Tramite t')
                            ->where('t.id = ?', 'NULL')
                            ->execute();
        }
    }

    public function getEtapasCompletadas($etapa_id) {
        return Doctrine_Query::create()
                        ->from('Etapa e, e.Tramite t')
                        ->where('(t.id = ? AND e.pendiente=0) or e.id = ?', array($this->id, $etapa_id))
                        ->orderBy('e.id ASC')
                        ->execute();
    }

    /*
      public function getTareaProxima() {
      $tarea_actual = $this->getEtapaActual()->Tarea;

      if ($tarea_actual->final)
      return NULL;

      $conexiones = $tarea_actual->ConexionesOrigen;

      foreach ($conexiones as $c) {
      if ($c->evaluarRegla($this->id))
      return $c->TareaDestino;
      }

      return NULL;
      }
     *
     */

    //Chequea si el usuario_id ha tenido participacion en este tramite.
    public function usuarioHaParticipado($usuario_id) {
        $tramite = Doctrine_Query::create()
                ->from('Tramite t, t.Etapas e, e.Usuario u')
                ->where('t.id = ? AND u.id = ?', array($this->id, $usuario_id))
                ->fetchOne();

        if ($tramite)
            return TRUE;

        return FALSE;
    }

    public function cerrar() {
        Doctrine_Manager::connection()->beginTransaction();

        foreach ($this->Etapas as $e) {
            $e->cerrar();
        }
        $this->pendiente = 0;
        $this->ended_at = date('Y-m-d H:i:s');
        $this->save();

        Doctrine_Manager::connection()->commit();
    }

    public function cerrar_sin_ejecutar_eventos() {
        Doctrine_Manager::connection()->beginTransaction();

        foreach ($this->Etapas as $e) {
            $e->cerrar_sin_ejecutar_eventos();
        }
        $this->pendiente = 0;
        $this->ended_at = date('Y-m-d H:i:s');
        $this->save();

        Doctrine_Manager::connection()->commit();
    }

    //Retorna el tramite convertido en array, solamente con los campos visibles al publico a traves de la API.
    public function toPublicArray() {
        $etapas = null;
        $etapas_obj = Doctrine_Query::create()->from('Etapa e')->where('e.tramite_id = ?', $this->id)->orderBy('id desc')->execute();
        foreach ($etapas_obj as $e)
            $etapas[] = $e->toPublicArray();

        $datos = null;
        $datos_obj = Doctrine::getTable('DatoSeguimiento')->findByTramite($this->id);
        foreach ($datos_obj as $d)
            $datos[] = $d->toPublicArray();

        $publicArray = array(
            'id' => (int) $this->id,
            'estado' => $this->pendiente ? 'pendiente' : 'completado',
            'proceso_id' => (int) $this->proceso_id,
            'fecha_inicio' => $this->created_at,
            'fecha_modificacion' => $this->updated_at,
            'fecha_termino' => $this->ended_at,
            'etapas' => $etapas,
            'datos' => $datos
        );

        return $publicArray;
    }

    public function getUltimaEtapaPendiente() {
        return Doctrine_Query::create()
                        ->from('Etapa e, e.Tramite t')
                        ->where('t.id = ?', $this->id)
                        ->andWhere('e.pendiente = 1')
                        ->orderBy('e.id DESC')
                        ->fetchOne();
    }   

    public function actualizarProceso($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);
        if ($proceso){
           $etapas = Doctrine::getTable('Etapa')->findByTramiteId($this->id);
           foreach ($etapas as $etapa) {
//               print_r("entro");
               $etapa->actualizarTarea($proceso->id);
           }
           
           $this->proceso_id = $proceso->id;
           $this->save();
        }
    }

}
