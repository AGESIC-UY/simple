<?php

function imprimir_pasos_pdf($etapa_id) {
  if($etapa_id) {
      $documento = new Documento();
      $documento->tipo='pasos';
      $documento->tamano = 'letter';
      $array_etapas_para_pdf = array();

      $etapa_actual = Doctrine::getTable('Etapa')->find($etapa_id);

      $tramite = $etapa_actual->Tramite;

      $etapas_compleatas = $tramite->getEtapasCompletadas($etapa_id);

      $primera_etapa = true;

      $generar_file = false;

      foreach ($etapas_compleatas as $etapa) {

        if(!$primera_etapa){
          $documento->contenido .= '<br pagebreak="true" />';
        }

        $array_pasos_ejecutables = $etapa->getPasosEjecutables();
        $array_formularios_ejecutables = array();
        $hay_pasos_generar_pdf = false;

        foreach ($array_pasos_ejecutables as $paso_ejecutable) {

          if($paso_ejecutable->generar_pdf == 1) {

            $hay_pasos_generar_pdf = true;

            foreach($etapa->Tramite->Proceso->Formularios as $formulario) {
              //si el formulario tiene campos
              if(count($formulario->Campos) > 0){
                //si el formulario de la etapa esta dentro de los pasos ejecutables, recorro su lista de campos
                 if($paso_ejecutable->formulario_id == $formulario->id) {
                    $array_campos_ejecutables = array();

                    foreach($formulario->Campos as $campo) {
                      //guardo un array de campos ejecutables (if con campos omitidos)
                      if($campo->tipo != 'agenda' && $campo->tipo != 'pagos' && $campo->tipo != 'documento' && $campo->tipo != 'fieldset' && $campo->tipo != 'estado_pago' && $campo->tipo != 'domicilio_ica'){

                        $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo->nombre, $etapa->id);

                        if($dato && $dato->valor != ''){
                          array_push($array_campos_ejecutables, $campo);
                        }

                        if($campo->valor_default != '' && $campo->readonly == 1 && !($campo->tipo == 'paragraph' || $campo->tipo == 'subtitle' || $campo->tipo == 'dialogo')){
                          array_push($array_campos_ejecutables, $campo);
                        }

                        if($campo->tipo == 'dialogo'){
                          array_push($array_campos_ejecutables, $campo);
                        }

                        if($campo->tipo == 'paragraph' || $campo->tipo == 'subtitle'){
                          array_push($array_campos_ejecutables, $campo);
                        }

                      }
                    }

                    if(count($array_campos_ejecutables) > 0){
                      $formulario_ejecutable = new stdClass();
                      $formulario_ejecutable->formulario = $formulario;
                      $formulario_ejecutable->campos = $array_campos_ejecutables;
                      $formulario_ejecutable->nombre_paso = $paso_ejecutable->nombre;
                      //Guardo en un array el formulario ejecutable con sus campos
                      array_push($array_formularios_ejecutables, $formulario_ejecutable);
                    }
                 }
              }
            }
          }
        }

        if($hay_pasos_generar_pdf) {
           $generar_file = true;

          $cont = 0;
          foreach($array_formularios_ejecutables as $formulario_ejecutable) {

              $documento->contenido .= '<h1 style="text-align:center">'.$formulario_ejecutable->nombre_paso.'</h1>';

              foreach ($formulario_ejecutable->campos as $campo_ejecutable) {

                if(($campo_ejecutable->valor_default != '' && $campo_ejecutable->readonly == 1) || ($campo_ejecutable->tipo == 'paragraph' || $campo->tipo == 'subtitle') || ($campo_ejecutable->esVisibleParaLaEtapaActual($etapa->id))) {
                  $variable_campo = '@@'.$campo_ejecutable->nombre;

                  if ($campo_ejecutable->tipo == 'tabla-responsive'){
                      $documento->contenido .= '<strong>'.$campo_ejecutable->etiqueta.': </strong><br>'.$variable_campo.'<br><br>';
                  }
                  else if($campo_ejecutable->tipo == 'radio' || $campo_ejecutable->tipo == 'select'){
                      $variable_campo = '@@'.$campo_ejecutable->nombre.'__etiqueta';
                      $documento->contenido .= '<strong>'.$campo_ejecutable->etiqueta.': </strong>'.$variable_campo.'<br><br>';
                  }
                  else if($campo_ejecutable->tipo == 'file'){
                      $variable_campo = '@@'.$campo_ejecutable->nombre.'__origen';
                      $documento->contenido .= '<strong>'.$campo_ejecutable->etiqueta.': </strong>'.$variable_campo.'<br><br>';
                  }
                  else if($campo_ejecutable->tipo == 'dialogo' || $campo_ejecutable->tipo == 'error'){

                      if($campo_ejecutable->dependiente_campo == ''){
                        $documento->contenido .=  $campo_ejecutable->valor_default.'<br><br>';
                      }
                      else {
                        $dependiente_tipo = $campo_ejecutable->dependiente_tipo;
                        $dependiente_relacion = $campo_ejecutable->dependiente_relacion;
                        $dependiente_campo = $campo_ejecutable->dependiente_campo;
                        $dependiente_valor = $campo_ejecutable->dependiente_valor;

                        foreach ($formulario_ejecutable->campos  as $campo_ejecutable_dependiente) {

                          if($campo_ejecutable_dependiente->nombre == $dependiente_campo) {
                            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_ejecutable_dependiente->nombre , $etapa->id);

                            if($dependiente_tipo =="regex" ){

                              if($dependiente_relacion == '==' && preg_match($dependiente_valor, $dato->valor)){
                                $documento->contenido .=  $campo_ejecutable->valor_default.'<br><br>';
                              }

                              if($dependiente_relacion == '!=' && !preg_match($dependiente_valor, $dato->valor)){
                                $documento->contenido .=  $campo_ejecutable->valor_default.'<br><br>';
                              }

                            }

                            if($dependiente_tipo == 'string'){

                              if($dependiente_relacion == '==' && $dependiente_valor == $dato->valor){
                                $documento->contenido .=  $campo_ejecutable->valor_default.'<br><br>';
                              }

                              if($dependiente_relacion == '!=' && $dependiente_valor != $dato->valor){
                                $documento->contenido .=  $campo_ejecutable->valor_default.'<br><br>';
                              }
                            }
                          }
                        }
                      }
                  }
                  else if($campo_ejecutable->tipo == 'paragraph' || $campo_ejecutable->tipo == 'subtitle'){

                      if($campo_ejecutable->dependiente_campo == ''){
                        $documento->contenido .=  '<br>'.$campo_ejecutable->etiqueta.'<br><br>';
                      }
                      else {
                        $dependiente_tipo = $campo_ejecutable->dependiente_tipo;
                        $dependiente_relacion = $campo_ejecutable->dependiente_relacion;
                        $dependiente_campo = $campo_ejecutable->dependiente_campo;
                        $dependiente_valor = $campo_ejecutable->dependiente_valor;

                        foreach ($formulario_ejecutable->campos  as $campo_ejecutable_dependiente) {

                          if($campo_ejecutable_dependiente->nombre == $dependiente_campo) {
                            $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_ejecutable_dependiente->nombre , $etapa->id);

                            if($dependiente_tipo =="regex" ){

                              if($dependiente_relacion == '==' && preg_match($dependiente_valor, $dato->valor)){
                                $documento->contenido .=  '<br>'.$campo_ejecutable->etiqueta.'<br><br>';
                              }

                              if($dependiente_relacion == '!=' && !preg_match($dependiente_valor, $dato->valor)){
                                $documento->contenido .=  '<br>'.$campo_ejecutable->etiqueta.'<br><br>';
                              }

                            }

                            if($dependiente_tipo == 'string'){

                              if($dependiente_relacion == '==' && $dependiente_valor == $dato->valor){
                                $documento->contenido .=  '<br>'.$campo_ejecutable->etiqueta.'<br><br>';
                              }

                              if($dependiente_relacion == '!=' && $dependiente_valor != $dato->valor){
                                $documento->contenido .=  '<br>'.$campo_ejecutable->etiqueta.'<br><br>';
                              }
                            }
                          }
                        }
                      }

                  }
                  else if($campo_ejecutable->tipo == 'agenda_sae'){
                    $dato_agenda = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo_ejecutable->nombre, $etapa_id);

                    if($dato_agenda){
                      $datos_comfirmacion = $dato_agenda->valor;
                      $documento->contenido .= '<strong>'.$campo_ejecutable->etiqueta.': </strong>';
                      $documento->contenido .= '<p>       Fecha y hora: '.$datos_comfirmacion->fecha_confirmacion.'</p>';
                      $documento->contenido .= '<p>       Serie y número: '.$datos_comfirmacion->serieNumero.'</p>';
                      $documento->contenido .= '<p>       Código de trazabilidad: '.$datos_comfirmacion->codigoTrazabilidad.'</p>';
                      $documento->contenido .= '<p>       Mensaje: '.$datos_comfirmacion->textoTicket.'</p>';
                    }
                  }
                  else if($campo_ejecutable->valor_default != '' && $campo_ejecutable->readonly == 1 ){
                      $documento->contenido .= '<strong>'.$campo_ejecutable->etiqueta.': </strong>'.$campo_ejecutable->valor_default.'<br><br>';
                  }
                  else {
                      $documento->contenido .= '<strong>'.$campo_ejecutable->etiqueta.':</strong> '.$variable_campo.'<br><br>';
                  }
                }
              }

              $cont++;

              if($cont != count($array_formularios_ejecutables)){
                $documento->contenido .= '<br pagebreak="true" />';
              }
              /*else{
                $documento->contenido .= '<p style="color:Red;text-align:center"> ----- Fin etapa '.$etapa->id.' ---- </p>';
              }*/
          }

          array_push($array_etapas_para_pdf, $etapa);
          $primera_etapa = false;

        }
    }
    $cuenta = Cuenta::cuentaSegunDominio();

    if($generar_file){
      $link_pdf = $documento->generar_pasos_pdf($etapa_actual , $etapa->Tramite->Proceso->nombre, $cuenta);
      return $link_pdf;
    }

    return NULL;
  }
  else{
    redirect(site_url());
  }
}
