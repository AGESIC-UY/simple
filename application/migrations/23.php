<?php
class Migration_23 extends Doctrine_Migration_Base
{
    public function up()
    {
        //migraciones necesarias para la versión 1.3 r1

        //la tabla etapa
        $this->addColumn('etapa', 'usuario_original_historico', 'VARCHAR(4000) DEFAULT null');

        //google analitics
        $this->changeColumn('cuenta', 'codigo_analytics',"VARCHAR(2000) DEFAULT null");
    }

    public function down()
    {
    }
}
