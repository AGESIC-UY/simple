<?php

class UsuarioBackend extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('usuario');
        $this->hasColumn('email');
        $this->hasColumn('password');
        $this->hasColumn('nombre');
        $this->hasColumn('apellidos');
        $this->hasColumn('rol');
        $this->hasColumn('salt');
        $this->hasColumn('cuenta_id');
        $this->hasColumn('reset_token');
        //los campos para el rol seguimiento
        $this->hasColumn('seg_alc_control_total');
        $this->hasColumn('seg_alc_grupos_usuarios');
        $this->hasColumn('seg_reasginar');
        $this->hasColumn('seg_reasginar_usu');
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('Cuenta',array(
            'local'=>'cuenta_id',
            'foreign'=>'id'
        ));
    }

    public function setSegAlcGruposUsuarios($grupo_usuarios){
        if($grupo_usuarios)
            $this->_set('seg_alc_grupos_usuarios',  implode ('|', $grupo_usuarios));
        else
            $this->_set('seg_alc_grupos_usuarios','');
    }

    public function getSegAlcGruposUsuarios(){
        if($this->_get('seg_alc_grupos_usuarios'))
            return explode('|',$this->_get('seg_alc_grupos_usuarios'));
        else
            return array();
    }

    function setPassword($password,$salt=null) {
        $hashPassword = sha1($password.$this->salt);
        $this->_set('password', $hashPassword);
    }

    function setPasswordWithSalt($password,$salt=null){
        if($salt!==null)
            $this->salt=$salt;
        else
            $this->salt=random_string ('alnum', 32);

        $this->setPassword($password);
    }

    public function setResetToken($llave){
        if($llave)
            $this->_set('reset_token',sha1($llave));
        else
            $this->_set('reset_token',null);
    }

    public function registrado_saml() {
      if(isset($_COOKIE['simple_bpm_saml_session_ref_k'])) {
        return true;
      }
      else {
        return false;
      }
    }

    public function user_has_rol($usuario_id, $rol) {
      $usuario = Doctrine::getTable('UsuarioBackend')->find($usuario_id);

      $roles = explode(',', $usuario->rol);
      if(array_search($rol, $roles) !== false) {
        return true;
      }
      else {
        return false;
      }
    }
}
