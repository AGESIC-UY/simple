<?php

class Validacion extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('contenido');
        $this->hasColumn('proceso_id');
        $this->hasColumn('filename');
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('Proceso', array(
            'local' => 'proceso_id',
            'foreign' => 'id'
        ));
    }

    public function displayForm() {
        return NULL;
    }

    public function validateForm() {
        return;
    }
    
    public function ejecutar($etapa) {
        $CI = & get_instance();
        $CI->load->helper('jsonvariable');
        $variables = '"' . jsonVariables($etapa->id) . '"';
        $b64 = base64_encode($variables);
        $output = array();
        if (!is_file(DIR_VALIDACION . $this->filename)) {
            $CI->load->helper('validacion_file_helper');
            validacionFile($this->filename, $this);
        }
        $comando = 'jjs ' . DIR_VALIDACION . $this->filename . ' -J-Djava.security.manager -- "' . $b64 . '"';
        exec($comando, $output, $return_var);
        $this->eliminarErrorValidacion($etapa->id);
        if ($return_var == 0) {
            return $this->guardar_dato_seguimiento($output[0], $etapa);
        } else {
            $dato_seguimiento = new DatoSeguimiento();
            $dato_seguimiento->etapa_id = $etapa->id;
            $dato_seguimiento->nombre = "validacion_error_ejecucion";
            $dato_seguimiento->valor = "Ha ocurrido un error ejecutando la validación " . $this->nombre . " de tipo " . $return_var;
            $dato_seguimiento->save();
        }

        return true;
    }

    private function guardar_dato_seguimiento($dato_seguimento_guardar, $etapa) {
        $datos = json_decode($dato_seguimento_guardar);
        $error = json_last_error();
        if ($error === JSON_ERROR_NONE) {
            if ($datos->resultado == "OK") {                
                
            } else if ($datos->resultado == "ERROR") {
                $dato_seguimiento = new DatoSeguimiento();
                $dato_seguimiento->etapa_id = $etapa->id;
                $dato_seguimiento->nombre = "validacion_error";
                $dato_seguimiento->valor = true;
                $dato_seguimiento->save();
                $array_error = $datos->errores;
                $dato_seguimiento = new DatoSeguimiento();
                $dato_seguimiento->etapa_id = $etapa->id;
                $dato_seguimiento->nombre = "validacion_error_campo";
                $dato_seguimiento->valor = (string) json_encode($array_error);
                $dato_seguimiento->save();
            }
            if (isset($datos->variables_seguimiento)) {
                $array_variables = $datos->variables_seguimiento;
                foreach ($array_variables as $key => $value) {
                    foreach ($value as $nombre => $valor) {
                        $dato_seguimiento = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($nombre, $etapa->id);
                        if ($dato_seguimiento) {
                            $dato_seguimiento->delete();
                        }
                        $dato_seguimiento = new DatoSeguimiento();
                        $dato_seguimiento->etapa_id = $etapa->id;
                        $dato_seguimiento->nombre = $nombre;
                        $dato_seguimiento->valor = (string) $valor;
                        $dato_seguimiento->save();
                    }
                }
            }
            return true;
        } else {
            $respuesta = new stdClass();
            $respuesta->errores .=json_last_error_msg();
            $respuesta->validacion = FALSE;
            return $respuesta;
        }
    }

    public function setExtra($datos_array) {
        if ($datos_array)
            $this->_set('extra', json_encode($datos_array));
        else
            $this->_set('extra', NULL);
    }

    public function getExtra() {
        return json_decode($this->_get('extra'));
    }

    public function eliminarErrorValidacion($etapaID) {
        $conn = Doctrine_Manager::connection();
        $stmt = $conn->prepare('delete FROM dato_seguimiento WHERE (etapa_id = ' . $etapaID . ' and nombre like \'validacion_error%\');');
        $stmt->execute();
    }

}
