<?php

class UsuarioTable extends Doctrine_Table
{


    //busca las etapas que no han sido asignadas y que usuario_id se podria asignar
    public function findUsuarioEnCuentaOrCiudadano($usuario_code, $cuenta_id)
    {

      if ($cuenta_id){
        $usuario = Doctrine::getTable('Usuario')->findByUsuarioAndCuentaId($usuario_code, $cuenta_id);
        if (count($usuario) == 0){
          //puede ser un ciudadano, se busca como ciudadano, con cuenta NULL
          $usuario = Doctrine_Query::create()
            ->from('Usuario u')
            ->where('u.usuario = ? and u.cuenta_id IS NULL', $usuario_code)
            ->execute();
        }
      }else{
        //no llega la cuenta se busca por default en simple
        $usuario = Doctrine::getTable('Usuario')->findByUsuarioAndOpenId($usuario_code, 0);
      }

      if (count($usuario) == 0) {
        return false;
      } else {
        return $usuario[0];
      }
    }


}
