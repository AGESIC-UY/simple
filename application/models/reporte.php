<?php

class Reporte extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('campos');
        $this->hasColumn('tipo');
        $this->hasColumn('proceso_id');
        $this->hasColumn('grupos_usuarios_permiso');
        $this->hasColumn('usuarios_permiso');
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('Proceso', array(
            'local' => 'proceso_id',
            'foreign' => 'id'
        ));

        $this->hasMany('Campo as Campos', array(
            'local' => 'id',
            'foreign' => 'reporte_id'
        ));

        $CI = & get_instance();
        $CI->load->helper('excel_helper');
    }

    public function setCampos($campos) {
        $this->_set('campos', json_encode($campos));
    }

    public function getCampos() {
        return json_decode($this->_get('campos'));
    }

    public function contar_generar_completo($grupo = null, $usuario = null, $desde = null, $hasta = null, $updated_at_desde = null, $updated_at_hasta = null, $pendiente = null) {
        set_time_limit(1600);
        ini_set('memory_limit', '-1');

        $header = array_merge(array('id etapa', 'id tramite', 'nombre etapa', 'estado', 'fecha inicio', 'fecha modificacion', 'fecha termino', 'asignado a', 'ejecutado por'), $this->campos);

        $excel[] = $header;

        $tramites_query = Doctrine_Query::create()
                ->select('t.*, p.*, e.*')
                ->from('Tramite t, t.Proceso p, t.Etapas e')
                ->where('p.id = ?', $this->proceso_id);


        if ($grupo) {
            $tramites_query->andWhere('e.Tarea.grupos_usuarios REGEXP ? OR e.Tarea.grupos_usuarios REGEXP ? OR e.Tarea.grupos_usuarios REGEXP ? OR e.Tarea.grupos_usuarios REGEXP ?', array('^' . $grupo . ',', ',' . $grupo . ',', '^' . $grupo . '$', ',' . $grupo . '$'));
        }

        if ($usuario) {
            $tramites_query->andWhere('e.usuario_id = ?', $usuario);
        }

        if ($desde) {
            $desde = $desde . ' 00:00:00';
            $tramites_query->andWhere('e.created_at >= ?', date("Y-m-d H:i:s", strtotime($desde)));
        }

        if ($hasta) {
            $hasta = $hasta . ' 23:59:59';
            $tramites_query->andWhere('e.created_at <= ?', date("Y-m-d H:i:s", strtotime($hasta)));
        }

        if ($updated_at_desde) {
            $updated_at_desde = $updated_at_desde . ' 00:00:00';
            $tramites_query->andWhere('e.updated_at >= ?', date("Y-m-d H:i:s", strtotime($updated_at_desde)));
        }

        if ($updated_at_hasta) {
            $updated_at_hasta = $updated_at_hasta . ' 23:59:59';
            $tramites_query->andWhere('e.updated_at <= ?', date("Y-m-d H:i:s", strtotime($updated_at_hasta)));
        }

        if ($pendiente != -1) {
            $tramites_query->andWhere('t.pendiente = ?', $pendiente);
        }

        $tramites = $tramites_query->orderBy('t.id', 'desc')
                        ->orderBy('e.created_at', 'desc')->fetchArray();
        $cantidad = 0;
        foreach ($tramites as $t) {
            $cantidad += count($t['Etapas']);
        }

        return $cantidad;
    }

    public function contar_generar_basico($desde, $hasta, $updated_at_desde = null, $updated_at_hasta = null, $pendiente = null) {
        set_time_limit(1600);
        ini_set('memory_limit', '-1');

        if ($desde) {
            $desde = $desde . ' 00:00:00';
        }

        if ($hasta) {
            $hasta = $hasta . ' 23:59:59';
        }

        $pendiente_query = array();
        if ($pendiente == -1) {
            $pendiente_query[0] = 1;
            $pendiente_query[1] = 0;
        } else {
            $pendiente_query[0] = $pendiente;
        }

        $tramites_query = Doctrine_Query::create()
                ->from('Tramite t, t.Proceso p, t.Etapas e, e.DatosSeguimiento d')
                ->where('p.id = ?', $this->proceso_id)
                ->andWhere('t.created_at >= ?', date("Y-m-d H:i:s", strtotime($desde)))
                ->andWhere('t.created_at <= ?', date("Y-m-d H:i:s", strtotime($hasta)))
                ->andWhereIn('t.pendiente ', $pendiente_query)
                ->having('COUNT(d.id) > 0 OR COUNT(e.id) > 1');

        if ($updated_at_desde) {
            $updated_at_desde = $updated_at_desde . ' 00:00:00';
            $tramites_query->andWhere('t.updated_at >= ?', date("Y-m-d H:i:s", strtotime($updated_at_desde)));
        }

        if ($updated_at_hasta) {
            $updated_at_hasta = $updated_at_hasta . ' 23:59:59';
            $tramites_query->andWhere('t.updated_at <= ?', date("Y-m-d H:i:s", strtotime($updated_at_hasta)));
        }

        $tramites_query->having('COUNT(d.id) > 0 OR COUNT(e.id) > 1');

        return $cantidad = $tramites_query->count();
    }

    public function generar_completo($grupo = null, $usuario = null, $desde = null, $hasta = null, $updated_at_desde = null, $updated_at_hasta = null, $pendiente = null) {
        set_time_limit(1600);
        ini_set('memory_limit', '-1');

        $CI = & get_instance();

        $CI->load->library('Excel_XML');

        $header = array_merge(array('id etapa', 'id tramite', 'nombre etapa', 'estado', 'fecha inicio', 'fecha modificacion', 'fecha termino', 'asignado a', 'ejecutado por'), $this->campos);

        $excel[] = $header;

        $tramites_query = Doctrine_Query::create()
                ->select('t.*, p.*, e.*')
                ->from('Tramite t, t.Proceso p, t.Etapas e')
                ->where('p.id = ?', $this->proceso_id);


        //print_r($updated_at_desde);
        if ($grupo) {
            $tramites_query->andWhere('e.Tarea.grupos_usuarios REGEXP ? OR e.Tarea.grupos_usuarios REGEXP ? OR e.Tarea.grupos_usuarios REGEXP ? OR e.Tarea.grupos_usuarios REGEXP ?', array('^' . $grupo . ',', ',' . $grupo . ',', '^' . $grupo . '$', ',' . $grupo . '$'));
        }

        if ($usuario) {
            $tramites_query->andWhere('e.usuario_id = ?', $usuario);
        }

        if ($desde) {
            $desde = $desde . ' 00:00:00';
            $tramites_query->andWhere('e.created_at >= ?', date("Y-m-d H:i:s", strtotime($desde)));
        }

        if ($hasta) {
            $hasta = $hasta . ' 23:59:59';
            $tramites_query->andWhere('e.created_at <= ?', date("Y-m-d H:i:s", strtotime($hasta)));
        }

        if ($updated_at_desde) {
            $updated_at_desde = $updated_at_desde . ' 00:00:00';
            $tramites_query->andWhere('e.updated_at >= ?', date("Y-m-d H:i:s", strtotime($updated_at_desde)));
        }

        if ($updated_at_hasta) {
            $updated_at_hasta = $updated_at_hasta . ' 23:59:59';
            $tramites_query->andWhere('e.updated_at <= ?', date("Y-m-d H:i:s", strtotime($updated_at_hasta)));
        }

        if ($pendiente != -1) {
            $tramites_query->andWhere('t.pendiente = ?', $pendiente);
        }

        //ejecuta la query
        $tramites = $tramites_query->orderBy('t.id', 'desc')
                        ->orderBy('e.created_at', 'desc')->fetchArray();

        foreach ($tramites as $t) {
            foreach ($t['Etapas'] as $etapa) {
                $etapa_linea = Doctrine_Core::getTable('Etapa')->find($etapa['id']);

                if (!$etapa_linea->Usuario) {
                    $usuario_asignado = 'no asignado';
                } else {
                    if ($etapa_linea->Usuario->registrado == '1') {
                        $usuario_asignado = $etapa_linea->Usuario->usuario;
                    } else {
                        $usuario_asignado = '';
                    }
                }

                if ($etapa_linea->pendiente) {
                    $usuario_ejecuto = '';
                } else {
                    if ($etapa_linea->Usuario->registrado == '1') {
                        $usuario_ejecuto = $etapa_linea->Usuario->usuario;
                    } else {
                        $usuario_ejecuto = 'anonimo';
                    }
                }

                $row = array(
                    $etapa_linea->id,
                    $t['id'],
                    quitar_caracteres_especiales($etapa_linea->Tarea->nombre),
                    $etapa_linea->pendiente ? 'pendiente' : 'completado',
                    $etapa_linea->created_at,
                    $etapa_linea->updated_at,
                    $etapa_linea->ended_at,
                    quitar_caracteres_especiales($usuario_asignado),
                    quitar_caracteres_especiales($usuario_ejecuto)
                );

                $datos = Doctrine_Core::getTable('DatoSeguimiento')->findByEtapaId($etapa_linea->id);

                foreach ($datos as $d) {
                    if (in_array($d['nombre'], $this->campos)) {
                        $val = $d->valor;
                        if (!is_string($val)) {
                            $val = json_encode($val, JSON_UNESCAPED_UNICODE);
                        }

                        $colindex = array_search($d->nombre, $header);
                        $row[$colindex] = $val;
                    }
                }

                for ($i = 0; $i < count($row); $i++) {
                    if (!isset($row[$i])) {
                        $row[$i] = '';
                    }
                }

                ksort($row);

                $excel[] = $row;
            }
        }

        $CI->excel_xml->addArray($excel);
        $CI->excel_xml->generateXML($this->nombre);
    }

    public function generar_basico($desde, $hasta, $updated_at_desde = null, $updated_at_hasta = null, $pendiente = null) {

        if (!$desde || !$hasta) {
            return;
        }


        //set_time_limit(600);
        //por bug  en uso de memoria para generar reporte
        ini_set('max_execution_time', 6000);
        ini_set('memory_limit', '-1');

        $CI = & get_instance();

        $CI->load->library('Excel_XML');

        $header = array_merge(array('id', 'estado', 'etapa_actual', 'fecha_inicio', 'fecha_modificacion', 'fecha_termino'), $this->campos);

        $excel[] = $header;

        if ($desde) {
            $desde = $desde . ' 00:00:00';
        }

        if ($hasta) {
            $hasta = $hasta . ' 23:59:59';
        }

        $pendiente_query = array();
        if ($pendiente == -1) {
            $pendiente_query[0] = 1;
            $pendiente_query[1] = 0;
        } else {
            $pendiente_query[0] = $pendiente;
        }

        $tramites = Doctrine_Query::create()
                ->from('Tramite t, t.Proceso p, t.Etapas e, e.DatosSeguimiento d')
                ->where('p.id = ?', $this->proceso_id)
                ->andWhere('t.created_at >= ?', date("Y-m-d H:i:s", strtotime($desde)))
                ->andWhere('t.created_at <= ?', date("Y-m-d H:i:s", strtotime($hasta)))
                ->andWhereIn('t.pendiente ', $pendiente_query);

        if ($updated_at_desde) {
            $updated_at_desde = $updated_at_desde . ' 00:00:00';
            $tramites->andWhere('t.updated_at >= ?', date("Y-m-d H:i:s", strtotime($updated_at_desde)));
        }

        if ($updated_at_hasta) {
            $updated_at_hasta = $updated_at_hasta . ' 23:59:59';
            $tramites->andWhere('t.updated_at <= ?', date("Y-m-d H:i:s", strtotime($updated_at_hasta)));
        }

        $tramites->having('COUNT(d.id) > 0 OR COUNT(e.id) > 1')  //Mostramos solo los que se han avanzado o tienen datos
                ->groupBy('t.id')
                ->orderBy('t.id desc');

        foreach ($tramites->execute() as $t) {
            $etapas_actuales = $t->getEtapasActuales();
            $etapas_actuales_arr = array();
            foreach ($etapas_actuales as $e)
                $etapas_actuales_arr[] = $e->Tarea->nombre;
            $etapas_actuales_str = implode(',', $etapas_actuales_arr);
            $row = array(
                $t->id,
                $t->pendiente ? 'pendiente' : 'completado',
                quitar_caracteres_especiales($etapas_actuales_str),
                $t->created_at,
                $t->updated_at,
                $t->ended_at
            );

            $datos = Doctrine_Core::getTable('DatoSeguimiento')->findByTramite($t->id);

            foreach ($datos as $d) {
                if (in_array($d['nombre'], $this->campos)) {
                    $val = $d->valor;
                    if (!is_string($val))
                        $val = json_encode($val, JSON_UNESCAPED_UNICODE);

                    $colindex = array_search($d->nombre, $header);
                    $row[$colindex] = $val;
                }
            }

            // Rellenamos con espacios en blanco los campos que no existen.
            for ($i = 0; $i < count($row); $i++)
                if (!isset($row[$i]))
                    $row[$i] = '';

            // Ordenamos
            ksort($row);

            $excel[] = $row;
        }

        $CI->excel_xml->addArray($excel);
        $CI->excel_xml->generateXML($this->nombre);
    }

    public function generar() {
        //set_time_limit(600);
        //por bug  en uso de memoria para generar reporte
        ini_set('max_execution_time', 6000);
        ini_set('memory_limit', '-1');

        $CI = & get_instance();

        $CI->load->library('Excel_XML');

        $header = array_merge(array('id', 'estado', 'etapa_actual', 'fecha_inicio', 'fecha_modificacion', 'fecha_termino'), $this->campos);

        $excel[] = $header;

        $tramites = Doctrine_Query::create()
                ->from('Tramite t, t.Proceso p, t.Etapas e, e.DatosSeguimiento d')
                ->where('p.id = ?', $this->proceso_id)
                ->having('COUNT(d.id) > 0 OR COUNT(e.id) > 1')  //Mostramos solo los que se han avanzado o tienen datos
                ->groupBy('t.id')
                ->orderBy('t.id desc')
                ->execute();

        foreach ($tramites as $t) {
            $etapas_actuales = $t->getEtapasActuales();
            $etapas_actuales_arr = array();
            foreach ($etapas_actuales as $e)
                $etapas_actuales_arr[] = $e->Tarea->nombre;
            $etapas_actuales_str = implode(',', $etapas_actuales_arr);
            $row = array(
                $t->id,
                $t->pendiente ? 'pendiente' : 'completado',
                quitar_caracteres_especiales($etapas_actuales_str),
                $t->created_at,
                $t->updated_at,
                $t->ended_at
            );

            $datos = Doctrine_Core::getTable('DatoSeguimiento')->findByTramite($t->id);

            foreach ($datos as $d) {
                if (in_array($d['nombre'], $this->campos)) {
                    $val = $d->valor;
                    if (!is_string($val))
                        $val = json_encode($val, JSON_UNESCAPED_UNICODE);

                    $colindex = array_search($d->nombre, $header);
                    $row[$colindex] = $val;
                }
            }

            // Rellenamos con espacios en blanco los campos que no existen.
            for ($i = 0; $i < count($row); $i++)
                if (!isset($row[$i]))
                    $row[$i] = '';

            // Ordenamos
            ksort($row);

            $excel[] = $row;
        }

        $CI->excel_xml->addArray($excel);
        $CI->excel_xml->generateXML($this->nombre);
    }

    public function setGruposUsuariosFromArray($grupos_array) {
        if (is_array($grupos_array))
            $this->grupos_usuarios_permiso = implode(',', $grupos_array);
        else
            $this->grupos_usuarios_permiso = '';
    }

    public function setUsuariosFromArray($usuarios_array) {
        if (is_array($usuarios_array))
            $this->usuarios_permiso = implode(',', $usuarios_array);
        else
            $this->usuarios_permiso = '';
    }

    public function verificar_permisos_backend($usuario_backend) {

        if (!$usuario_backend) {
            return false;
        }

        if (UsuarioBackend::user_has_rol($usuario_backend->id, 'super')) {
            return true;
        }

        if (!$this->grupos_usuarios_permiso && !$this->usuarios_permiso) {
            return true;
        }
        $usuario = Doctrine::getTable('Usuario')->findOneByUsuarioAndCuentaId($usuario_backend->usuario, $usuario_backend->cuenta_id);

        if ($this->grupos_usuarios_permiso && $usuario) {
            $array_grupos_con_permiso = explode(',', $this->grupos_usuarios_permiso);

            foreach ($array_grupos_con_permiso as $id_grupo_con_permisos) {
                foreach ($usuario->GruposUsuarios as $grupo_del_usuario) {
                    // este usuario pertenece a un grupo que esta dentro de la lista de grupos que pueden ejecutar reportes
                    if ($grupo_del_usuario->id == $id_grupo_con_permisos) {
                        return true;
                    }
                }
            }
        }

        if ($this->usuarios_permiso) {
            $array_usuarios_con_permiso = explode(',', $this->usuarios_permiso);
            foreach ($array_usuarios_con_permiso as $usuario_con_permisos) {
                // este usuario esta dentro de la lista de usuarios que pueden ejecutar el reporte
                if (trim($usuario_backend->usuario) == trim($usuario_con_permisos)) {
                    return true;
                }

                if ($usuario && trim($usuario->usuario) == trim($usuario_con_permisos)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function verificar_permisos_frontend($usuario_frontend) {

        if (!$usuario_frontend) {
            return false;
        }

        if (!$this->grupos_usuarios_permiso && !$this->usuarios_permiso) {
            return true;
        }

        if ($this->grupos_usuarios_permiso) {
            $array_grupos_con_permiso = explode(',', $this->grupos_usuarios_permiso);
            foreach ($array_grupos_con_permiso as $id_grupo_con_permisos) {
                foreach ($usuario_frontend->GruposUsuarios as $grupo_del_usuario) {
                    // este usuario pertenece a un grupo que esta dentro de la lista de grupos que pueden ejecutar reportes
                    if ($grupo_del_usuario->id == $id_grupo_con_permisos) {
                        return true;
                    }
                }
            }
        }

        if ($this->usuarios_permiso) {
            $array_usuarios_con_permiso = explode(',', $this->usuarios_permiso);
            foreach ($array_usuarios_con_permiso as $usuario_con_permisos) {
                // este usuario esta dentro de la lista de usuarios que pueden ejecutar el reporte
                if (trim($usuario_frontend->usuario) == trim($usuario_con_permisos)) {
                    return true;
                }
            }
        }

        return false;
    }

}
