<?php

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getSize()){
            return false;
        }

        $target = fopen($path, "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return true;
    }
    function getName() {
        return $_GET['qqfile'];
    }
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];
        } else {
            throw new Exception('Error en el servidor: no es posible obtener el tamaño del archivo.');
        }
    }
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
            return false;
        }
        return true;
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}

class qqFileUploader {
    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760) {
      $allowedExtensions = array_map("strtolower", $allowedExtensions);

      $this->allowedExtensions = $allowedExtensions;
      $this->sizeLimit = $sizeLimit;

      $this->checkServerSettings();

      if (isset($_GET['qqfile'])) {
          $this->file = new qqUploadedFileXhr();
      } elseif (isset($_FILES['qqfile'])) {
          $this->file = new qqUploadedFileForm();
      } else {
          $this->file = false;
      }
    }

    private function checkServerSettings(){
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
            die("{'error':'Error en el servidor: incremente el tamaño máximo para la subida de archivos $size'}");
        }
    }

    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE) {
      if (!is_writable($uploadDirectory)){
          return array('error' => "Error en el servidor: el directorio no cuenta con permisos de escritura.");
      }

      if (!$this->file){
          return array('error' => 'No se subieron archivos.');
      }

      $size = $this->file->getSize();

      if ($size == 0) {
          return array('error' => 'El archivo esta vacío');
      }

      if ($size > $this->sizeLimit) {
          return array('error' => 'El archivo es muy grande');
      }

      $pathinfo = pathinfo($this->file->getName());
      $filename = mb_strtolower($pathinfo['filename']);   //Lo convertimos a minusculas
      $filename=  preg_replace('/\s+/', ' ', $filename);  //Le hacemos un trim
      // $filename = sha1(uniqid(mt_rand(),true));
      $ext = $pathinfo['extension'];

      if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
          $these = implode(',', $this->allowedExtensions);
          return array('error' => 'Extensión de archivo inválida. Solo puedes subir archivos con las siguientes extensiones: '. $these . '.');
      }

      if(!$replaceOldFile){
          while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
              $filename .= rand(10, 99);
          }
      }

      if ($this->file->save($uploadDirectory . $filename . '.' . $ext)) {
        try {
          $finfo = new finfo(FILEINFO_MIME);
      		$uploaded_file = $uploadDirectory . $filename . '.' . $ext;
      		$mime_type = explode(';', $finfo->file($uploaded_file));
      		$mime_type = explode('/', $mime_type[0]);

          switch($mime_type[1]) {
            case 'vnd.openxmlformats-officedocument.spreadsheetml.sheet':
              $mtype = 'xlsx';
              break;
            case 'vnd.openxmlformats-officedocument.presentationml.presentation':
              $mtype = 'ppt';
              break;
            case 'vnd.ms-powerpoint':
              $mtype = 'ppt';
              break;
            case 'vnd.openxmlformats-officedocument.wordprocessingml.document':
              $mtype = 'xlsx';
              break;
            case 'vnd.ms-excel':
              $mtype = 'xls';
              break;
            case 'vnd.ms-office':
              $mtype = 'xls';
              break;
            case 'msword':
              $mtype = 'docx';
              break;
            case 'vnd.oasis.opendocument.text':
              $mtype = 'odt';
              break;
            case 'x-rar':
              $mtype = 'rar';
              break;
            case 'vnd.ms-project':
              $mtype = 'mpp';
              break;
            case 'msproj':
              $mtype = 'mpp';
              break;
            case 'msproj':
              $mtype = 'msproject';
              break;
            case 'jpeg':
              $mtype = 'jpg';
              break;
            case 'octet-stream' || 'plain':
              if(in_array('p12', $this->allowedExtensions) && in_array('pem', $this->allowedExtensions)) {
                if($ext == 'p12' || $ext == 'pem') {
                  $mtype = 'p12';
                }
              }
              elseif(in_array('crt', $this->allowedExtensions) && in_array('key', $this->allowedExtensions)) {
                if($ext == 'crt' || $ext == 'key') {
                  $mtype = 'crt';
                }
              }
              else {
                $mtype = $mime_type[1];
              }
              break;
            default:
              $mtype = $mime_type[1];
          }

      		if($this->allowedExtensions && !in_array(strtolower($mtype), $this->allowedExtensions)) {
            $these = implode(',', $this->allowedExtensions);
      			return array('error'=> 'Tipo de archivo inválido. Solo puedes subir los siguientes tipos de archivos: '. $these);
      		}
        }
        catch(Exception $error) {
          return array('error'=> 'Ha fallado la subida del archivo. Por favor vuelva a intentarlo.');
        }

        return array('success'=>true, 'file_name'=>$filename.'.'.$ext, 'full_path'=>$uploadDirectory . $filename . '.' . $ext);
      }
      else {
        return array('error'=> 'No se puede subir el archivo.' . 'La subida fue cancelada o ha ocurrido un error en el servidor.');
      }
    }
}
