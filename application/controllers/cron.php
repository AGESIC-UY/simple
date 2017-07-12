<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cron extends CI_Controller {

    public function __construct() {
        parent::__construct();

        if (!$this->input->is_cli_request()) {
            echo 'Accion no permitida';
            exit;
        }
    }

    public function hourly(){
        //Indexamos las busquedas en Sphinx
        system('cd sphinx; searchd; indexer --rotate --all');
    }

    public function daily() {
        //Buscamos las etapas que estan por vencer, pendientes y que requieren ser notificadas
        $etapas = Doctrine_Query::create()
                ->from('Etapa e, e.Tarea t')
                ->where('e.pendiente = 1 AND t.vencimiento_notificar = 1')
                ->andWhere('DATEDIFF(e.vencimiento_at,NOW()) <= t.vencimiento_notificar_dias')
                ->execute();
        foreach ($etapas as $e) {
          $dias_por_vencer=ceil((strtotime($e->vencimiento_at)-time())/60/60/24);
          $regla=new Regla($e->Tarea->vencimiento_notificar_email);
          $email=$regla->getExpresionParaOutput($e->id);

          $CI = & get_instance();
          $cuenta=$e->Tramite->Proceso->Cuenta;

          if(!$cuenta->correo_remitente) {
            ($CI->config->item('main_domain') == '') ? $from = $cuenta->nombre.'@simple' : $from = $cuenta->nombre.'@'.$CI->config->item('main_domain');
          }
          else {
            $from = $cuenta->correo_remitente;
          }

          echo 'Enviando correo de notificacion para etapa ' . $e->id . "\n";
          $this->email->from($from, 'Simple');
          $this->email->to($email);
          $this->email->subject('Simple - Etapa se encuentra por vencer');
          $this->email->message('La etapa "' . $e->Tarea->nombre . '" se encuentra '.($dias_por_vencer>0?'a '.$dias_por_vencer.' días por vencer':'vencida').'.' . "\n\n" . 'Usuario asignado: ' . $e->Usuario->usuario);
          $this->email->send();
        }

        //Limpia los tramites que que llevan mas de 1 dia sin modificarse, sin avanzar de etapa y sin datos ingresados (En blanco).
        $tramites_en_blanco=Doctrine_Query::create()
                ->from('Tramite t, t.Etapas e, e.Usuario u, e.DatosSeguimiento d')
                ->where('t.updated_at < DATE_SUB(NOW(),INTERVAL 1 DAY) AND t.pendiente = 1')
                ->groupBy('t.id')
                ->having('COUNT(e.id) = 1 AND COUNT(d.id) = 0')
                ->execute();
        $tramites_en_blanco->delete();

        //Elimino los registros no registrados con mas de 1 dia de antiguedad y que no hayan iniciado etapas
        $noregistrados=Doctrine_Query::create()
                ->from('Usuario u, u.Etapas e')
                ->where('u.registrado = 0 AND DATEDIFF(NOW(),u.updated_at) >= 1')
                ->groupBy('u.id')
                ->having('COUNT(e.id) = 0')
                ->execute();
        $noregistrados->delete();
    }
}
