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

    //busca los tramites donde el $usuario_id ha participado
    public function findParticipadosConPaginacion($usuario_id,$cuenta='localhost', $offset, $per_page){
        $query=Doctrine_Query::create()
                ->from('Tramite t, t.Proceso.Cuenta c, t.Etapas e, e.Usuario u')
                ->where('u.id = ?',$usuario_id)
                ->andWhere('e.pendiente=0')
                ->orderBy('t.updated_at desc')
                ->limit($per_page)
                ->offset($offset);

        if($cuenta!='localhost')
            $query->andWhere('c.nombre = ?',$cuenta->nombre);

        $resultado = new stdClass();
        $resultado->tramites = $query->execute();
        $resultado->cantidad = $query->count();

        return $resultado;
    }

    public function cantidadParticipados($usuario_id,$cuenta='localhost'){
        $query=Doctrine_Query::create()
                ->select('COUNT(t.id)')
                ->from('Tramite t, t.Proceso.Cuenta c, t.Etapas e, e.Usuario u')
                ->where('u.id = ?',$usuario_id)
                ->andWhere('e.pendiente=0')
                ->orderBy('t.updated_at desc');

        if($cuenta!='localhost')
            $query->andWhere('c.nombre = ?',$cuenta->nombre);

        return $query->count();
    }

    public function findParticipadosFiltro($usuario_id,
    $cuenta = 'localhost',
    $orderby,
    $direction,
    $busqueda_id_tramite,
    $busqueda_etapa,
    $busqueda_grupo,
    $busqueda_nombre,
    $busqueda_documento,
    $busqueda_modificacion_desde,
    $busqueda_modificacion_hasta,
    $per_page,
    $offset,
    $count= false){


    $conn = Doctrine_Manager::connection();

    if(!isset($offset) || !$offset){
        $offset = 0;
      }

   $opciones_busqueda .= '';
   $limit = $per_page;

   if($cuenta!='localhost'){
      //$query->andWhere('c.nombre = ?',$cuenta->nombre);
      $opciones_busqueda .= ' and c.nombre = :cuenta ';
    }
    if($busqueda_id_tramite){
      //$query->andWhere('t.id = ?',$busqueda_id_tramite);
      $opciones_busqueda .= ' and t.id = :busqueda_id_tramite ';
    }
    if($busqueda_nombre){
      //$query->andWhere('t.Proceso.nombre LIKE ?','%'.$busqueda_nombre.'%');
      $opciones_busqueda .= ' and p.nombre LIKE :busqueda_nombre ';
    }

    if($busqueda_etapa){
       $opciones_busqueda .= ' and tar.nombre LIKE :busqueda_etapa ';
     }
     if($busqueda_modificacion_desde){
       //, date("Y-m-d H:i:s", strtotime($busqueda_modificacion_desde)))
      $opciones_busqueda .= ' and e.updated_at >= :busqueda_modificacion_desde ';
     }
    if($busqueda_modificacion_hasta){
        //$busqueda_modificacion_hasta = $busqueda_modificacion_hasta . ' 23:59:59';
        //$query->andWhere('e.updated_at <= ?', date("Y-m-d H:i:s", strtotime($busqueda_modificacion_hasta)));
        $opciones_busqueda .= ' and e.updated_at <=  :busqueda_modificacion_hasta ';
     }
     if ($busqueda_grupo) {
           $opciones_busqueda .= ' and tar.acceso_modo = :acceso_modo and (tar.grupos_usuarios REGEXP  :busqueda_grupo_1
           OR
           tar.grupos_usuarios REGEXP :busqueda_grupo_2
           OR
           tar.grupos_usuarios REGEXP :busqueda_grupo_3
           OR
           tar.grupos_usuarios REGEXP :busqueda_grupo_4) ';
     }
     if ($busqueda_documento) {
          $busqueda_documento_encode = json_encode($busqueda_documento);
     }



    if($busqueda_documento){
      if ($count){
        $query = 'select count(distinct t.id) ';
      }else{
        $query = 'select distinct t.id ';
      }

      $query .= ' from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = '. $usuario_id . $opciones_busqueda .
                 ' and (e.tramite_id  IN (
                          SELECT e.tramite_id
                          FROM dato_seguimiento d1, dato_seguimiento d2
                          WHERE
                           d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                           AND d1.etapa_id = d2.etapa_id
                           AND d2.nombre = replace(d1.valor,\'"\', \'\')
                           AND d2.valor = \''. $busqueda_documento_encode.'\')
                           or e.tramite_id  IN (
                                    SELECT ee.tramite_id
                                    FROM etapa ee, usuario uu
                                    WHERE
                                      ee.usuario_id = uu.id and
                                      ee.tramite_id =  e.tramite_id and
                                      trim(uu.usuario) like  \'%'. $busqueda_documento.'\' and not exists (select 1 from etapa eee where eee.tramite_id = ee.tramite_id and eee.id < ee.id) ))';

      if (!$count){
        $query.= ' order by e.updated_at desc limit '. $offset .','. $limit;
      }
    }
    else {
      if ($count){
        $query = 'select count(distinct t.id) ';
      }else{
        $query = 'select distinct t.id ';
      }

      $query .= ' from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = '. $usuario_id . $opciones_busqueda;

      if (!$count){
        $query.= ' order by e.updated_at desc limit '. $offset .','. $limit;
      }
    }

    $stmt= $conn->prepare($query);

    if ($cuenta != 'localhost') {
      $stmt->bindParam(":cuenta", $cuenta->nombre, PDO::PARAM_STR);
    }

    if ($busqueda_id_tramite) {
      $stmt->bindParam(":busqueda_id_tramite", $busqueda_id_tramite, PDO::PARAM_INT);
    }
    if ($busqueda_etapa) {
      $busqueda_etapa_query = "%".$busqueda_etapa."%";
      $stmt->bindParam(":busqueda_etapa", $busqueda_etapa_query, PDO::PARAM_STR);
    }

    if ($busqueda_grupo) {
      $busqueda_grupo_1 = '^' . $busqueda_grupo . ',';
      $busqueda_grupo_2 = ',' . $busqueda_grupo . ',';
      $busqueda_grupo_3 = '^' . $busqueda_grupo . '$';
      $busqueda_grupo_4 = ',' . $busqueda_grupo . '$';
      $acceso_modo = 'grupos_usuarios';
      $stmt->bindParam(":acceso_modo", $acceso_modo, PDO::PARAM_STR);
      $stmt->bindParam(":busqueda_grupo_1", $busqueda_grupo_1, PDO::PARAM_STR);
      $stmt->bindParam(":busqueda_grupo_2", $busqueda_grupo_2, PDO::PARAM_STR);
      $stmt->bindParam(":busqueda_grupo_3", $busqueda_grupo_3, PDO::PARAM_STR);
      $stmt->bindParam(":busqueda_grupo_4",$busqueda_grupo_4, PDO::PARAM_STR);
    }
    if ($busqueda_nombre) {
      $busqueda_nombre_query = "%".$busqueda_nombre."%";
      $stmt->bindParam(":busqueda_nombre", $busqueda_nombre_query, PDO::PARAM_STR);
    }
    if ($busqueda_modificacion_desde) {
      $busqueda_modificacion_desde_date = date("Y-m-d H:i:s", strtotime($busqueda_modificacion_desde));
      $stmt->bindParam(":busqueda_modificacion_desde", $busqueda_modificacion_desde_date, PDO::PARAM_STR);
    }
    if ($busqueda_modificacion_hasta) {
      $busqueda_modificacion_hasta_date = date("Y-m-d H:i:s", strtotime($busqueda_modificacion_hasta. ' 23:59:59'));
      $stmt->bindParam(":busqueda_modificacion_hasta", $busqueda_modificacion_hasta_date, PDO::PARAM_STR);
    }

    //if ($busqueda_documento) {
    //  $stmt->bindParam(":busqueda_documento", $busqueda_documento, PDO::PARAM_STR);
    //}

    //log_message('error', $query);

    $stmt->execute();

      if ($count){
          $datos = $stmt->fetchColumn();
          return $datos;

      }else{
        $datos = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        $tareas = [];
        if ($datos and count($datos) > 0){
        $query=Doctrine_Query::create()
        ->from('Tramite e')
        ->whereIn('e.id', $datos)
        ->orderBy('e.updated_at desc');
        $tareas=$query->execute();
      }
      return $tareas;
    }

  }
}
