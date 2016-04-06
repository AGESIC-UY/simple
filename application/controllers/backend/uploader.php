<?php

require_once 'application/third_party/file-uploader.php';

class Uploader extends MY_BackendController {

    function __construct() {
        parent::__construct();

        UsuarioBackendSesion::force_login();
    }

    function logo() {        
        // list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $allowedExtensions = array('png','jpg','gif');
        // max file size in bytes
        $sizeLimit = 20 * 1024 * 1024;

        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
        $result = $uploader->handleUpload('uploads/logos/');
        
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }
    
    function firma() {        
        // list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $allowedExtensions = array('png','jpg','gif');
        // max file size in bytes
        $sizeLimit = 20 * 1024 * 1024;

        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
        $result = $uploader->handleUpload('uploads/firmas/');
        
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }
    
    function firma_get($filename){
        readfile('uploads/firmas/'.$filename);
    }
    
    function timbre() {        
        // list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $allowedExtensions = array('png','jpg','gif');
        // max file size in bytes
        $sizeLimit = 20 * 1024 * 1024;

        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
        $result = $uploader->handleUpload('uploads/timbres/');
        
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }
    
    function timbre_get($filename){
        readfile('uploads/timbres/'.$filename);
    }
    
    function logo_certificado() {        
        // list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $allowedExtensions = array('png','jpg','gif');
        // max file size in bytes
        $sizeLimit = 20 * 1024 * 1024;

        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
        $result = $uploader->handleUpload('uploads/logos_certificados/');
        
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }
    
    function logo_certificado_get($filename){
        readfile('uploads/logos_certificados/'.$filename);
    }


}

?>
