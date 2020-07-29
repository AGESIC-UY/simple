<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Obns extends MY_BackendController {

    public function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();

        if (!UsuarioBackendSesion::has_rol('super') && !UsuarioBackendSesion::has_rol('modelamiento')) {
            redirect('backend');
        }
        $this->load->helper('auditoria_helper');
        $this->load->helper('buscar_obn_helper');
    }

    public function listar() {
        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $obn = Doctrine::getTable('ObnStructure')->findByCuentaId($cuenta_id);
        $lista = array();
        foreach ($obn as $value) {
            if(existeClase($value)) {
                $lista[]=$value;
            }   
        }
        $data['obn'] = $lista;
        $data['title'] = 'Objetos de Negocio';
        $data['content'] = 'backend/obn/index';

        $this->load->view('backend/template', $data);
    }

    public function crear() {
        $lista_obn = Doctrine::getTable('ObnStructure')->findAll();
        $data['edit'] = FALSE;
        $data['lista_obn'] = $lista_obn;
        $data['instancias'] = 0;
        $data['title'] = 'Edición de OBN';
        $data['content'] = 'backend/obn/editar';

        $this->load->view('backend/template', $data);
    }

    public function editar($obn_id) {
        $obn = Doctrine::getTable('ObnStructure')->find($obn_id);
        if (!$obn) {
            echo 'No tiene permisos para editar este OBN';
            exit;
        }
        $lista_obn = Doctrine::getTable('ObnStructure')->findAll();
        $obn_objeto = Doctrine::getTable(crearNombreClaseObjeto($obn->identificador))->count();
        
        if ($obn->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'No tiene permisos para editar este OBN';
            exit;
        }

        $attr = json_decode($obn->json)->OBN_ATR;
        $query = json_decode($obn->json)->OBN_CA;

        $data['obn'] = $obn;
        $data['lista_obn'] = $lista_obn;
        $data['attr'] = $attr;
        $data['instancias'] = $obn_objeto;
        $data['query'] = $query;
        $data['edit'] = TRUE;

        $data['title'] = 'Edición de OBN';
        $data['content'] = 'backend/obn/editar';

        $this->load->view('backend/template', $data);
    }

    public function editar_form($obn_id = NULL) {
        $op = "update";
        if ($obn_id) {
            $obn = Doctrine::getTable('ObnStructure')->find($obn_id);
        } else {
            $obn = new ObnStructure();
            $obn->cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
            $op = "insert";
        }

        if ($obn->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para editar este OBN.';
            exit;
        }

        $this->form_validation->set_rules('identificador', 'Identificador', 'validar_id_obn|required');
        $this->form_validation->set_rules('descripcion', 'Descripción', 'required');


        $respuesta = new stdClass();
        if ($this->form_validation->run() == TRUE) {
            $nuevos_atributos = array();
            if ($op == "update") {
                $nuevos_atributos = $obn->nuevosAtributosFromArray($this->input->post('attr', false));
            }
            $obn->identificador = strtolower($this->input->post('identificador'));
            $obn->descripcion = $this->input->post('descripcion', false);
            $obn->id_tabla_interna = crearIdentificadorTabla(strtolower($this->input->post('identificador')));
            try {
                $obn->save();
                if ($this->input->post('attr')) {
                    $existe = $obn->setAtributosFromArray($this->input->post('attr', false));
                    if ($existe) {
                        $obn->save();
                        $respuesta->validacion = true;
                    } else {
                        $respuesta->validacion = FALSE;
                        $respuesta->errores = '<div class="alert alert-error">Ocurrió un error insertando los "<strong>tab_attrs@Atributos</strong>". No puede repetir los nombres de los atributos</div> ';
                    }
                } else {
                    if ($op == "insert") {
                        $obn->delete();
                    }
                    $respuesta->validacion = FALSE;
                    $respuesta->errores = '<div class="alert alert-error">Ocurrió un error insertando los "<strong>tab_attrs@Atributos</strong>". Estos no pueden estar vacíos</div> ';
                }

                if ($this->input->post('query')) {
                    $existe = $obn->setQuerysFromArray($this->input->post('query', false));
                    if ($existe) {
                        $obn->save();
                        $respuesta->validacion = true;
                    } else {
                        if ($op == "insert") {
                            $obn->delete();
                        }
                        $respuesta->validacion = FALSE;
                        $respuesta->errores = '<div class="alert alert-error">Ocurrió un error insertando las "<strong>tab_querys@Consultas</strong>". No puede repetir los nombres de las consultas</div> ';
                    }
                }
                if ($respuesta->validacion) {
                    try {
                        $obn->setJsonObn();
                        $obn->save();
                        $creado = $obn->crearModelo();
                        $modelo = new stdClass();
                        $modelo->validacion = false;
                        if ($creado) {
                            if ($op == "update") {
                                $clase = crearNombreClaseObjeto($obn->identificador);
                                $obn_nueva = Doctrine::getTable($clase)->findAll()->count();
                                if ($obn_nueva > 0) {
                                    $modelo = json_decode($obn->editarTablaModelo($nuevos_atributos));
                                } else {
                                    $modelo = json_decode($obn->crearTablaModelo());
                                }
                            } else {
                                $modelo = json_decode($obn->crearTablaModelo());
                            }
                        }
                        if (!$creado) {
                            if ($op == "insert") {
                                $obn->eliminarTablaModelo();
                                $obn->delete();
                            }
                            $respuesta->validacion = FALSE;
                            $respuesta->errores = '<div class="alert alert-error">No se pudo crear el Objeto de Negocio. Existe error al crear el modelo de dato.</div> ';
                        }
                        if ($modelo->validacion == true && $creado) {
                            $respuesta->validacion = TRUE;
                            $respuesta->redirect = site_url('backend/obns/listar');
                            auditar('ObnStructure', $op, $obn->id, UsuarioBackendSesion::usuario()->usuario);
                        } else if ($modelo->validacion == false && $creado) {
                            if ($op == "insert") {
                                $obn->eliminarTablaModelo();
                                $obn->delete();
                            }
                            $respuesta = $modelo;
                        }
                    } catch (Exception $exc) {
                        if ($op == "insert") {
                            $obn->eliminarTablaModelo();
                            $obn->delete();
                        }
                        $respuesta->validacion = FALSE;
                        $respuesta->errores = '<div class="alert alert-error">No se pudo crear el Objeto de Negocio. Ocurió un error inesperado.</div> ';
                    }
                }
            } catch (Exception $e) {
                if (true) {
                    $respuesta->validacion = FALSE;
                    $respuesta->errores = '<div class="alert alert-error">Ya existe un Objeto de Negocio con este "<strong>identificador@Identificador</strong>".</div> ';
                }
            }
        } else {
            $respuesta->validacion = FALSE;
            $respuesta->errores = validation_errors();
        }

        echo json_encode($respuesta);
    }

    public function eliminar($obn_id) {
        $obn = Doctrine::getTable('ObnStructure')->find($obn_id);

        if ($obn->cuenta_id != UsuarioBackendSesion::usuario()->cuenta_id) {
            echo 'Usuario no tiene permisos para eliminar este OBN.';
            exit;
        }

        auditar('ObnStructure', 'delete', $obn->id, UsuarioBackendSesion::usuario()->usuario);
        $sql = "DROP TABLE IF EXISTS " . $obn->id_tabla_interna . ";" . "\n";

        unlink(DIR_OBN . $obn->id_tabla_interna . ".php");
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();
        try {
            $q->execute($sql);
            $obn->delete();
            redirect('backend/obns/listar');
        } catch (Exception $exc) {
            echo $exc->getCode();
        }
    }

    function getObn($param) {
        if (is_file(DIR_OBN . "obn_" . $param . ".php")) {
            $clase = crearNombreClaseObjeto($param);
            $lista = $clase::obtenerOBN();
            $result = array();
            foreach ($lista as $value) {
                $result[] = json_decode(obtenerOBN($value->id, $param));
            }
            echo json_encode($result);
        } else {
            echo "No se pudo cargar el objeto especificado. No existe o no tienes acceso";
        }
    }

    public function exportar($obn_id) {
        $obn = Doctrine::getTable('ObnStructure')->find($obn_id);

        $json = $obn->exportComplete();

        header("Content-Disposition: attachment; filename=\"" . mb_convert_case(str_replace(' ', '-', $obn->identificador), MB_CASE_LOWER) . ".obn\"");
        header('Content-Type: application/json');
        echo $json;
    }

    public function importar() {
        $mensajes = '';
        $file_path = $_FILES['archivo']['tmp_name'];

        $permitido = array('obn');
        $nombre_archivo = $_FILES['archivo']['name'];
        $ext = pathinfo($nombre_archivo, PATHINFO_EXTENSION);
        if (!in_array($ext, $permitido)) {
            $mensajes = '<div class="alert alert-error"><i class="icon-exclamation-sign"></i> La extensión del archivo de importación no es la correcta.</div>';
        } else {
            if ($file_path) {
                $input = file_get_contents($_FILES['archivo']['tmp_name']);
                $obj = json_decode($input);
                $obn_existe = Doctrine::getTable('ObnStructure')->findOneByIdentificador($obj->identificador);
                if ($obn_existe) {
                    $mensajes = '<div class="alert alert-error"><i class="icon-exclamation-sign"></i> Hubo un error al procesar la importación, es posible que ya exista un Objeto de Negocio con el mismo identificador.</div>';
                } else {
                    $obn = ObnStructure::importComplete($input);
                    if ($obn == '-1') {
                        $mensajes = '<div class="alert alert-error"><i class="icon-exclamation-sign"></i> Hubo un error al procesar la importación, es posible que el archivo se encuentre dañado.</div>';
                    } else {
                        $obn->save();
                        $obn->setJsonObn();
                        $obn->save();
                        auditar('ObnStructure', "insert", $obn->id, UsuarioBackendSesion::usuario()->usuario);
                        $obn->crearModelo();
                        $obn->crearTablaModelo();
                    }
                }
            }
        }

        if ($mensajes != '') {
            $data['mensajes'] = $mensajes;
        }

        $cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;
        $obn = Doctrine::getTable('ObnStructure')->findByCuentaId($cuenta_id);
        $lista = array();
        foreach ($obn as $value) {
            if(existeClase($value)) {
                $lista[]=$value;
            }   
        }
        $data['obn'] = $lista;
        $data['title'] = 'Objetos de Negocio';
        $data['content'] = 'backend/obn/index';
        $this->load->view('backend/template', $data);
    }

}
