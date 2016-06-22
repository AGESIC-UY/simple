<?php
require_once 'widget.php';

class WidgetEtapaUsuarios extends Widget {


    public function display(){
        if (!$this->config) {
            $display = '<p>Widget requiere configuración</p>';
            return $display;
        }

      $tarea=Doctrine::getTable('Tarea')->find($this->config->tarea_id);

      if($tarea) {
        $tmp=  Doctrine_Query::create()
                ->select('u.*, COUNT(e.id) as cantidad')
                ->from('Usuario u, u.Etapas e, e.Tarea t, t.Proceso.Cuenta c')
                ->where('t.id = ? and c.id = ?',array($tarea->id,$this->cuenta_id))
                ->andWhere('e.pendiente = 1')
                ->groupBy('u.id')
                ->execute();

        $datos=array();
        foreach($tmp as $t)
            $datos[]=array($t->usuario,(float)$t->cantidad);

        $datos_armados = '';
        foreach($datos as $dato) {
          $datos_armados .= '{
              value: '. $dato[1] .',
              label: "'. $dato[0] .'",
              color: getRandomColor()
          },';
        }

        $elem_id = mt_rand() . '_widget';

        $display = '<div class="dashboard_wrap_chart">';
        $display .= '<canvas id="'. $elem_id .'" width="390" height="280" class="dashboard_pie_chart"></canvas>';
        $display .='
            <script type="text/javascript">
              $(document).ready(function(){
                var data = ['. $datos_armados .'];

                var originalCalculateXLabelRotation = Chart.Scale.prototype.calculateXLabelRotation
                Chart.Scale.prototype.calculateXLabelRotation = function () {
                    originalCalculateXLabelRotation.apply(this, arguments);
                    this.xScalePaddingRight = 20;
                    this.xScalePaddingLeft = 20;
                }

                var isOldIE = $("body").hasClass("ie_support");
                var $canvas = $("body").find("canvas");
                var canvas = $canvas[0];
                if(isOldIE) {
                  canvas = G_vmlCanvasManager.initElement(canvas);
                }
                var ctx = $("#'. $elem_id .'").get(0).getContext("2d");

                new Chart(ctx).Doughnut(data, {maintainAspectRatio: true, responsive: true});
              });
            </script>';
        $display .= '</div>';
        $display .= '<p class="dashboard_chart_footer">Carga de usuarios por etapa</p>';

        return $display;
      }
    }

    public function displayForm(){
        $tarea_id=$this->config?$this->config->tarea_id:null;

        $procesos=  Doctrine_Query::create()
                ->from('Proceso p, p.Tareas t')
                ->where('p.cuenta_id = ?',$this->Cuenta->id)
                ->where('p.nombre != ?', 'BLOQUE')
                ->andWhere('t.acceso_modo = ?','grupos_usuarios')
                ->execute();

        if(!$procesos->count())
            return '<p>No se puede utilizar este widget ya que no tiene tareas asignadas a grupos de usuarios.</p>';
            $rand = mt_rand();

        $display='<label for="proceso_'.$rand.'">Tareas</label>';
        $display.= '<select name="config[tarea_id]" id="proceso_'.$rand.'">';
        foreach($procesos as $p){
            $display.='<optgroup label="'.$p->nombre.'">';
            foreach($p->Tareas as $t)
                $display.= '<option value="'.$t->id.'" '.($tarea_id==$t->id?'selected':'').'>'.$t->nombre.'</option>';
            $display.='</optgroup>';
        }
        $display.= '</select>';



        return $display;

    }

    public function validateForm(){
        $CI=& get_instance();
        $CI->form_validation->set_rules('config[tarea_id]','Tarea','required');
    }


}
