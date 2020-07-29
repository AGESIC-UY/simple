<?php

class AudUsuario extends Doctrine_Record {

    function setTableDefinition() {        
        $this->hasColumn('id');
        $this->hasColumn('usuario');
        $this->hasColumn('nombres');
        $this->hasColumn('apellidos');
        $this->hasColumn('email');
        $this->hasColumn('vacaciones');
        $this->hasColumn('cuenta_id');               
        $this->hasColumn('grupos_usuario');
        $this->hasColumn('acceso_reportes');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud'); 
        $this->hasColumn('fecha_aud');
    }

    function setUp() {
        parent::setUp();       
    }

    
    public static function auditar($obj,$usuario,$operacion) {
        $new = new AudUsuario();
        $conn = Doctrine_Manager::connection();
        $stmt = $conn->prepare("SELECT GROUP_CONCAT(g.nombre) FROM grupo_usuarios_has_usuario gu JOIN grupo_usuarios g ON g.id = gu.grupo_usuarios_id WHERE gu.usuario_id = $obj->id");
        $stmt->execute();
        $grupos = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);        
        $new->id=$obj->id;
        $new->usuario=$obj->usuario;
        $new->nombres=$obj->nombres;
        $new->apellidos=$obj->apellido_paterno.' '.$obj->apellido_materno;
        $new->email=$obj->email;
        $new->vacaciones=$obj->vacaciones;
        $new->cuenta_id=$obj->cuenta_id;
        $new->acceso_reportes=$obj->acceso_reportes;
        $new->grupos_usuario=$grupos[0];
        $new->usuario_aud=$usuario;
        $new->tipo_operacion_aud=$operacion;
        $new->fecha_aud=  date("Y-m-d H:i:s");
        $new->save();
        if($operacion=="insert"){
          $grupos = Doctrine::getTable("Usuario")->find($obj->id)->GruposUsuarios;  
          foreach ($grupos as $gu) {
              AudGrupoUsuarios::auditar($gu, $usuario, "update");
          }
        }
        return $new;
    }
}
