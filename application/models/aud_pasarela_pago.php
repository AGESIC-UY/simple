<?php

class AudPasarelaPago extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('metodo');
        $this->hasColumn('activo');
        $this->hasColumn('cuenta_id');
        $this->hasColumn('id_aud');
        $this->hasColumn('tipo_operacion_aud');
        $this->hasColumn('usuario_aud');
        $this->hasColumn('fecha_aud');
    }

    function setUp() {
        parent::setUp();
    }
    

    public static function auditar($obj, $usuario, $operacion) {
        $new = new AudPasarelaPago();
        $new->id = $obj->id;
        $new->nombre = $obj->nombre;
        $new->metodo = $obj->metodo;
        $new->activo = $obj->activo;
        $new->cuenta_id = $obj->cuenta_id;
        $new->usuario_aud = $usuario;
        
        $new->tipo_operacion_aud = $operacion;
        $new->fecha_aud = date("Y-m-d H:i:s");
        $new->save();
        
        $conn = Doctrine_Manager::connection();
        $stmt = $conn->prepare("SELECT id_aud FROM aud_pasarela_pago WHERE id=$obj->id ORDER BY id_aud DESC;");
        $stmt->execute();
        $pasarela = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $pasarela_id = $pasarela[0];
        switch ($new->metodo) {
            // -- Método ANTEL
            case 'antel':
                $obj_pa = Doctrine::getTable("PasarelaPagoAntel")->findOneByPasarelaPagoId($obj->id);
                //if (isset($obj_pa->id))
                AudPasarelaPagoAntel::auditar($obj_pa, $usuario, $operacion, $pasarela_id);
                break;
            case 'generico':
                $obj_pg = Doctrine::getTable("PasarelaPagoGenerica")->findOneByPasarelaPagoId($obj->id);
                //if (isset($obj_pg->id))
                AudPasarelaPagoGenerica::auditar($obj_pg, $usuario, $operacion, $pasarela_id);
                break;
        }
        return $new;
    }

}
