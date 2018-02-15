<?php

require_once 'application/third_party/file-uploader.php';

class Uploader extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    function datos($campo_id,$etapa_id) {
        $etapa=Doctrine::getTable('Etapa')->find($etapa_id);
        $funcionario_actuando_como_ciudadano = UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano');
        if(!$funcionario_actuando_como_ciudadano && UsuarioSesion::usuario()->id!=$etapa->usuario_id){
            echo 'Usuario no tiene permisos para subir archivos en esta etapa';
            exit;
        }
        $campo=  Doctrine_Query::create()
                ->from('Campo c, c.Formulario.Pasos.Tarea.Etapas e')
                ->where('c.id = ? AND e.id = ?',array($campo_id,$etapa_id))
                ->fetchOne();
        if(!$campo){
            echo 'Campo no existe';
            exit;
        }

        // list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $allowedExtensions = array('gif', 'jpg', 'png', 'pdf', 'doc', 'docx','zip','rar','ppt','pptx','xls','xlsx','mpp','vsd');
        if(isset($campo->extra->filetypes))
            $allowedExtensions=$campo->extra->filetypes;

        // max file size in bytes
        $sizeLimit = 40 * 1024 * 1024;

        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
        $result = $uploader->handleUpload('uploads/datos/');
          if(isset($result['success'])){
              $file=new File();
              $file->tramite_id=$etapa->Tramite->id;
              $file->filename=$result['file_name'];
              $file->tipo='dato';
              $file->llave=strtolower(random_string('alnum', 12));
              //$file->etapa_id=$etapa_id; // agrega la etapa a la tabla file
              $file->file_origen=$result['file_origen']; // agrega el nombre original a la tabla file
              $file->save();

              $result['id']=$file->id;
              $result['llave']=$file->llave;
          }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }

    function datos_get($id) {
        // $id=$this->input->get('id');
        $token=$this->input->get('token');
        $usuario_id = UsuarioSesion::usuario()->id;

        $funcionario_actuando_como_ciudadano = UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano');
        if ($funcionario_actuando_como_ciudadano){
            $usuario_id = $this->session->userdata('id_usuario_ciudadano');
        }
        //Chequeamos los permisos en el frontend
        $file=Doctrine_Query::create()
                ->from('File f, f.Tramite t, t.Etapas e, e.Usuario u')
                ->where('f.id = ? AND f.llave = ? AND u.id = ?',array($id,$token,$usuario_id))
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

        $path='uploads/datos/'.$file->filename;

        if(preg_match('/^\.\./', $file->filename)){
            echo 'Archivo invalido';
            exit;
        }

        if(!file_exists($path)){
            echo 'Archivo no existe';
            exit;
        }

        header('Content-Type: '. get_mime_by_extension($path));
        header('Content-Length: ' . filesize($path));
        header('Content-Disposition: filename='.$file->filename);
        readfile($path);
    }
}
