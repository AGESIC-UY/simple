<?php
require_once 'widget.php';

class WidgetTramiteEtapas extends Widget {


    public function display(){
        if (!$this->config) {
            $display = '<p>Widget requiere configuración</p>';
            return $display;
        }

      $proceso=Doctrine::getTable('Proceso')->find($this->config->proceso_id);

      if($proceso) {
        $tmp=  Doctrine_Query::create()
                ->select('tar.id, tar.nombre, COUNT(tar.id) as cantidad')
                ->from('Tarea tar, tar.Etapas e, e.DatosSeguimiento d, e.Tramite t, t.Proceso p, p.Cuenta c')
                ->where('p.id = ? and c.id = ?',array($proceso->id,$this->cuenta_id))
                ->andWhere('e.pendiente = 1')
                ->having('COUNT(d.id) > 0 OR COUNT(e.id) > 1')  //Mostramos solo los que se han avanzado o tienen datos
                ->groupBy('tar.id')
                ->execute();

        $datos=array();
        foreach($tmp as $t)
            $datos[]=array($t->nombre,(float)$t->cantidad);

        $datos_armados = '';
        foreach($datos as $dato) {
          $datos_armados .= '{
              value: '. $dato[1] .',
              label: "'. $dato[0] .'",
              color: getRandomColor()
          },';
        }

        $elem_id = mt_rand() . '_widget';

        $nombre_proceso = $proceso->nombre;

        $display = '<div class="dashboard_wrap_chart">';
        $display .= '<canvas id="'. $elem_id .'" width="390" height="280" class="dashboard_pie_chart"></canvas>';
        $display .='
            <script type="text/javascript">
              $(document).ready(function(){
                Chart.types.Doughnut.extend({
                    name: "DoughnutTextInside",
                    showTooltip: function() {
                        this.chart.ctx.save();
                        Chart.types.Doughnut.prototype.showTooltip.apply(this, arguments);
                        this.chart.ctx.restore();
                    },
                    draw: function() {
                        Chart.types.Doughnut.prototype.draw.apply(this, arguments);

                        var width = this.chart.width,
                            height = this.chart.height;

                        var fontSize = (height / 300).toFixed(2);
                        this.chart.ctx.font = fontSize + "em Verdana";
                        this.chart.ctx.textBaseline = "middle";

                        var text = "'. $nombre_proceso .'",
                            textX = Math.round((width - this.chart.ctx.measureText(text).width) / 2),
                            textY = height / 2;

                        this.chart.ctx.fillText(text, textX, textY);
                    }
                });

                var data = ['. $datos_armados .'];

                var originalCalculateXLabelRotation = Chart.Scale.prototype.calculateXLabelRotation
                Chart.Scale.prototype.calculateXLabelRotation = function () {
                    originalCalculateXLabelRotation.apply(this, arguments);
                    this.xScalePaddingRight = 20  ;
                    this.xScalePaddingLeft = 20;
                }

                var isOldIE = $("body").hasClass("ie_support");
                var $canvas = $("body").find("canvas");
                var canvas = $canvas[0];
                if(isOldIE) {
                  canvas = G_vmlCanvasManager.initElement(canvas);
                }
                var ctx = $("#'. $elem_id .'").get(0).getContext("2d");

                new Chart(ctx).DoughnutTextInside(data, {maintainAspectRatio: true, responsive: true, multiTooltipTemplate: "<%=datasetLabel%>: <%= value %>", percentageInnerCutout: 70});
              });
            </script>';
        $display .= '</div>';
        $display .= '<p class="dashboard_chart_footer">Trámites por etapas</p>';

        return $display;
      }
    }

    public function displayForm(){
        $proceso_id=$this->config?$this->config->proceso_id:null;
        $rand = mt_rand();

        $display='<label for="proceso_'.$rand.'">Proceso</label>';
        $procesos=$this->Cuenta->Procesos;
        $display.= '<select name="config[proceso_id]" id="proceso_'.$rand.'">';
        foreach($procesos as $p) {
          if($p->nombre != 'BLOQUE') {
            $display.= '<option value="'.$p->id.'" '.($proceso_id==$p->id?'selected':'').'>'.$p->nombre.'</option>';
          }
        }
        $display.= '</select>';

        return $display;

    }

    public function validateForm(){
        $CI=& get_instance();
        $CI->form_validation->set_rules('config[proceso_id]','Proceso','required');
    }


}
