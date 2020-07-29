<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Procesos extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if (!UsuarioBackendSesion::has_rol('super') && !UsuarioBackendSesion::has_rol('modelamiento')) {
            redirect('backend');
        }
        $this->load->helper('auditoria_helper');
    }

    public function index() {
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $query = Doctrine_Query::create()
                ->select('p.*')
                ->from('Proceso p, p.Cuenta c')
                ->addSelect('(SELECT count(t.id)
                        FROM Tramite t
                        WHERE t.proceso_id = p.id
                        LIMIT 1) as ntramites')
                ->where('p.activo=1 AND p.estado!="arch" AND c.id = ? 
                AND ((SELECT COUNT(proc.id) FROM Proceso proc WHERE proc.cuenta_id = ? AND (proc.root = p.id OR proc.root = p.root) AND proc.estado = "public") = 0 
                OR p.estado = "public")
                ', array($cuenta_id, $cuenta_id))
                ->andWhere('p.nombre != "BLOQUE"')
                ->orderBy('p.nombre asc');
        $query_root = Doctrine_Query::create()
                ->select('p.*')
                ->from('Proceso p, p.Cuenta c')
                ->where('p.activo=1 AND c.id = ? ', array($cuenta_id))
                ->andWhere('p.nombre != "BLOQUE"')
                ->addGroupBy("p.root")
                ->orderBy('p.nombre asc');
        $data['procesos_root'] = $query_root->execute();
        $data['editar_proceso'] = true;
        $data['procesos'] = $query->execute();

        $data['title'] = 'Listado de Procesos';
        $data['content'] = 'backend/procesos/index';
        $this->load->view('backend/template', $data);
    }

    public function crear() {
        $proceso = new Proceso();
        $proceso->nombre = 'Proceso';
        $proceso->width = '100%';
        $proceso->height = '100%';
        $proceso->cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $proceso->estado = 'draft';
        $proceso->save();
        $proceso->root = $proceso->id;
        $proceso->save();
        $proceso_trazabilidad = new ProcesoTrazabilidad();
        $proceso_trazabilidad->proceso_id = $proceso->id;
        $proceso_trazabilidad->organismo_id = 'Organismo ID';
        $proceso_trazabilidad->proceso_externo_id = 'Proceso ID';
        $proceso_trazabilidad->save();
        auditar('Proceso', "insert", $proceso->id, UsuarioBackendSesion::usuario()->usuario);

        redirect('backend/procesos/editar/' . $proceso->id);
    }

    public function eliminar($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para eliminar este proceso';
            exit;
        }

        //se verifica que el proceso no tenga instancias, si tiene instancias no se
        //permite eliminar
        if (count($proceso->Tramites) > 0) {
            //no se permite eliminar
            echo 'El proceso tiene instancias asociadas, no se puede eliminar.';
            exit;
        } else if ($proceso->estado == "public") {
            //no se permite eliminar
            echo 'El proceso se encuentra publicado, no se puede eliminar.';
            exit;
        } else {
            $proceso_root = $proceso->root;
            auditar('Proceso', "delete", $proceso->id, UsuarioBackendSesion::usuario()->usuario);
            $proceso->delete();
            //$activo = $proceso->findIdProcesoActivo($proceso->root, UsuarioBackendSesion::usuario()->cuenta_id);
            // $proceso_draft = $proceso->findDraftProceso($proceso->root, UsuarioBackendSesion::usuario()->cuenta_id);
            // $proceso_archivados = $proceso->findProcesosArchivados($proceso->root);
            $max_version = $proceso->findIdMaxVersion($proceso_root, UsuarioBackendSesion::usuario()->cuenta_id);

            if ($max_version) {
                $proceso_max_version = Doctrine::getTable('Proceso')->find($max_version);
                if (count($proceso_max_version) > 0) {
                    $proceso_max_version->estado = "draft";
                    $proceso_max_version->save();
                    auditar('Proceso', "update", $proceso_max_version->id, UsuarioBackendSesion::usuario()->usuario);
                }
            }
        }

        redirect('backend/procesos/index/');
    }

    public function ajax_editar($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar este proceso';
            exit;
        }

        $proceso->width = str_replace('%', '', str_replace('px', '', $proceso->width));
        $proceso->height = str_replace('%', '', str_replace('px', '', $proceso->height));

        $data['proceso'] = $proceso;

        $this->load->view('backend/procesos/ajax_editar', $data);
    }

    public function editar_form($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar este proceso';
            exit;
        }

        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        $this->form_validation->set_rules('width', 'Width', 'numeric');
        $this->form_validation->set_rules('height', 'Height', 'numeric');

        $respuesta = new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $proceso->nombre = $this->input->post('nombre');
            $proceso->width = $this->input->post('width') . '%';
            $proceso->height = $this->input->post('height') . '%';
            $proceso->save();

            auditar('Proceso', "update", $proceso->id, UsuarioBackendSesion::usuario()->usuario);
            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/procesos/editar/' . $proceso->id);
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function ajax_crear_tarea($proceso_id, $tarea_identificador) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para crear esta tarea.';
            exit;
        }
        $conn = Doctrine_Manager::connection();
        $stmt = $conn->prepare("SELECT SUBSTRING(MD5(RAND()) FROM 1 FOR 8)");
        $stmt->execute();
        $clave = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $tarea = new Tarea();
        $tarea->proceso_id = $proceso->id;
        $tarea->identificador = $tarea_identificador;
        $tarea->nombre = $this->input->post('nombre');
        $tarea->posx = $this->input->post('posx');
        $tarea->posy = $this->input->post('posy');
        $tarea->id_x_tarea = $clave[0];
        $tarea->save();
        auditar('Tarea', "insert", $tarea->id, UsuarioBackendSesion::usuario()->usuario);
    }

    public function ajax_editar_tarea($proceso_id, $tarea_identificador) {
        $tarea = Doctrine::getTable('Tarea')->findOneByProcesoIdAndIdentificador($proceso_id, $tarea_identificador);

        if ($tarea->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar esta tarea.';
            exit;
        }
        $data['etiquetas'] = Doctrine::getTable('EtiquetaTraza')->findAll();
        $data['tarea'] = $tarea;
        $data['formularios'] = Doctrine::getTable('Formulario')->findByProcesoIdAndTipo($proceso_id, "comun");
        $data['acciones'] = Doctrine::getTable('Accion')->findByProcesoId($proceso_id);
        $data['validaciones'] = Doctrine::getTable('Validacion')->findByProcesoId($proceso_id);
        $data['variablesFormularios'] = Doctrine::getTable('Proceso')->findVariblesFormularios($proceso_id, $tarea['id']);
        $data['variablesProcesos'] = Doctrine::getTable('Proceso')->findVariblesProcesos($proceso_id);

        $this->load->view('backend/procesos/ajax_editar_tarea', $data);
    }

    public function automatica_check($str) {
        $pasos = $this->input->post('pasos', false);
        if ($pasos) {
            if (count($pasos) > 1) {
                $this->form_validation->set_message('automatica_check', 'Si la tarea es automática solo debe contener un paso.');
                return FALSE;
            }
        }
        return TRUE;
    }

    public function trazabilidad_id_oficina_check($str) {
        if (trim($str) == '') {
            $this->form_validation->set_message('trazabilidad_id_oficina_check', 'El campo <strong>"ID de oficina"</strong> en la pestaña Trazabilidad es obligatorio.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function trazabilidad_oficina_check($str) {
        if (trim($str) == '') {
            $this->form_validation->set_message('trazabilidad_oficina_check', 'El campo <strong>"Nombre de oficina"</strong> en la pestaña Trazabilidad es obligatorio.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function editar_tarea_form($tarea_id) {
        $tarea = Doctrine::getTable('Tarea')->find($tarea_id);
        if ($tarea->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar esta tarea.';
            exit;
        }

        $this->form_validation->set_rules('nombre', 'Nombre', 'required');
        if ($this->input->post('vencimiento')) {
            $this->form_validation->set_rules('vencimiento_valor', 'Valor de Vencimiento', 'required|is_natural_no_zero');

            if ($this->input->post('vencimiento_notificar')) {
                $this->form_validation->set_rules('vencimiento_notificar_dias', 'Días para notificar vencimiento', 'required|is_natural_no_zero');
                $this->form_validation->set_rules('vencimiento_notificar_email', 'Correo electronico para notificar vencimiento', 'required');
            }

            if ($this->input->post('chk_despues') == 'despues_fecha') {
                $this->form_validation->set_rules('vencimiento_a_partir_de_variable', 'Fecha a partir de la cual vence la etapa', 'required');
            }

            if ($this->input->post('notificar_vencida')) {
                $this->form_validation->set_rules('vencimiento_notificar_email', 'Correo electronico para notificar vencimiento', 'required');
            }
        }

        Doctrine::getTable('Proceso')->updateVaribleExposed($this->input->post('varForm'), $this->input->post('varPro'), $tarea->Proceso->id, $tarea_id);

        if ($this->input->post('automatica')) {
            $this->form_validation->set_rules('automatica', 'Tarea automáctica', 'callback_automatica_check');
        }

        if ($this->input->post('trazabilidad')) {
            if (!$this->input->post('trazabilidad_visible')) {
                $this->form_validation->set_message('required', 'El campo <strong>"Visibilidad de traza"</strong> en la pestaña Trazabilidad es obligatorio.');
            }
            if (!$this->input->post('trazabilidad_etiqueta')) {
                $this->form_validation->set_message('required', 'El campo <strong>"Etiqueta de traza"</strong> en la pestaña Trazabilidad es obligatorio.');
            }
            $this->form_validation->set_rules('trazabilidad_id_oficina', 'ID de oficina', 'callback_trazabilidad_id_oficina_check');
            $this->form_validation->set_rules('trazabilidad_nombre_oficina', 'Nombre de oficina', 'callback_trazabilidad_oficina_check');
            $this->form_validation->set_rules('trazabilidad_visible', 'Visibilidad de traza', 'required');
        }

        $respuesta = new stdClass();
        if ($this->form_validation->run() == TRUE) {
            //show_error(1);
            $tarea->nombre = $this->input->post('nombre');
            $tarea->inicial = $this->input->post('inicial');
            $tarea->final = $this->input->post('final');
            $tarea->asignacion = $this->input->post('asignacion');
            $tarea->asignacion_usuario = $this->input->post('asignacion_usuario');
            $tarea->asignacion_notificar = $this->input->post('asignacion_notificar');
            $tarea->asignacion_notificar_mensaje = $this->input->post('asignacion_notificar_mensaje');
            $tarea->setGruposUsuariosFromArray($this->input->post('grupos_usuarios'));
            $tarea->setPasosFromArray($this->input->post('pasos', false));
            $tarea->paso_confirmacion = $this->input->post('paso_confirmacion');
            $tarea->setEventosFromArray($this->input->post('eventos', false));
            $tarea->setValidacionesFromArray($this->input->post('validaciones', false));
            $tarea->almacenar_usuario = $this->input->post('almacenar_usuario');
            $tarea->almacenar_usuario_variable = $this->input->post('almacenar_usuario_variable');
            $tarea->acceso_modo = $this->input->post('acceso_modo');
            $tarea->nivel_confianza = $this->input->post('nivel_confianza');
            $tarea->activacion = $this->input->post('activacion');
            $tarea->activacion_inicio = strtotime($this->input->post('activacion_inicio'));
            $tarea->activacion_fin = strtotime($this->input->post('activacion_fin'));
            $tarea->vencimiento = $this->input->post('vencimiento');
            $tarea->vencimiento_valor = $this->input->post('vencimiento_valor');
            $tarea->vencimiento_unidad = $this->input->post('vencimiento_unidad');
            $tarea->vencimiento_habiles = $this->input->post('vencimiento_habiles');
            $tarea->vencimiento_notificar = $this->input->post('vencimiento_notificar');
            $tarea->vencimiento_notificar_dias = $this->input->post('vencimiento_notificar_dias');
            $tarea->vencimiento_notificar_email = $this->input->post('vencimiento_notificar_email');
            $tarea->previsualizacion = $this->input->post('previsualizacion');
            $tarea->trazabilidad = $this->input->post('trazabilidad');
            $tarea->trazabilidad_id_oficina = $this->input->post('trazabilidad_id_oficina');
            $tarea->trazabilidad_estado = $this->input->post('trazabilidad_estado');
            $tarea->trazabilidad_cabezal = $this->input->post('trazabilidad_cabezal');
            $tarea->etiqueta_traza = $this->input->post('trazabilidad_etiqueta');
            $tarea->visible_traza = $this->input->post('trazabilidad_visible');
            $tarea->trazabilidad_nombre_oficina = $this->input->post('trazabilidad_nombre_oficina');
            $tarea->automatica = $this->input->post('automatica');
            $tarea->paso_final_pendiente = $this->input->post('paso_final_pendiente');
            $tarea->paso_final_standby = $this->input->post('paso_final_standby');
            $tarea->paso_final_completado = $this->input->post('paso_final_completado');
            $tarea->paso_final_sincontinuacion = $this->input->post('paso_final_sincontinuacion');
            $tarea->texto_boton_paso_final = $this->input->post('texto_boton_paso_final');
            $tarea->texto_boton_generar_pdf = $this->input->post('texto_boton_generar_pdf');
            $tarea->escalado_automatico = $this->input->post('escalado_automatico');
            $tarea->vencimiento_a_partir_de_variable = $this->input->post('vencimiento_a_partir_de_variable');
            $tarea->notificar_vencida = $this->input->post('notificar_vencida');

            $tarea->save();
            auditar('Tarea', "update", $tarea->id, UsuarioBackendSesion::usuario()->usuario);
            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/procesos/editar/' . $tarea->Proceso->id);
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function eliminar_tarea($tarea_id) {
        $tarea = Doctrine::getTable('Tarea')->find($tarea_id);

        if ($tarea->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para eliminar esta tarea.';
            exit;
        }

        $proceso = $tarea->Proceso;

        auditar('Tarea', "delete", $tarea->id, UsuarioBackendSesion::usuario()->usuario);
        $tarea->delete();

        redirect('backend/procesos/editar/' . $proceso->id);
    }

    public function ajax_crear_conexion($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);
        $tarea_origen = Doctrine::getTable('Tarea')->findOneByProcesoIdAndIdentificador($proceso_id, $this->input->post('tarea_id_origen'));
        $tarea_destino = Doctrine::getTable('Tarea')->findOneByProcesoIdAndIdentificador($proceso_id, $this->input->post('tarea_id_destino'));

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para crear esta conexion.';
            exit;
        }
        if ($tarea_origen->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para crear esta conexion.';
            exit;
        }
        if ($tarea_destino->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para crear esta conexion.';
            exit;
        }

        //El tipo solamente se setea en la primera conexion creada para esa tarea.
        $tipo = $this->input->post('tipo');
        if ($tarea_origen->ConexionesOrigen->count())
            $tipo = $tarea_origen->ConexionesOrigen[0]->tipo;

        $conexion = new Conexion();
        $conexion->tarea_id_origen = $tarea_origen->id;
        $conexion->tarea_id_destino = $tarea_destino->id;
        $conexion->tipo = $tipo;
        $conexion->save();
        auditar('Conexion', "insert", $conexion->id, UsuarioBackendSesion::usuario()->usuario);
    }

    public function ajax_editar_conexiones($proceso_id, $tarea_origen_identificador) {
        $conexiones = Doctrine_Query::create()
                ->from('Conexion c, c.TareaOrigen t')
                ->where('t.proceso_id=? AND t.identificador=?', array($proceso_id, $tarea_origen_identificador))
                ->execute();

        if ($conexiones[0]->TareaOrigen->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar estas conexiones.';
            exit;
        }

        $data['conexiones'] = $conexiones;

        $this->load->view('backend/procesos/ajax_editar_conexiones', $data);
    }

    public function editar_conexiones_form($tarea_id) {
        $tarea = Doctrine::getTable('Tarea')->find($tarea_id);

        if ($tarea->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar estas conexiones.';
            exit;
        }

        $this->form_validation->set_rules('conexiones', 'Conexiones', 'required');

        $respuesta = new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $tarea->setConexionesFromArray($this->input->post('conexiones', false));
            $tarea->save();
            auditar('Tarea', "update", $tarea->id, UsuarioBackendSesion::usuario()->usuario);
            $respuesta->validacion = TRUE;
            $respuesta->redirect = site_url('backend/procesos/editar/' . $tarea->Proceso->id);
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function eliminar_conexiones($tarea_id) {
        $tarea = Doctrine::getTable('Tarea')->find($tarea_id);

        if ($tarea->Proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para eliminar esta conexion.';
            exit;
        }

        $proceso = $tarea->Proceso;
        auditar('Conexion', "delete", $tarea->id, UsuarioBackendSesion::usuario()->usuario);
        $tarea->ConexionesOrigen->delete();
        auditar('Tarea', "update", $tarea->id, UsuarioBackendSesion::usuario()->usuario);

        redirect('backend/procesos/editar/' . $proceso->id);
    }

    public function ajax_editar_modelo($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar este proceso';
            exit;
        }

        $modelo = $this->input->post('modelo');

        $proceso->updateModelFromJSON($modelo);
    }

    public function exportar($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        $json = $proceso->exportComplete();
        $leng = rand(25, 34);
        $json = $leng . $this->cadenaAlpha($leng) . base64_encode($json) . $this->cadenaAlpha($leng);
        $json = base64_encode($json);

        header("Content-Disposition: attachment; filename=\"" . mb_convert_case(str_replace(' ', '-', $proceso->nombre), MB_CASE_LOWER) . ".simple\"");
        header('Content-Type: application/json');
        echo $json;
    }

    public function importar() {
        $mensajes = '';
        $file_path = $_FILES['archivo']['tmp_name'];
        $root = $this->input->post('root_id');
        $permitido = array('simple');
        $nombre_archivo = $_FILES['archivo']['name'];
        $ext = pathinfo($nombre_archivo, PATHINFO_EXTENSION);
        if (!in_array($ext, $permitido)) {
            $mensajes = '<div class="alert alert-error"><i class="icon-exclamation-sign"></i> La extensión del archivo de importación no es la correcta.</div>';
        } else {
            if ($file_path) {
               $input = file_get_contents($_FILES['archivo']['tmp_name']);
                $input = base64_decode($input);
                $leng = (int) $input[0] . $input[1];
                $input = substr($input, $leng + 2);
                $input = substr($input, 0, -$leng);
                $input = base64_decode($input);
                json_decode($input);
                if (json_last_error()!=0) {
                    $mensajes = '<div class="alert alert-error"><i class="icon-exclamation-sign"></i> Hubo un error al procesar la importación, es posible que el archivo esté corrupto.</div>';
                } else {
                    // -- Verifica si el documento a importar contiene acciones que requieren servicios
                    preg_match_all("/(?<=\"soap_operacion\":\").*?(?=\")/", $input, $webservices_encontrados);
                    preg_match_all("/pasarela_pago/", $input, $pasarelas_encontradas);
                    preg_match_all("/(?<=\"requiere_accion\":\"1\").*?(?=\")/", $input, $campos_requiere_accion);

                    if (count($webservices_encontrados[0])) {
                        $mensajes .= '<div class="alert alert-info"><i class="icon-exclamation-sign"></i> La importación realizada requiere de servicios del <strong>Catálogo de Servicios</strong> que podrían no estar disponibles en esta instalación. <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;A continuación se listan los servicios requeridos: <br /><br />';
                        $mensajes .= '<dl class="lista_codigos_servicios">';
                        foreach ($webservices_encontrados[0] as $codigo) {
                            $mensajes .= '<dt>Código de servicio: </dt><dd>' . $codigo . '&nbsp;&nbsp;&nbsp;</dd>';
                        }
                        $mensajes .= '</dl>';
                        $mensajes .= '</div>';
                    }

                    if (count($pasarelas_encontradas[0])) {
                        $mensajes .= '<div class="alert alert-info"><i class="icon-exclamation-sign"></i> La importación realizada requiere de servicios de <strong>Pasarela de Pagos</strong> que podrían no estar disponibles en esta instalación. <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;Por favor, verifique en el catálogo que cuenta con dichos servicios para su correcto funcionamiento.</div>';
                    }

                    if (count($campos_requiere_accion[0])) {
                        $mensajes .= '<div class="alert alert-info"><i class="icon-exclamation-sign"></i> En la importación realizada <strong>Existen campos que requieren acción.</strong> <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;Por favor, verifique dichos campos para su correcto funcionamiento.<br /><br />';
                        //preg_match_all("/(?<=\"Campos\").*?(?=\")/", $input, $campos_json);
                        //echo $campos_json[0];
                        $proceso = json_decode($input);
                        $formularios = $proceso->Formularios;
                        $mensajes .= '<dl class="lista_codigos_servicios">';
                        foreach ($formularios as $formulario) {
                            $campos = $formulario->Campos;
                            $req_acc = 0;
                            $mensajes1 = "";
                            $mensajes1 .= '<dt>En el formulario: </dt><dd>' . $formulario->nombre . ' &nbsp;</dd> <br /><br />';
                            foreach ($campos as $campo) {
                                if ($campo->requiere_accion == 1) {
                                    $mensajes1 .= '<dt>Campo que requiere acción: </dt><dd>' . $campo->nombre . '&nbsp;&nbsp;&nbsp;</dd>';
                                    $req_acc++;
                                }
                            }
                            if ($req_acc > 0) {
                                $mensajes .=$mensajes1;
                            }
                        }
                        $mensajes .= '</dl>';
                        $mensajes .= '</div>';
                    }

                    $proceso = Proceso::importComplete($input);
                    if ($proceso == '-1') {
                        $mensajes = '<div class="alert alert-error"><i class="icon-exclamation-sign"></i> Hubo un error al procesar la importación, es posible que el archivo se encuentre dañado.</div>';
                    } else {
                        if (!$root) {
                            $proceso->estado = 'draft';
                            $proceso->version = 1;
                            $proceso->save();
                            $proceso->root = $proceso->id;
                            $proceso->save();
                            auditar('Proceso', "insert", $proceso->id, UsuarioBackendSesion::usuario()->usuario);
                        } else {
                            $proceso_root = Doctrine::getTable('Proceso')->find($root);
                            $proceso_root->findProcesoBorrador($proceso_root->root);
                            $proceso->root = $proceso_root->root;
                            $proceso->estado = 'draft';
                            $max_version = $proceso_root->findMaxVersion($proceso_root->root, UsuarioBackendSesion::usuario()->cuenta_id);
                            $proceso->version = $max_version + 1;
                            $proceso->save();
                            auditar('Proceso', "insert", $proceso->id, UsuarioBackendSesion::usuario()->usuario);
                            $mensajes = '<div class="alert alert-warning"><i class="icon-exclamation-sign"></i> Importó un proceso como versión de un proceso existen, tenga en cuenta que estos pueden no ser compatibles.</div>';
                        }
                    }
                }
            }
        }

        if ($mensajes != '') {
            $data['mensajes'] = $mensajes;
        }
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $query = Doctrine_Query::create()
                ->select('p.*')
                ->from('Proceso p, p.Cuenta c')
                ->addSelect('(SELECT count(t.id)
                        FROM Tramite t
                        WHERE t.proceso_id = p.id
                        LIMIT 1) as ntramites')
                ->where('p.activo=1 AND p.estado!="arch" AND c.id = ? 
                AND ((SELECT COUNT(proc.id) FROM Proceso proc WHERE proc.cuenta_id = ? AND (proc.root = p.id OR proc.root = p.root) AND proc.estado = "public") = 0 
                OR p.estado = "public")
                ', array($cuenta_id, $cuenta_id))
                ->andWhere('p.nombre != "BLOQUE"')
                ->orderBy('p.nombre asc');
        $query_root = Doctrine_Query::create()
                ->select('p.*')
                ->from('Proceso p, p.Cuenta c')
                ->where('p.activo=1 AND c.id = ? ', array($cuenta_id))
                ->andWhere('p.nombre != "BLOQUE"')
                ->addGroupBy("p.root")
                ->orderBy('p.nombre asc');
        $data['procesos_root'] = $query_root->execute();
        $data['procesos'] = $query->execute();
        $data['editar_proceso'] = true;
        $data['title'] = 'Listado de Procesos';
        $data['content'] = 'backend/procesos/index';
        $this->load->view('backend/template', $data);
    }

    public function editar_codigo_tramite_ws_grep($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar este proceso';
            exit;
        }
        $data['proceso'] = $proceso;
        $data['codigo_tramite_ws_grep'] = $proceso->ProcesoTrazabilidad->proceso_externo_id;
        $procesosArchivados = $proceso->findProcesosArchivados((($proceso->root) ? $proceso->root : $proceso->id));
        $data['procesos_arch'] = $procesosArchivados;
        $data['title'] = 'Código tramites.gub.uy';
        $data['content'] = 'backend/procesos/editar_codigo_tramite_ws_grep';
        $this->load->view('backend/template', $data);
    }

    public function editar_form_codigo_tramite_ws_grep($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar este proceso';
            exit;
        }

        $codigo_tramite_ws_grep = $this->input->post('codigo_tramite_ws_grep');

        $respuesta = new stdClass();
        $proceso->ProcesoTrazabilidad->proceso_externo_id = $codigo_tramite_ws_grep;
        $proceso->save();
        auditar('Proceso', "update", $proceso->id, UsuarioBackendSesion::usuario()->usuario);
        $respuesta->validacion = TRUE;
        $respuesta->redirect = site_url('backend/procesos/editar_codigo_tramite_ws_grep/' . $proceso_id);

        echo json_encode($respuesta);
    }

    public function editar_api($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar este proceso';
            exit;
        }
        $data['proceso'] = $proceso;
        $procesosArchivados = $proceso->findProcesosArchivados((($proceso->root) ? $proceso->root : $proceso->id));
        $data['procesos_arch'] = $procesosArchivados;
        $data['title'] = 'API';
        $data['content'] = 'backend/procesos/editar_api';
        $this->load->view('backend/template', $data);
    }

    public function editar_form_api($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar este proceso';
            exit;
        }

        $instanciar_api = $this->input->post('instanciar_api');

        $respuesta = new stdClass();

        $proceso->instanciar_api = (int) $instanciar_api;
        $proceso->save();
        auditar('Proceso', "update", $proceso->id, UsuarioBackendSesion::usuario()->usuario);

        $respuesta->validacion = TRUE;
        $respuesta->redirect = site_url('backend/procesos/editar_api/' . $proceso_id);

        echo json_encode($respuesta);
    }

    public function editar($proceso_id) {
        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);

        $CI = & get_instance();
        if ($proceso->estado != 'public') {
            $CI->session->unset_userdata('nueva_version');
            $nueva_version = false;
        } else {
            $nueva_version = $CI->session->userdata('nueva_version');
        }


        // Verificar si es draft o un proceso publicado
        if ($proceso->estado == 'public' && $nueva_version) { //no es draft
            //Se crea Draft
            $proceso = $this->crearDraft($proceso);
            redirect('backend/procesos/editar/' . $proceso->id);
        } elseif ($proceso->estado == 'arch') {
            $root = $proceso_id;

            log_message("INFO", "Editando proceso id " . $proceso_id, FALSE);

            if (isset($proceso->root) && strlen($proceso->root) > 0) {
                $root = $proceso->root;
            }
            $proceso_draft = $proceso->findDraftProceso($root, UsuarioBackendSesion::usuario()->cuenta_id);

            log_message("INFO", "Se obtiene draft con id " . $proceso_draft->id, FALSE);

            if (isset($proceso_draft) && $proceso_draft->id > 0) {
                $proceso_draft->estado = 'arch';
                $proceso_draft->save();
            }
            $proceso->estado = 'draft';
            $proceso->save();
            redirect('backend/procesos/editar/' . $proceso->id);
        }
        log_message('debug', '$proceso->activo [' . $proceso->activo . '])');

        if ($proceso->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id || $proceso->activo != true) {
            echo 'Usuario no tiene permisos para editar este proceso';
            exit;
        }

        $procesosArchivados = $proceso->findProcesosArchivados((($proceso->root) ? $proceso->root : $proceso->id));
        $data['procesos_arch'] = $procesosArchivados;

        $data['proceso'] = $proceso;

        $data['proceso_id'] = $proceso_id;

        $data['title'] = 'Modelador';
        $data['content'] = 'backend/procesos/editar';
        $data['iconos'] = ''; //$iconos;

        $this->load->view('backend/template', $data);
    }

    private function crearDraft($proceso) {

        $proceso_id = $proceso->id;

        log_message("INFO", "Buscando si proceso ya tiene draft creado", FALSE);

        $root = (($proceso->root) ? $proceso->root : $proceso->id);
        /* if (isset($proceso->root) && strlen($proceso->root) > 0) {
          $root = $proceso->root;
          } */

        log_message("INFO", "Buscando draft con root: " . $root, FALSE);

        $draft = $proceso->findDraftProceso($root, UsuarioBackendSesion::usuario()->cuenta_id);
        //print_r($draft);

        log_message("INFO", "Draft: *" . $draft->id . "*", FALSE);
        //log_message("INFO", "Draft2: ".$draft[0]->id, FALSE);

        if (strlen($draft->id) == 0) { //No existe draft
            log_message("INFO", "Draft no existe", FALSE);
            $proceso = Proceso::importComplete($proceso->exportComplete());

            log_message("INFO", "Buscando última version", FALSE);
            $max_version = $proceso->findMaxVersion($root, UsuarioBackendSesion::usuario()->cuenta_id);
            log_message("INFO", "Ultima version recuperada. " . $max_version, FALSE);

            $proceso->version = $max_version + 1;
            $proceso->estado = 'draft';

            if (!isset($proceso->root) || strlen($proceso->root) == 0) {
                $proceso->root = $root;
            }
            $proceso->root = $root;
            $proceso->save();
            auditar('Proceso', "insert", $proceso->id, UsuarioBackendSesion::usuario()->usuario);
        } else {
            log_message("INFO", "Redirigiendo a edición de Draft con id: " . $draft->id, FALSE);
            $proceso = $draft; //Doctrine::getTable('Proceso')->find($draft[0]["id"]);
        }

        return $proceso;
    }

    public function ajax_editar_proceso($proceso_id) {
        if (!in_array('super', explode(",", UsuarioBackendSesion::usuario()->rol)))
            show_error('No tiene permisos', 401);

        $proceso = Doctrine::getTable("Proceso")->find($proceso_id);
        $data['proceso'] = $proceso;
        $this->load->view('backend/procesos/ajax_editar_proceso', $data);
    }

    public function editar_publicado($proceso_id, $publicado = 0) {
        $nueva_version = $publicado == 1 ? false : true;
        $CI = & get_instance();
        $CI->session->set_userdata('nueva_version', $nueva_version);
        redirect('backend/procesos/editar/' . $proceso_id);
    }

    public function ajax_publicar_proceso($proceso_id) {
        if (!in_array('super', explode(",", UsuarioBackendSesion::usuario()->rol)))
            show_error('No tiene permisos', 401);

        $proceso = Doctrine::getTable("Proceso")->find($proceso_id);
        $data['proceso'] = $proceso;
        $this->load->view('backend/procesos/ajax_publicar_proceso', $data);
    }

    public function publicar($proceso_draft_id, $recursivo = null) {

        $proceso_draft = Doctrine::getTable('Proceso')->find($proceso_draft_id);

        $activo = $proceso_draft->findIdProcesoActivo($proceso_draft->root, UsuarioBackendSesion::usuario()->cuenta_id);



        if (strlen($activo->id) > 0) { // Existe proceso activo
            $activo->estado = 'arch';
            $activo->save();
            auditar('Proceso', "update", $activo->id, UsuarioBackendSesion::usuario()->usuario);
        }
//        else {
//            $proceso_draft->root = $proceso_draft->id;
//        }

        $proceso_draft->estado = 'public';
        $proceso_draft->save();
        auditar('Proceso', "update", $proceso_draft->id, UsuarioBackendSesion::usuario()->usuario);

        if ($recursivo) {

            $this->activarProcesoTramites($proceso_draft->root, $proceso_draft->id);
        }
        $respuesta = new stdClass ();
        $respuesta->validacion = TRUE;
        $respuesta->redirect = site_url('backend/procesos/index/');
        echo json_encode($respuesta);
    }

    private function activarProcesoTramites($root, $activo) {
        $tramites = Doctrine::getTable('Tramite')->getTramitesProceso($root);
        foreach ($tramites as $tramite) {
            $t = Doctrine::getTable('Tramite')->find($tramite);
            $t->actualizarProceso($activo);
        }
    }

    public function ocultar($proceso_id) {
        if (!in_array('super', explode(",", UsuarioBackendSesion::usuario()->rol)))
            show_error('No tiene permisos', 401);

        $proceso = Doctrine::getTable('Proceso')->find($proceso_id);
        $proceso->estado = 'draft';
        $proceso->save();
        auditar('Proceso', "update", $proceso->id, UsuarioBackendSesion::usuario()->usuario);
        redirect('backend/procesos/index');
    }
    
    private function cadenaAlpha($length) {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $characters = $uppercase . $lowercase . $numbers;

        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function clonar() {
        $root = $this->input->post('proceso_clonar');
        $proceso_draft = Doctrine::getTable('Proceso')->find($root);
        if ($proceso_draft) {
            $proceso = $this->crearDraft($proceso_draft);
            $proceso->version = 1;
            $proceso->nombre = $proceso_draft->nombre . "_clonado";
            $proceso->estado = 'draft';
            $proceso->root = $proceso->id;
            $proceso->save();
            auditar('Proceso', "insert", $proceso->id, UsuarioBackendSesion::usuario()->usuario);
            redirect('backend/procesos/editar/' . $proceso->id);
        } else {
            $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
            $mensajes = '<div class="alert alert-error"><i class="icon-exclamation-sign"></i> No se pudo clonar el proceso seleccionado</div>';
            $query = Doctrine_Query::create()
                    ->select('p.*')
                    ->from('Proceso p, p.Cuenta c')
                    ->addSelect('(SELECT count(t.id)
                        FROM Tramite t
                        WHERE t.proceso_id = p.id
                        LIMIT 1) as ntramites')
                    ->where('p.activo=1 AND p.estado!="arch" AND c.id = ? 
                AND ((SELECT COUNT(proc.id) FROM Proceso proc WHERE proc.cuenta_id = ? AND (proc.root = p.id OR proc.root = p.root) AND proc.estado = "public") = 0 
                OR p.estado = "public")
                ', array($cuenta_id, $cuenta_id))
                    ->andWhere('p.nombre != "BLOQUE"')
                    ->orderBy('p.nombre asc');
            $query_root = Doctrine_Query::create()
                    ->select('p.*')
                    ->from('Proceso p, p.Cuenta c')
                    ->where('p.activo=1 AND c.id = ? ', array($cuenta_id))
                    ->andWhere('p.nombre != "BLOQUE"')
                    ->addGroupBy("p.root")
                    ->orderBy('p.nombre asc');
            $data['procesos_root'] = $query_root->execute();
            $data['procesos'] = $query->execute();
            $data['mensajes'] = $mensajes;
            $data['editar_proceso'] = true;
            $data['title'] = 'Listado de Procesos';
            $data['content'] = 'backend/procesos/index';
            $this->load->view('backend/template', $data);
        }
    }

    public function get_version() {
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $proceso_root = $this->input->post('proceso_root');
        $respuesta = new stdClass();
        if ($proceso_root) {
            $query_version = Doctrine_Query::create()
                    ->select('p.*')
                    ->from('Proceso p, p.Cuenta c')
                    ->where('p.activo=1 AND c.id = ? AND p.root=?', array($cuenta_id, $proceso_root))
                    ->andWhere('p.nombre != "BLOQUE"')
                    ->orderBy('p.version asc');
            $versiones = $query_version->fetchArray();
            if ($versiones) {
                $respuesta->versiones = $versiones;
            }
        }
        if (!isset($respuesta->versiones)) {
            $respuesta->error = true;
        }
        echo json_encode($respuesta);
    }

}
