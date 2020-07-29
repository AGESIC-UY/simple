<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Reportes extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if (!UsuarioBackendSesion::has_rol('super') && !UsuarioBackendSesion::has_rol('gestion')) {
            redirect('backend');
        }

        $this->load->helper('excel_helper');
        $this->load->helper('auditoria_helper');
    }

    public function index() {
        $procesos = Doctrine_Query::create()
                ->from('Proceso p')
                ->where('p.cuenta_id = ?', UsuarioBackendSesion::usuario()->cuenta_id)
                ->where('p.nombre != ?', 'BLOQUE')
                ->orderBy('p.id')
                ->execute();

        $data['procesos'] = $procesos;

        $data['title'] = 'Gestión';
        $data['content'] = 'backend/reportes/index';

        $this->load->view('backend/template', $data);
    }

    public function listar($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);
        $reportes = Doctrine_Query::create()
                ->from('Reporte r')
                ->where('r.proceso_id = ?', $proceso_id)
                ->execute();

        $reportes_con_permisos = array();
        foreach ($reportes as $reporte) {
            if ($reporte->verificar_permisos_backend(UsuarioBackendSesion::usuario())) {
                array_push($reportes_con_permisos, $reporte);
            }
        }

        $grupos = Doctrine::getTable('GrupoUsuarios')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para listar los formularios de este proceso';
            exit;
        }

        $usuarios = Doctrine::getTable('Usuario')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);

        $data['proceso'] = $proceso;
        $data['reportes'] = $reportes_con_permisos;
        $data['grupos'] = $grupos;
        $data['usuarios'] = $usuarios;

        $data['title'] = 'Documentos';
        $data['content'] = 'backend/reportes/listar';

        $this->load->view('backend/template', $data);
    }

    public function generar_basico($reporte_id) {

        $reporte = Doctrine::getTable('Reporte')->find($reporte_id);

        $desde = $this->input->get('filtro_desde');
        $hasta = $this->input->get('filtro_hasta');
        $updated_at_desde = $this->input->get('desdeCambio');
        $updated_at_hasta = $this->input->get('hastaCambio');
        $pendiente = $this->input->get('estado');

        if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos';
            exit;
        }

        $cantidad = $reporte->contar_generar_basico($desde, $hasta, $updated_at_desde, $updated_at_hasta, $pendiente);
        $cuenta_segun_dominio = Cuenta::cuentaSegunDominio();

        $cantidad_limite = Doctrine_Query::create()
                ->from('Parametro p')
                ->where('p.cuenta_id = ? AND p.clave = ?', array($cuenta_segun_dominio->id, 'reporte_limite_permitido_basico'))
                ->fetchOne();
        $respuesta = new stdClass();
        if ($cantidad > $cantidad_limite->valor) {
            $respuesta->error = true;
            $respuesta->msg = REPORT_LIMIT_MESSAGE;
        } else {
            $respuesta->error = false;
        }
        if (!$respuesta->error) {
            $cantidad_maxima = Doctrine_Query::create()
                    ->from('Parametro p')
                    ->where('p.cuenta_id = ? AND p.clave = ?', array($cuenta_segun_dominio->id, 'reporte_basico_cantidad_maxima'))
                    ->fetchOne();
            if (!$cantidad_maxima) {
                $cantidad_maxima = -1;
            } else {
                $cantidad_maxima = $cantidad_maxima->valor;
            }

            if ($cantidad > $cantidad_maxima) {
                $respuesta->email = true;
            } else {
                $respuesta->email = false;
            }
        }
        echo json_encode($respuesta);
    }

    //operacion que se llama para validar si se tiene que generar por email o se genera online
    //al realizar clic en el boton generar se llama a esta funcion que retorna true si se debe generar y enviar por email o false
    //si se puede generar online
    public function generar_completo($reporte_id) {

        $reporte = Doctrine::getTable('Reporte')->find($reporte_id);

        $grupo = $this->input->get('filtro_grupo');
        $usuario = $this->input->get('filtro_usuario');
        $desde = $this->input->get('filtro_desde');
        $hasta = $this->input->get('filtro_hasta');
        $updated_at_desde = $this->input->get('desdeCambio');
        $updated_at_hasta = $this->input->get('hastaCambio');
        $pendiente = $this->input->get('estado');

        if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos';
            exit;
        }

        $cantidad = $reporte->contar_generar_completo($grupo, $usuario, $desde, $hasta, $updated_at_desde, $updated_at_hasta, $pendiente);
        $cuenta_segun_dominio = Cuenta::cuentaSegunDominio();
        $cantidad_limite = Doctrine_Query::create()
                ->from('Parametro p')
                ->where('p.cuenta_id = ? AND p.clave = ?', array($cuenta_segun_dominio->id, 'reporte_limite_permitido_completo'))
                ->fetchOne();
        $respuesta = new stdClass();
        if ($cantidad > $cantidad_limite->valor) {
            $respuesta->error = true;
            $respuesta->msg = REPORT_LIMIT_MESSAGE;
        } else {
            $respuesta->error = false;
        }
        if (!$respuesta->error) {
            $cantidad_maxima = Doctrine_Query::create()
                    ->from('Parametro p')
                    ->where('p.cuenta_id = ? AND p.clave = ?', array($cuenta_segun_dominio->id, 'reporte_completo_cantidad_maxima'))
                    ->fetchOne();
            if (!$cantidad_maxima) {
                $cantidad_maxima = -1;
            } else {
                $cantidad_maxima = $cantidad_maxima->valor;
            }
            if ($cantidad > $cantidad_maxima) {
                $respuesta->email = true;
            } else {
                $respuesta->email = false;
            }
        }

        echo json_encode($respuesta);
    }

    //metodo que atiende el boton generar en el caso que se este en generación
    //del reporte completo por email
    public function ver_completo_email($reporte_id) {
        $reporte = Doctrine::getTable('Reporte')->find($reporte_id);
        if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos';
            exit;
        }
        //llama al command line para que se ejecute en el background
        $filtro_grupo = $this->input->get('filtro_grupo');
        $filtro_usuario = $this->input->get('filtro_usuario');
        $filtro_desde = $this->input->get('filtro_desde');
        $filtro_hasta = $this->input->get('filtro_hasta');
        $email = $this->input->get('email');
        $updated_at_desde = $this->input->get('desdeCambio');
        $updated_at_hasta = $this->input->get('hastaCambio');
        $pendiente = $this->input->get('estado');

        //ejecuta el comando en background con dev/null &
        $arr = array(
            "reporte_id" => $reporte_id,
            "filtro_grupo" => $filtro_grupo,
            "filtro_usuario" => $filtro_usuario,
            "filtro_desde" => $filtro_desde,
            "filtro_hasta" => $filtro_hasta,
            "email" => $email,
            "updated_at_desde" => $updated_at_desde,
            "updated_at_hasta" => $updated_at_hasta,
            "pendiente" => $pendiente,
        );

        //$comando = 'php index.php tasks/reportecompleto generar "'. $reporte_id.'"  "'. $filtro_grupo.'" "'. $filtro_usuario.'" "'. $filtro_desde.'" "'. $filtro_hasta.'" "'. $email.'" > /dev/null &';


        $data = json_encode($arr);
        $data = str_replace('=', '', base64_encode($data));
        $comando = 'php index.php tasks/reportecompleto generar "' . $data . '" > /dev/null &';
        exec($comando);
    }

    //metodo que atiende el boton generar en el caso que se este en generación
    //del reporte completo por email
    public function ver_basico_email($reporte_id) {
        $reporte = Doctrine::getTable('Reporte')->find($reporte_id);
        if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos';
            exit;
        }
        //llama al command line para que se ejecute en el background
        $filtro_desde = $this->input->get('filtro_desde');
        $filtro_hasta = $this->input->get('filtro_hasta');
        $email = $this->input->get('email');
        $updated_at_desde = $this->input->get('desdeCambio');
        $updated_at_hasta = $this->input->get('hastaCambio');
        $pendiente = $this->input->get('estado');

        //ejecuta el comando en background con dev/null &
        $arr = array(
            "reporte_id" => $reporte_id,
            "filtro_desde" => $filtro_desde,
            "filtro_hasta" => $filtro_hasta,
            "email" => $email,
            "updated_at_desde" => $updated_at_desde,
            "updated_at_hasta" => $updated_at_hasta,
            "pendiente" => $pendiente,
        );

        //$comando = 'php index.php tasks/reportecompleto generar "'. $reporte_id.'"  "'. $filtro_grupo.'" "'. $filtro_usuario.'" "'. $filtro_desde.'" "'. $filtro_hasta.'" "'. $email.'" > /dev/null &';

        $data = json_encode($arr);
        $data = str_replace('=', '', base64_encode($data));
        $comando = 'php index.php tasks/reportebasico generar "' . $data . '" > /dev/null &';
        exec($comando);
    }

    //metodo que atiende la generación del reporte cuando se genera online
    public function ver($reporte_id) {
        $reporte = Doctrine::getTable('Reporte')->find($reporte_id);

        $desde = $this->input->get('filtro_desde');
        $hasta = $this->input->get('filtro_hasta');
        $updated_at_desde = $this->input->get('desdeCambio') ? $this->input->get('desdeCambio') : false;
        $updated_at_hasta = $this->input->get('hastaCambio') ? $this->input->get('hastaCambio') : false;
        $pendiente = $this->input->get('estado');

        if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos';
            exit;
        }

        $tiene_permisos_ejecutar = $reporte->verificar_permisos_backend(UsuarioBackendSesion::usuario());

        if (!$tiene_permisos_ejecutar) {
            echo 'Usuario no tiene permisos';
            exit;
        }

        if ($reporte->tipo == 'completo') {
            $grupo = $this->input->get('filtro_grupo');
            $usuario = $this->input->get('filtro_usuario');
            $reporte->generar_completo($grupo, $usuario, $desde, $hasta, $updated_at_desde, $updated_at_hasta, $pendiente);
        } else {
            $reporte->generar_basico($desde, $hasta, $updated_at_desde, $updated_at_hasta, $pendiente);
        }
    }

    public function ver_reporte_usuario() {
        $grupo = $this->input->get('filtro_grupo');
        $usuario = $this->input->get('filtro_usuario');
        $desde = $this->input->get('created_at_desde');
        $hasta = $this->input->get('created_at_hasta');
        $updated_at_desde = $this->input->get('updated_at_desde');
        $updated_at_hasta = $this->input->get('updated_at_hasta');
        $pendiente = $this->input->get('pendiente');
        set_time_limit(1600);
        ini_set('memory_limit', '-1');

        $CI = & get_instance();
        $CI->load->library('Excel_XML');

        $header = array('Tramite Id', 'Proceso Nombre', 'Etapa', 'Estado', 'Fecha Creacion', 'Fecha Finalizada', 'Usuario', 'Nombre');

        $excel[] = $header;

        $tramites_query = Doctrine_Query::create()
                ->select('t.*, p.*, e.*')
                ->from('Tramite t, t.Proceso p, t.Etapas e, e.Usuario u')
                ->where('u.cuenta_id = ?', UsuarioBackendSesion::usuario()->cuenta_id)
                ->andWhere('u.registrado = 1');

        if ($grupo) {
            $tramites_query->andWhere('e.Tarea.grupos_usuarios REGEXP ? OR e.Tarea.grupos_usuarios REGEXP ? OR e.Tarea.grupos_usuarios REGEXP ? OR e.Tarea.grupos_usuarios REGEXP ?', array('^' . $grupo . ',', ',' . $grupo . ',', '^' . $grupo . '$', ',' . $grupo . '$'));
        }

        if ($usuario) {
            $tramites_query->andWhere('u.id = ?', $usuario);
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
            $tramites_query->andWhere('e.updated_at >=?', date("Y-m-d H:i:s", strtotime($updated_at_desde)));
        }

        if ($updated_at_hasta) {
            $updated_at_hasta = $updated_at_hasta . ' 23:59:59';
            $tramites_query->andWhere('e.updated_at <= ?', date("Y-m-d H:i:s", strtotime($updated_at_hasta)));
        }

        if ($pendiente !== -1) {
            $tramites_query->andWhere('t.pendiente = ?', $pendiente);
        }

        $tramites = $tramites_query->orderBy('t.id', 'desc')
                ->orderBy('e.created_at', 'desc')
                ->fetchArray();

        foreach ($tramites as $t) {
            foreach ($t['Etapas'] as $etapa) {
                $etapa_linea = Doctrine_Core::getTable('Etapa')->find($etapa['id']);

                $usuario_asignado = quitar_caracteres_especiales($etapa_linea->Usuario->usuario);

                $tramite_id = $t['id'];
                $proceso_nombre = quitar_caracteres_especiales($t['Proceso']['nombre']);
                $etapa = quitar_caracteres_especiales($etapa_linea->Tarea->nombre);
                $fecha_creacion = $etapa_linea->created_at;
                $fecha_finalizada = $etapa_linea->ended_at;
                $etapa_estado = $etapa_linea->pendiente ? 'pendiente' : 'completado';
                $nombre_apellido = quitar_caracteres_especiales($etapa_linea->Usuario->nombres . ' ' . $etapa_linea->Usuario->apellido_paterno);

                $row = array(
                    $tramite_id,
                    $proceso_nombre,
                    $etapa,
                    $etapa_estado,
                    $fecha_creacion,
                    $fecha_finalizada,
                    $usuario_asignado,
                    $nombre_apellido
                );

                $excel[] = $row;
            }
        }
        $CI->excel_xml->addArray($excel);
        $CI->excel_xml->generateXML('actividad_usuarios_' . date("m_d_y"));
    }

    public function crear($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'No tiene permisos para crear este documento';
            exit;
        }

        $data['edit'] = FALSE;
        $data['proceso'] = $proceso;
        $data['grupos_usuarios'] = Doctrine::getTable('GrupoUsuarios')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);

        $usuarios_frontend = Doctrine::getTable('Usuario')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);
        $usuarios_backend = Doctrine::getTable('UsuarioBackend')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);

        $array_usuarios_backend = array();
        foreach ($usuarios_backend as $usuario) {
            array_push($array_usuarios_backend, $usuario);
        }

        foreach ($usuarios_frontend as $usuario_f) {
            $cont = 0;
            sort($array_usuarios_backend);
            foreach ($array_usuarios_backend as $usuario_b) {
                if (trim($usuario_b->usuario) == trim($usuario_f->usuario)) {
                    unset($array_usuarios_backend[$cont]);
                    break;
                }
                $cont++;
            }
        }

        $data['usuarios_frontend_y_backend'] = $usuarios_frontend;
        $data['usuarios_solo_backend'] = $array_usuarios_backend;

        $data['title'] = 'Edición de Documento';
        $data['content'] = 'backend/reportes/editar';

        $this->load->view('backend/template', $data);
    }

    public function editar($reporte_id) {
        $reporte = Doctrine::getTable('Reporte')->find($reporte_id);

        if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'No tiene permisos para editar este documento';
            exit;
        }

        $data['reporte'] = $reporte;
        $data['edit'] = TRUE;
        $data['proceso'] = $reporte->Proceso;
        $data['grupos_usuarios'] = Doctrine::getTable('GrupoUsuarios')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);

        $usuarios_frontend = Doctrine::getTable('Usuario')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);
        $usuarios_backend = Doctrine::getTable('UsuarioBackend')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);

        $array_usuarios_backend = array();
        foreach ($usuarios_backend as $usuario) {
            array_push($array_usuarios_backend, $usuario);
        }

        foreach ($usuarios_frontend as $usuario_f) {
            $cont = 0;
            sort($array_usuarios_backend);
            foreach ($array_usuarios_backend as $usuario_b) {
                if (trim($usuario_b->usuario) == trim($usuario_f->usuario)) {
                    unset($array_usuarios_backend[$cont]);
                    break;
                }
                $cont++;
            }
        }

        $data['usuarios_frontend_y_backend'] = $usuarios_frontend;
        $data['usuarios_solo_backend'] = $array_usuarios_backend;

        $data['title'] = 'Edición de Reporte';
        $data['content'] = 'backend/reportes/editar';

        $this->load->view('backend/template', $data);
    }

    public function editar_form($reporte_id = NULL) {
        $reporte = NULL;
        $op = "update";
        if ($reporte_id) {
            $reporte = Doctrine::getTable('Reporte')->find($reporte_id);
        } else {
            $reporte = new Reporte();
            $reporte->proceso_id = $this->input->post('proceso_id');
            $op = "insert";
        }

        if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar este documento.';
            exit;
        }

        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        $this->form_validation->set_rules('campos', 'Campos[]', 'required');

        $respuesta = new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $reporte->nombre = $this->input->post('nombre');
            $reporte->tipo = $this->input->post('tipo');
            $reporte->campos = $this->input->post('campos');
            $reporte->setGruposUsuariosFromArray($this->input->post('grupos_usuarios_permiso'));
            $reporte->setUsuariosFromArray($this->input->post('usuarios_permiso'));
            $reporte->save();
            auditar('Reporte', $op, $reporte->id, UsuarioBackendSesion::usuario()->usuario);
            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/reportes/listar/' . $reporte->Proceso->id);
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function eliminar($reporte_id) {
        $reporte = Doctrine::getTable('Reporte')->find($reporte_id);

        if ($reporte->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para eliminar este documento.';
            exit;
        }

        $proceso = $reporte->Proceso;
        auditar('Reporte', 'delete', $reporte->id, UsuarioBackendSesion::usuario()->usuario);
        $reporte->delete();

        redirect('backend/reportes/listar/' . $proceso->id);
    }

    public function reporte_satisfaccion($reporte_id = null) {
        $page_size = MAX_REGISTROS_PAGINA;
        $page_num = (int) ($this->input->get('p') == null ? 1 : $this->input->get('p'));
        $offset = $page_size * ($page_num - 1);

        if (!UsuarioBackendSesion::usuario()->cuenta_id) {
            redirect('/backend');
        }

        if ($reporte_id) {
            $reporte = Doctrine::getTable('ReporteSatisfaccion')->find($reporte_id);
            $reporte->reporte = json_decode($reporte->reporte);

            $data['detalle'] = $reporte;
            $data['title'] = 'Reporte de satisfacción';
            $data['content'] = 'backend/reportes/reporte_satisfaccion_detalle';

            $this->load->view('backend/template', $data);
        } else {
            $data['reportes'] = Doctrine_Query::create()
                    ->from('ReporteSatisfaccion rs')
                    ->orderBy('rs.id')
                    ->offset($offset)
                    ->limit($page_size)
                    ->orderBy('rs.id DESC')
                    ->execute();

            $data['reportes_chart'] = Doctrine_Query::create()
                    ->from('ReporteSatisfaccion rs')
                    ->orderBy('rs.id')
                    ->limit(100)
                    ->orderBy('rs.id DESC')
                    ->execute();

            $total = Doctrine_Query::create()
                    ->from('ReporteSatisfaccion rs')
                    ->count();

            $data['reportes_total'] = $total;
            $data['pagina_actual'] = $page_num;
            $data['tam_pagina'] = $page_size;

            $data['title'] = 'Reporte de satisfacción';
            $data['content'] = 'backend/reportes/reporte_satisfaccion';

            $this->load->view('backend/template', $data);
        }
    }

    public function reporte_usuario() {
        $grupos = Doctrine::getTable('GrupoUsuarios')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);
        $usuarios = Doctrine::getTable('Usuario')->findByCuentaId(UsuarioBackendSesion::usuario()->cuenta_id);

        $data['grupos'] = $grupos;
        $data['usuarios'] = $usuarios;

        $data['title'] = 'Reportes de Usuario';
        $data['content'] = 'backend/reportes/reporte_usuario';

        $this->load->view('backend/template', $data);
    }
    
}
