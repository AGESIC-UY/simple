<?php
class TareaHasGrupoUsuarios extends Doctrine_Record {
    function  setTableDefinition() {
        $this->hasColumn('tarea_id','integer',4,array('primary' => true));   
        $this->hasColumn('grupo_usuarios_id','integer',4,array('primary' => true));
    }
}
?>
