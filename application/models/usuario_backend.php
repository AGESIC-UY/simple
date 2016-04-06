<?php

class UsuarioBackend extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('email');
        $this->hasColumn('password');
        $this->hasColumn('nombre');
        $this->hasColumn('apellidos');
        $this->hasColumn('rol');
        $this->hasColumn('salt');
        $this->hasColumn('cuenta_id');
        $this->hasColumn('reset_token');
    }

    function setUp() {
        parent::setUp();
        
        $this->hasOne('Cuenta',array(
            'local'=>'cuenta_id',
            'foreign'=>'id'
        ));
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

}
