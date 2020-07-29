<?php

require_once 'application/third_party/file-uploader.php';

class Uploader extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    function datos($campo_id, $etapa_id) {
        $etapa = Doctrine::getTable('Etapa')->find($etapa_id);
        $funcionario_actuando_como_ciudadano = UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano');
        if (!$funcionario_actuando_como_ciudadano && UsuarioSesion::usuario()->id != $etapa->usuario_id) {
            echo 'Usuario no tiene permisos para subir archivos en esta etapa';
            exit;
        }
        $campo = Doctrine_Query::create()
                ->from('Campo c, c.Formulario.Pasos.Tarea.Etapas e')
                ->where('c.id = ? AND e.id = ?', array($campo_id, $etapa_id))
                ->fetchOne();
        if (!$campo) {
            echo 'Campo no existe';
            exit;
        }

        // list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $allowedExtensions = array('gif', 'jpg', 'png', 'pdf', 'doc', 'docx', 'zip', 'rar', 'ppt', 'pptx', 'xls', 'xlsx', 'mpp', 'vsd');
        if (isset($campo->extra->filetypes))
            $allowedExtensions = $campo->extra->filetypes;

        $tamanio_max_parametro = Doctrine::getTable('Parametro')->findOneByCuentaIdAndClave($etapa->Tramite->Proceso->Cuenta->id, "tamanio_maximo_archivo");
        if (!$tamanio_max_parametro) {
            $tamanio_max_parametro = 40;
        } else {
            $tamanio_max_parametro = $tamanio_max_parametro->valor;
        }
        $tamanio_max = isset($campo->extra->tamanio_max) ? ($campo->extra->tamanio_max > 0 && $campo->extra->tamanio_max <= $tamanio_max_parametro ? $campo->extra->tamanio_max : $tamanio_max_parametro) : $tamanio_max_parametro;
        // max file size in bytes
        $sizeLimit = $tamanio_max * 1024 * 1024;

        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
        $result = $uploader->handleUpload('uploads/datos/');
        
        if (isset($result['success'])) {
            $datos_seguimiento_fileActual = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($campo->nombre, $etapa_id);
            if ($datos_seguimiento_fileActual) {
                if ($datos_seguimiento_fileActual->valor != "" || $datos_seguimiento_fileActual->valor != null) {
                    $fileActual = 'uploads/datos/' . $datos_seguimiento_fileActual->valor;
                    if (file_exists($fileActual)) {
                        unlink($fileActual);
                    }
                }
            }
            $file = new File();
            $file->tramite_id = $etapa->Tramite->id;
            $file->filename = $result['file_name'];
            $file->tipo = 'dato';
            $file->llave = strtolower(random_string('alnum', 12));
            //$file->etapa_id=$etapa_id; // agrega la etapa a la tabla file
            $file->file_origen = $result['file_origen']; // agrega el nombre original a la tabla file
            $file->save();
            $pathinfo = pathinfo($result['file_name']);
            if (isset($campo->extra->firmar_servidor)) {
                if ($campo->extra->firmar_servidor && strtolower($pathinfo['extension'])=="pdf") {
                    $this->firmar_documento_servidor($file->filename, $campo_id);
                }
            }
            $result['id'] = $file->id;
            $result['llave'] = $file->llave;
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }

    function datos_get($id) {
        // $id=$this->input->get('id');
        $token = $this->input->get('token');
        $usuario_id = UsuarioSesion::usuario()->id;

        $funcionario_actuando_como_ciudadano = UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano');
        if ($funcionario_actuando_como_ciudadano) {
            $usuario_id = $this->session->userdata('id_usuario_ciudadano');
        }
        //Chequeamos los permisos en el frontend
        $file = Doctrine_Query::create()
                ->from('File f, f.Tramite t, t.Etapas e, e.Usuario u')
                ->where('f.id = ? AND f.llave = ? AND u.id = ?', array($id, $token, $usuario_id))
                ->fetchOne();

        if (!$file) {
            //Chequeamos permisos en el backend
            $file = Doctrine_Query::create()
                    ->from('File f, f.Tramite.Proceso.Cuenta.UsuariosBackend u')
                    ->where('f.id = ? AND f.llave = ? AND u.id = ? AND (u.rol like "%super%" OR u.rol like "%operacion%" OR u.rol like "%seguimiento%")', array($id, $token, UsuarioBackendSesion::usuario()->id))
                    ->fetchOne();

            if (!$file) {
                echo 'Usuario no tiene permisos para ver este archivo.';
                exit;
            }
        }

        $path = 'uploads/datos/' . $file->filename;

        if (preg_match('/^\.\./', $file->filename)) {
            echo 'Archivo invalido';
            exit;
        }

        if (!file_exists($path)) {
            echo 'Archivo no existe';
            exit;
        }

        header('Content-Type: ' . get_mime_by_extension($path));
        header('Content-Length: ' . filesize($path));
        header( 'Content-Disposition: attachment;filename="'.$file->file_origen.'"');
        readfile($path);
    }

    // -- Firma documento en el servidor
    function firmar_documento_servidor($filename, $campo_id) {
        $uploadDirectory = DIRECTORIO_SUBIDA_ARCHIVOS;
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

}
