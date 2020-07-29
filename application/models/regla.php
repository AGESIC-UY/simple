<?php

class Regla {

    private $regla;

    function __construct($regla) {
        $this->regla = $regla;
    }

    //Evalua la regla de acuerdo a los datos capturados en el tramite tramite_id
    public function evaluar($etapa_id) {
        if (!$this->regla) {
            return TRUE;
        }

        $new_regla = $this->getExpresionParaEvaluar($etapa_id);
        $new_regla = 'return ' . $new_regla . ';';
        $CI = & get_instance();
        $CI->load->library('SaferEval');
        $resultado = FALSE;
        if (!$errores = $CI->safereval->checkScript($new_regla, FALSE)) {
            $resultado = @eval($new_regla);
        }
        return $resultado;
    }

    //Obtiene la expresion con los reemplazos de variables ya hechos de acuerdo a los datos capturados en el tramite tramite_id.
    //Esta expresion es la que se evalua finalmente en la regla
    public function getExpresionParaEvaluar($etapa_id) {
        $new_regla = $this->regla;

        $new_regla = preg_replace_callback('/@@(\w+)((->\w+|\[\w+\])*)/', function($match) use ($etapa_id) {
            $nombre_dato = $match[1];
            $accesor = isset($match[2]) ? $match[2] : '';

            $dato = Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($nombre_dato, $etapa_id);
            if ($dato) {
                if (is_array($dato->valor)) {
                    $dato_almacenado = eval('$x=json_decode(\'' . json_encode($dato->valor, JSON_UNESCAPED_UNICODE) . '\'); $x=$x; return $x' . $accesor . ';');
                    if (!$dato_almacenado) {
                        $dato_almacenado = eval('$x=json_decode(\'' . json_encode($dato->valor, JSON_HEX_APOS) . '\'); $x=$x; return $x' . $accesor . ';');
                    }
                    $valor_dato = 'json_decode(\'' . json_encode($dato_almacenado, JSON_UNESCAPED_UNICODE) . '\')';
                } else {
                    $dato_almacenado = eval('$x=json_decode(\'' . json_encode($dato->valor, JSON_HEX_APOS) . '\'); return $x' . $accesor . ';');
                    $valor_dato = 'json_decode(\'' . json_encode($dato_almacenado, JSON_UNESCAPED_UNICODE) . '\')';
                }
            } else {
                //No reemplazamos el dato
                $valor_dato = 'json_decode(\'' . json_encode(null) . '\')';
            }

            return $valor_dato;
        }, $new_regla);

        //Variables globales
        $new_regla = preg_replace_callback('/@#(\w+)/', function($match) use ($etapa_id) {
            $nombre_dato = $match[1];

            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            $dato = Doctrine::getTable('DatoSeguimiento')->findGlobalByNombreAndProceso($nombre_dato, $etapa->Tramite->id);
            $valor_dato = var_export($dato, true);

            return $valor_dato;
        }, $new_regla);

        $new_regla = preg_replace_callback('/@!(\w+)/', function($match) use ($etapa_id) {
            $nombre_dato = $match[1];

            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            $usuario = $etapa->Usuario;

            if ($nombre_dato == 'rut') {
                $dato_usuario_empresa = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('usuario_actuando_como_empresa', $etapa->id);
                if ($dato_usuario_empresa) {
                    return "'" . $usuario->usuario . "'";
                } else {
                    return "''";
                }
            } else if ($nombre_dato == 'nombre')         //Deprecated
                return "'" . $usuario->nombres . "'";
            else if ($nombre_dato == 'apellidos')      //Deprecated
                return "'" . $usuario->apellido_paterno . ' ' . $usuario->apellido_materno . "'";
            else if ($nombre_dato == 'nombres')
                return "'" . $usuario->nombres . "'";
            else if ($nombre_dato == 'apellido_paterno')
                return "'" . $usuario->apellido_paterno . "'";
            else if ($nombre_dato == 'apellido_materno')
                return "'" . $usuario->apellido_materno . "'";
            else if ($nombre_dato == 'email')
                return "'" . $usuario->email . "'";
            else if ($nombre_dato == 'documento') {
                $CI = &get_instance();
                $CI->load->helper('validacion_ci_regla_helper');
                $dato_usuario_empresa = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('usuario_actuando_como_empresa', $etapa->id);
                if ($dato_usuario_empresa) {
                    $usuario_empresa = Doctrine::getTable('Usuario')->find($dato_usuario_empresa->valor);
                    if (ci_validacion($usuario_empresa->usuario)) {
                        return "'" . $usuario_empresa->usuario . "'";
                    }
                } else {
                    if (ci_validacion($usuario->usuario)) {
                        return "'" . $usuario->usuario . "'";
                    }
                }
            } else if ($nombre_dato == 'version_simple') {
                return "'" . SIMPLE_VERSION . "'";
            } else if ($nombre_dato == 'tramite_id') {
                return "'" . Doctrine::getTable('Etapa')->find($etapa_id)->tramite_id . "'";
            } else if ($nombre_dato == 'nivel_confianza') {
                return "'" . UsuarioSesion::getNivel_confianza() . "'";
            } else if ($nombre_dato == 'UsuarioMesaDeEntrada') {
                $CI = &get_instance();
                if (UsuarioSesion::usuarioMesaDeEntrada() && $CI->session->userdata('id_usuario_ciudadano')) {
                    return 1;
                } else {
                    return 0;
                }
            }
        }, $new_regla);

        $new_regla = preg_replace_callback('/\$(\w+)((.)(\w+)){0,1}((.)(\w+)){0,1}/', function($match) use ($etapa_id) {
            $nombre_dato = $match[1];
            $accesor1 = "";
            $accesor2 = "";
            if (isset($match[2])) {
                $accesor1 = "->" . $match[4];
            }
            if (isset($match[5])) {
                $accesor2 = "->" . $match[7];
            }
            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            if ($etapa) {
                $dato_campo = Doctrine::getTable('ObnDatosSeguimiento')->findByNombreHastaEtapa($nombre_dato, $etapa->id);
            } else {
                $dato_campo = false;
            }
            $valor_dato = "";
            if ($dato_campo) {
                if (json_decode($dato_campo->valor)) {
                    $dato_almacenado = @eval('$x=json_decode(\'' . $dato_campo->valor . '\'); $x=$x; return $x' . $accesor1 . ';');
                    if ($dato_almacenado && $accesor2 != "") {
                        $dato_almacenado = @eval('$x=json_decode(\'' . json_encode($dato_almacenado, JSON_HEX_APOS) . '\'); $x=$x; return $x' . $accesor2 . ';');
                    }
                } else if ($dato_campo->valor) {
                    $dato_almacenado = @eval('$x=json_decode(\'' . json_encode($dato_campo->valor, JSON_HEX_APOS) . '\'); return $x' . $accesor1 . ';');
                    if ($dato_almacenado && $accesor2 != "") {
                        $dato_almacenado = @eval('$x=json_decode(\'' . json_encode($dato_almacenado, JSON_HEX_APOS) . '\'); $x=$x; return $x' . $accesor2 . ';');
                    }
                } else {
                    $dato_almacenado = "";
                }

                if (!is_string($dato_almacenado)) {
                    $valor_dato = 'json_decode(\'' . json_encode($dato_almacenado, JSON_UNESCAPED_UNICODE) . '\')';
                    //$valor_dato = htmlentities((string) $valor_dato,JSON_HEX_QUOT , 'utf-8', FALSE);
                    //print_r($valor_dato);
                } else {
                    $dato_almacenado = trim($dato_almacenado, "\"");
                    $valor_dato = 'json_decode(\'' . json_encode($dato_almacenado, JSON_UNESCAPED_UNICODE) . '\')';
                }
            }

            return $valor_dato;
        }, $new_regla);
        //Si quedaron variables sin reemplazar, la evaluacion deberia ser siempre falsa.
        if (preg_match('/@@\w+/', $new_regla))
            return false;
        if (preg_match('/$\w+/', $new_regla))
            return false;
        return $new_regla;
    }

    //Obtiene la expresion con los reemplazos de variables ya hechos de acuerdo a los datos capturados en el tramite tramite_id.
    //Esta es una representacion con las variables reemplazadas. No es una expresion evaluable. (Los arrays y strings no estan definidos como tal)
    public function getExpresionParaOutput($etapa_id) {
        $new_regla = $this->regla;
        $new_regla = preg_replace_callback('/@@(\w+)((->\w+|\[\w+\])*)/', function($match) use ($etapa_id) {
            $nombre_dato = $match[1];
            $accesor = isset($match[2]) ? $match[2] : '';

            $dato = Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($nombre_dato, $etapa_id);
            if ($dato) {
                if (is_array($dato->valor)) {
                    $dato_almacenado = eval('$x=json_decode(\'' . json_encode($dato->valor, JSON_UNESCAPED_UNICODE) . '\'); $x=$x; return $x' . $accesor . ';');
                    if (!$dato_almacenado) {
                        $dato_almacenado = eval('$x=json_decode(\'' . json_encode($dato->valor, JSON_HEX_APOS) . '\'); $x=$x; return $x' . $accesor . ';');
                    }
                } else {
                    $dato_almacenado = eval('$x=json_decode(\'' . json_encode($dato->valor, JSON_HEX_APOS) . '\'); return $x' . $accesor . ';');
                }

                if (!is_string($dato_almacenado))
                    $valor_dato = json_encode($dato_almacenado, JSON_UNESCAPED_UNICODE);
                else
                    $valor_dato = $dato_almacenado;
            }
            else {
                //Entregamos vacio
                $valor_dato = '';
            }

            return $valor_dato;
        }, $new_regla);

        //Variables globales
        $new_regla = preg_replace_callback('/@#(\w+)/', function($match) use ($etapa_id) {
            $nombre_dato = $match[1];

            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            $dato = Doctrine::getTable('DatoSeguimiento')->findGlobalByNombreAndProceso($nombre_dato, $etapa->Tramite->id);
            $valor_dato = json_encode($dato);

            return $valor_dato;
        }, $new_regla);

        $new_regla = preg_replace_callback('/@!(\w+)/', function($match) use ($etapa_id) {
            $nombre_dato = $match[1];

            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            $usuario = $etapa->Usuario;

            if ($nombre_dato == 'rut') {
                $dato_usuario_empresa = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('usuario_actuando_como_empresa', $etapa->id);
                if ($dato_usuario_empresa) {
                    return $usuario->usuario;
                } else {
                    return "";
                }
            } else if ($nombre_dato == 'nombre')         //Deprecated
                return $usuario->nombres;
            else if ($nombre_dato == 'apellidos')      //Deprecated
                return $usuario->apellido_paterno . ' ' . $usuario->apellido_materno;
            else if ($nombre_dato == 'nombres')
                return $usuario->nombres;
            else if ($nombre_dato == 'apellido_paterno')
                return $usuario->apellido_paterno;
            else if ($nombre_dato == 'apellido_materno')
                return $usuario->apellido_materno;
            else if ($nombre_dato == 'email')
                return $usuario->email;
            else if ($nombre_dato == 'documento') {
                $CI = &get_instance();
                $CI->load->helper('validacion_ci_regla_helper');
                $dato_usuario_empresa = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('usuario_actuando_como_empresa', $etapa->id);
                if ($dato_usuario_empresa) {
                    $usuario_empresa = Doctrine::getTable('Usuario')->find($dato_usuario_empresa->valor);
                    if (ci_validacion($usuario_empresa->usuario)) {
                        return $usuario_empresa->usuario;
                    }
                } else {
                    if (ci_validacion($usuario->usuario)) {
                        return $usuario->usuario;
                    }
                }
            } else if ($nombre_dato == 'version_simple') {
                return "" . SIMPLE_VERSION . "";
            } else if ($nombre_dato == 'tramite_id') {
                return Doctrine::getTable('Etapa')->find($etapa_id)->tramite_id;
            } else if ($nombre_dato == 'nivel_confianza') {
                return UsuarioSesion::getNivel_confianza();
            } else if ($nombre_dato == 'UsuarioMesaDeEntrada') {
                $CI = &get_instance();
                if (UsuarioSesion::usuarioMesaDeEntrada() && $CI->session->userdata('id_usuario_ciudadano')) {
                    return 1;
                } else {
                    return 0;
                }
            }
        }, $new_regla);

        $new_regla = preg_replace_callback('/\$(\w+)((.)(\w+)){0,1}((.)(\w+)){0,1}/', function($match) use ($etapa_id) {

            if ($this->evaluar($etapa_id)) {
                $nombre_dato = $match[1];
                $accesor1 = "";
                $accesor2 = "";
                if (isset($match[2])) {
                    $accesor1 = "->" . $match[4];
                }
                if (isset($match[5])) {
                    $accesor2 = "->" . $match[7];
                }

                $valor_dato = "";
                $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
                if ($etapa) {
                    $dato_campo = Doctrine::getTable('ObnDatosSeguimiento')->findByNombreHastaEtapa($nombre_dato, $etapa->id);
                } else {
                    $dato_campo = false;
                }
                if ($dato_campo) {
                    if (json_decode($dato_campo->valor)) {
                        $dato_almacenado = eval('$x=json_decode(\'' . $dato_campo->valor . '\'); $x=$x; return $x' . $accesor1 . $accesor2 . ';');
                    } else if ($dato_campo->valor) {
                        $dato_almacenado = eval('$x=json_decode(\'' . json_encode($dato_campo->valor, JSON_HEX_APOS) . '\'); return $x' . $accesor1 . $accesor2 . ';');
                    } else {
                        $dato_almacenado = "";
                    }

                    if (!is_string($dato_almacenado)) {
                        $valor_dato = json_encode($dato_almacenado, JSON_HEX_APOS);
                        $valor_dato = htmlentities((string) $valor_dato, ENT_QUOTES, 'utf-8', FALSE);
                    } else {
                        $dato_almacenado = trim($dato_almacenado, "\"");
                        $valor_dato = $dato_almacenado;
                    }
                }
            } else {
                $valor_dato = "";
            }
            return $valor_dato;
        }, $new_regla);

        return $new_regla;
    }

}
