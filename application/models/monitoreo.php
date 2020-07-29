<?php

class Monitoreo extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('proceso_id');
        $this->hasColumn('url_web_service');
        $this->hasColumn('fecha');
        $this->hasColumn('tipo');
        $this->hasColumn('rol');
        $this->hasColumn('certificado');
        $this->hasColumn('error_texto');
        $this->hasColumn('error');
        $this->hasColumn('soap_peticion');
        $this->hasColumn('soap_respuesta');
        $this->hasColumn('catalogo_id');
        $this->hasColumn('seguridad');
        $this->hasColumn('tramite_id');
        $this->hasColumn('etapa_id');
        $this->hasColumn('paso_id');
        $this->hasColumn('fecha_respuesta_servicio');
    }

    function setUp() {
      parent::setUp();
    }

    public static function getListaEjecuciones($tipo) {
        return Doctrine_Query::create()
                        ->from('monitoreo m')
                        ->where('m.tipo = ?', $tipo)
                        ->orderBy('id desc')
                        ->limit(11)
                        ->execute();
    }

    public static function getListaOrdenadaId() {
        return Doctrine_Query::create()
                        ->from('monitoreo m')
                        ->orderBy('id desc')
                        ->limit(11)
                        ->execute();
    }

    public static function getListaOrdenadaIdPaginado($per_page ,$offset, $busqueda_tipo_servicio, $busqueda_nombre_servicio, $busqueda_fecha_desde, $busqueda_fecha_hasta, $busqueda_id_tramite, $busqueda_id_etapa) {

      $query = Doctrine_Query::create()->from('monitoreo m');

      //-- filtros
      $where_antes = false;
      if($busqueda_tipo_servicio) {
        $query->where("m.tipo LIKE '".$busqueda_tipo_servicio."'");
        $where_antes = true;
      }
      if($busqueda_nombre_servicio) {
        if(!$where_antes){
          $query->where("m.url_web_service LIKE '%".$busqueda_nombre_servicio."%'");
          $where_antes = true;
        }
        else{
          $query->andWhere("m.url_web_service LIKE '%".$busqueda_nombre_servicio."%'");
        }
      }
      if($busqueda_fecha_desde) {
        $busqueda_fecha_desde = $busqueda_fecha_desde.' 00:00:00';
        $busqueda_fecha_desde = date("Y-m-d H:i:s", strtotime($busqueda_fecha_desde));

        if(!$where_antes){
          $query->where('m.fecha >= ?', $busqueda_fecha_desde);
          $where_antes = true;
        }
        else{
          $query->andWhere('m.fecha >= ?', $busqueda_fecha_desde);
        }
      }
      if($busqueda_fecha_hasta) {
        $busqueda_fecha_hasta = $busqueda_fecha_hasta.' 23:59:59';
        $busqueda_fecha_hasta = date("Y-m-d H:i:s", strtotime($busqueda_fecha_hasta));

        if(!$where_antes){
          $query->where('m.fecha <= ?', $busqueda_fecha_hasta);
          $where_antes = true;
        }
        else{
          $query->andWhere('m.fecha <= ?', $busqueda_fecha_hasta);
        }
      }
      if($busqueda_id_tramite) {
        if(!$where_antes){
          $query->where('m.tramite_id = ?', $busqueda_id_tramite);
          $where_antes = true;
        }
        else{
          $query->andWhere('m.tramite_id = ?', $busqueda_id_tramite);
        }
      }
      if($busqueda_id_etapa) {
        if(!$where_antes){
          $query->where('m.etapa_id = ?', $busqueda_id_etapa);
          $where_antes = true;
        }
        else{
          $query->andWhere('m.etapa_id = ?', $busqueda_id_etapa);
        }
      }
      //-- termina filtros

      $query->limit($per_page)
              ->offset($offset)
              ->orderBy('id desc');

        //$query_sql = $query->getSqlQuery();

        $resultado = new stdClass();
        $resultado->datos = $query->execute();
        $resultado->cantidad = $query->count();

        return $resultado;
    }
}
