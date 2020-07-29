<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class obn_consultas extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('cookies_helper');
        $this->load->helper('buscar_obn_helper');
        if (UsuarioSesion::usuario_con_empresas_luego_login()) {
            redirect('autenticacion/login_empresa');
        }
    }

    //OK
    public function obener_atributo_obn() {
        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $end = $this->input->post('length');
        $etapa = $this->input->post('etapa');
        if ($this->input->post('variable_obn') && $this->input->post('etapa')) {
            $variable = $this->input->post('variable_obn');
            $buscando_atributo2 = explode(".", $variable);
            $variable = $buscando_atributo2[0];
            if (isset($buscando_atributo2[1])) {
                if ($buscando_atributo2[1][0] != "[") {
                    $variable = $buscando_atributo2[0] . "." . $buscando_atributo2[1];
                    if (isset($buscando_atributo2[2]) && isset($buscando_atributo2[3])) {
                        $indice = $buscando_atributo2[2];
                        $atributo2 = $buscando_atributo2[3];
                    } else if (isset($buscando_atributo2[2])) {
                        $indice = false;
                        $atributo2 = $buscando_atributo2[2];
                    }
                } else {
                    if (isset($buscando_atributo2[1]) && isset($buscando_atributo2[2])) {
                        $indice = $buscando_atributo2[1];
                        $atributo2 = $buscando_atributo2[2];
                    } else if (isset($buscando_atributo2[1])) {
                        $indice = false;
                        $atributo2 = $buscando_atributo2[1];
                    }
                }
            }
            $atributos = json_decode(htmlspecialchars_decode($this->input->post('atributos')));
            $regla = new Regla($variable);
            $valor = $regla->getExpresionParaOutput($etapa);
            if ($valor) {
                $v = json_decode(htmlspecialchars_decode($valor));
            } else {
                $buscando_atributo1 = explode(".", $variable);
                $variable = $buscando_atributo1[0];
                if (isset($buscando_atributo1[1]))
                    $atributo2 = $buscando_atributo1[1];
                $indice = false;
                $regla = new Regla($variable);
                $valor = $regla->getExpresionParaOutput($etapa);
                if ($valor) {
                    $v = json_decode(htmlspecialchars_decode($valor));
                } else {
                    $v = "";
                }
            }
            $consulta = "obtenerIn";
            $data = array();
            if (isset($v->sql) && !isset($atributo2)) {
                if (isset($v->consulta)) {
                    $consulta = $v->consulta;
                }
                $obn_id = $v->identificador;
                $param_base = isset($v->parametros) ? $v->parametros : "";
                $param = "";
                if (is_array($param_base)) {
                    $param = $param_base;
                }
                $clase = $v->sql;
                if (count($param) > 0) {
                    $total = $clase::$consulta($param)->count();
                    $query = $clase::$consulta($param);
                    $query = $query
                            ->offset($start)
                            ->limit($end)
                            ->execute();
                } else {
                    $query = false;
                    $total = 1;
                }
                if ($query) {
                    foreach ($query as $value) {
                        $obn = json_decode(obtenerOBN($value->id, $obn_id));
                        $obj = array();
                        $obj["acciones"] = "";
                        foreach ($atributos as $value) {
                            $nomnbre_atributo = $value->atributo;
                            if (isset($obn->$nomnbre_atributo)) {
                                $obj[$nomnbre_atributo] = $obn->$nomnbre_atributo;
                            }
                        }
                        $obj["id"] = $obn->id;
                        $data[] = $obj;
                    }
                } else {
                    $data = "";
                }
            } else if (isset($v->OBN) && isset($v->id) && !isset($atributo2)) {
                if ($v->id != null) {
                    $obn = $v;
                    $obj = array();
                    $obj["acciones"] = "";
                    foreach ($atributos as $value) {
                        $nomnbre_atributo = $value->atributo;
                        if (isset($obn->$nomnbre_atributo)) {
                            $obj[$nomnbre_atributo] = $obn->$nomnbre_atributo;
                        }
                    }
                    $obj["id"] = $obn->id;
                    $data[] = $obj;
                    $total = 1;
                } else {
                    $data = "";
                    $total = 0;
                }
            } else if (isset($atributo2) && (isset($v->sql) || isset($v->OBN))) {

                if (isset($v->sql)) {
                    $clase = $v->sql;
                    $obn_id = $v->identificador;
                } else {
                    $clase = crearNombreClaseObjeto($v->OBN);
                    $obn_id = $v->OBN;
                }
                $query = json_decode(obnAtributo2($obn_id, $indice));

                if (isset($query->$atributo2)) {
                    $obn = $query;
                    if (isset($obn->$atributo2)) {
                        $objeto1 = $obn->$atributo2;
                        if (isset($objeto1->OBN)) {
                            if ($objeto1->id) {
                                $obn = $objeto1;
                                $obj = array();
                                $obj["acciones"] = "";
                                foreach ($atributos as $value) {
                                    $nomnbre_atributo = $value->atributo;
                                    if (isset($obn->$nomnbre_atributo)) {
                                        $obj[$nomnbre_atributo] = $obn->$nomnbre_atributo;
                                    }
                                }
                                $obj["id"] = $obn->id;
                                $data[] = $obj;
                                $total = 1;
                            } else {
                                $data = "";
                                $total = 0;
                            }
                        } else if (isset($objeto1->sql)) {
                            $obn_id = $objeto1->identificador;
                            $param_base = $objeto1->parametros;
                            if (isset($objeto1->consulta)) {
                                $consulta = $objeto1->consulta;
                            }
                            $param = "";
                            if (is_array($param_base)) {
                                $param = $param_base;
                            }
                            $clase = $objeto1->sql;
                            if (count($param) > 0) {
                                $total = $clase::$consulta($param)->count();
                                $query = $clase::$consulta($param);
                                $query = $query
                                        ->offset($start)
                                        ->limit($end)
                                        ->execute();
                            } else {
                                $query = false;
                                $total = 1;
                            }
                            if ($query) {
                                foreach ($query as $value) {
                                    $obn = json_decode(obtenerOBN($value->id, $obn_id));
                                    $obj = array();
                                    $obj["acciones"] = "";
                                    foreach ($atributos as $value) {
                                        $nomnbre_atributo = $value->atributo;
                                        if (isset($obn->$nomnbre_atributo)) {
                                            $obj[$nomnbre_atributo] = $obn->$nomnbre_atributo;
                                        }
                                    }
                                    $obj["id"] = $obn->id;
                                    $data[] = $obj;
                                }
                            } else {
                                $data = "";
                                $total = 0;
                            }
                        }
                    }
                } else {
                    $data = "";
                    $total = 0;
                }
            } else {
                $data = "";
                $total = 0;
            }
            $json_data = array(
                "draw" => intval($draw),
                "recordsTotal" => intval($total),
                "recordsFiltered" => intval($total),
                "data" => $data   // total data array
            );
        } else {
            $json_data = array(
                "draw" => intval($draw),
                "recordsTotal" => intval(0),
                "recordsFiltered" => intval(0),
                "data" => array()   // total data array
            );
        }
        echo json_encode($json_data);
    }

    public function obener_atributo_obn_delete() {
        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $end = $this->input->post('length');
        $etapa = $this->input->post('etapa');

        if ($this->input->post('variable_obn') && $this->input->post('etapa')) {
            $variable = $this->input->post('variable_obn');
            $buscando_atributo2 = explode(".", $variable);
            $variable = $buscando_atributo2[0];
            if (isset($buscando_atributo2[1])) {
                if ($buscando_atributo2[1][0] != "[") {
                    $variable = $buscando_atributo2[0] . "." . $buscando_atributo2[1];
                    if (isset($buscando_atributo2[2]) && isset($buscando_atributo2[3])) {
                        $indice = $buscando_atributo2[2];
                        $atributo2 = $buscando_atributo2[3];
                    } else if (isset($buscando_atributo2[2])) {
                        $indice = false;
                        $atributo2 = $buscando_atributo2[2];
                    }
                } else {
                    if (isset($buscando_atributo2[1]) && isset($buscando_atributo2[2])) {
                        $indice = $buscando_atributo2[1];
                        $atributo2 = $buscando_atributo2[2];
                    } else if (isset($buscando_atributo2[1])) {
                        $indice = false;
                        $atributo2 = $buscando_atributo2[1];
                    }
                }
            }
            $atributos = json_decode(htmlspecialchars_decode($this->input->post('atributos')));
            if ($this->input->get('id')) {
                $parametro = json_decode(base64_decode($this->input->get('id')));
            }
            $regla = new Regla($variable);
            $valor = $regla->getExpresionParaOutput($etapa);
            if ($valor) {
                $v = json_decode(htmlspecialchars_decode($valor));
            } else {
                $v = "";
            }
            if (isset($v->sql)) {
                $clase = $v->sql;
                $identificador = $v->identificador;
            } elseif (isset($v->OBN)) {
                $clase = crearNombreClaseObjeto($v->OBN);
                $identificador = $v->OBN;
            }
            $consulta = "obtenerIn";
            $data = array();
            if (isset($parametro->eliminar)) {
                $remove = $parametro->eliminar;
                $eliminar = Doctrine::getTable($clase)->find($remove);
                if ($eliminar) {
                    $eliminar->delete();
                }
            }
            if (isset($v->sql) && isset($v->parametros)) {
                if (isset($v->consulta)) {
                    $consulta = $v->consulta;
                }
                $obn_id = $v->identificador;
                $param_base = $v->parametros;
                $param = "";
                if (is_array($param_base)) {
                    $param = $param_base;
                }
                if (isset($parametro->del)) {
                    $del = json_decode(htmlspecialchars_decode($parametro->del));
                    $param = array_diff($param, $del);
                }
                if (isset($parametro->add)) {
                    $add = json_decode(htmlspecialchars_decode($parametro->add));
                    $param = array_merge($param, $add);
                }
                $clase = $v->sql;
                if (count($param) > 0) {
                    $total = $clase::$consulta($param)->count();
                    $query = $clase::$consulta($param);
                    $query = $query
                            ->offset($start)
                            ->limit($end)
                            ->execute();
                } else {
                    $query = false;
                    $total = 1;
                }
                if ($query) {
                    foreach ($query as $value) {
                        $obn = json_decode(obtenerOBN($value->id, $identificador));
                        $obj = array();
                        $obj["acciones"] = "";
                        foreach ($atributos as $value) {
                            $nomnbre_atributo = $value->atributo;
                            $obj[$nomnbre_atributo] = $obn->$nomnbre_atributo;
                        }
                        $obj["id"] = $obn->id;
                        $data[] = $obj;
                    }
                } else {
                    $data = "";
                }
            } else if (isset($v->sql) && !isset($v->parametros)) {
                if (isset($v->consulta)) {
                    $consulta = $v->consulta;
                }
                $total = $clase::$consulta()->count();
                $query = $clase::$consulta();
                $query = $query
                        ->offset($start)
                        ->limit($end)
                        ->execute();
                if ($query) {
                    foreach ($query as $value) {
                        $obn = json_decode(obtenerOBN($value->id, $identificador));
                        $obj = array();
                        $obj["acciones"] = "";
                        foreach ($atributos as $value) {
                            $nomnbre_atributo = $value->atributo;
                            $obj[$nomnbre_atributo] = $obn->$nomnbre_atributo;
                        }
                        $obj["id"] = $obn->id;
                        $data[] = $obj;
                    }
                } else {
                    $data = "";
                }
            } else if (isset($v->OBN)) {
                $data = "";
                $total = 0;
            } else {
                $obn = $v;
                $obj = array();
                $obj["acciones"] = "";
                foreach ($atributos as $value) {
                    $nomnbre_atributo = $value->atributo;
                    $obj[$nomnbre_atributo] = $obn->$nomnbre_atributo;
                }
                $obj["id"] = $obn->id;
                $data = "";
                $total = 1;
            }
            $json_data = array(
                "draw" => intval($draw),
                "recordsTotal" => intval($total),
                "recordsFiltered" => intval($total),
                "data" => $data   // total data array
            );
        } else {
            $json_data = array(
                "draw" => intval($draw),
                "recordsTotal" => intval(0),
                "recordsFiltered" => intval(0),
                "data" => array()   // total data array
            );
        }
        echo json_encode($json_data);
    }

    //OK
    public function obener_atributo_obn_op() {
        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $end = $this->input->post('length');
        $etapa = $this->input->post('etapa');

        if ($this->input->post('variable_obn') && $this->input->post('etapa')) {
            $variable = $this->input->post('variable_obn');
            $buscando_atributo2 = explode(".", $variable);
            $variable = $buscando_atributo2[0];
            if (isset($buscando_atributo2[1])) {
                if ($buscando_atributo2[1][0] != "[") {
                    $variable = $buscando_atributo2[0] . "." . $buscando_atributo2[1];
                    if (isset($buscando_atributo2[2]) && isset($buscando_atributo2[3])) {
                        $indice = $buscando_atributo2[2];
                        $atributo2 = $buscando_atributo2[3];
                    } else if (isset($buscando_atributo2[2])) {
                        $indice = false;
                        $atributo2 = $buscando_atributo2[2];
                    }
                } else {
                    if (isset($buscando_atributo2[1]) && isset($buscando_atributo2[2])) {
                        $indice = $buscando_atributo2[1];
                        $atributo2 = $buscando_atributo2[2];
                    } else if (isset($buscando_atributo2[1])) {
                        $indice = false;
                        $atributo2 = $buscando_atributo2[1];
                    }
                }
            }
            $atributos = json_decode(htmlspecialchars_decode($this->input->post('atributos')));
            if ($this->input->get('id')) {
                $parametro = json_decode(base64_decode($this->input->get('id')));
            }
            $regla = new Regla($variable);
            $valor = $regla->getExpresionParaOutput($etapa);
            if ($valor) {
                $v = json_decode(htmlspecialchars_decode($valor));
            } else {
                $v = "";
            }
            $consulta = "obtenerIn";
            $data = array();
            if (isset($v->sql)) {
                $clase = $v->sql;
                $identificador = $v->identificador;
            } elseif (isset($v->OBN)) {
                $clase = crearNombreClaseObjeto($v->OBN);
                $identificador = $v->OBN;
            }
            if (isset($atributo2) && isset($indice)) {
                $obn = json_decode(htmlspecialchars_decode(obnAtributo2($identificador, $indice)));
                if (isset($obn->$atributo2)) {
                    $v = $obn->$atributo2;
                    $objeto1 = $obn->$atributo2;
                    if (isset($objeto1->OBN)) {
                        $identificador = $objeto1->OBN;
                        $clase = crearNombreClaseObjeto($objeto1->OBN);
                    } else if (isset($objeto1->sql)) {
                        $identificador = $objeto1->identificador;
                        $clase = $objeto1->sql;
                    }
                }
            }
            if (isset($v->sql)) {
                $param_base = $v->parametros;
                $param = "";
                if (is_array($param_base)) {
                    $param = $param_base;
                }
                if (isset($parametro->del)) {
                    $del = json_decode(htmlspecialchars_decode($parametro->del));
                    $param = array_diff($param, $del);
                }
                if (isset($parametro->add)) {
                    $add = json_decode(htmlspecialchars_decode($parametro->add));
                    $param = array_merge($param, $add);
                }
                if (count($param) > 0) {
                    $total = $clase::$consulta($param)->count();
                    $query = $clase::$consulta($param);
                    $query = $query
                            ->offset($start)
                            ->limit($end)
                            ->execute();
                } else {
                    $query = false;
                    $total = 1;
                }
                if ($query) {
                    foreach ($query as $value) {
                        $obn = json_decode(obtenerOBN($value->id, $identificador));
                        $obj = array();
                        $obj["acciones"] = "";
                        foreach ($atributos as $value) {
                            $nomnbre_atributo = $value->atributo;
                            if (isset($obn->$nomnbre_atributo)) {
                                $obj[$nomnbre_atributo] = $obn->$nomnbre_atributo;
                            }
                        }
                        $obj["id"] = $obn->id;
                        $data[] = $obj;
                    }
                } else {
                    $data = "";
                }
            } else {
                $add = json_decode(htmlspecialchars_decode($parametro->add));
                if (isset($add[0])) {
                    $obn = $obn = json_decode(obtenerOBN($add[0], $identificador));
                    $obj = array();
                    $obj["acciones"] = "";
                    foreach ($atributos as $value) {
                        $nomnbre_atributo = $value->atributo;
                        if (isset($obn->$nomnbre_atributo)) {
                            $obj[$nomnbre_atributo] = $obn->$nomnbre_atributo;
                        }
                    }
                    $obj["id"] = $obn->id;
                    $data[] = $obj;
                    $total = 1;
                } else {
                    $data = "";
                    $total = 0;
                }
            }

            $json_data = array(
                "draw" => intval($draw),
                "recordsTotal" => intval($total),
                "recordsFiltered" => intval($total),
                "data" => $data   // total data array
            );
        } else {
            $json_data = array(
                "draw" => intval($draw),
                "recordsTotal" => intval(0),
                "recordsFiltered" => intval(0),
                "data" => array()   // total data array
            );
        }
        echo json_encode($json_data);
    }

    //OK
    public function lista_obn_atributo() {
        $etapa = $this->input->post('etapa');
        $variable = $this->input->post('variable_obn');
        $buscando_atributo2 = explode(".", $variable);
        $variable = $buscando_atributo2[0];
        if (isset($buscando_atributo2[1])) {
            if ($buscando_atributo2[1][0] != "[") {
                $variable = $buscando_atributo2[0] . "." . $buscando_atributo2[1];
                if (isset($buscando_atributo2[2]) && isset($buscando_atributo2[3])) {
                    $indice = $buscando_atributo2[2];
                    $atributo2 = $buscando_atributo2[3];
                } else if (isset($buscando_atributo2[2])) {
                    $indice = false;
                    $atributo2 = $buscando_atributo2[2];
                }
            } else {
                if (isset($buscando_atributo2[1]) && isset($buscando_atributo2[2])) {
                    $indice = $buscando_atributo2[1];
                    $atributo2 = $buscando_atributo2[2];
                } else if (isset($buscando_atributo2[1])) {
                    $indice = false;
                    $atributo2 = $buscando_atributo2[1];
                }
            }
        }
        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $end = $this->input->post('length');

        $regla = new Regla($variable);
        $valor = $regla->getExpresionParaOutput($etapa);
        if ($valor) {
            $v = json_decode(htmlspecialchars_decode($valor));
        } else {
            $buscando_atributo1 = explode(".", $variable);
            $variable = $buscando_atributo1[0];
            $atributo2 = $buscando_atributo1[1];
            $indice = false;
            $regla = new Regla($variable);
            $valor = $regla->getExpresionParaOutput($etapa);
            if ($valor) {
                $v = json_decode(htmlspecialchars_decode($valor));
            } else {
                $v = "";
            }
        }
        $consulta = "obtenerIn";
        $data = array();
        if (isset($v->sql)) {
            $clase = $v->sql;
            $identificador = $v->identificador;
        } elseif (isset($v->OBN)) {
            $clase = crearNombreClaseObjeto($v->OBN);
            $identificador = $v->OBN;
        }

        if (isset($atributo2) && isset($indice)) {
            $obn = json_decode(obnAtributo2($identificador, $indice));
            if (isset($obn->$atributo2)) {
                $objeto1 = $obn->$atributo2;
                if (isset($objeto1->OBN)) {
                    $identificador = $objeto1->OBN;
                    $clase = crearNombreClaseObjeto($objeto1->OBN);
                } else if (isset($objeto1->sql)) {
                    $identificador = $objeto1->identificador;
                    $clase = $objeto1->sql;
                }
            }
        }
        if (isset($clase)) {
            if ($this->input->post('columns')) {
                $where = "";
                $col = $this->input->post('columns');
                foreach ($col as $value) {
                    if ($value['search']['value']) {
                        $where .= $value['name'] . " like '%" . $value['search']['value'] . "%' and ";
                    }
                }
                $where = substr($where, 0, -4);
            }
            $obn_datos = Doctrine::getTable('ObnStructure')->findOneByIdentificador($identificador);
            $atributos = json_decode($obn_datos->json);
            $atributos = $atributos->OBN_ATR;
            $query = $clase::obtenerOBN();
            if ($where != "") {
                $query = $query->Where($where);
            }
            $total = $query->count();
            $query = $query->offset($start)
                    ->limit($end)
                    ->execute();

            if ($query) {
                foreach ($query as $value) {
                    $obn = json_decode(obtenerOBN($value->id, $identificador));
                    $obj = array();
                    foreach ($atributos as $attr) {
                        if ($attr->multiple != 1 && $attr->tipo != "obn") {
                            $nomnbre_atributo = $attr->nombre;
                            $obj[$nomnbre_atributo] = $obn->$nomnbre_atributo;
                        }
                    }
                    $obj["id"] = $obn->id;
                    $data[] = $obj;
                }
            } else {
                $data = "";
                $total = 1;
            }
        } else {
            $data = $v;
            $total = 1;
        }
        $json_data = array(
            "draw" => intval($draw),
            "recordsTotal" => intval($total),
            "recordsFiltered" => intval($total),
            "data" => $data   // total data array
        );
        echo json_encode($json_data);
    }

    //OK
    public function tabla_obn() {
        $etapa = $this->input->get('etapa');
        $variable = base64_decode($this->input->get('obn'));
        $buscando_atributo2 = explode(".", $variable);
        $variable = $buscando_atributo2[0];
        if (isset($buscando_atributo2[1])) {
            if ($buscando_atributo2[1][0] != "[") {
                $variable = $buscando_atributo2[0] . "." . $buscando_atributo2[1];
                if (isset($buscando_atributo2[2]) && isset($buscando_atributo2[3])) {
                    $indice = $buscando_atributo2[2];
                    $atributo2 = $buscando_atributo2[3];
                } else if (isset($buscando_atributo2[2])) {
                    $indice = false;
                    $atributo2 = $buscando_atributo2[2];
                }
            } else {
                if (isset($buscando_atributo2[1]) && isset($buscando_atributo2[2])) {
                    $indice = $buscando_atributo2[1];
                    $atributo2 = $buscando_atributo2[2];
                } else if (isset($buscando_atributo2[1])) {
                    $indice = false;
                    $atributo2 = $buscando_atributo2[1];
                }
            }
        }

        $campo = $this->input->get('campo');
        $campo = Doctrine::getTable('Campo')->find($campo);
        $regla = new Regla($variable);
        $valor = $regla->getExpresionParaOutput($etapa);
        if ($valor) {
            $v = json_decode(htmlspecialchars_decode($valor));
        } else {
            $buscando_atributo1 = explode(".", $variable);
            $variable = $buscando_atributo1[0];
            $atributo2 = $buscando_atributo1[1];
            $indice = false;
            $regla = new Regla($variable);
            $valor = $regla->getExpresionParaOutput($etapa);
            if ($valor) {
                $v = json_decode(htmlspecialchars_decode($valor));
            } else {
                $v = "";
            }
        }

        if (isset($v->sql)) {
            $clase = $v->sql;
            $identificador = $v->identificador;
        } elseif (isset($v->OBN)) {
            $clase = crearNombreClaseObjeto($v->OBN);
            $identificador = $v->OBN;
        }

        if (isset($atributo2) && isset($indice)) {
            $obn = json_decode(obnAtributo2($identificador, $indice));
            if (isset($obn->$atributo2)) {
                $objeto1 = $obn->$atributo2;
                if (isset($objeto1->OBN)) {
                    $identificador = $objeto1->OBN;
                    $clase = crearNombreClaseObjeto($objeto1->OBN);
                } else if (isset($objeto1->sql)) {
                    $identificador = $objeto1->identificador;
                    $clase = $objeto1->sql;
                }
            }
        }
        $data = array();
        if (isset($clase)) {
            $obn_datos = Doctrine::getTable('ObnStructure')->findOneByIdentificador($identificador);
            $atributos = json_decode($obn_datos->json);
            $atributos = $atributos->OBN_ATR;
            foreach ($atributos as $attr) {
                if ($attr->multiple != 1 && $attr->tipo != "obn") {
                    $nomnbre_atributo = $attr->nombre;
                    $obj[] = $nomnbre_atributo;
                }
            }
        } else {
            $obj = $v;
            $total = 1;
        }
        $data['etapa'] = $etapa;
        $data['columnas'] = json_encode($obj);
        $data['campo'] = $campo;
        $data['variable_obn'] = base64_decode($this->input->get('obn'));
        $this->load->view('obn/tabla_obn', $data);
    }

    public function alta_obn() {
        $etapa_id = $this->input->post('etapa');
        $id_obn = $this->input->post('id');
        $url = $this->input->post('secuencia');
        $variable = $this->input->post('variable_obn');
        $regla = new Regla($variable);
        $valor = $regla->getExpresionParaOutput($etapa_id);
        if ($valor) {
            $v = json_decode(htmlspecialchars_decode($valor));
        } else {
            $v = "";
        }
        $objeto = null;
        if (isset($v->sql)) {
            $clase = $v->sql;
        } else if (isset($v->OBN)) {
            $clase = crearNombreClaseObjeto($v->OBN);
        } else {
            $clase = false;
        }
        if ($clase) {
            if ($this->input->post('id')) {
                $objeto = Doctrine::getTable($clase)->find($this->input->post('id'));
                $edicion = 1;
            } else {
                $objeto = new $clase();
                $edicion = 0;
            }
            $warning = "";
            $campo_id = $this->input->post('campo');
            $campo = Doctrine::getTable('Campo')->find($campo_id);
            $formulario_id = $campo->extra->formulario_tabla_datos;
            $formulario = Doctrine::getTable('Formulario')->find($formulario_id);
            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            $data = array();
            $data['redirect_form'] = $url;
            $data['content'] = 'obn/edit';
            $data['objeto'] = $objeto;
            $data['edicion'] = $edicion;
            $data['etapa'] = $etapa;
            $data['formulario'] = $formulario;
            $data['variable_obn'] = $variable;
            $data['title'] = "Edición de Objeto de Negocio";
            $this->load->view('template_sin_menu', $data);
        } else {
            $data = array();
            $data['error'] = 'Ocurrió un error al cargar el formulario';
            $data['content'] = 'obn/error';
            $data['redirect_form'] = $url;
            $data['title'] = $data['error'];
            $this->load->view('template', $data);
            return;
        }
    }

    public function alta_obn_form() {
        $formulario_id = $this->input->post('formulario_id');
        $variable_obn = $this->input->post('variable_obn');
        $redirect_form = $this->input->post('redirect_form');
        $etapa_id = $this->input->post('etapa_id');
        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
        $formulario = Doctrine::getTable('Formulario')->find($formulario_id);
        $obj_id = $this->input->post('id');
        $regla = new Regla($variable_obn);
        $valor = $regla->getExpresionParaOutput($etapa_id);
        $respuesta = new stdClass();

        if ($valor) {
            $v = json_decode(htmlspecialchars_decode($valor));
        } else {
            $v = "";
        }
        if (isset($v->sql)) {
            $clase = $v->sql;
        } else if (isset($v->OBN)) {
            $clase = crearNombreClaseObjeto($v->OBN);
            $identificador = $v->OBN;
        } else {
            $clase = FALSE;
        }

        if ($clase == false) {
            $respuesta->validacion = FALSE;
            $respuesta->errores = "No se pudo crear el Objeto";
        } else {
            if ($obj_id) {
                $objeto = Doctrine::getTable($clase)->find($obj_id);
                $edicion = 1;
            } else {
                $objeto = new $clase();
                $edicion = 0;
            }


            $validar_formulario = FALSE;
            foreach ($formulario->Campos as $c) {
                //Validamos los campos que no sean readonly y que esten disponibles (que su campo dependiente se cumpla)
                if ($c->isEditableWithCurrentPOST()) {
                    $c->formValidate($etapa->id);
                    $validar_formulario = TRUE;
                }
            }
            if ($this->form_validation->run() == TRUE) {
                $validado = true;
            } else {
                $validado = false;
            }
            if (!$validar_formulario || $validado) {
                foreach ($formulario->Campos as $c) {
                    $nombre_atributo = $c->nombre;
                    if (isset($objeto->$nombre_atributo)) {
                        if ($c->tipo == "date") {
                            $fecha = $this->input->post($c->nombre);
                            if ($fecha != "") {
                                $fecha_ok = date("Y/m/d", strtotime($fecha));
                            } else {
                                $fecha_ok = null;
                            }
                            $objeto->$nombre_atributo = $fecha_ok;
                        } elseif ($c->tipo == "tabla_datos") {
                            $nuevos_param = json_decode(htmlspecialchars_decode($this->input->post($c->nombre . '_idobn')));
                            $nuevos_id = "";
                            foreach ($nuevos_param as $value2) {
                                $nuevos_id .= $value2 . ",";
                            }
                            $objeto->$nombre_atributo = substr($nuevos_id, 0, -1);
                        } else {
                            $objeto->$nombre_atributo = $this->input->post($c->nombre);
                        }
                    }
                }
                try {
                    $objeto->save();
                    if ($edicion == 0) {
                        $obn = explode(".", $variable_obn);

                        if (isset($obn[1])) {
                            $atributo = $obn[1];
                            $obn = $obn[0];
                            $obn = substr($obn, 1);
                            $obj_obn_padre = Doctrine::getTable('ObnDatosSeguimiento')->findByNombreHastaEtapa($obn, $etapa->id);
                            $json_obn_padre = json_decode(htmlspecialchars_decode($obj_obn_padre->valor));
                            $atributo_obn_padre = $json_obn_padre->$atributo;
                            if (isset($atributo_obn_padre->parametros)) {
                                if (!in_array($objeto->id, $atributo_obn_padre)) {
                                    array_push($atributo_obn_padre, $objeto->id);
                                }
                                $json_obn_padre->$atributo->parametros = $atributo_obn_padre;
                            } else {
                                $json_obn_padre->$atributo = json_decode(obtenerOBN($objeto->id, $identificador, 1));
                            }
                            $obj_obn_padre->valor = json_encode($json_obn_padre);
                            $obj_obn_padre->save();
                        }
                    }
                    $respuesta = new stdClass();
                    $respuesta->redirect = $redirect_form;
                    $respuesta->validacion = true;
                } catch (Exception $exc) {
                    if ($exc->getCode() == 23000) {
                        $respuesta->errores = "Existen registros con esos datos.";
                        $respuesta->validacion = FALSE;
                    } else {
                        $respuesta->errores = "Ocurrió un error al intentar guardar los datos.";
                        $respuesta->validacion = FALSE;
                    }
                }
            } else {
                $respuesta->validacion = FALSE;
                $respuesta->errores = validation_errors();
            }
        }
        echo json_encode($respuesta);
    }

}
