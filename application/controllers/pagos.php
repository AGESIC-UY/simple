<?php

class Pagos extends MY_Controller {

  public function __construct() {
      parent::__construct();
  }

  public function control() {
    redirect(site_url());
  }

  public function completado() {
    if($this->input->post('IdSol')) {
      $registro_pago = Doctrine_Query::create()
          ->from('Pago p')
          ->where('p.id_solicitud = ?', $this->input->post('IdSol'))
          ->execute();

      if(count($registro_pago) > 0) {
        $registro_pago[0]->estado = 'realizado';
        $registro_pago[0]->fecha_actualizacion = date('d/m/Y H:i');
        $registro_pago[0]->save();
      }
      else {
        $pago = new Pago();
        $pago->id_tramite = $this->input->post('IdTramite');
        $pago->id_solicitud = $this->input->post('IdSol');
        $pago->estado = 'realizado';
        $pago->fecha_actualizacion = date('d/m/Y H:i');
        $pago->pasarela = 'ANTEL';
        $pago->save();
      }

      echo 'OK';
    }
    else {
      redirect(site_url());
    }
  }

  public function error() {
    if($this->input->post('IdSol')) {
      $registro_pago = Doctrine_Query::create()
          ->from('Pago p')
          ->where('p.id_solicitud = ?', $this->input->post('IdSol'))
          ->execute();

      if(count($registro_pago) > 0) {
        $registro_pago[0]->estado = 'error';
        $registro_pago[0]->fecha_actualizacion = date('d/m/Y H:i');
        $registro_pago[0]->save();
      }
      else {
        $pago = new Pago();
        $pago->id_tramite = $this->input->post('IdTramite');
        $pago->id_solicitud = $this->input->post('IdSol');
        $pago->estado = 'error';
        $pago->fecha_actualizacion = date('d/m/Y H:i');
        $pago->pasarela = 'ANTEL';
        $pago->save();
      }

      echo 'OK';
    }
    else {
      redirect(site_url());
    }
  }

  public function pendiente() {
    if($this->input->post('IdSol')) {
      $registro_pago = Doctrine_Query::create()
          ->from('Pago p')
          ->where('p.id_solicitud = ?', $this->input->post('IdSol'))
          ->execute();

      if(count($registro_pago) > 0) {
        $registro_pago[0]->estado = 'pendiente';
        $registro_pago[0]->fecha_actualizacion = date('d/m/Y H:i');
        $registro_pago[0]->save();
      }
      else {
        $pago = new Pago();
        $pago->id_tramite = $this->input->post('IdTramite');
        $pago->id_solicitud = $this->input->post('IdSol');
        $pago->estado = 'pendiente';
        $pago->fecha_actualizacion = date('d/m/Y H:i');
        $pago->pasarela = 'ANTEL';
        $pago->save();
      }

      echo 'OK';
    }
    else {
      redirect(site_url());
    }
  }

  public function rechazado() {
    if($this->input->post('IdSol')) {
      $registro_pago = Doctrine_Query::create()
          ->from('Pago p')
          ->where('p.id_solicitud = ?', $this->input->post('IdSol'))
          ->execute();

      if(count($registro_pago) > 0) {
        $registro_pago[0]->estado = 'rechazado';
        $registro_pago[0]->fecha_actualizacion = date('d/m/Y H:i');
        $registro_pago[0]->save();
      }
      else {
        $pago = new Pago();
        $pago->id_tramite = $this->input->post('IdTramite');
        $pago->id_solicitud = $this->input->post('IdSol');
        $pago->estado = 'rechazado';
        $pago->fecha_actualizacion = date('d/m/Y H:i');
        $pago->pasarela = 'ANTEL';
        $pago->save();
      }

      echo 'OK';
    }
    else {
      redirect(site_url());
    }
  }
}
