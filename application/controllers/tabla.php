<?php

class Tabla extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function accion($etapa_id) {


      $etapa = $dato = Doctrine::getTable('Etapa')->find($etapa_id);
      if(!$etapa) {
          show_404();
      }

      //verifico si el usuario pertenece el grupo MesaDeEntrada y  esta actuando como un ciudadano
      if(UsuarioSesion::usuarioMesaDeEntrada() && $this->session->userdata('id_usuario_ciudadano')) {
        //el usuario se saca desde la session con el id_usuario_ciudadano
        $usuario_sesion = Doctrine_Query::create()
            ->from('Usuario u')
            ->where('u.id = ?', $this->session->userdata('id_usuario_ciudadano'))
            ->fetchOne();

            $funcionario_actuando_como_ciudadano = true;
      }
      else {
        //caso normal el usuario es el logueado
        $usuario_sesion = UsuarioSesion::usuario();
        $funcionario_actuando_como_ciudadano = false;
      }

      //la etapa siempre tiene usuario id o el ficticio (no registrado) o el registrado
      //en caso de ser el ficticio tambien esta en la session con el mismo id.
      if ($etapa->usuario_id != $usuario_sesion->id) {
          if (!$usuario_sesion->registrado) {
              $this->session->set_flashdata('redirect', current_url());
              //TODO modificado antes llamaba a login_saml
              redirect('autenticacion/login?redirect='.current_url());
          }

          $data['error'] = 'Usuario no tiene permisos para ejecutar esta etapa.';
          $data['content'] = 'etapas/error';
          $data['title'] = $data['error'];
          $this->load->view($template, $data);
          return;
      }
      if (!$etapa->pendiente) {
          $data['error'] = 'Esta etapa ya fue completada';
          $data['content'] = 'etapas/error';
          $data['title'] = $data['error'];
          $this->load->view($template, $data);
          return;
      }
      if (!$etapa->Tarea->activa()) {
          $data['error'] = 'Esta etapa no se encuentra activa';
          $data['content'] = 'etapas/error';
          $data['title'] = $data['error'];
          $this->load->view($template, $data);
          return;
      }
      if ($etapa->vencida()) {
          $data['error'] = 'Esta etapa se encuentra vencida';
          $data['content'] = 'etapas/error';
          $data['title'] = $data['error'];
          $this->load->view($template, $data);
          return;
      }

      $tabla_data = json_encode($this->input->post('tabla_data'));
      $tabla_id = $this->input->post('tabla_id');
      $row_number = $this->input->post('row_number');

      //convierte a un array de php
      $arr = json_decode($tabla_data);

      //obtiene el campo tabla para obtener la accion a ejecutar
      $campo  = Doctrine::getTable('Campo')->find($tabla_id);
      $accion_id = $campo->extra->accion_id;

      //obtiene la accion, debe ser de tipo web service extended
      $accion  = Doctrine::getTable('Accion')->find($accion_id);


      //para cada columna de la tabla se genera la variables definidas
      //segun los datos del $row_number
      $fila_datos = $arr[$row_number];
      $columns = $campo->extra->columns;

      foreach($columns as $key => $c){
        $variable_generar = $c->variable_accion;
        $valor = $fila_datos[$key];

        //genera las variables para cada columna
        $variable_generar = str_replace('@@','',$variable_generar);
        if ($variable_generar && !empty($variable_generar)){
          //genera el dato de seguimiento
          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($variable_generar, $etapa_id);
          if($dato)
            $dato->delete();

          $dato = new DatoSeguimiento();
          $dato->nombre = $variable_generar;
          $dato->valor = $valor;
          $dato->etapa_id = $etapa_id;
          $dato->save();
        }
      }

      //limpia la variable de error si está seteada antes de ejecutar
      if ($campo->extra->accion_error){
        $dato_error = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $campo->extra->accion_error), $etapa->id);
        if($dato_error)
          $dato_error->delete();
      }



      //la ejecuta, genera las variables de la respuesta.
      $accion->ejecutar($etapa);

      foreach($columns as $key => $c){
        $variable_cargar = $c->variable_accion;
        $variable_cargar = str_replace('@@','',$variable_cargar);
        if ($variable_cargar && !empty($variable_cargar)){
          //genera el dato de seguimiento
          $dato = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId($variable_cargar, $etapa_id);
          if($dato){
            $fila_datos[$key] = $dato->valor;
          }
        }
      }

      $arr[$row_number] = $fila_datos;

      $return = null;
      //se debe verificar si la accion generó el ws_errror y en ese caso retornar algo distinto para que el js lo procese y despliegue mensaje.
      $dato_error = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId('ws_error', $etapa->id);
      if ($dato_error){
        $arr_error= array("ws_error" => $dato_error->valor);
        $arr_error =json_encode($arr_error);
        $return =  $arr_error;
      }else{
        //se verifica si la tabla tiene seteada la variable de error y la invocacion la generó
        if ($campo->extra->accion_error){
          $dato_error = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId(str_replace('@@', '', $campo->extra->accion_error), $etapa->id);
          if ($dato_error){
            $arr_error= array("ws_error" => $dato_error->valor);
            $arr_error =json_encode($arr_error);
            $return =  $arr_error;
          }else{
            $arr =json_encode($arr);
            $return =  $arr;
          }
        }else {
          $arr =json_encode($arr);
          $return =  $arr;
        }

      }

      //como se está ejecutando la accion desde una tabla la variable @@error se tiene que limpiar
      //porque esta variable se utiliza en ejecutar_fin
      $error_servicio = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("error", $etapa->id);
      if ($error_servicio){
        $error_servicio->delete();
      }
      $error_servicio_ws = Doctrine::getTable('DatoSeguimiento')->findOneByNombreAndEtapaId("ws_error", $etapa->id);
      if ($error_servicio_ws){
        $error_servicio_ws->delete();
      }


      echo $return;
    }
}
