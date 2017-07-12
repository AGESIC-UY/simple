<?php
class Documentos extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    // -- Permite firmar documentos
    function firmar_documento() {
        $filename = $this->input->post('filename');
        $campo_id = $this->input->post('campo');

        $uploadDirectory = DIRECTORIO_SUBIDA_DOCUMENTOS;
        $soap_endpoint_location = WS_FIRMA_DOCUMENTOS;

        $CI = &get_instance();

        $soap_body = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.firma.agesic.gub.uy/">
                       <soapenv:Header/>
                       <soapenv:Body>
                          <ws:firmarDocumentos>
                             <ws:tipo_firma>pdf</ws:tipo_firma>
                             <ws:documentos>'. base64_encode(file_get_contents($uploadDirectory . $filename)) .'</ws:documentos>
                          </ws:firmarDocumentos>
                       </soapenv:Body>
                    </soapenv:Envelope>';

        $soap_header = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Content-length: ".strlen($soap_body)
        );

        $soap_do = curl_init();

        curl_setopt($soap_do, CURLOPT_URL, $soap_endpoint_location);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, WS_TIMEOUT_CONEXION);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        WS_TIMEOUT_RESPUESTA);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST,           true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $soap_body);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $soap_header);

        $soap_response = curl_exec($soap_do);
        curl_close($soap_do);

        $xml = new SimpleXMLElement($soap_response);
        $token_id = $xml->xpath(WS_AGESIC_FIRMA_XPATH);

        $respuesta = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
                        <jnlp codebase="'. WS_AGESIC_FIRMA_CODEBASE .'" href="" spec="1.0+">
                            <information>
                                <title>Agesic Firma</title>
                                <vendor>Agesic</vendor>
                                <homepage href=""/>
                                <description>Agesic Firma</description>
                                <description kind="short">Agesic Firma</description>
                                <offline-allowed/>
                            </information>
                            <update check="always" policy="prompt-update"/>
                            <security>
                                 <all-permissions/>
                            </security>
                            <resources>
                                <j2se version="1.6+"/>
                                <jar href="AgesicFirmaApplet-AgesicFirmaApplet-4.3.jar" main="true"/>
                                <jar href="activation-1.1.jar" main="false"/>
                                <jar href="aerogear-crypto-0.1.5.jar" main="false"/>
                                <jar href="avalon-framework-4.1.3.jar" main="false"/>
                                <jar href="batik-awt-util-1.6.jar" main="false"/>
                                <jar href="batik-dom-1.6.jar" main="false"/>
                                <jar href="batik-svg-dom-1.6.jar" main="false"/>
                                <jar href="batik-svggen-1.6.jar" main="false"/>
                                <jar href="batik-util-1.6.jar" main="false"/>
                                <jar href="batik-xml-1.6.jar" main="false"/>
                                <jar href="bcmail-jdk15-1.46.jar" main="false"/>
                                <jar href="bcprov-jdk15-1.46.jar" main="false"/>
                                <jar href="bctsp-jdk15-1.46.jar" main="false"/>
                                <jar href="commons-codec-1.2.jar" main="false"/>
                                <jar href="commons-httpclient-3.0.1.jar" main="false"/>
                                <jar href="commons-io-2.1.jar" main="false"/>
                                <jar href="commons-lang-2.4.jar" main="false"/>
                                <jar href="commons-logging-1.1.jar" main="false"/>
                                <jar href="icepdf-core-4.3.2.jar" main="false"/>
                                <jar href="icepdf-viewer-4.3.2.jar" main="false"/>
                                <jar href="ini4j-0.5.2.jar" main="false"/>
                                <jar href="itextpdf-5.2.0.jar" main="false"/>
                                <jar href="jai-codec-1.1.3.jar" main="false"/>
                                <jar href="jai-core-1.1.3.jar" main="false"/>
                                <jar href="java-plugin-jre-1.5.0_09.jar" main="false"/>
                                <jar href="junit-3.8.1.jar" main="false"/>
                                <jar href="log4j-1.2.14.jar" main="false"/>
                                <jar href="logkit-1.0.1.jar" main="false"/>
                                <jar href="mail-1.4.1.jar" main="false"/>
                                <jar href="MITyCLibAPI-1.0.4.jar" main="false"/>
                                <jar href="MITyCLibCert-1.0.4.jar" main="false"/>
                                <jar href="MITyCLibPolicy-1.0.4.jar" main="false"/>
                                <jar href="MITyCLibTrust-1.0.4.jar" main="false"/>
                                <jar href="MITyCLibTSA-1.0.4.jar" main="false"/>
                                <jar href="MITyCLibXADES-1.0.4.jar" main="false"/>
                                <jar href="sc-light-jdk15on-1.47.0.3.jar" main="false"/>
                                <jar href="scprov-jdk15on-1.47.0.3.jar" main="false"/>
                                <jar href="serializer-2.7.1.jar" main="false"/>
                                <jar href="servlet-api-2.3.jar" main="false"/>
                                <jar href="swing-layout-1.0.3.jar" main="false"/>
                                <jar href="UserAgentUtils-1.15.jar" main="false"/>
                                <jar href="webservices-api-1.4.jar" main="false"/>
                                <jar href="webservices-rt-1.4.jar" main="false"/>
                                <jar href="xalan-2.7.1.jar" main="false"/>
                                <jar href="xml-apis-1.3.04.jar" main="false"/>
                                <jar href="xmlsec-1.4.2-ADSI-1.0.jar" main="false"/>
                            </resources>
                            <application-desc main-class="uy.gub.agesic.firma.cliente.applet.SignAppletStub">
                                <argument>-ID_TRANSACCION='. $token_id[0] .'</argument>
                                <argument>-TIPO_DOCUMENTO=pdf</argument>
                                <argument>-AGESIC_FIRMA_WS='. WS_AGESIC_FIRMA .'</argument>
                                <argument>-URL_OK_POST='. WS_AGESIC_FIRMA_OK .'?filename='. $filename .'&campo='. $campo_id .'</argument>
                            </application-desc>
                        </jnlp>';

        echo $respuesta;
    }

    function confirmar_firma() {
        $id_transaccion = $_POST['idtransaccion'];
        $id_file_name =  $_GET['filename'];
        $campo_id = $_GET['campo'];

        $uploadDirectory = DIRECTORIO_SUBIDA_DOCUMENTOS;
        $soap_endpoint_location = WS_FIRMA_DOCUMENTOS;

        $soap_body = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.firma.agesic.gub.uy/">
                       <soapenv:Header/>
                       <soapenv:Body>
                          <ws:obtenerDocumentosFirmados>
                             <ws:id_transaccion>'. $id_transaccion .'</ws:id_transaccion>
                          </ws:obtenerDocumentosFirmados>
                       </soapenv:Body>
                    </soapenv:Envelope>';

        $soap_header = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Content-length: ".strlen($soap_body)
        );

        $soap_do = curl_init();

        curl_setopt($soap_do, CURLOPT_URL, $soap_endpoint_location);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, WS_TIMEOUT_CONEXION);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        WS_TIMEOUT_RESPUESTA);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST,           true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $soap_body);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $soap_header);

        $soap_response = curl_exec($soap_do);
        curl_close($soap_do);

        $xml = new SimpleXMLElement($soap_response);
        $documento_base64 = $xml->xpath(WS_AGESIC_DOCUMENTO_XPATH);
        $documento = base64_decode($documento_base64[0]);

        $file = Doctrine_Query::create()
                ->from('File f')
                ->where('f.filename = ? AND f.tipo = ?',array($id_file_name, 'documento'))
                ->fetchOne();

        $resultado = new stdClass();
        if(!$file) {
            $resultado->status=1;
            $resultado->error='Token no corresponde';
        }
        else {
            $resultado->status=0;
            file_put_contents($uploadDirectory.$file->filename, $documento);

            $file->firmado = true;
            $file->save();
        }

        $campo_documento = Doctrine::getTable('Campo')->find($campo_id);

        if($campo_documento) {
            $campos_extra = $campo_documento->extra;


            if(isset($campos_extra->firmar_servidor)) {
                if($campos_extra->firmar_servidor == 'on') {
                    if($campos_extra->firmar_servidor_momento == 'despues') {
                        $documento = new Documento();
                        $documento->firmar_documento_servidor($file->filename, $campo_id);
                    }
                }
            }
        }
    }

    function get($id) {
        $CI = &get_instance();
        //verifico si el usuario pertenece el grupo MesaDeEntrada y esta actuando como un ciudadano
        if(UsuarioSesion::usuarioMesaDeEntrada() && $CI->session->userdata('id_usuario_ciudadano')) {
          $usuario_sesion = Doctrine_Query::create()
              ->from('Usuario u')
              ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
              ->fetchOne();
        }
        else {
          $usuario_sesion = UsuarioSesion::usuario();
        }

        // $id=$this->input->get('id');
        $token=$this->input->get('token');

        //Chequeamos permisos del frontend
        $file=Doctrine_Query::create()
                ->from('File f, f.Tramite t, t.Etapas e, e.Usuario u')
                ->where('f.id = ? AND f.llave = ? AND u.id = ?',array($id,$token,$usuario_sesion->id))
                ->fetchOne();

        if(!$file){
            //Chequeamos permisos en el backend
            $file=Doctrine_Query::create()
                ->from('File f, f.Tramite.Proceso.Cuenta.UsuariosBackend u')
                ->where('f.id = ? AND f.llave = ? AND u.id = ? AND (u.rol="super" OR u.rol="operacion" OR u.rol="seguimiento")',array($id,$token,UsuarioBackendSesion::usuario()->id))
                ->fetchOne();

            if(!$file){
                echo 'Usuario no tiene permisos para ver este archivo.';
                exit;
            }
        }

        $path='uploads/documentos/'.$file->filename;

        if(preg_match('/^\.\./', $file->filename)){
            echo 'Archivo invalido';
            exit;
        }

        if(!file_exists($path)){
            echo 'Archivo no existe';
            exit;
        }

        $friendlyName=str_replace(' ','-',convert_accented_characters(mb_convert_case($file->Tramite->Proceso->Cuenta->nombre.' '.$file->Tramite->Proceso->nombre,MB_CASE_LOWER).'-'.$file->id)).'.'.pathinfo($path,PATHINFO_EXTENSION);

        header('Content-Type: '. get_mime_by_extension($path));
        header('Content-Length: ' . filesize($path));
        header('Content-Disposition: attachment; filename="'.$friendlyName.'"');
        readfile($path);
    }

    //Acceso que utiliza applet de firma con token
    function firma_get(){
        $id=$this->input->get('id');
        $llave_firma=$this->input->get('token');

        if(!$id || !$llave_firma){
            $resultado=new stdClass();
            $resultado->status=1;
            $resultado->error='Faltan parametros';
            echo json_encode($resultado);
            exit;
        }

        $file=Doctrine_Query::create()
                ->from('File f, f.Tramite.Etapas.Usuario u')
                ->where('f.id = ? AND f.tipo = ? AND f.llave_firma = ? AND u.id = ?',array($id,'documento',$llave_firma,UsuarioSesion::usuario()->id))
                ->fetchOne();

        $resultado=new stdClass();
        if(!$file){
            $resultado->status=1;
            $resultado->error='Token no corresponde';
        }else{
            $resultado->status=0;
            $resultado->tipo='pdf';
            $resultado->documento=base64_encode(file_get_contents('uploads/documentos/'.$file->filename));
        }

        echo json_encode($resultado);
    }

    function firma_post(){
        $id=$this->input->post('id');
        $llave_firma=$this->input->post('token');
        $documento=$this->input->post('documento');

        if(!$id || !$llave_firma || !$documento){
            $resultado=new stdClass();
            $resultado->status=1;
            $resultado->error='Faltan parametros';
            echo json_encode($resultado);
            exit;
        }

        $file=Doctrine_Query::create()
                ->from('File f, f.Tramite.Etapas.Usuario u')
                ->where('f.id = ? AND f.tipo = ? AND f.llave_firma = ? AND u.id = ?',array($id,'documento',$llave_firma,UsuarioSesion::usuario()->id))
                ->fetchOne();

        $resultado=new stdClass();
        if(!$file){
            $resultado->status=1;
            $resultado->error='Token no corresponde';
        }else{
            $resultado->status=0;
            file_put_contents('uploads/documentos/'.$file->filename, base64_decode($documento));
        }

        echo json_encode($resultado);
    }
}
