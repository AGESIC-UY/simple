<?php
class Migration_5 extends Doctrine_Migration_Base
{
    public function up()
    {
      // --migraciones necesarias para la versión 1.6

      $this->addColumn('monitoreo', 'tramite_id', 'INT(11) DEFAULT null');
      $this->addColumn('monitoreo', 'etapa_id', 'INT(11) DEFAULT null');
      $this->addColumn('monitoreo', 'paso_id', 'INT(11) DEFAULT null');
      $this->addColumn('monitoreo', 'fecha_respuesta_servicio', 'DATETIME DEFAULT null');
      
      // modifica tabla tarea
      $this->addColumn('tarea', 'escalado_automatico', 'TINYINT(1) default 0');
      $this->addColumn('tarea', 'vencimiento_a_partir_de_variable', 'VARCHAR(255) default null');
      $this->addColumn('tarea', 'notificar_vencida', 'TINYINT(1) default 0');

      // modifica tabla campo
      $this->changeColumn('campo', 'dependiente_campo',"VARCHAR(255) default null");

    }

    public function down()
    {
    }
}
