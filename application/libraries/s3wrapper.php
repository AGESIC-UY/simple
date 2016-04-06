<?php
require_once 'S3.php';

class S3Wrapper extends S3{
    public function __construct() {
        $CI=&get_instance();
        $accessKey=$CI->config->item('awsAccessKey');
        $secretKey=$CI->config->item('awsSecretKey');
        
        parent::__construct($accessKey, $secretKey, true);
        
    }
}