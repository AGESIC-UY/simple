<?php

class EtapaTable extends Doctrine_Table
{


    //busca las etapas que no han sido asignadas y que usuario_id se podria asignar
    public function findSinAsignar($usuario_id, $cuenta='localhost')
    {
        set_time_limit(1600);
        ini_set('memory_limit', '-1');

        $query=Doctrine_Query::create()
                ->from('Etapa e, e.Tarea tar, e.Tramite.Proceso.Cuenta c')
                //Si la etapa no se encuentra asignada
                ->where('e.usuario_id IS NULL')
                //Si el usuario tiene permisos de acceso
                //->andWhere('(tar.acceso_modo="grupos_usuarios" AND g.id IN (SELECT gru.id FROM GrupoUsuarios gru, gru.Usuarios usr WHERE usr.id = ?)) OR (tar.acceso_modo = "registrados" AND 1 = ?) OR (tar.acceso_modo = "claveunica" AND 1 = ?) OR (tar.acceso_modo="publico")',array($usuario->id,$usuario->registrado,$usuario->open_id))
                ->orderBy('e.updated_at desc');

        if ($cuenta!='localhost') {
            $query->andWhere('c.nombre = ?', $cuenta->nombre);
        }

        $tareas=$query->execute();

        //Chequeamos los permisos de acceso
        foreach ($tareas as $key=>$t) {
            if (!$t->canUsuarioAsignarsela($usuario_id)) {
                unset($tareas[$key]);
            }
        }

        return $tareas;
    }




    public function cantidadSinAsignar($usuario_id, $cuenta = 'localhost')
    {
        set_time_limit(1600);
        ini_set('memory_limit', '-1');

        $opciones_busqueda = '';

        $conn = Doctrine_Manager::connection();

        $stmt= $conn->prepare('select COUNT(distinct id) from (
            select e.id as id, e.updated_at
            from etapa e
            join tarea t on t.id=e.tarea_id
            join proceso p on p.id=t.proceso_id
            join cuenta c on c.id = p.cuenta_id
            join usuario u on u.id = '.$usuario_id.'
            join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
            where e.usuario_id is NULL
            and t.acceso_modo="grupos_usuarios"
            and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios) '. $opciones_busqueda. '
              union all (

             select e.id as id, e.updated_at
            from etapa e
            join tarea_grupos_view t on t.id=e.tarea_id
            join proceso p on p.id=t.proceso_id
            join cuenta c on c.id = p.cuenta_id
            join usuario u on u.id = '.$usuario_id.'
            join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
            join etapa e2 on e.tramite_id = e2.tramite_id and e2.id < e.id and e2.pendiente = 0
            where e.usuario_id is NULL  '. $opciones_busqueda. '
		           and gu.grupo_usuarios_id =  (select replace(valor, \'"\',\'\') from dato_seguimiento d where d.nombre = replace(t.grupos_usuarios,\'@@\',\'\')
                   and  d.etapa_id =  e2.id)
              )
            union (
            select e.id as id, e.updated_at
            from etapa e
            join tarea t on t.id=e.tarea_id
            join proceso p on p.id=t.proceso_id
            join cuenta c on c.id = p.cuenta_id
            join usuario u on u.id = '.$usuario_id.'
            where e.usuario_id is NULL
            and (
               (t.acceso_modo="publico")
               or (t.acceso_modo="registrados" and u.registrado=1)
               or (t.acceso_modo = "claveunica" AND u.open_id=1)
               )

            ) '. $opciones_busqueda. '
            ) as a');

        $stmt->execute();

        $datos = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        $cantidad_tareas= $datos[0];

        return $cantidad_tareas;
  }

  public function findSinAsignarConPaginacion($usuario_id,
    $cuenta = 'localhost',
    $orderby,
    $direction,
    $per_page,
    $offset)
    {
        set_time_limit(1600);
        ini_set('memory_limit', '-1');

        $opciones_busqueda = '';
        $limit = $per_page;
        $conn = Doctrine_Manager::connection();

        if(!isset($offset) || !$offset){
          $offset = 0;
        }

        $stmt= $conn->prepare('select distinct id from (
            select e.id as id, e.updated_at
            from etapa e
            join tarea t on t.id=e.tarea_id
            join proceso p on p.id=t.proceso_id
            join cuenta c on c.id = p.cuenta_id
            join usuario u on u.id = '.$usuario_id.'
            join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
            where e.usuario_id is NULL
            and t.acceso_modo="grupos_usuarios"
            and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios) '. $opciones_busqueda. '
              union all (

             select e.id as id, e.updated_at
            from etapa e
            join tarea_grupos_view t on t.id=e.tarea_id
            join proceso p on p.id=t.proceso_id
            join cuenta c on c.id = p.cuenta_id
            join usuario u on u.id = '.$usuario_id.'
            join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
            join etapa e2 on e.tramite_id = e2.tramite_id and e2.id < e.id and e2.pendiente = 0
            where e.usuario_id is NULL  '. $opciones_busqueda. '
		           and gu.grupo_usuarios_id =  (select replace(valor, \'"\',\'\') from dato_seguimiento d where d.nombre = replace(t.grupos_usuarios,\'@@\',\'\')
                   and  d.etapa_id =  e2.id)
              )
            union (
            select e.id as id, e.updated_at
            from etapa e
            join tarea t on t.id=e.tarea_id
            join proceso p on p.id=t.proceso_id
            join cuenta c on c.id = p.cuenta_id
            join usuario u on u.id = '.$usuario_id.'
            where e.usuario_id is NULL
            and (
               (t.acceso_modo="publico")
               or (t.acceso_modo="registrados" and u.registrado=1)
               or (t.acceso_modo = "claveunica" AND u.open_id=1)
               )

            ) '. $opciones_busqueda. '
            ) as a order by a.updated_at desc limit '. $offset .','. $limit);

        $stmt->execute();

        $datos = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $tareas = [];

        if ($datos and count($datos) > 0){
        $query=Doctrine_Query::create()
        ->from('Etapa e')
        ->whereIn('e.id', $datos)
        ->orderBy('e.updated_at desc');
        $tareas=$query->execute();
      }

      return $tareas;
  }

    public function findSinAsignarFiltro($usuario_id,
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
    $count)
    {
        set_time_limit(1600);
        ini_set('memory_limit', '-1');

        $opciones_busqueda = '';
        $opciones_busqueda_1 = '';
        $limit = $per_page;
        $conn = Doctrine_Manager::connection();

        if(!isset($offset) || !$offset){
          $offset = 0;
        }

        $opciones_busqueda .= '';

        $params = array();

        if ($cuenta!='localhost') {
            $opciones_busqueda .= ' and c.nombre = :cuenta ';
        }

        if ($busqueda_id_tramite) {
            $opciones_busqueda .= ' and e.tramite_id = :busqueda_id_tramite ';
        }
        if ($busqueda_etapa) {
            $opciones_busqueda .= ' and t.nombre LIKE :busqueda_etapa';
        }
        if ($busqueda_nombre) {
              $opciones_busqueda .= ' and p.nombre LIKE :busqueda_nombre';
        }

        if ($busqueda_modificacion_desde) {
          $opciones_busqueda .= ' and e.updated_at >= :busqueda_modificacion_desde ';
        }

        if ($busqueda_modificacion_hasta) {
          $opciones_busqueda .= ' and e.updated_at <=  :busqueda_modificacion_hasta ';
        }

        if ($busqueda_documento) {
             $busqueda_documento_encode=  json_encode($busqueda_documento);
        }

        $opciones_busqueda_1 = $opciones_busqueda;

        if ($busqueda_grupo) {
              $opciones_busqueda .= ' and t.acceso_modo = :acceso_modo and (t.grupos_usuarios REGEXP  :busqueda_grupo_1
              OR
              t.grupos_usuarios REGEXP :busqueda_grupo_2
              OR
              t.grupos_usuarios REGEXP :busqueda_grupo_3
              OR
              t.grupos_usuarios REGEXP :busqueda_grupo_4) ';
        }

        if($busqueda_documento){

              if ($count){
                $query = 'select count(distinct id) from (';
              }else{
                $query = 'select distinct id from (';
              }
              $query = $query . ' select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = '.$usuario_id.'
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios) ' . $opciones_busqueda. '
              and (e.tramite_id  IN (
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
                                   trim(uu.usuario) like  \'%'. $busqueda_documento.'\' and not exists (select 1 from etapa eee where eee.tramite_id = ee.tramite_id and eee.id < ee.id) ))


              union all (

                                  select e.id as id, e.updated_at
                                 from etapa e
                                 join tarea_grupos_view t on t.id=e.tarea_id
                                 join proceso p on p.id=t.proceso_id
                                 join cuenta c on c.id = p.cuenta_id
                                 join usuario u on u.id = '.$usuario_id.'
                                 join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                                 join etapa e2 on e.tramite_id = e2.tramite_id and e2.id < e.id and e2.pendiente = 0
                                 where e.usuario_id is NULL  '. $opciones_busqueda_1. '
                                    and gu.grupo_usuarios_id =  (select replace(valor, \'"\',\'\') from dato_seguimiento d where d.nombre = replace(t.grupos_usuarios,\'@@\',\'\')
                                        and  d.etapa_id =  e2.id ';

                                        if ($busqueda_grupo)  {
                                             $query = $query . ' and replace(valor, \'"\',\'\')  = :busqueda_grupo ';

                                        }

                                   $query = $query  .' )   and (e.tramite_id  IN (
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
                                                          trim(uu.usuario) like  \'%'. $busqueda_documento.'\' and not exists (select 1 from etapa eee where eee.tramite_id = ee.tramite_id and eee.id < ee.id) ))
                       )

              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = '.$usuario_id.'
              where e.usuario_id is NULL
              and (e.tramite_id  IN (
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
                                   trim(uu.usuario) like  \'%'. $busqueda_documento.'\' and not exists (select 1 from etapa eee where eee.tramite_id = ee.tramite_id and eee.id < ee.id) ))
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
               ' . $opciones_busqueda. '
              )) as a ';

               if (!$count){
                   $query.= ' order by a.updated_at desc limit '. $offset .','. $limit;
               }

              $stmt= $conn->prepare($query);
          }
          else{
            if ($count){
              $query = 'select count(distinct id) from (';
            }else{
              $query = 'select distinct id from (';
            }
            $query = $query . '
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = '.$usuario_id.'
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios) '. $opciones_busqueda. '
                  union all (

                 select e.id as id, e.updated_at
                from etapa e
                join tarea_grupos_view t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = '.$usuario_id.'
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                join etapa e2 on e.tramite_id = e2.tramite_id and e2.id < e.id and e2.pendiente = 0
                where e.usuario_id is NULL  '. $opciones_busqueda_1. '
    		           and gu.grupo_usuarios_id =  (select replace(valor, \'"\',\'\') from dato_seguimiento d where d.nombre = replace(t.grupos_usuarios,\'@@\',\'\')
                       and  d.etapa_id =  e2.id ';

                       if ($busqueda_grupo)  {
                            $query = $query . ' and replace(valor, \'"\',\'\')  = :busqueda_grupo ';

                       }

                  $query = $query  .' )
                  )
                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = '.$usuario_id.'
                where e.usuario_id is NULL
                and (
                   (t.acceso_modo="publico")
                   or (t.acceso_modo="registrados" and u.registrado=1)
                   or (t.acceso_modo = "claveunica" AND u.open_id=1)
                   )

                 '. $opciones_busqueda. ')
                ) as a ';

                 if (!$count){
                     $query.= ' order by a.updated_at desc limit '. $offset .','. $limit;
                 }
                 $stmt= $conn->prepare($query);
          }

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
          $stmt->bindParam(":busqueda_grupo", $busqueda_grupo, PDO::PARAM_STR);
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
          ->from('Etapa e')
          ->whereIn('e.id', $datos)
          ->orderBy('e.updated_at desc');
          $tareas=$query->execute();
        }
        return $tareas;

      }


  }

    //busca las etapas donde esta pendiente una accion de $usuario_id
    public function findPendientes($usuario_id, $cuenta='localhost', $orderby='updated_at', $direction='desc')
    {
        $query=Doctrine_Query::create()
                ->from('Etapa e, e.Tarea tar, e.Usuario u, e.Tramite t, t.Etapas hermanas, t.Proceso p, p.Cuenta c')
                ->select('e.*,COUNT(hermanas.id) as netapas, p.nombre as proceso_nombre, tar.nombre as tarea_nombre')
                ->groupBy('e.id')
                //Si la etapa se encuentra pendiente y asignada al usuario
                ->where('e.pendiente = 1 and u.id = ?', $usuario_id)
                //Si la tarea se encuentra activa
                ->andWhere('1!=(tar.activacion="no" OR ( tar.activacion="entre_fechas" AND ((tar.activacion_inicio IS NOT NULL AND tar.activacion_inicio>NOW()) OR (tar.activacion_fin IS NOT NULL AND NOW()>tar.activacion_fin) )))')
                ->orderBy($orderby.' '.$direction);

        if ($cuenta!='localhost') {
            $query->andWhere('c.nombre = ?', $cuenta->nombre);
        }

        return $query->execute();
    }

    public function findPendientesConPaginacion($usuario_id, $cuenta='localhost', $orderby='updated_at', $direction='desc', $per_page ,$offset)
    {
        $query=Doctrine_Query::create()
                ->from('Etapa e, e.Tarea tar, e.Usuario u, e.Tramite t, t.Proceso p, p.Cuenta c')
                ->select('e.*')//,COUNT(hermanas.id) as netapas, p.nombre as proceso_nombre, tar.nombre as tarea_nombre')
                //->groupBy('e.id')
                //Si la etapa se encuentra pendiente y asignada al usuario
                ->where('e.pendiente = 1 and u.id = ?', $usuario_id)
                //Si la tarea se encuentra activa
                ->andWhere('1!=(tar.activacion="no" OR ( tar.activacion="entre_fechas" AND ((tar.activacion_inicio IS NOT NULL AND tar.activacion_inicio>NOW()) OR (tar.activacion_fin IS NOT NULL AND NOW()>tar.activacion_fin) )))')
                ->orderBy($orderby.' '.$direction)
                ->limit($per_page)
                ->offset($offset);

        if ($cuenta!='localhost') {
            $query->andWhere('c.nombre = ?', $cuenta->nombre);
        }

        $resultado = new stdClass();
        $resultado->etapas = $query->execute();
        $resultado->cantidad = $query->count();

        return $resultado;
    }

    public function cantidadPendientes($usuario_id, $cuenta='localhost')
    {
        $query=Doctrine_Query::create()
                ->select('COUNT(e.id)')
                ->from('Etapa e, e.Tarea tar, e.Usuario u, e.Tramite t, t.Proceso p, p.Cuenta c')
                //->select('e.*')//,COUNT(hermanas.id) as netapas, p.nombre as proceso_nombre, tar.nombre as tarea_nombre')
                //->groupBy('e.id')
                //Si la etapa se encuentra pendiente y asignada al usuario
                ->where('e.pendiente = 1 and u.id = ?', $usuario_id)
                //Si la tarea se encuentra activa
                ->andWhere('1!=(tar.activacion="no" OR ( tar.activacion="entre_fechas" AND ((tar.activacion_inicio IS NOT NULL AND tar.activacion_inicio>NOW()) OR (tar.activacion_fin IS NOT NULL AND NOW()>tar.activacion_fin) )))');

        if ($cuenta!='localhost') {
            $query->andWhere('c.nombre = ?', $cuenta->nombre);
        }

        return $query->count();
    }

    public function findPendientesFiltro($usuario_id,
    $cuenta = 'localhost',
    $orderby='updated_at',
    $direction='desc',
    $busqueda_id_tramite,
    $busqueda_etapa,
    $busqueda_grupo,
    $busqueda_nombre,
    $busqueda_documento,
    $busqueda_modificacion_desde,
    $busqueda_modificacion_hasta,
    $per_page,
    $offset)
    {
        if ($busqueda_documento) {


            $query=Doctrine_Query::create()
            ->select('e.id')
            ->from('Etapa e')
            ->leftJoin('e.Tramite t')
            ->leftJoin('t.Proceso p')
            ->leftJoin('p.Cuenta c')
            ->leftJoin('e.Usuario u')
            ->leftJoin('e.Tarea tar')
           //Si tiene un dato de seguimiento con el documento
           ->where('(  t.id  IN (
                          SELECT t.id
                          FROM DatoSeguimiento d1, DatoSeguimiento d2
                          WHERE
                           d1.nombre = CONCAT("documento_tramite_inicial__e", t.id)
                           AND d1.etapa_id = d2.etapa_id
                           AND d2.nombre = replace(d1.valor,\'"\', \'\')
                           AND d2.valor = "\"'. $busqueda_documento.'\"")
                     or t.id IN (
                         SELECT ee.tramite_id
                         FROM Etapa ee, ee.Usuario uu
                         where ee.tramite_id = e.tramite_id and
                         trim(uu.usuario) like "%'. $busqueda_documento.'"
                            and ee.id
                                   IN (SELECT min(eee.id) FROM etapa eee
                                     where eee.tramite_id = ee.tramite_id
                                  )
                         )
                  )'
                )
          //Si la etapa se encuentra pendiente y asignada al usuario
          ->andWhere('e.pendiente = 1 and u.id = ?', $usuario_id)
          //Si la tarea se encuentra activa
          ->andWhere('1!=(tar.activacion="no" OR ( tar.activacion="entre_fechas" AND ((tar.activacion_inicio IS NOT NULL AND tar.activacion_inicio>NOW()) OR (tar.activacion_fin IS NOT NULL AND NOW()>tar.activacion_fin) )))')
          ->orderBy($orderby.' '.$direction)
          ->limit($per_page)
          ->offset($offset);
        } else {
            $query=Doctrine_Query::create()

              ->select('e.*')//,COUNT(hermanas.id) as netapas, p.nombre as proceso_nombre, tar.nombre as tarea_nombre')
              ->from('Etapa e, e.Tarea tar, e.Usuario u, e.Tramite t, t.Proceso p, p.Cuenta c')
              //->groupBy('e.id')
              //Si la etapa se encuentra pendiente y asignada al usuario
              ->where('e.pendiente = 1 and u.id = ?', $usuario_id)
              //Si la tarea se encuentra activa
              ->andWhere('1!=(tar.activacion="no" OR ( tar.activacion="entre_fechas" AND ((tar.activacion_inicio IS NOT NULL AND tar.activacion_inicio>NOW()) OR (tar.activacion_fin IS NOT NULL AND NOW()>tar.activacion_fin) )))')
              ->orderBy($orderby.' '.$direction)
              ->limit($per_page)
              ->offset($offset);
        }

        if ($cuenta!='localhost') {
            $query->andWhere('c.nombre = ?', $cuenta->nombre);
        }
        if ($busqueda_id_tramite) {
            $query->andWhere('t.id = ?', $busqueda_id_tramite);
        }
        if ($busqueda_etapa) {
            $query->andWhere('e.Tarea.nombre LIKE ?', '%'.$busqueda_etapa.'%');
        }
        if ($busqueda_grupo) {
            $query->andWhere('e.Tarea.acceso_modo  = ? and (e.Tarea.grupos_usuarios REGEXP ? OR e.Tarea.grupos_usuarios REGEXP ? OR e.Tarea.grupos_usuarios REGEXP ? OR e.Tarea.grupos_usuarios REGEXP ?)', array('grupos_usuarios','^' . $busqueda_grupo . ',', ',' . $busqueda_grupo . ',', '^' . $busqueda_grupo . '$', ',' . $busqueda_grupo . '$'));
        }
        if ($busqueda_nombre) {
            $query->andWhere('t.Proceso.nombre LIKE ?', '%'.$busqueda_nombre.'%');
        }
        if ($busqueda_modificacion_desde) {
            $query->andWhere('e.updated_at >= ?', date("Y-m-d H:i:s", strtotime($busqueda_modificacion_desde)));
        }
        if ($busqueda_modificacion_hasta) {
            $busqueda_modificacion_hasta = $busqueda_modificacion_hasta . ' 23:59:59';
            $query->andWhere('e.updated_at <= ?', date("Y-m-d H:i:s", strtotime($busqueda_modificacion_hasta)));
        }
        if ($busqueda_documento) {
            //$doc_search = " (d.nombre = CONCAT('documento_tramite_inicial__e', t.id) and d1.nombre = replace(d.valor,'\"', '') and d1.valor = '\"".$busqueda_documento."\"' )"
            //$query->andWhere("d.nombre = CONCAT('documento_tramite_inicial__e', t.id)");
            //$query->andWhere("d1.nombre = replace(d.valor,'\"', '')");
            //$query->andWhere("d1.valor = '\"".$busqueda_documento."\"'");
        }


        //log_message('error',$query->getSQLQuery());

        $resultado = new stdClass();
        $resultado->etapas = $query->execute();
        $resultado->cantidad = $query->count();

        return $resultado;
    }
}
