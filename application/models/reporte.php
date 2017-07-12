<?php
class Reporte extends Doctrine_Record {
    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
        $this->hasColumn('campos');
        $this->hasColumn('tipo');
        $this->hasColumn('proceso_id');
    }

    function setUp() {
        parent::setUp();

        $this->hasOne('Proceso', array(
            'local' => 'proceso_id',
            'foreign' => 'id'
        ));

        $this->hasMany('Campo as Campos', array(
            'local' => 'id',
            'foreign' => 'reporte_id'
        ));

        $CI =& get_instance();
        $CI->load->helper('excel_helper');
    }

    public function setCampos($campos){
        $this->_set('campos', json_encode($campos));
    }

    public function getCampos(){
        return json_decode($this->_get('campos'));
    }

    public function generar_completo($grupo=null, $usuario=null, $desde=null, $hasta=null){
        set_time_limit(1600);
        ini_set('memory_limit', '-1');

        $CI=& get_instance();

        $CI->load->library('Excel_XML');

        $header=array_merge(array('id etapa','id tramite','nombre etapa','estado','fecha inicio','fecha modificacion','fecha termino','asignado a','ejecutado por'),$this->campos);

        $excel[]=$header;

        $tramites_query=Doctrine_Query::create()
                ->select('t.*, p.*, e.*')
                ->from('Tramite t, t.Proceso p, t.Etapas e')
                ->where('p.id = ?', $this->proceso_id);


        if($grupo) {
          $tramites_query->andWhere('e.Tarea.grupos_usuarios REGEXP ? OR e.Tarea.grupos_usuarios REGEXP ? OR e.Tarea.grupos_usuarios REGEXP ? OR e.Tarea.grupos_usuarios REGEXP ?', array('^' . $grupo . ',', ',' . $grupo . ',', '^' . $grupo . '$', ',' . $grupo . '$'));
        }

        if($usuario) {
          $tramites_query->andWhere('e.usuario_id = ?', $usuario);
        }

        if($desde) {
          $tramites_query->andWhere('e.created_at >= ?', date("Y-m-d H:i:s", strtotime($desde)));
        }

        if($hasta) {
          $hasta = $hasta . ' 23:59:59';
          $tramites_query->andWhere('e.created_at <= ?', date("Y-m-d H:i:s", strtotime($hasta)));
        }

        $tramites = $tramites_query->orderBy('t.id','desc')
        ->orderBy('e.created_at','desc')
                                    ->fetchArray();

        foreach($tramites as $t) {
          foreach($t['Etapas'] as $etapa) {
              $etapa_linea = Doctrine_Core::getTable('Etapa')->find($etapa['id']);

              if(!$etapa_linea->Usuario) {
                $usuario_asignado = 'no asignado';
              }
              else {
                if($etapa_linea->Usuario->registrado == '1') {
                  $usuario_asignado = $etapa_linea->Usuario->usuario;
                }
                else {
                  $usuario_asignado = '';
                }
              }

              if($etapa_linea->pendiente) {
                $usuario_ejecuto = '';
              }
              else {
                if($etapa_linea->Usuario->registrado == '1') {
                  $usuario_ejecuto = $etapa_linea->Usuario->usuario;
                }
                else {
                  $usuario_ejecuto = 'anonimo';
                }
              }

              $row = array(
                            $etapa_linea->id,
                            $t['id'],
                            quitar_caracteres_especiales($etapa_linea->Tarea->nombre),
                            $etapa_linea->pendiente?'pendiente':'completado',
                            $etapa_linea->created_at,
                            $etapa_linea->updated_at,
                            $etapa_linea->ended_at,
                            quitar_caracteres_especiales($usuario_asignado),
                            quitar_caracteres_especiales($usuario_ejecuto)
                          );

              $datos = Doctrine_Core::getTable('DatoSeguimiento')->findByEtapaId($etapa_linea->id);

              foreach($datos as $d) {
                  if(in_array($d['nombre'], $this->campos)) {
                      $val = $d->valor;
                      if(!is_string($val)) {
                        $val = json_encode($val, JSON_UNESCAPED_UNICODE);
                      }

                      $colindex = array_search($d->nombre, $header);
                      $row[$colindex] = $val;
                  }
              }

              for($i=0; $i<count($row); $i++) {
                if(!isset($row[$i])) {
                  $row[$i]='';
                }
              }

              ksort($row);

              $excel[]=$row;
          }
        }

        $CI->excel_xml->addArray($excel);
        $CI->excel_xml->generateXML($this->nombre);
    }

    public function generar(){
        //set_time_limit(600);
        //por bug  en uso de memoria para generar reporte
        ini_set('max_execution_time', 6000);
        ini_set('memory_limit', '-1');

        $CI=& get_instance();

        $CI->load->library('Excel_XML');

        $header=array_merge(array('id','estado','etapa_actual','fecha_inicio','fecha_modificacion','fecha_termino'),$this->campos);

        $excel[]=$header;

        $tramites=Doctrine_Query::create()
                ->from('Tramite t, t.Proceso p, t.Etapas e, e.DatosSeguimiento d')
                ->where('p.id = ?', $this->proceso_id)
                ->having('COUNT(d.id) > 0 OR COUNT(e.id) > 1')  //Mostramos solo los que se han avanzado o tienen datos
                ->groupBy('t.id')
                ->orderBy('t.id desc')
                ->execute();

        foreach($tramites as $t) {
            $etapas_actuales=$t->getEtapasActuales();
            $etapas_actuales_arr=array();
            foreach($etapas_actuales as $e)
                $etapas_actuales_arr[]=$e->Tarea->nombre;
            $etapas_actuales_str=implode(',', $etapas_actuales_arr);
            $row=array(
                        $t->id,
                        $t->pendiente?'pendiente':'completado',
                        quitar_caracteres_especiales($etapas_actuales_str),
                        $t->created_at,
                        $t->updated_at,
                        $t->ended_at
                      );

            $datos = Doctrine_Core::getTable('DatoSeguimiento')->findByTramite($t->id);

            foreach($datos as $d) {
                if(in_array($d['nombre'], $this->campos)){
                    $val = $d->valor;
                    if(!is_string($val))
                        $val = json_encode($val, JSON_UNESCAPED_UNICODE);

                    $colindex = array_search($d->nombre, $header);
                    $row[$colindex] = $val;
                }
            }

            // Rellenamos con espacios en blanco los campos que no existen.
            for($i=0; $i<count($row); $i++)
                if(!isset($row[$i]))
                    $row[$i]='';

            // Ordenamos
            ksort($row);

            $excel[]=$row;
        }

        $CI->excel_xml->addArray($excel);
        $CI->excel_xml->generateXML($this->nombre);
    }
}
