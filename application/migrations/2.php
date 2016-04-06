<?php
class Migration_2 extends Doctrine_Migration_Base {
    public function up(){
        $this->addColumn( 'documento', 'subtitulo', 'string' , 128, array( 'notnull' => 1));
        
    }
    
    public function postUp() {
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();
        $q->execute("UPDATE documento SET subtitulo='Certificado Gratuito' WHERE tipo='certificado'");
    }
    
    public function down(){
        $this->removeColumn( 'documento', 'subtitulo' );
    }
}