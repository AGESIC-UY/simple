<?php

class TramiteTable extends Doctrine_Table {
    

    //busca los tramites donde el $usuario_id ha participado
    public function findParticipados($usuario_id,$cuenta='localhost'){        
        $query=Doctrine_Query::create()
                ->from('Tramite t, t.Proceso.Cuenta c, t.Etapas e, e.Usuario u')
                ->where('u.id = ?',$usuario_id)
                ->andWhere('e.pendiente=0')
                ->orderBy('t.updated_at desc');
        
        if($cuenta!='localhost')
            $query->andWhere('c.nombre = ?',$cuenta->nombre);
        
        return $query->execute();
    }
    
}
