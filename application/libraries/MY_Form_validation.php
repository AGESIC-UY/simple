<?php

Class MY_Form_validation extends CI_Form_validation {

    public function __construct($rules = array()) {
        parent::__construct($rules);

        $this->set_error_delimiters('<div class="alert alert-error">', '</div>');
    }

  public function alpha_numeric_ext($str){
         $convert = array(
                         "á"=>"a", "é"=>"e", "í"=>"i", "ó"=>"o", "ú"=>"u", "ñ"=>"n",
                         "Á"=>"A", "É"=>"E", "Í"=>"I", "Ó"=>"O", "Ú"=>"U", "Ñ"=>"N"
                         );
         $str = strtr($str, $convert);
         return ctype_alnum($str);
    }

    function rut_cl($rut_con_dv) {
        $rut_con_dv = explode('-', $rut_con_dv);
        if (count($rut_con_dv) == 2) {
            $rut = str_replace('.','',$rut_con_dv[0]);
            $dv = strtolower($rut_con_dv[1]);
            /* Con las lineas anteriores le asignanos a las variables $rut y $dv, lo ingresado por formulario en la página anterior, solo utilizaremos el rut. El digito verificador, lo usaremos al final */
            $rutin = strrev($rut);
            /* Invertimos el rut con la funcion “strrev” */
            $cant = strlen($rutin);
            /* Contamos la cantidad de numeros que tiene el rut */
            $c = 0;
            /* Creamos un contador con valor inicial cero */
            while ($c < $cant) {
                $r[$c] = substr($rutin, $c, 1);
                $c++;
            }
            /* Hacemos un ciclo en el que se creara un array o arreglo que se llamara $r, en el cual se le asignara a cada valor del array, el valor correspodiente del rut, Por ej: para el rut 12346578, que invertido sería 87654321, el valor de $r[0] es 8, de $r[5] es 3 y asi sucesiva y respectivamente. */
            $ca = count($r);
            /* Contamos la cantidad de valores que tiene el arreglo con la función “count” */
            $m = 2;
            $c2 = 0;
            $suma = 0;
            /* En las lineas anteriores creamos 3 cosas, un multiplicador con el nombre $m y que su valor inicial es 2, ya que por formula es el primero que necesitamos, creamos tambien un segundo contador con el nombre $c2 y valor inicial cero y por ultimo creamos un acumulador de nombre $suma en el cual se guardara el total luego de multiplicar y sumar como manda la formula */
            while ($c2 < $ca) {
                $suma = $suma + ($r[$c2] * $m);
                if ($m == 7) {
                    $m = 2;
                } else {
                    $m++;
                }
                $c2++;
            }
            /* Hacemos un nuevo ciclo en el cual a $suma se le suma (valga la redundancia) su propio valor (que inicialmente es cero) más el resultado de la multiplicación entre el valor del array correspondiente por el multiplicador correspondiente, basandonos en la formula */
            $resto = $suma % 11;
            /* Calculamos el resto de la división usando el simbolo % */
            $digito = 11 - $resto;
            /* Calculamos el digito que corresponde al Rut, restando a 11 el resto obtenido anteriormente */
            if ($digito == 10) {
                $digito = 'k';
            } else {
                if ($digito == 11) {
                    $digito = '0';
                }
            }
            /* Creamos dos condiciones, la primero dice que si el valor de $digito es 11, lo reemplazamos por un cero (el cero va entre comillas. De no hacerlo así, el programa considerará “nada” como cero, es decir si la persona no ingresa Digito Verificado y este corresponde a un cero, lo tomará como valido, las comillas, al considerarlo texto, evitan eso). El segundo dice que si el valor de $digito es 10, lo reemplazamos por una K, de no cumplirse ninguno de las condiciones, el valor de $digito no cambiará. */
            if ($dv == $digito) {
                return $rut.'-'.$dv;
            }
            /* Por ultimo comprobamos si el resultado que obtuvimos es el mismo que ingreso la persona, de ser así se muestra el mensaje “Valido”, de no ser así se muestra el mensaje “No Valido” */
        }

        return FALSE;
    }

    //Convierte una fecha en humano al formato mysql.
    function date_prep($string){
        return strftime('%Y-%m-%d',strtotime($string));
    }

    /**
     * Alpha-numeric with spaces
     *
     * @access	public
     * @param	string
     * @return	bool
     */
    public function alpha_space($str)
    {
        return ( ! preg_match('/^([-a-z0-9\s])+$/i', $str)) ? FALSE : TRUE;
    }

    /**
     * Funcion que chequea si el valor es unico. Es decir, no se encuentra dentro del conjunto json.
     *
     */
    public function is_unique($value,$json){
        $array=json_decode($json);

        return !in_array($value,$array);
    }


  function rut($rut) {
  	if(strlen(trim($rut)) >= 11) {
  		$len = strlen(trim($rut));
  		$sub = substr(trim($rut), 0, 11);

  		$v = [2,3,4,5,6,7,8,9,2,3,4];
  		$i = 0;
  		$sum = 0;

  		try {
  			for($i = 0; $i < strlen($sub); $i++) {
  				$sum = $sum + (int) $sub[$len - 2 - $i] * $v[$i];
  			}
  		}
  		catch(Exception $e) {
  			return false;
  		}

  		$resto = $sum % 11;

  		$prob = 11 - $resto;
  		$dv = -1;
  		if(strlen($rut) >= 12) {
  			$dv = (int) $rut[11];
  		}

  		if($prob < 10 && $dv != $prob) {
  			return false;
  		}
  		if($prob == 11 && $dv != 0) {
  			return false;
  		}
  		if($prob == 10 && $dv > 0) {
  			return false;
  		}

  		$c = (int) substr($rut, 0, 2);
  		if($c < 1 || $c > 21) {
  			return false;
  		}

  		$c2 = (int) substr($rut, 2, 6);
  		if($c2 == 0) {
  			echo "false5";
  		}

  		$c3 = (int) substr($rut, 8, 2);
  		if($c3 != 0) {
  			return false;
  		}
  		return true;
  	}
  	else {
  		return false;
  	}
  }


  //dado los datos de la taba y la emtada de la tabala (la definicion de la columna) valida que sean correcto
  public function validar_campos_tabla($str,$tablametada){
    if ($this->not_empty_table($str)){
      $str= json_decode($str);
      $tablametada = unserialize($tablametada);
      $OK = TRUE;
      $message = null;
      foreach ($str as $keyFila => $fila) {
        foreach ($tablametada->columns as $key => $column) {
          //se valida las reglas de validacion si es que tiene la columna
          if (!empty($column->validacion)){
            if (strpos($column->validacion,'required') !== FALSE){
              if (!$this->required($fila[$key])){
                if ($message){
                  $message= $message . ',la columna \'' . $column->header . '\' en la fila '. ($keyFila+1) . ' es requerida';
                }else{
                  $message= 'En la tabla "<strong>%s</strong>": la columna \'' . $column->header . '\' en la fila '. ($keyFila+1) . ' es requerida';
                }
                  $OK =  FALSE;
              }
            }
          }
          //se valida el tipo de la columna
          if ($column->type == 'numeric'){
            //verificar que es numerico
            if (!is_numeric($fila[$key])){
              if ($message){
                $message= $message . ',la columna \'' . $column->header . '\' en la fila '. ($keyFila+1) . ' debe ser numérica';
              }else{
                $message= 'En la tabla "<strong>%s</strong>": la columna \'' . $column->header . '\' en la fila '. ($keyFila+1) . ' debe ser numérica';
              }
              $OK =  FALSE;
            }else{
              //tipo de columna valida
            }
          }else{
            //tipo de columna text siempre es valida
          }
        }
      }
      if (!$OK){
        $this->set_message('validar_campos_tabla',$message);
      }

      return $OK;
    }
  }
  public function max_length_table($str,$num){
      if ($this->not_empty_table($str)){
        //la tabla no es vacia
        $str= json_decode($str);
        if (count($str) > $num){
          return false;
        }else{
          return true;
        }
      }
  }

  public function min_length_table($str,$num){
    if ($this->not_empty_table($str)){
      //la tabla no es vacia
      $str= json_decode($str);
      if (count($str) < $num){
        return false;
      }else{
        return true;
      }
    }else{
      //la tabla es vacia
      if ($num > 0){
        return false;
      }
    }


  }

  public function consulta_pago_completo_generico($pasarela_id, $etapa_id) {
    $pasarela = Doctrine::getTable('PasarelaPagoGenerica')->find($pasarela_id);
    $operacion = Doctrine_Query::create()
                ->from('WsOperacion o')
                ->where('o.codigo = ?', $pasarela->codigo_operacion_soap_consulta)
                ->fetchOne();

    if($operacion) {
      $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

      $servicio = Doctrine::getTable('WsCatalogo')->find($operacion->catalogo_id);
      $soap_wsdl = $servicio->wsdl;
      $soap_endpoint_location = $servicio->endpoint_location;

      $ci = get_instance();
      $ci->load->helper('soap_execute');
      soap_execute($etapa, $servicio, $operacion, $operacion->soap);

      $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('ws_error', $etapa->id);
      if(!$dato) {
        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $pasarela->variable_evaluar), $etapa->id);
        if($dato) {
          $estado = $dato->valor;

          if($estado[0][0] == 'CONTINUAR') {
            return true;
          }
          elseif($estado[0][0] == 'NO_CONTINUAR_IDSOL') {
            redirect(site_url());
          }
          else {
            $this->set_message('consulta_pago_completo_generico', $estado[0][1]); return false;
          }
        }
        else {
          $this->set_message('consulta_pago_completo_generico', 'No es posible obtener el estado del pago.'); return false;
        }
      }
      else {
        // -- Fallo la invocacion al servicio y se deja en manos de ws_error
        $this->set_message('consulta_pago_completo_generico', $dato->valor);
        return false;
      }
    }
    else {
      $this->set_message('consulta_pago_completo_generico', 'No se ha encontrado la operación de consulta para la pasarela indicada.'); return false;
    }
  }

  public function not_empty_table($str){
    $str= json_decode($str);
    if (count($str) == 0){
      return false;
    }
    if (count($str) == 1){
      if (count($str[0]) == 0 || count($str[0]) == 1){
        return false;
      }else{
        return true;
      }
    }
    return true;
  }

  function ci($d) {
    if(!is_numeric($d)) {
      return false;
    }

    if(strlen($d) < 6 || strlen($d) > 8) {
      return false;
    }

  	$rep = str_replace(substr($d, 0, 1), "", $d);
  	if(empty($rep)) {
  		return false;
  	}

  	$d = str_replace("[^\\d]", "", $d);
  	$d1 = substr($d, 0, strlen($d)-1);
  	$d2 = substr($d, strlen($d)-1, strlen($d));

  	$s = $this->calcularDigitoCedulaIdentidadUruguaya($d1);
  	$z = (int) $d2[0];
  	if($s == $z) {
  	  return true;
  	}
  	else {
  	  return false;
  	}
  }

  function calcularDigitoCedulaIdentidadUruguaya($ci) {
    if(!$ci) {
      throw new InvalidArgumentException("");
    }

    $ci = str_replace("[^\\d]", "", $ci);
    $ciLen = (int) strlen($ci);

    $s = 0;
    $v = [2,9,8,7,6,3,4];
    for($i = count($v) - $ciLen; $i < count($v); $i++) {
  	  $a = $v[$i];
  	  $b = (int) $ci[$i - (count($v) - $ciLen)];
  	  $s = ($s + ($a * $b)) % 10;
    }

    $s = (10 - ($s % 10)) % 10;

    return $s;
  }
}
