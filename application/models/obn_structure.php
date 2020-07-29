<?php

class ObnStructure extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('descripcion');
        $this->hasColumn('identificador');
        $this->hasColumn('json');
        $this->hasColumn('id_tabla_interna');
        $this->hasColumn('cuenta_id');
    }

    function setUp() {
        parent::setUp();

        $this->hasMany('ObnAttributes as ObnAttributesList', array(
            'local' => 'id',
            'foreign' => 'id_obn',
            'orderBy' => 'nombre'
        ));

        $this->hasMany('ObnQueries as ObnQueriesList', array(
            'local' => 'id',
            'foreign' => 'id_obn',
            'orderBy' => 'nombre'
        ));

        $this->hasMany('ObnDatosSeguimiento as ObnDatosSeguimientoList', array(
            'local' => 'id',
            'foreign' => 'obn_id'
        ));

        $this->hasOne('Cuenta', array(
            'local' => 'cuenta_id',
            'foreign' => 'id'
        ));
    }

    public function setAtributosFromArray($atributos_array) {
        //Limpiamos la lista antigua

        foreach ($this->ObnAttributesList as $key => $val)
            unset($this->ObnAttributesList[$key]);

        //Agregamos los nuevos
        if (is_array($atributos_array)) {
            foreach ($atributos_array as $key => $p) {
                $exite = $this->existeFromArray($p, $atributos_array);
                if ($exite) {
                    return false;
                } else {
                    $attr = new ObnAttributes();
                    $attr->nombre = $p['nombre'];
                    $attr->tipo = $p['tipo'];
                    $attr->clave_logica = $p['clave_logica'];
                    $attr->multiple = $p['multiple'];
                    $attr->valores = $p['valores'];
                    $attr->id_obn = $this->id;
                    $this->ObnAttributesList[] = $attr;
                }
            }
        }
        return true;
    }

    public function nuevosAtributosFromArray($atributos_array) {
        //Limpiamos la lista antigua
        //Agregamos los nuevos
        $nuevo = array();
        if (is_array($atributos_array)) {
            foreach ($atributos_array as $key => $p) {
                $buscar = $this->buscarAtributoFromArray($p);
                if ($buscar != false) {
                    $nuevo[] = $buscar;
                }
            }
        }
        return $nuevo;
    }

    private function buscarAtributoFromArray($atributo_array) {
        $lista_actual = $this->ObnAttributesList;
        foreach ($this->ObnAttributesList as $key => $p) {
            if ($atributo_array['nombre'] == $p['nombre']) {
                return false;
            }
        }
        $attr = new ObnAttributes();
        $attr->nombre = $atributo_array['nombre'];
        $attr->tipo = $atributo_array['tipo'];
        $attr->clave_logica = $atributo_array['clave_logica'];
        $attr->multiple = $atributo_array['multiple'];
        $attr->valores = $atributo_array['valores'];
        return $attr;
    }

    private function existeFromArray($atributo, $array_atributo) {
        $contar = 0;
        foreach ($array_atributo as $key => $p) {
            if ($atributo['nombre'] == $p['nombre']) {
                $contar++;
            }
        }
        return $contar > 1 ? true : false;
    }

    public function setQuerysFromArray($querys_array) {
        //Limpiamos la lista antigua

        foreach ($this->ObnQueriesList as $key => $val)
            unset($this->ObnQueriesList[$key]);

        //Agregamos los nuevos
        if (is_array($querys_array)) {
            foreach ($querys_array as $key => $p) {
                $exite = $this->existeFromArray($p, $querys_array);
                if ($exite) {
                    return false;
                } else {
                    $sql = "";
                    if ($p['tipo'] == 'count') {
                        $sql = "select count(*) from " . $this->id_tabla_interna . " ";
                    } else {
                        $sql = "select * from " . $this->id_tabla_interna . " ";
                    }
                    if ($p['consulta']) {
                        $sql.="where " . $p['consulta'];
                    }
                    $query = new ObnQueries();
                    $query->nombre = $p['nombre'];
                    $query->tipo = $p['tipo'];
                    $query->consulta = $p['consulta'];
                    $query->consulta_sql = $sql;
                    $query->id_obn = $this->id;
                    $this->ObnQueriesList[] = $query;
                }
            }
        }
        return true;
    }

    public function setJsonObn() {
        //Limpiamos la lista antigua
        $json = new stdClass();
        $json->id = $this->id;
        $json->identificador = $this->identificador;
        $json->descripcion = $this->descripcion;
        $json->id_tabla_interna = $this->id_tabla_interna;
        $json->OBN_ATR = array();

        $attr = $this->ObnAttributesList;
        foreach ($attr as $value) {
            $obn_attr = new stdClass();
            $obn_attr->id = $value->id;
            $obn_attr->nombre = $value->nombre;
            $obn_attr->tipo = $value->tipo;
            $obn_attr->clave_logica = $value->clave_logica;
            $obn_attr->multiple = $value->multiple;
            $obn_attr->valores = $value->valores;
            $json->OBN_ATR[] = $obn_attr;
        }
        $quey = $this->ObnQueriesList;
        $json->OBN_CA = array();
        foreach ($quey as $value) {
            $obn_query = new stdClass();
            $obn_query->id = $value->id;
            $obn_query->nombre = $value->nombre;
            $obn_query->tipo = $value->tipo;
            $obn_query->consulta = $value->consulta;
            $obn_query->consulta_sql = $value->consulta_sql;
            $json->OBN_CA[] = $obn_query;
        }

        $this->json = json_encode($json);
    }

    function crearModelo() {
        $CI = & get_instance();
        $CI->load->helper('buscar_obn_helper');
        $json = json_decode($this->json);
        $contenido = "<?php" . "\n";
        $contenido .="class " . crearNombreClaseObjeto($this->identificador) . " extends Doctrine_Record {" . "\n";
        $contenido .="   function setTableDefinition() {" . "\n";
        $contenido.="$" . "this->hasColumn('id');" . "\n";
        foreach ($json->OBN_ATR as $value) {
            $contenido.="$" . "this->hasColumn('$value->nombre');" . "\n";
        }
        $contenido.="}" . "\n";

        $contenido.=" function setUp() {
            parent::setUp();
        } " . "\n";

        $contenido.="public static function obtenerOBN(){" . "\n";
        $contenido.="$" . "query = Doctrine_Query::create()" . "\n";
        $contenido.="->from('" . crearNombreClaseObjeto($this->identificador) . "');" . "\n";
        $contenido.=" return $" . "query;" . "\n";
        $contenido.="}" . "\n";

        $contenido.="public static function contarOBN(){" . "\n";
        $contenido.="$" . "query = Doctrine_Query::create()" . "\n";
        $contenido.="->from('" . crearNombreClaseObjeto($this->identificador) . "')" . "\n";
        $contenido.="->count();" . "\n";
        $contenido.=" return $" . "query;" . "\n";
        $contenido.="}" . "\n";

        $contenido.="public static function obtenerIn($" . "array_param){" . "\n";
        $contenido.="$" . "query = Doctrine_Query::create()" . "\n";
        $contenido.="->from('" . crearNombreClaseObjeto($this->identificador) . "')" . "\n";
        $contenido.="->andWhereIn('id', $" . "array_param);" . "\n";
        $contenido.=" return $" . "query;" . "\n";
        $contenido.="}" . "\n";

        $contenido.="public static function contarIn($" . "array_param){" . "\n";
        $contenido.="$" . "query = Doctrine_Query::create()" . "\n";
        $contenido.="->from('" . crearNombreClaseObjeto($this->identificador) . "')" . "\n";
        $contenido.="->andWhereIn('id', $" . "array_param)" . "\n";
        $contenido.="->count();" . "\n";
        $contenido.=" return $" . "query;" . "\n";
        $contenido.="}" . "\n";

        foreach ($json->OBN_CA as $value) {
            $contenido.="public static function " . $value->nombre . "($" . "array_param){" . "\n";
            $contenido.="$" . "query = Doctrine_Query::create()" . "\n";
            $contenido.="->from('" . crearNombreClaseObjeto($this->identificador) . "')" . "\n";
            $contenido.="->where(\"" . $value->consulta . "\",$" . "array_param)" . "\n";
            if ($value->tipo == "count") {
                $contenido.="->count()" . "\n";
            }
            $contenido = substr($contenido, 0, -1);
            $contenido.=";" . "\n";

            $contenido.=" return $" . "query;" . "\n";
            $contenido.="}" . "\n";
        }
        $contenido.="}";

        $models = fopen(DIR_OBN . $this->id_tabla_interna . ".php", "w+");
        fwrite($models, $contenido . "\n");
        fclose($models);
        $comando = "php -l " . DIR_OBN . $this->id_tabla_interna . ".php";
        $result = exec($comando);
        $pos = strpos($result, "No syntax errors");
        if ($pos === false) {
            unlink(DIR_OBN . $this->id_tabla_interna . ".php");
            return false;
        } else {
            return true;
        }
    }

    function crearTablaModelo() {
        $respuesta = new stdClass();
        $CI = & get_instance();
        $CI->load->helper('buscar_obn_helper');
        $json = json_decode($this->json);
        $drop = "DROP TABLE IF EXISTS " . $json->id_tabla_interna . ";" . "\n";
        $sql = "CREATE TABLE " . $json->id_tabla_interna . " (" . "\n" . "id INT AUTO_INCREMENT PRIMARY KEY,";
        $isIndex = false;
        $index = "UNIQUE KEY " . $json->id_tabla_interna . "_clave_logica (";
        foreach ($json->OBN_ATR as $value) {
            $sql.=$value->nombre . " " . ($value->tipo == 'obn' ? "text" : ($value->multiple == 1 ? "text" : ($value->tipo == 'time' ? "time" : ($value->tipo == 'enum' ? "varchar(500)" : ($value->tipo . ($value->tipo == 'varchar' ? "($value->valores)" : "")))))) . "," . "\n";
            if ($value->clave_logica == 1) {
                $isIndex = true;
                $index.= $value->nombre . ",";
            }
        }
        if ($isIndex) {
            $index = substr($index, 0, -1);
            $index.=")";
            $sql.=$index . ");";
        } else {
            $sql = substr($sql, 0, -2);
            $sql.=");";
        }
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();
        $q->execute($drop);
        try {
            $q->execute($sql);
            $respuesta->validacion = true;
        } catch (Exception $exc) {
            $respuesta->validacion = FALSE;
            $respuesta->errores = '<div class="alert alert-error">Ocurrió un error creando la tabla del Objeto de negocio. Por favor verifique los datos' . $exc->getMessage() . '</div> ';
        }

        return json_encode($respuesta);
    }

    function eliminarTablaModelo() {
        $respuesta = new stdClass();
        $CI = & get_instance();
        $CI->load->helper('buscar_obn_helper');
        $json = json_decode($this->json);
        $drop = "DROP TABLE IF EXISTS " . $json->id_tabla_interna . ";" . "\n";
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();
        $q->execute($drop);
        unlink(DIR_OBN . $this->id_tabla_interna . ".php");
        return true;
    }

    function editarTablaModelo($atributos) {
        $json = json_decode($this->json);
        $respuesta = new stdClass();
        $respuesta->validacion = true;
        if (count($atributos) > 0) {
            $drop_index = "DROP INDEX " . $json->id_tabla_interna . "_clave_logica ON " . $json->id_tabla_interna . ";";

            $index = "ALTER TABLE " . $json->id_tabla_interna . " ADD CONSTRAINT " . $json->id_tabla_interna . "_clave_logica UNIQUE (";

            $sql = "ALTER TABLE " . $json->id_tabla_interna . " ADD " . "\n";
            foreach ($atributos as $value) {
                $sql.=$value->nombre . " " . ($value->tipo == 'obn' ? "text" : ($value->multiple == 1 ? "text" : ($value->tipo == 'time' ? "time" : ($value->tipo == 'enum' ? "varchar(500)" : ($value->tipo . ($value->tipo == 'varchar' ? "($value->valores)" : "")))))) . "," . "\n";
            }

            foreach ($json->OBN_ATR as $value) {
                if ($value->clave_logica == 1) {
                    $index.= $value->nombre . ",";
                }
            }
            $index = substr($index, 0, -1);
            $sql = substr($sql, 0, -2);
            $index.=");";
            $sql.=";";
            $q = Doctrine_Manager::getInstance()->getCurrentConnection();
            try {
                $q->execute($drop_index);
            } catch (Exception $exc) {
                
            }
            try {
                $q->execute($sql);
                $respuesta->validacion = true;
            } catch (Exception $exc) {
                $respuesta->validacion = FALSE;
                $respuesta->errores = '<div class="alert alert-error">Ocurrió un error creando la tabla del Objeto de negocio. Por favor verifique los datos</div> ';
            }
            try {
                $q->execute($index);
            } catch (Exception $exc) {
                $respuesta->validacion = FALSE;
                $respuesta->errores = '<div class="alert alert-error">Ocurrió un error creando la tabla del Objeto de negocio. Por favor verifique la clave lógica</div> ';
            }
        }
        return json_encode($respuesta);
    }

    public function exportComplete() {
        $obn = $this;
        $obn->ObnAttributesList;
        $obn->json = "";
        $obn->ObnQueriesList;
        $object = $obn->toArray();

        return json_encode($object);
    }

    public static function importComplete($input) {
        try {
            $json = json_decode($input);

            $obn = new ObnStructure();
            $obn->cuenta_id = UsuarioBackendSesion::usuario()->cuenta_id;

            if (isset($json->ObnAttributesList)) {
                foreach ($json->ObnAttributesList as $key => $value) {
                    $obn->ObnAttributesList[$key] = new ObnAttributes();
                    foreach ($value as $keyf => $f_attr) {
                        if ($keyf != 'id' && $keyf != 'id_obn') {
                            $obn->ObnAttributesList[$key]->{$keyf} = $f_attr;
                        }
                    }
                }
            } else {
                throw new Exception("No se pudo importa el OBN. El archivo está mal formado");
            }

            if (isset($json->ObnQueriesList)) {
                foreach ($json->ObnQueriesList as $key => $value) {
                    $obn->ObnQueriesList[$key] = new ObnQueries();
                    foreach ($value as $keyf => $f_attr) {
                        if ($keyf != 'id' && $keyf != 'id_obn') {
                            $obn->ObnQueriesList[$key]->{$keyf} = $f_attr;
                        }
                    }
                }
            } else {
                throw new Exception("No se pudo importa el OBN. El archivo está mal formado");
            }
            if (isset($json->descripcion)) {
                $obn->descripcion = $json->descripcion;
            } else {
                throw new Exception("No se pudo importa el OBN. El archivo está mal formado");
            }
            if (isset($json->identificador)) {
                $re = '/^[a-zA-Z_$][a-zA-Z_]*$/';
                $str = strtolower(trim($json->identificador));
                if (preg_match($re, $str)) {
                    $obn->identificador = $str;
                    $obn->id_tabla_interna = crearIdentificadorTabla($json->identificador);
                } else {
                    throw new Exception("No se pudo importa el OBN. El identificador no es válido");
                }
            } else {
                throw new Exception("No se pudo importa el OBN. El archivo está mal formado");
            }

            return $obn;
        } catch (Exception $error) {
            return -1;
        }
    }

}
