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
      $CI = & get_instance();
      $CI->load->helper('filename_concurrencia_helper');
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
          return array('error' => 'El archivo excede el tamaño máximo permitido');
      }

      $pathinfo = pathinfo($this->file->getName());
      //archivos con tildes o caracters no ascii
      $file_origen = $pathinfo['filename'];
      $filename = obtenerFileName();

      // $filename = sha1(uniqid(mt_rand(),true));
      $ext = $pathinfo['extension'];

      if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
          $these = implode(',', $this->allowedExtensions);
          return array('error' => 'Extensión de archivo inválida. Solo puedes subir archivos con las siguientes extensiones: '. $these . '.');
      }

      if(!$replaceOldFile){
          while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
              $filename = obtenerFileName();
          }
      }

      if ($this->file->save($uploadDirectory . $filename . '.' . $ext, $file_origen.'.'.$ext)) {
        try {
          //TODO Solicitado por nicolas martinez para presidencia. en
          //la version 1.1 se solicita que se incluya en simple
          //si se graba un audio con el celular por ejemplo, el finfo no lo
          //detecta como audio lo detecta como bytes y no validaba
          //por lo tanto para el caso mp3 y mp4 si la extension es valida se da por bueno.
          if ($ext == 'mp3' || $ext == 'mp4' || strtoupper($ext) == 'KML' || strtoupper($ext) == 'KMZ'){
              return array('success'=>true, 'file_name'=>$filename.'.'.$ext, 'full_path'=>$uploadDirectory . $filename . '.' . $ext, 'file_origen'=>$file_origen.'.'.$ext);
          }

          //la validacion por finfo
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
              $mtype = 'docx';
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
              if (strtolower($ext) == 'jpg'){
                $mtype = 'jpg';
              }
              if (strtolower($ext) == 'jpeg'){
                $mtype = 'jpeg';
              }

              break;
            case 'x-ms-wmv':
              $mtype = 'wmv';
              break;
            case 'x-ms-asf':
              $mtype = 'wmv';
              break;
            case 'x-msvideo':
              $mtype = 'avi';
              break;
            case 'quicktime':
              $mtype = 'mov';
              break;
            case 'mpeg3':
              $mtype = 'mp3';
              break;
            case 'x-mpeg-3':
              $mtype = 'mp3';
              break;
            case 'x-mpeg-3':
              $mtype = 'mp3';
              break;
            case 'x-mpeg-3':
              $mtype = 'mp3';
              break;
            case 'x-mpeg-3':
              $mtype = 'mp3';
              break;
            case 'x-aac':
              $mtype = 'aac';
              break;
            case 'vnd.uvvu.mp4':
              $mtype = 'mp4';
              break;
            case 'mp4':
              $mtype = 'mp4';
              break;
            case 'mpg':
              $mtype = 'mp3';
              break;
            case '3gpp':
              $mtype = '3gp';
              break;
            case 'x-ms-wma':
              $mtype = 'wma';
              break;
            case 'mpeg':
              $mtype = 'mpg';
              break;
            case 'x-mpeg':
              $mtype = 'mpg';
              break;
            case 'voxware':
              $mtype = 'vox';
              break;
            case 'x-pn-realaudio':
              $mtype = 'ra';
              break;
            case 'x-pn-realaudio-plugin':
              $mtype = 'ra';
              break;
            case 'x-realaudio':
              $mtype = 'ra';
              break;
            case 'vnd.rn-realmedia':
              $mtype = 'rm';
              break;
            case 'x-pn-realaudio':
              $mtype = 'rm';
              break;
            case 'vnd.rn-realmedia':
              $mtype = 'rm';
              break;
            case 'vnd.rn-realmedia':
              $mtype = 'rm';
              break;
            case 'vnd.rn-realmedia':
              $mtype = 'rm';
              break;
            case 'octet-stream' || 'plain':
              if(in_array('crt', $this->allowedExtensions) || in_array('key', $this->allowedExtensions) || in_array('pem', $this->allowedExtensions) || in_array('p12', $this->allowedExtensions)) {
                switch($ext) {
                  case 'crt':
                    $mtype = 'crt';
                    break;
                  case 'key':
                    $mtype = 'key';
                    break;
                  case 'pem':
                    $mtype = 'pem';
                    break;
                  case 'p12':
                    $mtype = 'p12';
                    break;
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

        return array('success'=>true, 'file_name'=>$filename.'.'.$ext, 'full_path'=>$uploadDirectory . $filename . '.' . $ext, 'file_origen'=>$file_origen.'.'.$ext);
        /*Se incluyó la variable del nombre original del archivo para poder guardarlo en la tabla file como parámetros que recibe el controlador */
      }
      else {
        return array('error'=> 'No se puede subir el archivo.' . 'La subida fue cancelada o ha ocurrido un error en el servidor.');
      }
    }

    function convert_ascii($string)
    {
      // Replace Single Curly Quotes
      $search[]  = chr(226).chr(128).chr(152);
      $replace[] = "'";
      $search[]  = chr(226).chr(128).chr(153);
      $replace[] = "'";
      // Replace Smart Double Curly Quotes
      $search[]  = chr(226).chr(128).chr(156);
      $replace[] = '"';
      $search[]  = chr(226).chr(128).chr(157);
      $replace[] = '"';
      // Replace En Dash
      $search[]  = chr(226).chr(128).chr(147);
      $replace[] = '--';
      // Replace Em Dash
      $search[]  = chr(226).chr(128).chr(148);
      $replace[] = '---';
      // Replace Bullet
      $search[]  = chr(226).chr(128).chr(162);
      $replace[] = '*';
      // Replace Middle Dot
      $search[]  = chr(194).chr(183);
      $replace[] = '*';
      // Replace Ellipsis with three consecutive dots
      $search[]  = chr(226).chr(128).chr(166);
      $replace[] = '...';
      // Apply Replacements
      $string = str_replace($search, $replace, $string);
      // Remove any non-ASCII Characters
      $string = preg_replace("/[^\x01-\x7F]/","", $string);
      return $string;
    }

}
