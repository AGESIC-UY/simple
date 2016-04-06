<?php
class GrupoUsuariosHasUsuario extends Doctrine_Record {
    function  setTableDefinition() {
        $this->hasColumn('usuario_id','integer',4,array('primary' => true));   
        $this->hasColumn('grupo_usuarios_id','integer',4,array('primary' => true));
    }
}
?>
