<?php

class Validador extends MY_Controller {

    function __construct() {
        parent::__construct();

    }
    
    public function index(){
        redirect('validador/documento');
    }

    public function documento(){
        if($this->input->get('id'))
            $_POST['id']=$this->input->get('id');
        if($this->input->get('key'))
            $_POST['key']=$this->input->get('key');
        
        $this->form_validation->set_rules('id','Folio','required|callback_check_documento');
        $this->form_validation->set_rules('key','Código de verificación','required');
        
        if($this->form_validation->run()==TRUE){
            $file=Doctrine::getTable('File')->find($this->input->post('id'));
            $filename_copia=  str_replace('.pdf', '.copia.pdf', $file->filename);
            $path='uploads/documentos/'.$filename_copia;
            header('Content-Type: '. get_mime_by_extension($path));
            header('Content-Length: ' . filesize($path));
            readfile($path);
        }
        
        $this->load->view('validador/documento');
    }
    
    public function check_documento($id){  
        $key=$this->input->post('key');
        $key=  preg_replace('/\W/', '', $key);
                
        $file=Doctrine_Query::create()
                ->from('File f')
                ->where('f.id = ?',$id)
                ->fetchOne();
        
        if(!$file){
            $this->form_validation->set_message('check_documento','Folio y/o código no válido.');
            return FALSE;
        }
        

        if($file->llave_copia!=$key){
            $this->form_validation->set_message('check_documento','Folio y/o código no válido.');
            return FALSE;
        }
        
        if($file->validez!==null){
            if($file->validez_habiles){
                $fecha_expiracion=strtotime(add_working_days($file->created_at,$file->validez));
            }else{
                $fecha_expiracion=strtotime($file->created_at.' + '.$file->validez.' days');
            }


            if(now()>$fecha_expiracion){
                $this->form_validation->set_message('check_documento','Documento expiró su periodo de validez.');
                return FALSE;
            }
        }
        
        return TRUE;
    }
}

?>
