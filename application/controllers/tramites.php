<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tramites extends MY_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->helper('cookies_helper');
        $this->load->helper('trazabilidad_helper');
        $this->load->helper('eliminartramite_helper');


        UsuarioSesion::limpiar_sesion();

        if (UsuarioSesion::usuario_con_empresas_luego_login()) {
            redirect('autenticacion/login_empresa');
        }
    }

    public function index() {
        redirect('etapas/inbox');
    }

    public function participados() {

        $usuario = UsuarioSesion::usuario();

        if (!$usuario->registrado) {
            redirect('autenticacion/login');
        }

        $usuario_id = $usuario->id;
        $cuenta_segun_dominio = Cuenta::cuentaSegunDominio();

        $paginado = Doctrine_Query::create()
                ->from('Parametro p')
                ->where('p.cuenta_id = ? AND p.clave = ?', array($cuenta_segun_dominio->id, 'resultados_por_pagina'))
                ->fetchOne();

        if ($paginado) {
            $per_page = $paginado->valor;
        } else {
            $per_page = 50;
        }

        $offset = $this->input->get('offset');

        $resultado_query = Doctrine::getTable('Tramite')->findParticipadosConPaginacion($usuario_id, $cuenta_segun_dominio, $offset, $per_page);
        $cantidad_tramites = $resultado_query->cantidad;

        $this->load->library('pagination');
        $this->pagination->initialize(array(
            'base_url' => site_url('tramites/participados?'),
            'total_rows' => $cantidad_tramites,
            'per_page' => $per_page
        ));

        $query = $this->input->post('termino');

        if ($query) {
            $tramites_particiapados = $resultado_query->tramites;
            $tramites_id = [];

            foreach ($tramites_particiapados as $tramite_participado) {
                array_push($tramites_id, $tramite_participado->id);
            }

            $doctrine_query = Doctrine_Query::create()
                    ->from('Etapa e, e.DatosSeguimiento d, e.Tramite t')
                    ->having('COUNT(d.id) > 0 OR COUNT(e.id) > 1')
                    ->orderBy('e.id desc')
                    ->groupBy('t.id');

            $this->load->library('sphinxclient');
            $this->sphinxclient->setServer($this->config->item('sphinx_host'), $this->config->item('sphinx_port'));

            $this->sphinxclient->setFilter('tramite_id', $tramites_id);
            $result = $this->sphinxclient->query($query, 'etapas');

            if ($result['total'] > 0) {
                $matches = array_keys($result['matches']);
                $doctrine_query->whereIn('e.id', $matches);
            } else {
                $doctrine_query->where('0');
            }

            $etapas = $doctrine_query->execute();
            if (count($etapas) < 1) {
                $tramites = 0;
            } else {
                $tramites = [];
                foreach ($etapas as $etapa) {
                    $tramite = Doctrine::getTable('Tramite')->find($etapa->tramite_id);
                    array_push($tramites, $tramite);
                }
            }
        } else {
            $tramites = $resultado_query->tramites;
        }

        $data['tramites'] = $tramites;
        $data['sidebar'] = 'participados';
        $data['content'] = 'tramites/participados';
        $data['title'] = 'Bienvenido';
        $this->load->view('template', $data);
    }

    public function busqueda_filtros_participados() {
        $usuario = UsuarioSesion::usuario();

        $usuario_id = $usuario->id;
        $cuenta_segun_dominio = Cuenta::cuentaSegunDominio();

        $busqueda_modificacion_desde = $this->input->get('busqueda_modificacion_desde');
        $busqueda_modificacion_hasta = $this->input->get('busqueda_modificacion_hasta');
        $busqueda_id_tramite = $this->input->get('busqueda_id_tramite');
        $busqueda_etapa = $this->input->get('busqueda_etapa');
        $busqueda_grupo = $this->input->get('busqueda_grupo');
        $busqueda_nombre = $this->input->get('busqueda_nombre');
        $busqueda_documento = $this->input->get('busqueda_documento');

        $orderby = $this->input->get('orderby') && $this->input->get('orderby') != '' ? $this->input->get('orderby') : 'updated_at';
        $direction = $this->input->get('direction') && $this->input->get('direction') != '' ? $this->input->get('direction') == 'desc' ? 'desc' : 'asc' : 'desc';

        $paginado = Doctrine_Query::create()
                ->from('Parametro p')
                ->where('p.cuenta_id = ? AND p.clave = ?', array($cuenta_segun_dominio->id, 'resultados_por_pagina'))
                ->fetchOne();

        if ($paginado) {
            $per_page = $paginado->valor;
        } else {
            $per_page = 50;
        }

        $offset = $this->input->get('offset');

        $resultado_query = Doctrine::getTable('Tramite')->findParticipadosFiltro($usuario_id, $cuenta_segun_dominio, $orderby, $direction, $busqueda_id_tramite, $busqueda_etapa, $busqueda_grupo, $busqueda_nombre, $busqueda_documento, $busqueda_modificacion_desde, $busqueda_modificacion_hasta, $per_page, $offset, false);

        $query = $this->input->get('termino');

        if ($query) {
            $tramites_particiapados = $resultado_query;
            $tramites_id = [];

            foreach ($tramites_particiapados as $tramite_participado) {
                array_push($tramites_id, $tramite_participado->id);
            }
            $tramites = 0;
            $cantidad_tramites = 0;

            if ($tramites_id && count($tramites_id) > 0) {
                $doctrine_query = Doctrine_Query::create()
                        ->from('Etapa e, e.DatosSeguimiento d, e.Tramite t')
                        ->having('COUNT(d.id) > 0 OR COUNT(e.id) > 1')
                        ->orderBy('e.id desc')
                        ->groupBy('t.id');

                $this->load->library('sphinxclient');
                $this->sphinxclient->setServer($this->config->item('sphinx_host'), $this->config->item('sphinx_port'));

                $this->sphinxclient->setFilter('tramite_id', $tramites_id);
                $result = $this->sphinxclient->query($query, 'etapas');

                if ($result['total'] > 0) {
                    $matches = array_keys($result['matches']);
                    $doctrine_query->whereIn('e.id', $matches);
                } else {
                    $doctrine_query->where('0');
                }

                $etapas = $doctrine_query->execute();
                if (count($etapas) < 1) {
                    $tramites = 0;
                } else {
                    $tramites = [];
                    foreach ($etapas as $etapa) {
                        $tramite = Doctrine::getTable('Tramite')->find($etapa->tramite_id);
                        array_push($tramites, $tramite);
                    }
                }

                $cantidad_tramites = count($tramites);
            }
        } else {
            $cantidad_tramites = Doctrine::getTable('Tramite')->findParticipadosFiltro($usuario_id, $cuenta_segun_dominio, $orderby, $direction, $busqueda_id_tramite, $busqueda_etapa, $busqueda_grupo, $busqueda_nombre, $busqueda_documento, $busqueda_modificacion_desde, $busqueda_modificacion_hasta, $per_page, $offset, true);
            $tramites = $resultado_query;
        }


        $this->load->library('pagination');

        $this->pagination->initialize(array(
            'base_url' => site_url('tramites/busqueda_filtros_participados?' .
                    'busqueda_id_tramite=' . $busqueda_id_tramite .
                    '&busqueda_etapa=' . $busqueda_etapa .
                    '&busqueda_grupo=' . $busqueda_grupo .
                    '&busqueda_nombre=' . $busqueda_nombre .
                    '&busqueda_documento=' . $busqueda_documento .
                    '&busqueda_modificacion_desde=' . $busqueda_modificacion_desde .
                    '&busqueda_modificacion_hasta=' . $busqueda_modificacion_hasta),
            'total_rows' => $cantidad_tramites,
            'per_page' => $per_page
        ));


        $data['tramites'] = $tramites;

        $data['busqueda_id_tramite'] = $busqueda_id_tramite;
        $data['busqueda_etapa'] = $busqueda_etapa;
        $data['busqueda_grupo'] = $busqueda_grupo;
        $data['busqueda_nombre'] = $busqueda_nombre;
        $data['busqueda_documento'] = $busqueda_documento;
        $data['busqueda_modificacion_desde'] = $busqueda_modificacion_desde;
        $data['busqueda_modificacion_hasta'] = $busqueda_modificacion_hasta;

        $data['sidebar'] = 'participados';
        $data['content'] = 'tramites/participados';
        $data['title'] = 'Bienvenido';
        $this->load->view('template', $data);
    }

    public function reportes() {
        if (!UsuarioSesion::usuario()->acceso_reportes || !UsuarioSesion::usuario()->cuenta_id) {
            redirect('/');
            exit;
        }

        $data['tramites'] = Doctrine::getTable('Tramite')->findParticipados(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio(), 0, 0);

        $data['sidebar'] = 'reportes';
        $data['content'] = 'tramites/reportes';
        $data['title'] = 'Bienvenido';
        $this->load->view('template', $data);
    }

    public function reportes_procesos() {
        if (!UsuarioSesion::usuario()->acceso_reportes || !UsuarioSesion::usuario()->cuenta_id) {
            redirect('/');
            exit;
        }

        $data['procesos'] = $procesos = Doctrine_Query::create()
                ->from('Proceso p')
                ->where('p.cuenta_id = ?', UsuarioSesion::usuario()->cuenta_id)
                ->andWhere('p.nombre != ?', 'BLOQUE')
                ->orderBy('p.id')
                ->execute();

        $data['sidebar'] = 'reportes';
        $data['content'] = 'tramites/reportes_procesos';
        $data['title'] = 'Bienvenido';
        $this->load->view('template', $data);
    }

    public function generar_basico($reporte_id) {

        $reporte = Doctrine::getTable('Reporte')->find($reporte_id);

        if (!UsuarioSesion::usuario()->acceso_reportes || !UsuarioSesion::usuario()->cuenta_id) {
            redirect('/');
            exit;
        }

        $desde = $this->input->get('filtro_desde');
        $hasta = $this->input->get('filtro_hasta');
        $updated_at_desde = $this->input->get('desdeCambio');
        $updated_at_hasta = $this->input->get('hastaCambio');
        $pendiente = $this->input->get('estado');
        
        $cantidad = $reporte->contar_generar_basico($desde, $hasta, $updated_at_desde,$updated_at_hasta,$pendiente);
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

            $respuesta = new stdClass();

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

        if (!UsuarioSesion::usuario()->acceso_reportes || !UsuarioSesion::usuario()->cuenta_id) {
            redirect('/');
            exit;
        }

        $grupo = $this->input->get('filtro_grupo');
        $usuario = $this->input->get('filtro_usuario');
        $desde = $this->input->get('filtro_desde');
        $hasta = $this->input->get('filtro_hasta');
        $updated_at_desde = $this->input->get('desdeCambio');
        $updated_at_hasta = $this->input->get('hastaCambio');
        $pendiente = $this->input->get('estado');

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

            $respuesta = new stdClass();
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

        if (!UsuarioSesion::usuario()->acceso_reportes || !UsuarioSesion::usuario()->cuenta_id) {
            redirect('/');
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
        //show_error($comando);
        exec($comando);
    }

    //metodo que atiende el boton generar en el caso que se este en generación
    //del reporte completo por email
    public function ver_basico_email($reporte_id) {
        $reporte = Doctrine::getTable('Reporte')->find($reporte_id);

        if (!UsuarioSesion::usuario()->acceso_reportes || !UsuarioSesion::usuario()->cuenta_id) {
            redirect('/');
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
        //show_error($comando);
        exec($comando);
    }

    public function ver_reportes($proceso_id) {
        if (!UsuarioSesion::usuario()->acceso_reportes || !UsuarioSesion::usuario()->cuenta_id) {
            redirect('/');
            exit;
        }

        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);
        $reportes = Doctrine_Query::create()
                ->from('Reporte r')
                ->where('r.proceso_id = ?', $proceso_id)
                ->execute();

        if ($proceso->cuenta_id != UsuarioSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para listar los reportes de este proceso';
            exit;
        }

        $grupos = Doctrine::getTable('GrupoUsuarios')->findByCuentaId(UsuarioSesion::usuario()->cuenta_id);

        $reportes_con_permisos = array();
        foreach ($reportes as $reporte) {
            if ($reporte->verificar_permisos_frontend(UsuarioSesion::usuario())) {
                array_push($reportes_con_permisos, $reporte);
            }
        }

        $usuarios = Doctrine_Query::create()
                ->from('Usuario u')
                ->where('u.registrado = ?', 1)
                ->andWhere('u.cuenta_id=?', UsuarioSesion::usuario()->cuenta_id)
                ->execute();

        $data['proceso'] = $proceso;
        $data['reportes'] = $reportes_con_permisos;
        $data['grupos'] = $grupos;
        $data['usuarios'] = $usuarios;

        $data['sidebar'] = 'reportes';
        $data['content'] = 'tramites/reportes_procesos_listado';
        $data['title'] = 'Bienvenido';
        $this->load->view('template', $data);
    }

    public function ver_reporte($reporte_id) {
        if (!UsuarioSesion::usuario()->acceso_reportes || !UsuarioSesion::usuario()->cuenta_id) {
            redirect('/');
            exit;
        }

        $reporte = Doctrine::getTable('Reporte')->find($reporte_id);

        if ($reporte->Proceso->cuenta_id != UsuarioSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos';
            exit;
        }

        $tiene_permisos_ejecutar = $reporte->verificar_permisos_frontend(UsuarioSesion::usuario());

        if (!$tiene_permisos_ejecutar) {
            echo 'Usuario no tiene permisos';
            exit;
        }

        $desde = $this->input->get('filtro_desde');
        $hasta = $this->input->get('filtro_hasta');
        $updated_at_desde = $this->input->get('desdeCambio') ? $this->input->get('desdeCambio') : false;
        $updated_at_hasta = $this->input->get('hastaCambio') ? $this->input->get('hastaCambio') : false;
        $pendiente = $this->input->get('estado');

        if ($reporte->tipo == 'completo') {
            $grupo = $this->input->get('filtro_grupo');
            $usuario = $this->input->get('filtro_usuario');
            $reporte->generar_completo($grupo, $usuario, $desde, $hasta, $updated_at_desde, $updated_at_hasta, $pendiente);
        } else {
            $reporte->generar_basico($desde, $hasta, $updated_at_desde, $updated_at_hasta, $pendiente);
        }
    }

    public function generar_reporte($tramite_id) {
        if (UsuarioSesion::usuario()->acceso_reportes && UsuarioSesion::usuario()->cuenta_id) {
            $tramite = Doctrine_Query::create()
                    ->from('Tramite t')
                    ->where('t.id = ?', $tramite_id)
                    ->execute();

            $tramite = $tramite[0];

            set_time_limit(600);

            $CI = & get_instance();

            $CI->load->library('Excel_XML');

            $campos = array();
            /* foreach($tramite->Proceso->getNombresDeDatos() as $c) {
              $campos[]=$c;
              } */
            foreach ($tramite->Proceso->getNombresDeDatosTramite($tramite_id) as $c) {
                $campos[] = $c;
            }

            $header = array_merge(array('id', 'estado', 'etapa_actual', 'fecha_inicio', 'fecha_modificacion', 'fecha_termino'), $campos);

            $excel[] = $header;

            $etapas_actuales = $tramite->getEtapasActuales();
            $etapas_actuales_arr = array();
            foreach ($etapas_actuales as $e)
                $etapas_actuales_arr[] = $e->Tarea->nombre;
            $etapas_actuales_str = implode(',', $etapas_actuales_arr);
            $row = array($tramite->id, $tramite->pendiente ? 'pendiente' : 'completado', $etapas_actuales_str, $tramite->created_at, $tramite->updated_at, $tramite->ended_at);

            $datos = Doctrine_Core::getTable('DatoSeguimiento')->findByTramite($tramite->id);

            foreach ($datos as $d) {
                if (in_array($d['nombre'], $campos)) {
                    $val = $d->valor;
                    if (!is_string($val))
                        $val = json_encode($val, JSON_UNESCAPED_UNICODE);

                    $colindex = array_search($d->nombre, $header);
                    $row[$colindex] = $val;
                }
            }

            //Rellenamos con espacios en blanco los campos que no existen.
            for ($i = 0; $i < count($row); $i++)
                if (!isset($row[$i]))
                    $row[$i] = '';

            //Ordenamos
            ksort($row);

            $excel[] = $row;

            $CI->excel_xml->addArray($excel);
            $CI->excel_xml->generateXML('Reporte');
        }
    }

    public function disponibles() {
        //verifico si el usuario pertenece el grupo MesaDeEntrada y esta actuando como un ciudadano
        if (!$this->input->get('funcionario_ciudadano') == 1) {
            $this->session->unset_userdata('id_usuario_ciudadano');
        }
        if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
            $usuario_sesion = Doctrine_Query::create()
                    ->from('Usuario u')
                    ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                    ->fetchOne();
            $funcionario_actuando_como_ciudadano = true;
        } else {
            $usuario_sesion = UsuarioSesion::usuario();
            $funcionario_actuando_como_ciudadano = false;
        }

        $cuenta = Cuenta::cuentaSegunDominio();
        UsuarioSesion::registrar_acceso($cuenta->id);

        $orderby = 'nombre';
        $direction = $this->input->get('direction') && $this->input->get('direction') != '' ? $this->input->get('direction') == 'desc' ? 'desc' : 'asc' : 'desc';

        $data['procesos'] = Doctrine::getTable('Proceso')->findProcesosDisponiblesParaIniciar($usuario_sesion->id, $cuenta, $orderby, $direction);

        $data['orderby'] = $orderby;
        $data['direction'] = $direction;

        if (!$funcionario_actuando_como_ciudadano) {
            $data['sidebar'] = 'disponibles';
            $data['content'] = 'tramites/disponibles';
        } else {
            $data['sidebar'] = 'busqueda_ciudadano';
            $data['content'] = 'tramites/disponibles_ciudadano';
            $data['ciudadano'] = $usuario_sesion;
        }
        $data['funcionario_actuando_como_ciudadano'] = $funcionario_actuando_como_ciudadano;
        $data['usuario_nombres'] = $usuario_sesion->nombres . ' ' . $usuario_sesion->apellido_paterno;

        //$data['content'] = 'tramites/disponibles';
        $data['title'] = 'Trámites disponibles a iniciar';
        $this->load->view('template', $data);
    }

    //fuerza el inicio de un tramite sin importar qeu existan instancias previas
    public function iniciar_f($proceso_id) {
        $cuenta = Cuenta::cuentaSegunDominio();
        $proceso_param = Doctrine::getTable('Proceso')->find($proceso_id);
        if (!isset($proceso_param->id)) {
            $data['error'] = 'No existe el proceso con id "' . $proceso_id . '".';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view("template_iframe", $data);
            return;
        }
        $proces_activo = Doctrine::getTable('Proceso')->findIdProcesoActivoRoot($proceso_param->root, $cuenta->id);
        if (!$proces_activo->id) {
            $data['error'] = 'No existe una versión activa para el proceso con id "' . $proceso_id . '".';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view("template_iframe", $data);
            return;
        }
        $proceso_id = $proces_activo->id;
        //verifico si el usuario pertenece el grupo MesaDeEntrada y esta actuando como un ciudadano
        if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
            $usuario_sesion = Doctrine_Query::create()
                    ->from('Usuario u')
                    ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                    ->fetchOne();
            $funcionario_actuando_como_ciudadano = true;
        } else {
            $usuario_sesion = UsuarioSesion::usuario();
            $funcionario_actuando_como_ciudadano = false;
        }

        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if (!$proceso->canUsuarioIniciarlo($usuario_sesion->id)) {
            redirect(site_url());
        }

        //simepre se inicia el tramite sin importar que existan previos
        $tramite = new Tramite();
        $tramite->iniciar($proceso->id);
        $qs = $this->input->server('QUERY_STRING');
        redirect('etapas/ejecutar/' . $tramite->getEtapasActuales()->get(0)->id . ($qs ? '?' . $qs : ''));
    }

    public function iniciar($proceso_id) {
        /* error_reporting(E_ALL);
          ini_set('display_errors', '1'); */
        $cuenta = Cuenta::cuentaSegunDominio();
        $proceso_param = Doctrine::getTable('Proceso')->find($proceso_id);
        if (!isset($proceso_param->id)) {
            $data['error'] = 'No existe el proceso con id "' . $proceso_id . '".';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view("template_iframe", $data);
            return;
        }
        $proces_activo = Doctrine::getTable('Proceso')->findIdProcesoActivoRoot($proceso_param->root, $cuenta->id);
        if (!$proces_activo->id) {
            $data['error'] = 'No existe una versión activa para el proceso con id "' . $proceso_id . '".';
            $data['content'] = 'etapas/error';
            $data['title'] = $data['error'];
            $this->load->view("template_iframe", $data);
            return;
        }
        $proceso_id = $proces_activo->id;
        if (!$this->verificar_permisos_ejecucion_grep($proceso_id)) {
            $data['proceso'] = Doctrine_Query::create()
                    ->from('Proceso p')
                    ->where('p.id = ?', $proceso_id)
                    ->fetchOne();
            $data['content'] = 'etapas/permisos_ejecucion_grep';
            $this->load->view('template', $data);
            return;
        }

        //verifico si el usuario pertenece el grupo MesaDeEntrada y esta actuando como un ciudadano
        if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
            $usuario_sesion = Doctrine_Query::create()
                    ->from('Usuario u')
                    ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                    ->fetchOne();
            $funcionario_actuando_como_ciudadano = true;
        } else {
            $usuario_sesion = UsuarioSesion::usuario();
            $funcionario_actuando_como_ciudadano = false;
        }

        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if (!$proceso->canUsuarioIniciarlo($usuario_sesion->id)) {
            redirect(site_url());
        }

        //Vemos si es que usuario ya tiene un tramite de proceso_id ya iniciado, y que se encuentre en su primera etapa.
        //Si es asi, hacemos que lo continue. Si no, creamos uno nuevo
        $tramite = Doctrine_Query::create()
                ->from('Tramite t, t.Proceso p, t.Etapas e, e.Tramite.Etapas hermanas')
                ->where('t.pendiente=1 AND p.id = ? AND e.usuario_id = ?', array($proceso_id, $usuario_sesion->id))
                ->groupBy('t.id')
                ->having('COUNT(hermanas.id) = 1')
                ->fetchOne();

        //print_r($tramite);
        if (!$tramite || !$tramite->getEtapasActuales()->get(0)->id) {
            //show_error($usuario_sesion->id);
            //sin tramite  ya iniciado, se inicia
            $tramite = new Tramite();
            $tramite->iniciar($proceso->id);
            $qs = $this->input->server('QUERY_STRING');
            redirect('etapas/ejecutar/' . $tramite->getEtapasActuales()->get(0)->id . ($qs ? '?' . $qs : ''));
        } else {
            //con instancias anteriores se da la opcion que seleccione continuar o iniciar
            $tramites = Doctrine_Query::create()
                    ->from('Tramite t, t.Proceso p, t.Etapas e, e.Tramite.Etapas hermanas')
                    ->where('t.pendiente=1 AND p.id = ? AND e.pendiente = 1 AND e.usuario_id = ?', array($proceso_id, $usuario_sesion->id))
                    ->groupBy('t.id')
                    ->having('COUNT(hermanas.id) = 1')
                    ->execute();

            //se le debe preguntar si quiere continuar el existente o iniciar uno nuevo
            $qs = $this->input->server('QUERY_STRING');
            $data['qs'] = $qs;
            $data['tramites'] = $tramites;
            $data['proceso'] = $proceso;
            $data['sidebar'] = 'disponibles';
            $data['content'] = 'tramites/iniciados';
            $data['title'] = 'Existen Instancias del tramite inciadas';
            $this->load->view('template', $data);
        }
    }

    public function eliminar($tramite_id) {
        //verifico si el usuario pertenece el grupo MesaDeEntrada y esta actuando como un ciudadano
        if (UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
            $usuario_sesion = Doctrine_Query::create()
                    ->from('Usuario u')
                    ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
                    ->fetchOne();
        } else {
            $usuario_sesion = UsuarioSesion::usuario();
        }

        $tramite = Doctrine::getTable('Tramite')->find($tramite_id);
        if ($tramite) {
            if ($tramite->Etapas->count() > 1) {
                echo 'Tramite no se puede eliminar, ya ha avanzado mas de una etapa';
                exit;
            }
            if (!eliminarTramite($tramite_id)) {
                echo 'El trámite no se puede eliminar, tiene un pago asociado.';
                exit;
            }

            if ($usuario_sesion->id != $tramite->Etapas[0]->usuario_id) {
                redirect(site_url());
            }

            //se elimina el historial de ejecuciones
            $etapa0 = $tramite->Etapas[0];
            if ($etapa0) {
                $conn = Doctrine_Manager::connection();
                $stmt = $conn->prepare('delete from etapa_historial_ejecuciones  where etapa_id = ' . $etapa0->id);
                $stmt->execute();
            }

            $count_tramites_pendientes = Doctrine_Query::create()
                    ->from('Tramite t, t.Proceso p, t.Etapas e, e.Tramite.Etapas hermanas')
                    ->where('t.pendiente=1 AND p.id = ? AND e.usuario_id = ?', array($tramite->proceso_id, $usuario_sesion->id))
                    ->groupBy('t.id')
                    ->having('COUNT(hermanas.id) = 1')
                    ->count();
            //traza
            enviar_traza_eliminar_tramite($tramite);
            $tramite->delete();
        }
        //si se borra desde frontend instancias iniciadas de un tramite
        if ($count_tramites_pendientes == 1 && strpos($this->input->server('HTTP_REFERER'), 'iniciar')) {
            redirect('tramites/disponibles');
        } else {
            redirect($this->input->server('HTTP_REFERER'));
        }
    }

    public function verificar_permisos_ejecucion_grep($proceso_id) {

        if (UsuarioSesion::usuario_actuando_como_empresa()) {
            $documento_usuario_real = Doctrine::getTable('Usuario')->find(UsuarioSesion::usuario_actuando_como_empresa())->usuario;
            $rut_usuario_empresa = UsuarioSesion::usuario()->usuario;
            $lista_codigos_tramite_ws_grep = UsuarioSesion::ws_permisos_tramites_usuario_grep($documento_usuario_real, $rut_usuario_empresa);

            if ($lista_codigos_tramite_ws_grep) {
                $proceso = Doctrine_Query::create()->from('Proceso p')->where('p.id = ?', $proceso_id)->fetchOne();

                if (empty($proceso)) {
                    return false;
                }

                $tiene_permisos = false;

                for ($i = 0; $i < count($lista_codigos_tramite_ws_grep); $i++) {
                    if ($proceso->ProcesoTrazabilidad->proceso_externo_id == $lista_codigos_tramite_ws_grep[$i]) {
                        $tiene_permisos = true;
                        break;
                    }
                }

                return $tiene_permisos;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function firma_por_lotes($proceso_id) {

        $cuenta_segun_dominio = Cuenta::cuentaSegunDominio();
        $paginado = Doctrine_Query::create()
                ->from('Parametro p')
                ->where('p.cuenta_id = ? AND p.clave = ?', array($cuenta_segun_dominio->id, 'resultados_por_pagina'))
                ->fetchOne();

        if ($paginado) {
            $per_page = $paginado->valor;
        } else {
            $per_page = 50;
        }

        $offset = $this->input->get('offset');

        if (!$offset) {
            $offset = 0;
        }

        $busqueda_id_tramite = ($this->input->post('busqueda_id_tramite')) ? $this->input->post('busqueda_id_tramite') : $this->input->get('busqueda_id_tramite');
        $busqueda_id_etapa = ($this->input->post('busqueda_id_etapa')) ? $this->input->post('busqueda_id_etapa') : $this->input->get('busqueda_id_etapa');
        $busqueda_nombre_tarea = ($this->input->post('busqueda_nombre_tarea')) ? $this->input->post('busqueda_nombre_tarea') : $this->input->get('busqueda_nombre_tarea');
        $busqueda_modificacion_desde = ($this->input->post('busqueda_modificacion_desde')) ? $this->input->post('busqueda_modificacion_desde') : $this->input->get('busqueda_modificacion_desde');
        $busqueda_modificacion_hasta = ($this->input->post('busqueda_modificacion_hasta')) ? $this->input->post('busqueda_modificacion_hasta') : $this->input->get('busqueda_modificacion_hasta');

        $etapas = Doctrine::getTable('Etapa')->findEtapasFirmaPorLote(UsuarioSesion::usuario()->id, $proceso_id, false, $per_page, $offset, $busqueda_id_tramite, $busqueda_id_etapa, $busqueda_nombre_tarea, $busqueda_modificacion_desde, $busqueda_modificacion_hasta);
        $etapas_total = Doctrine::getTable('Etapa')->findEtapasFirmaPorLote(UsuarioSesion::usuario()->id, $proceso_id, true, null, null, $busqueda_id_tramite, $busqueda_id_etapa, $busqueda_nombre_tarea, $busqueda_modificacion_desde, $busqueda_modificacion_hasta);
        $total = count($etapas_total);
        $this->load->library('pagination');

        $this->pagination->initialize(array(
            'base_url' => site_url('tramites/firma_por_lotes/' .
                    $proceso_id . '?' .
                    (($busqueda_id_tramite) ? '&busqueda_id_tramite=' . $busqueda_id_tramite . '' : '') .
                    (($busqueda_id_etapa) ? '&busqueda_id_etapa=' . $busqueda_id_etapa . '' : '') .
                    (($busqueda_nombre_tarea) ? '&busqueda_nombre_tarea=' . $busqueda_nombre_tarea . '' : '') .
                    (($busqueda_modificacion_desde) ? '&busqueda_modificacion_desde=' . $busqueda_modificacion_desde . '' : '') .
                    (($busqueda_modificacion_hasta) ? '&busqueda_modificacion_hasta=' . $busqueda_modificacion_hasta . '' : '')),
            'total_rows' => $total,
            'per_page' => $per_page
        ));

        $data['busqueda_id_tramite'] = $busqueda_id_tramite;
        $data['busqueda_id_etapa'] = $busqueda_id_etapa;
        $data['busqueda_nombre_tarea'] = $busqueda_nombre_tarea;
        $data['busqueda_modificacion_desde'] = $busqueda_modificacion_desde;
        $data['busqueda_modificacion_hasta'] = $busqueda_modificacion_hasta;

        $data['etapas'] = $etapas;
        $data['proceso'] = Doctrine_Query::create()->from('Proceso p')->where('p.id = ?', $proceso_id)->fetchOne();
        $data['sidebar'] = 'firma_por_lotes';
        $limite = Doctrine_Query::create()->from('Parametro p')->where('p.cuenta_id = ? AND p.clave = ?', array(Cuenta::cuentaSegunDominio()->id, 'limite_firma_lotes'))->fetchOne();
        $data['limite_firma_lote'] = ($limite) ? $limite->valor : "50";
        $data['content'] = 'tramites/firma_por_lotes';
        $data['title'] = 'Firma por lotes';
        $this->load->view('template', $data);
    }

    public function confirmar_firma_por_lotes() {
        $doc_funcionario = json_decode(base64_decode($this->input->post('doc_funcionario')));
        $doc_organismo = json_decode(base64_decode($this->input->post('doc_organismo')));
        $doc_ambos = json_decode(base64_decode($this->input->post('doc_ambos')));
        $respuesta = new stdClass();
        foreach ($doc_organismo as $value) {
            $campo = Doctrine::getTable('Campo')->find($value->campo);
            $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo->nombre, $value->etapa);
            if ($dato_seguimiento) {
                $dato_seguimiento->delete();
            }
            $dato_seguimiento = new DatoSeguimiento();
            $dato_seguimiento->etapa_id = $value->etapa;
            $dato_seguimiento->nombre = $campo->nombre;
            $dato_seguimiento->valor = $value->file;
            $dato_seguimiento->save();
        }

        foreach ($doc_funcionario as $value) {
            $campo = Doctrine::getTable('Campo')->find($value->campo);
            if (isset($campo->extra->requerido)) {
                if ($campo->extra->requerido == 'on') {
                    $id = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo->nombre . '__firmado', $value->etapa);
                    if ($id) {
                        $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo->nombre, $value->etapa);
                        if ($dato_seguimiento) {
                            $dato_seguimiento->delete();
                        }
                        $dato_seguimiento = new DatoSeguimiento();
                        $dato_seguimiento->etapa_id = $value->etapa;
                        $dato_seguimiento->nombre = $campo->nombre;
                        $dato_seguimiento->valor = $value->file;
                        $dato_seguimiento->save();
                    } else {
                        $respuesta->error = true;
                        $respuesta->msg = "Existen documentos que no han sido firmados por el Funcionario";
                        echo json_encode($respuesta);
                        return;
                    }
                }
            }
        }

        foreach ($doc_ambos as $value) {
            $campo = Doctrine::getTable('Campo')->find($value->campo);
            if (isset($campo->extra->requerido)) {
                if ($campo->extra->requerido == 'on') {
                    $id = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo->nombre . '__firmado', $value->etapa);
                    if ($id) {
                        $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo->nombre, $value->etapa);
                        if ($dato_seguimiento) {
                            $dato_seguimiento->delete();
                        }
                        $dato_seguimiento = new DatoSeguimiento();
                        $dato_seguimiento->etapa_id = $value->etapa;
                        $dato_seguimiento->nombre = $campo->nombre;
                        $dato_seguimiento->valor = $value->file;
                        $dato_seguimiento->save();
                    } else {
                        $respuesta->error = true;
                        $respuesta->msg = "Existen documentos que no han sido firmados por el Funcionario";
                        echo json_encode($respuesta);
                        return;
                    }
                }
            }
        }
        $respuesta->error = false;
        echo json_encode($respuesta);
        return;
    }

    public function firmar_documentos_lotes() {

        $lista = json_decode($this->input->post('lista'));
        $etapas = array();
        foreach ($lista as $value) {
            $etapa = Doctrine::getTable('Etapa')->find($value->id_etapa);
            array_push($etapas, $etapa);
        }
        $id_tarea = $etapas[0]->tarea_id;
        $distinto = false;
        foreach ($etapas as $value) {
            if ($id_tarea != $value->tarea_id) {
                $distinto = true;
                break;
            }
        }
        $respuesta = new stdClass();
        if ($distinto) {
            $respuesta->error = true;
            $respuesta->msg = "Solo puede seleccionar trámites de la misma tarea.";
            $respuesta->id_tarea = $id_tarea;
        } else {
            $lista_etapas = array();
            foreach ($etapas as $value) {
                if ($value->usuario_id == null) {
                    $value->asignar(UsuarioSesion::usuario()->id);
                }
                array_push($lista_etapas, $value->id);
            }

            $campos = Doctrine::getTable('Etapa')->findDocumentosFirmaPorLote($lista_etapas);
            $files_ambos = array();
            $ambos = 0;
            $files_funcionario = array();
            $funcionario = 0;
            $files_organismo = array();
            $organismo = 0;
            $files_firma_ambos = array();
            $firma_ambos = 0;
            foreach ($campos as $value) {
                $campo_documento = Doctrine::getTable('Campo')->find($value["id"]);
                if ($campo_documento) {
                    $campos_extra = $campo_documento->extra;
                    if ($campos_extra->regenerar == 1) {
                        $doc = Doctrine::getTable('Documento')->find($value["documento"]);
                        $file = $doc->generar($value["etapa"], $value["id"]);
                        if (isset($campos_extra->firmar) && isset($campos_extra->firmar_servidor)) {
                            if ($campos_extra->firmar == "on" && $campos_extra->firmar_servidor == "on") {
                                $files_ambos[$ambos] = new stdClass();
                                $files_ambos[$ambos]->file = $file->filename;
                                $files_ambos[$ambos]->etapa = $value["etapa"];
                                $files_ambos[$ambos]->campo = $value["id"];
                                $files_firma_ambos[$firma_ambos] = new stdClass();
                                $files_firma_ambos[$firma_ambos]->file = $file->filename;
                                $files_firma_ambos[$firma_ambos]->etapa = $value["etapa"];
                                $files_firma_ambos[$firma_ambos]->campo = $value["id"];
                                $ambos++;
                                $firma_ambos++;
                            }
                        } else if (isset($campos_extra->firmar)) {
                            if ($campos_extra->firmar == "on") {
                                $files_funcionario[$funcionario] = new stdClass();
                                $files_funcionario[$funcionario]->file = $file->filename;
                                $files_funcionario[$funcionario]->etapa = $value["etapa"];
                                $files_funcionario[$funcionario]->campo = $value["id"];
                                $files_firma_ambos[$firma_ambos] = new stdClass();
                                $files_firma_ambos[$firma_ambos]->file = $file->filename;
                                $files_firma_ambos[$firma_ambos]->etapa = $value["etapa"];
                                $files_firma_ambos[$firma_ambos]->campo = $value["id"];
                                $firma_ambos++;
                                $funcionario++;
                            }
                        } else if (isset($campos_extra->firmar_servidor)) {
                            if ($campos_extra->firmar_servidor == "on") {
                                $files_organismo[$organismo] = new stdClass();
                                $files_organismo[$organismo]->file = $file->filename;
                                $files_organismo[$organismo]->etapa = $value["etapa"];
                                $files_organismo[$organismo]->campo = $value["id"];
                                $organismo++;
                            }
                        }
                    } else {
                        $et = Doctrine::getTable('Etapa')->find($value["etapa"]);
                        $campo_nombre = Doctrine::getTable('Campo')->find($value["id"]);
                        $name_file = Doctrine::getTable('DatoSeguimiento')->findCampoFile($et->tramite_id, $campo_nombre->nombre);
                        if (isset($name_file[0])) {
                            $file = Doctrine_Query::create()->from('File f')->where('f.filename = ?', array(trim($name_file[0], '"')))->fetchOne();
                            if (!$file) {
                                $doc = Doctrine::getTable('Documento')->find($value["documento"]);
                                $file = $doc->generar($value["etapa"], $value["id"]);
                            }
                        } else {
                            $doc = Doctrine::getTable('Documento')->find($value["documento"]);
                            $file = $doc->generar($value["etapa"], $value["id"]);
                        }
                        if (isset($campos_extra->firmar) && isset($campos_extra->firmar_servidor)) {
                            if ($campos_extra->firmar == "on" && $campos_extra->firmar_servidor == "on") {
                                $files_ambos[$ambos] = new stdClass();
                                $files_ambos[$ambos]->file = $file->filename;
                                $files_ambos[$ambos]->etapa = $value["etapa"];
                                $files_ambos[$ambos]->campo = $value["id"];
                                $files_firma_ambos[$firma_ambos] = new stdClass();
                                $files_firma_ambos[$firma_ambos]->file = $file->filename;
                                $files_firma_ambos[$firma_ambos]->etapa = $value["etapa"];
                                $files_firma_ambos[$firma_ambos]->campo = $value["id"];
                                $ambos++;
                                $firma_ambos++;
                            }
                        } else if (isset($campos_extra->firmar)) {
                            if ($campos_extra->firmar == "on") {
                                $files_funcionario[$funcionario] = new stdClass();
                                $files_funcionario[$funcionario]->file = $file->filename;
                                $files_funcionario[$funcionario]->etapa = $value["etapa"];
                                $files_funcionario[$funcionario]->campo = $value["id"];
                                $files_firma_ambos[$firma_ambos] = new stdClass();
                                $files_firma_ambos[$firma_ambos]->file = $file->filename;
                                $files_firma_ambos[$firma_ambos]->etapa = $value["etapa"];
                                $files_firma_ambos[$firma_ambos]->campo = $value["id"];
                                $firma_ambos++;
                                $funcionario++;
                            }
                        } else if (isset($campos_extra->firmar_servidor)) {
                            if ($campos_extra->firmar_servidor == "on") {
                                $files_organismo[$organismo] = new stdClass();
                                $files_organismo[$organismo]->file = $file->filename;
                                $files_organismo[$organismo]->etapa = $value["etapa"];
                                $files_organismo[$organismo]->campo = $value["id"];
                                $organismo++;
                            }
                        }
                    }
                }
            }
            $respuesta->error = false;
            $respuesta->files_ambos = base64_encode(json_encode($files_ambos));
            $respuesta->files_organismo = base64_encode(json_encode($files_organismo));
            $respuesta->files_funcionario = base64_encode(json_encode($files_funcionario));
            $respuesta->jnlp = $this->firmar_documento($files_firma_ambos);
        }
        echo json_encode($respuesta);
    }

    function firmar_documento($documentos) {

        $uploadDirectory = DIRECTORIO_SUBIDA_DOCUMENTOS;
        $soap_endpoint_location = WS_FIRMA_DOCUMENTOS;

        $CI = &get_instance();
        $doc = "";
        $files_ambos = array();
        $ambos = 0;
        if (count($documentos) == 0) {
            return "";
        }
        foreach ($documentos as $value) {
            $files_ambos[$ambos] = new stdClass();
            $files_ambos[$ambos]->file = $value->file;
            $files_ambos[$ambos]->etapa = $value->etapa;
            $files_ambos[$ambos]->campo = $value->campo;
            $url = $uploadDirectory . $value->file;

            $doc .= '<ws:documentos>' . base64_encode(file_get_contents($uploadDirectory . $value->file)) . '</ws:documentos>' . '\n';
            $ambos++;
        }
        $soap_body = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.firma.agesic.gub.uy/">
                         <soapenv:Header/>
                         <soapenv:Body>
                            <ws:firmarDocumentos>
                               <ws:tipo_firma>pdf</ws:tipo_firma>
                               ' . $doc . '
                            </ws:firmarDocumentos>
                         </soapenv:Body>
                      </soapenv:Envelope>';

        $soap_header = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Content-length: " . strlen($soap_body)
        );

        $soap_do = curl_init();

        curl_setopt($soap_do, CURLOPT_URL, $soap_endpoint_location);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, WS_TIMEOUT_CONEXION);
        curl_setopt($soap_do, CURLOPT_TIMEOUT, WS_TIMEOUT_RESPUESTA);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $soap_body);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER, $soap_header);

        $soap_response = curl_exec($soap_do);
        curl_close($soap_do);

        try {
            $xml = new SimpleXMLElement($soap_response);
        } catch (Exception $exc) {
            exit("Error en el servicio de la firma");
        }


        $token_id = $xml->xpath(WS_AGESIC_FIRMA_XPATH);
        $respuesta = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
                          <jnlp codebase="' . WS_AGESIC_FIRMA_CODEBASE . '" href="" spec="1.0+">
                              <information>
                                  <title>Agesic Firma</title>
                                  <vendor>Agesic</vendor>
                                  <homepage href=""/>
                                  <description>Agesic Firma</description>
                                  <description kind="short">Agesic Firma</description>
                                  <offline-allowed/>
                              </information>
                              <update check="always" policy="prompt-update"/>
                              <security>
                                   <all-permissions/>
                              </security>
                              <resources>
                                  <j2se version="1.6+"/>
                                  <jar href="AgesicFirmaApplet-AgesicFirmaApplet-4.3.jar" main="true"/>
                                  <jar href="activation-1.1.jar" main="false"/>
                                  <jar href="aerogear-crypto-0.1.5.jar" main="false"/>
                                  <jar href="avalon-framework-4.1.3.jar" main="false"/>
                                  <jar href="batik-awt-util-1.6.jar" main="false"/>
                                  <jar href="batik-dom-1.6.jar" main="false"/>
                                  <jar href="batik-svg-dom-1.6.jar" main="false"/>
                                  <jar href="batik-svggen-1.6.jar" main="false"/>
                                  <jar href="batik-util-1.6.jar" main="false"/>
                                  <jar href="batik-xml-1.6.jar" main="false"/>
                                  <jar href="bcmail-jdk15-1.46.jar" main="false"/>
                                  <jar href="bcprov-jdk15-1.46.jar" main="false"/>
                                  <jar href="bctsp-jdk15-1.46.jar" main="false"/>
                                  <jar href="commons-codec-1.2.jar" main="false"/>
                                  <jar href="commons-httpclient-3.0.1.jar" main="false"/>
                                  <jar href="commons-io-2.1.jar" main="false"/>
                                  <jar href="commons-lang-2.4.jar" main="false"/>
                                  <jar href="commons-logging-1.1.jar" main="false"/>
                                  <jar href="icepdf-core-4.3.2.jar" main="false"/>
                                  <jar href="icepdf-viewer-4.3.2.jar" main="false"/>
                                  <jar href="ini4j-0.5.2.jar" main="false"/>
                                  <jar href="itextpdf-5.2.0.jar" main="false"/>
                                  <jar href="jai-codec-1.1.3.jar" main="false"/>
                                  <jar href="jai-core-1.1.3.jar" main="false"/>
                                  <jar href="java-plugin-jre-1.5.0_09.jar" main="false"/>
                                  <jar href="junit-3.8.1.jar" main="false"/>
                                  <jar href="log4j-1.2.14.jar" main="false"/>
                                  <jar href="logkit-1.0.1.jar" main="false"/>
                                  <jar href="mail-1.4.1.jar" main="false"/>
                                  <jar href="MITyCLibAPI-1.0.4.jar" main="false"/>
                                  <jar href="MITyCLibCert-1.0.4.jar" main="false"/>
                                  <jar href="MITyCLibPolicy-1.0.4.jar" main="false"/>
                                  <jar href="MITyCLibTrust-1.0.4.jar" main="false"/>
                                  <jar href="MITyCLibTSA-1.0.4.jar" main="false"/>
                                  <jar href="MITyCLibXADES-1.0.4.jar" main="false"/>
                                  <jar href="sc-light-jdk15on-1.47.0.3.jar" main="false"/>
                                  <jar href="scprov-jdk15on-1.47.0.3.jar" main="false"/>
                                  <jar href="serializer-2.7.1.jar" main="false"/>
                                  <jar href="servlet-api-2.3.jar" main="false"/>
                                  <jar href="swing-layout-1.0.3.jar" main="false"/>
                                  <jar href="UserAgentUtils-1.15.jar" main="false"/>
                                  <jar href="webservices-api-1.4.jar" main="false"/>
                                  <jar href="webservices-rt-1.4.jar" main="false"/>
                                  <jar href="xalan-2.7.1.jar" main="false"/>
                                  <jar href="xml-apis-1.3.04.jar" main="false"/>
                                  <jar href="xmlsec-1.4.2-ADSI-1.0.jar" main="false"/>
                              </resources>
                              <application-desc main-class="uy.gub.agesic.firma.cliente.applet.SignAppletStub">
                                  <argument>-ID_TRANSACCION=' . $token_id[0] . '</argument>
                                  <argument>-TIPO_DOCUMENTO=pdf</argument>
                                  <argument>-AGESIC_FIRMA_WS=' . WS_AGESIC_FIRMA . '</argument>
                                  <argument>-URL_OK_POST=' . WS_AGESIC_FIRMA_LOTE_OK . '?filesname=' . base64_encode(json_encode($files_ambos)) . '</argument>
                              </application-desc>
                          </jnlp>';

        return $respuesta;
    }

}
