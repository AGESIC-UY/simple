<?php

class Documento extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('tipo');
        $this->hasColumn('nombre');
        $this->hasColumn('titulo');
        $this->hasColumn('subtitulo');
        $this->hasColumn('contenido');
        $this->hasColumn('servicio');
        $this->hasColumn('servicio_url');
        $this->hasColumn('validez');
        $this->hasColumn('validez_habiles');
        $this->hasColumn('firmador_nombre');
        $this->hasColumn('firmador_cargo');
        $this->hasColumn('firmador_servicio');
        $this->hasColumn('firmador_imagen');
        $this->hasColumn('proceso_id');
        $this->hasColumn('timbre');
        $this->hasColumn('logo');
        $this->hasColumn('hsm_configuracion_id');
        $this->hasColumn('tamano');
        $this->hasColumn('imagenes');
        $this->hasColumn('unir_pdf');
        $this->hasColumn('lista_pdf');
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('Proceso', array(
            'local' => 'proceso_id',
            'foreign' => 'id'
        ));

        $this->hasMany('Campo as Campos', array(
            'local' => 'id',
            'foreign' => 'documento_id'
        ));

        $this->hasOne('HsmConfiguracion', array(
            'local' => 'hsm_configuracion_id',
            'foreign' => 'id'
        ));
    }

    public function setValidez($validez) {
        if (!$validez)
            $validez = null;

        $this->_set('validez', $validez);
    }

    public function setHsmConfiguracionId($hsm_configuracion_id) {
        if (!$hsm_configuracion_id)
            $hsm_configuracion_id = null;

        $this->_set('hsm_configuracion_id', $hsm_configuracion_id);
    }

    public function generar($etapa_id, $campo_id = null) {
        $CI = & get_instance();
        $CI->load->helper('filename_concurrencia_helper');
        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

        $filename_uniqid = obtenerFileName();

        //Generamos el file
        $file = new File();
        $file->tramite_id = $etapa->tramite_id;
        $file->etapa_id = $etapa->id;
        $file->tipo = 'documento';
        $file->llave = strtolower(random_string('alnum', 12));
        $file->llave_copia = $this->tipo == 'certificado' ? strtolower(random_string('alnum', 12)) : null;
        $file->llave_firma = strtolower(random_string('alnum', 12));
        if ($this->tipo == 'certificado') {
            $regla = new Regla($this->validez);
            $validez = $regla->getExpresionParaOutput($etapa->id);
            if (!is_numeric($validez)) {
                $validez = null;
            }
            $file->validez = $validez;
            $file->validez_habiles = $this->validez_habiles;
        }
        $file->filename = $filename_uniqid . '.pdf';
        $file->save();

        // Renderizamos
        $this->render($file->id, $file->llave_copia, $etapa->id, $file->filename, false, $campo_id);
        $filename_copia = $filename_uniqid . '.copia.pdf';
        $this->render($file->id, $file->llave_copia, $etapa->id, $filename_copia, true, $campo_id);



        return $file;
    }

    public function previsualizar() {
        $this->render('123456789', 'abcdefghijkl');
    }

    private function renderTable($etapa_id, $contenido) {

        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);

        //todos los campos de tipo tabla de la etapa.
        $arrayCampos = array();
        foreach ($etapa->Tramite->Proceso->Formularios as $formulario) {
            foreach ($formulario->Campos as $campo) {
                if ($campo->tipo == 'tabla-responsive') {
                    $arrayCampos['@@' . $campo->nombre] = $campo->id;
                }
            }
        }
        //buscamos todas las variables que contiene el documeto
        preg_match_all('/@@(\w+)((->\w+|\[\w+\])*)/', $contenido, $pat_array);
        //para cada variable del contenido se busca si es de tipo tabla
        $variable = '';
        for ($i = 0; $i < count($pat_array[0]); $i++) {
            $variable = $pat_array[0][$i];
            if (count($arrayCampos) > 0 && isset($arrayCampos[$variable]))
                if ($arrayCampos[$variable]) {
                    $tal = '<table  border="1" cellpadding="2" ><tr>';
                    //El contenido contiene un campo de tipo tabla que se debe sustituir por la tabla con los datos
                    $campo = Doctrine::getTable('Campo')->find($arrayCampos[$variable]);

                    //las columnas
                    foreach ($campo->extra->columns as $column) {
                        $tal = $tal . '<th style="font-weight: bold;text-align: center;background-color:#f4f4f5">' . $column->header . '</th>';
                    }
                    $tal = $tal . '</tr>';

                    //las filas
                    $variableSin = str_replace('@@', '', $variable);
                    $dato = Doctrine::getTable('DatoSeguimiento')->findByNombreHastaEtapa($variableSin, $etapa_id);

                    foreach ($dato->valor as $fila) {
                        $cont = 0;
                        $tal = $tal . '<tr>';
                        foreach ($fila as $td) {

                            if ($campo->extra->columns[$cont]->type === 'combo') {
                                $datos_extra_tabla = split(',', $campo->extra->columns[$cont]->data);

                                foreach ($datos_extra_tabla as $dato_tabla) {
                                    $dato_tabla_clave_valor = split(':', $dato_tabla);
                                    $dato_tabla_clave = $dato_tabla_clave_valor[0];
                                    $dato_tabla_valor = $dato_tabla_clave_valor[1];
                                    if ($dato_tabla_clave === $td) {
                                        $td = $dato_tabla_valor;
                                    }
                                }
                            }

                            $tal = $tal . '<td>' . $td . '</td>';
                            $cont++;
                        }
                        $tal = $tal . '</tr>';
                    }

                    if (count($dato->valor) == 0 || count($dato->valor[0]) == 0) {
                        $tal = $tal . '<tr style="text-align:center"><td colspan="' . count($campo->extra->columns) . '">Sin datos disponibles</td></tr>';
                    }

                    $tal = $tal . '</table>';

                    $patt = '/' . $variable . '\b/';
                    $contenido = preg_replace($patt, $tal, $contenido);
                    //$contenido = str_replace($variable, $tal, $contenido);
                }
        }
        return $contenido;
    }

    private function render($identifier, $key, $etapa_id = null, $filename = false, $copia = false, $campo_id = null) {
        $uploadDirectory = 'uploads/documentos/';
        $uploadDirectoryDatos = 'uploads/datos/';

        $CI = &get_instance();

        if ($this->tipo == 'certificado') {
            $CI->load->library('certificadopdf');
            $obj = new $CI->certificadopdf($this->tamano);

            $contenido = $this->contenido;
            $titulo = $this->titulo;
            $subtitulo = $this->subtitulo;
            $firmador_nombre = $this->firmador_nombre;
            $firmador_cargo = $this->firmador_cargo;
            $firmador_servicio = $this->firmador_servicio;
            if ($etapa_id) {

                //renderiza el contenido para la variables de tipo tabla
                $contenido = $this->renderTable($etapa_id, $contenido);
                //lo normal
                $regla = new Regla($contenido);
                $contenido = $regla->getExpresionParaOutput($etapa_id);
                $regla = new Regla($titulo);
                $titulo = $regla->getExpresionParaOutput($etapa_id);
                $regla = new Regla($subtitulo);
                $subtitulo = $regla->getExpresionParaOutput($etapa_id);
                $regla = new Regla($firmador_nombre);
                $firmador_nombre = $regla->getExpresionParaOutput($etapa_id);
                $regla = new Regla($firmador_cargo);
                $firmador_cargo = $regla->getExpresionParaOutput($etapa_id);
                $regla = new Regla($firmador_servicio);
                $firmador_servicio = $regla->getExpresionParaOutput($etapa_id);
            }

            $obj->content = $contenido;
            $obj->id = $identifier;
            $obj->key = $key;
            $obj->servicio = $this->servicio;
            $obj->servicio_url = $this->servicio_url;
            if ($this->logo)
                $obj->logo = 'uploads/logos_certificados/' . $this->logo;
            $obj->titulo = $titulo;
            $obj->subtitulo = $subtitulo;
            $regla = new Regla($this->validez);
            $validez = $regla->getExpresionParaOutput($etapa_id);
            if (!is_numeric($validez)) {
                $validez = null;
            }
            $obj->validez = $validez;
            $obj->validez_habiles = $this->validez_habiles;
            if ($this->timbre)
                $obj->timbre = 'uploads/timbres/' . $this->timbre;
            $obj->firmador_nombre = $firmador_nombre;
            $obj->firmado_cargo = $firmador_cargo;
            $obj->firmador_servicio = $firmador_servicio;
            if ($this->firmador_imagen)
                $obj->firmador_imagen = 'uploads/firmas/' . $this->firmador_imagen;
            $obj->firma_electronica = $this->hsm_configuracion_id ? true : false;
            $obj->copia = $copia;
        }else {
            $CI->load->library('blancopdf');
            $obj = new $CI->blancopdf($this->tamano);

            $contenido = $this->contenido;
            if ($etapa_id) {

                //renderiza el contenido para la variables de tipo tabla
                $contenido = $this->renderTable($etapa_id, $contenido);
                $regla = new Regla($contenido);
                $contenido = $regla->getExpresionParaOutput($etapa_id);
            }

            $obj->content = $contenido;
        }
        if ($etapa_id) {
            $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
            $file_dir = array();
            $imagenes = json_decode($this->imagenes);
            if (count($imagenes) > 0) {
                $obj->content.="<br pagebreak='true' />";
            }
            if (is_array($imagenes)) {
                foreach ($imagenes as $img) {
                    $variable = $img->variable;
                    $alto = ($img->alto >= 25) ? $img->alto : 100;
                    $ancho = ($img->ancho >= 25) ? $img->ancho : 100;
                    $descript = $img->descripcion;
                    if (strpos($variable, '[contenido]')) {
                        $imgb64 = str_replace("@@", '', $variable);
                        $regla = new Regla("@@" . str_replace('[contenido]', '', $imgb64));
                        $file_name = $regla->getExpresionParaOutput($etapa->id);
                        $file = Doctrine_Query::create()
                                ->from('File f, f.Tramite t')
                                ->where('f.filename = ? AND t.id = ?', array($file_name, $etapa->Tramite->id))
                                ->fetchOne();
                        if ($file) {
                            if (file_exists('uploads/datos/' . $file->filename)) {
                                $path = 'uploads/datos/' . $file->filename;
                                $type = pathinfo($path, PATHINFO_EXTENSION);
                                $data = file_get_contents($path);
                                if ($type == 'jpg' || $type == 'png' || $type == 'jpeg') {
                                    $img = '<div style="text-align:left;"><img style="width:' . $ancho . 'px;height:' . $alto . 'px;" src="' . $path . '"><div style="text-align:left;">' . $descript . ' </div></div>  ';
                                    $obj->content.=$img;
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($filename) {
            $obj->Output($uploadDirectory . $filename, 'F');

            if (!$copia && $this->hsm_configuracion_id) {

                $client = new SoapClient($CI->config->item('hsm_url'));

                $result = $client->IntercambiaDoc(array(
                    'Encabezado' => array(
                        'User' => $CI->config->item('hsm_user'),
                        'Password' => $CI->config->item('hsm_password'),
                        'TipoIntercambio' => 'pdf',
                        'NombreConfiguracion' => $this->HsmConfiguracion->nombre,
                        'FormatoDocumento' => 'b64'
                    ),
                    'Parametro' => array(
                        'Documento' => base64_encode(file_get_contents($uploadDirectory . $filename)),
                        'NombreDocumento' => $filename
                    )
                ));

                file_put_contents($uploadDirectory . $filename, base64_decode($result->IntercambiaDocResult->Documento));
            }
        } else {
            $obj->Output($filename);
        }
        $pdfs = json_decode($this->lista_pdf);
        if (count($pdfs) >= 1) {
            $CI->load->library('pdfconcat');
            $file2merge = array($uploadDirectory . $filename);
            foreach ($pdfs as $pdf) {
                $regla = new Regla($pdf->variable);
                $nmbre_pdf = $regla->getExpresionParaOutput($etapa_id);
                array_push($file2merge, $uploadDirectoryDatos . $nmbre_pdf);
            }
            $pdf = new $CI->pdfconcat();
            $pdf->setFiles($file2merge);
            $pdf->concat();
            $pdf->Output($uploadDirectory . $filename, "F");
        }

        $campo_documento = Doctrine::getTable('Campo')->find($campo_id);

        if ($campo_documento) {
            $campos_extra = $campo_documento->extra;

            if (isset($campos_extra->firmar_servidor)) {
                if ($campos_extra->firmar_servidor == 'on') {
                    if ($campos_extra->firmar_servidor_momento == 'antes') {
                        $this->firmar_documento_servidor($filename, $campo_id);
                    }
                }
            }
        }

        return;
    }

    // -- Firma documento en el servidor
    function firmar_documento_servidor($filename, $campo_id) {
        $uploadDirectory = DIRECTORIO_SUBIDA_DOCUMENTOS;
        $soap_endpoint_location = WS_FIRMA_DOCUMENTOS;

        $campo_documento = Doctrine::getTable('Campo')->find($campo_id);

        if ($campo_documento) {
            $campos_extra = $campo_documento->extra;

            if (isset($campos_extra->firmar_servidor)) {
                if ($campos_extra->firmar_servidor == 'on') {
                    $keystores = $campos_extra->firmar_servidor_keystores;

                    $soap_endpoint_location = WS_FIRMA_DOCUMENTOS;

                    $CI = &get_instance();

                    $soap_body = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.firma.agesic.gub.uy/">
                                   <soapenv:Header/>
                                   <soapenv:Body>
                                      <ws:firmarDocumentosServidor>
                                         <ws:tipo_firma>PDF</ws:tipo_firma>
                                         <ws:documentos>' . base64_encode(file_get_contents($uploadDirectory . $filename)) . '</ws:documentos>
                                         <ws:keys>' . $keystores . '</ws:keys>
                                      </ws:firmarDocumentosServidor>
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

                    $xml = new SimpleXMLElement($soap_response);
                    $documento_base64 = $xml->xpath(WS_AGESIC_DOCUMENTO_SERVIDOR_XPATH);
                    $documento = base64_decode($documento_base64[0]);

                    file_put_contents($uploadDirectory . $filename, $documento);
                }
            }
        }
    }

    function generar_pasos_pdf($etapa_actual, $proceso_nombre, $cuenta) {
        $CI = & get_instance();
        $CI->load->helper('filename_concurrencia_helper');
        $filename = obtenerFileName();
        //verifico que el nombre del archivo NO exista
        while (file_exists('uploads/documentos/' . $filename . '.pdf')) {
            $filename = obtenerFileName();
        }

        $file = Doctrine_Query::create()
                ->from('file f')
                ->where('f.etapa_id = ?', $etapa_actual->id)
                ->andWhere('f.tipo = ?',"etapa_pdf")
                ->fetchOne();

        if ($file) {
            //Si la etapa ya tiene un archivo pdf lo borro de la carpeta porque se va a actualizar con uno nuevo
            if (file_exists('uploads/documentos/' . $file->filename))
                unlink('uploads/documentos/' . $file->filename);
            $file->filename = $filename . '.pdf';
            $file->save();
        }
        else {
            //Si es la primera vez que se genera el pdf para la etapa entonces cro uno nuevo
            $file = new File();
            $file->tramite_id = $etapa_actual->tramite_id;
            $file->etapa_id = $etapa_actual->id;
            $file->tipo = 'etapa_pdf';
            $file->llave = strtolower(random_string('alnum', 12));
            $file->llave_copia = null;
            $file->llave_firma = strtolower(random_string('alnum', 12));
            $file->filename = $filename . '.pdf';
            $file->save();
        }

        $CI = &get_instance();
        $CI->load->library('pasospdf');
        $obj = new $CI->pasospdf($this->tamano);
        $contenido = $this->contenido;

        //renderiza el contenido para la variables de tipo tabla
        $contenido = $this->renderTable($etapa_actual->id, $contenido);
        $regla = new Regla($contenido);
        $contenido = $regla->getExpresionParaOutput($etapa_actual->id);

        $obj->content = $contenido;
        $obj->proceso_nombre = $proceso_nombre;
        $obj->cuenta = $cuenta;

        //creo el nuevo archivo pdf
        $obj->Output('uploads/documentos/' . $file->filename, 'F');

        $link = site_url('documentos/get/' . $file->id) . '?token=' . $file->llave;

        return $link;
    }

    function generar_pasos_pdf_tarae_sin_asignar($etapa_actual, $proceso_nombre, $cuenta) {
        $CI = &get_instance();
        $CI->load->library('pasospdf');
        $obj = new $CI->pasospdf($this->tamano);
        $contenido = $this->contenido;

        //renderiza el contenido para la variables de tipo tabla
        $contenido = $this->renderTable($etapa_actual->id, $contenido);
        $regla = new Regla($contenido);
        $contenido = $regla->getExpresionParaOutput($etapa_actual->id);

        $obj->content = $contenido;
        $obj->proceso_nombre = $proceso_nombre;
        $obj->cuenta = $cuenta;

        //creo el nuevo archivo pdf
        $obj->Output($proceso_nombre, 'I');
    }

}
