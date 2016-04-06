<?php

require_once 'application/third_party/file-uploader.php';

class Uploader extends CI_Controller {

    function __construct() {
        parent::__construct();

        UsuarioManagerSesion::force_login();
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
    



}

?>
