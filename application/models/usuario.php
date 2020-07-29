<?php

class Usuario extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('usuario');
        $this->hasColumn('password');
        $this->hasColumn('rut');
        $this->hasColumn('nombres');
        $this->hasColumn('apellido_paterno');
        $this->hasColumn('apellido_materno');
        $this->hasColumn('email');
        $this->hasColumn('vacaciones');
        $this->hasColumn('cuenta_id');
        $this->hasColumn('salt');
        $this->hasColumn('open_id');
        $this->hasColumn('registrado');
        $this->hasColumn('reset_token');
        $this->hasColumn('acceso_reportes');
    }

    function setUp() {
        parent::setUp();

        $this->actAs('Timestampable');

        $this->hasMany('GrupoUsuarios as GruposUsuarios',array(
            'local'=>'usuario_id',
            'foreign'=>'grupo_usuarios_id',
            'refClass' => 'GrupoUsuariosHasUsuario'
        ));

        $this->hasMany('Etapa as Etapas',array(
            'local'=>'id',
            'foreign'=>'usuario_id'
        ));

        $this->hasOne('Cuenta',array(
            'local'=>'cuenta_id',
            'foreign'=>'id'
        ));

        $this->hasMany('ReporteSatisfaccion',array(
            'local'=>'id',
            'foreign'=>'usuario_id'
        ));

        $this->hasMany('Pago',array(
            'local'=>'id',
            'foreign'=>'usuario_id'
        ));
    }

    function setPassword($password) {
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

    public function hasGrupoUsuarios($grupo_usuarios_id){
        foreach($this->GruposUsuarios as $g)
            if($g->id==$grupo_usuarios_id)
                return TRUE;

        return FALSE;
    }

    public function setGruposUsuariosFromArray($grupos_usuarios_array){
        foreach($this->GruposUsuarios as $key=>$val)
            unset($this->GruposUsuarios[$key]);

        if($grupos_usuarios_array)
            foreach($grupos_usuarios_array as $g)
                $this->GruposUsuarios[]=Doctrine::getTable('GrupoUsuarios')->find($g);
    }

    public function displayName(){
        if($this->nombres)
            return trim($this->nombres);
        else if($this->rut)
            return $this->rut;

        return $this->usuario;
    }

    public function displayUsername($extended=false){
        if($this->open_id)
            $display=$this->rut;
        else
            $display = $this->usuario;

        if($extended){
            if($this->email)
                $display.=' - '.$this->email;
        }

        return $display;
    }

    public function displayInfo(){
        $html='
            <ul style=\'text-align: left;\'>
                <li>Nombres: '.$this->nombres.'</li>
                <li>Apellido Paterno: '.$this->apellido_paterno.'</li>
                <li>Apellido Materno: '.$this->apellido_materno.'</li>
                <li>E-Mail: '.$this->email.'</li>
            </ul>
        ';

         return $html;
    }

    public function setResetToken($llave){
        if($llave)
            $this->_set('reset_token',sha1($llave));
        else
            $this->_set('reset_token',null);
    }

    public function toPublicArray(){
        $publicArray=array(
            'usuario'=>$this->usuario,
            'email'=>$this->email,
            'nombres'=>$this->nombres,
            'apellido_paterno'=>$this->apellido_paterno,
            'apellido_materno'=>$this->apellido_materno
        );

        return $publicArray;
    }

    public function esFuncionario(){
      return $this->cuenta_id > 0 &&  $this->registrado;
    }
    public function esCiudadano(){
      return empty($this->cuenta_id) || $this->cuenta_id == 0;
    }
}
