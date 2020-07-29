<?php

class AudGrupoUsuarios extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('cuenta_id');
        $this->hasColumn('usuarios_grupo');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud'); 
        $this->hasColumn('fecha_aud');
    }

    function setUp() {
        parent::setUp();
    }
    
   
     public static function auditar($obj,$usuario,$operacion) {
        $new = new AudGrupoUsuarios();
        $conn = Doctrine_Manager::connection();
        $stmt = $conn->prepare("SELECT GROUP_CONCAT(u.usuario) FROM grupo_usuarios_has_usuario gu JOIN usuario u ON u.id = gu.usuario_id WHERE gu.grupo_usuarios_id = $obj->id");
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);        
        $new->id=$obj->id;
        $new->nombre=$obj->nombre;
        $new->usuarios_grupo=$usuarios[0];
        $new->cuenta_id=$obj->cuenta_id;
        $new->usuario_aud=$usuario;
        $new->tipo_operacion_aud=$operacion;
        $new->fecha_aud=  date("Y-m-d H:i:s");
        $new->save();
        if($operacion=="insert"){
          $usuarios = Doctrine::getTable("GrupoUsuarios")->find($obj->id)->Usuarios;  
          foreach ($usuarios as $us) {
              AudUsuario::auditar($us, $usuario, "update");
          }
        }
        return $new;
    }
    
}
