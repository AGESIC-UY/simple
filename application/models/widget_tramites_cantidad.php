<?php
require_once 'widget.php';

class WidgetTramitesCantidad extends Widget {

    public function display() {
        if (!$this->config) {
            $display = '<p>Widget requiere configuración</p>';
            return $display;
        }

        $datos = array();

        foreach($this->config->procesos as $proceso_id){
            $p=Doctrine::getTable('Proceso')->find($proceso_id);
            if($p){
                $conteo = Doctrine_Query::create()
                    ->from('Tramite t, t.Etapas e, e.DatosSeguimiento d, t.Proceso p ,p.Cuenta c')
                    ->where('c.id = ?', $this->cuenta_id)
                    ->andWhere('p.id = ?', $p->id)
                    ->andWhere('t.pendiente=1')
                    ->having('COUNT(d.id) > 0 OR COUNT(e.id) > 1')  //Mostramos solo los que se han avanzado o tienen datos
                    ->groupBy('t.id')
                    ->count();

                $datos[$p->nombre]['pendientes'] = $conteo;
            }
        }

        foreach($this->config->procesos as $proceso_id){
            $p=Doctrine::getTable('Proceso')->find($proceso_id);
            if($p){
                $conteo = Doctrine_Query::create()
                    ->from('Tramite t, t.Etapas e, e.DatosSeguimiento d, t.Proceso p ,p.Cuenta c')
                    ->where('c.id = ?', $this->cuenta_id)
                    ->andWhere('p.id = ?', $p->id)
                    ->andWhere('t.pendiente=0')
                    ->having('COUNT(d.id) > 0 OR COUNT(e.id) > 1')  //Mostramos solo los que se han avanzado o tienen datos
                    ->groupBy('t.id')
                    ->count();

                $datos[$p->nombre]['completados'] = $conteo;
            }
        }

        $categories_arr = array();
        $pendientes_arr = array();
        $completados_arr = array();
        foreach ($datos as $key => $val) {
            $categories_arr[] = $key;
            $pendientes_arr[] = isset($val['pendientes']) ? (int)$val['pendientes'] : 0;
            $completados_arr[] = isset($val['completados']) ? (int)$val['completados'] : 0;
        }
        $categories = json_encode($categories_arr);
        $pendientes = json_encode($pendientes_arr);
        $completados = json_encode($completados_arr);

        $elem_id = mt_rand() . '_widget';

        $display  = '<div class="dashboard_wrap_chart">';
        $display .= '<canvas id="'. $elem_id .'" width="390" height="350"></canvas>';
        $display .= '
        <script type="text/javascript">
          $(document).ready(function(){
              var data = {
                labels: '. $categories .',
                datasets: [
                    {
                        label: "Pendientes",
                        fillColor: "#c4e3f3",
                        data: '. $pendientes .'
                    },
                    {
                        label: "Completados",
                        fillColor: "#d0e9c6",
                        data: '. $completados .'
                    }
                ]
            };

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

            new Chart(ctx).Bar(data, {barShowStroke: false, responsive: true, multiTooltipTemplate: "<%=datasetLabel%> : <%= value %>"});
          });
        </script>';
      $display .= '</div>';
      $display .= '<p class="dashboard_chart_footer">Trámites realizados</p>';

      return $display;
    }

    public function displayForm() {
        $procesos = $this->Cuenta->Procesos;
        $procesos_array=$this->config?$this->config->procesos:array();

        $display = '<span class="control-label">Procesos a desplegar</span>';
        foreach ($procesos as $p) {
          if($p->nombre != 'BLOQUE') {
            $display.='<label for="proceso_'.$p->id.'" class="checkbox"><input type="checkbox" id="proceso_'.$p->id.'" name="config[procesos][]" value="' . $p->id . '" ' . (in_array($p->id, $procesos_array) ? 'checked' : '') . ' /> ' . $p->nombre . '</label>';
          }
        }

        return $display;
    }

    public function validateForm() {
        $CI = & get_instance();
        $CI->form_validation->set_rules('config[procesos]', 'Procesos', 'required');
    }

}
