<?php

class Migration_18 extends Doctrine_Migration_Base {

    public function up() {
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();

        //aud_bloque
        $q->execute("CREATE TABLE IF NOT EXISTS aud_bloque AS SELECT id, id AS id_aud, nombre, 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM bloque;");

        //aud_usuario
        $q->execute("CREATE TABLE IF NOT EXISTS aud_usuario AS SELECT u.id,u.id AS id_aud, u.nombres, u.email, u.cuenta_id, u.acceso_reportes, CONCAT(u.apellido_paterno,' ',u.apellido_materno) AS apellidos, u.usuario, u.vacaciones,(SELECT GROUP_CONCAT(g.nombre) FROM grupo_usuarios_has_usuario gu JOIN grupo_usuarios g ON g.id = gu.grupo_usuarios_id WHERE gu.usuario_id = u.id) AS grupos_usuario, 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM usuario u WHERE u.registrado = 1 AND u.email IS NOT NULL AND u.cuenta_id IS NOT NULL;");

        //aud_accion
        $q->execute("CREATE TABLE IF NOT EXISTS aud_accion AS SELECT a.id, a.id AS id_aud, a.nombre, a.proceso_id, a.exponer_variable, a.extra, a.tipo, 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM accion a;");

        //aud_grupo_usuarios
        $q->execute("CREATE TABLE IF NOT EXISTS aud_grupo_usuarios AS SELECT gu.id, gu.id AS id_aud, gu.nombre, gu.cuenta_id, (SELECT GROUP_CONCAT(u.usuario) FROM grupo_usuarios_has_usuario gu JOIN usuario u ON u.id = gu.usuario_id WHERE gu.grupo_usuarios_id = gu.id) AS usuarios_grupo, 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM grupo_usuarios gu;");

        //aud_usuario_backend
        $q->execute("CREATE TABLE IF NOT EXISTS aud_usuario_backend AS SELECT ub.id, ub.id as id_aud, ub.nombre, ub.apellidos, ub.cuenta_id, ub.email,ub.rol, ub.seg_alc_auditor, ub.seg_alc_control_total, ub.seg_alc_grupos_usuarios, ub.seg_reasginar, ub.seg_reasginar_usu, ub.usuario, 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM usuario_backend ub;");

        //aud_campo
        $q->execute("CREATE TABLE IF NOT EXISTS aud_campo AS SELECT c.id,c.id as id_aud, c.ayuda,c.ayuda_ampliada,c.datos,c.dependiente_campo,c.dependiente_relacion,c.dependiente_tipo,c.dependiente_valor,c.documento_id,c.documento_tramite,c.email_tramite,c.etiqueta,c.exponer_campo,c.extra,c.fieldset,c.firma_electronica,c.formulario_id, c.nombre,c.pago_online, c.posicion,c.readonly,c.requiere_accion,c.requiere_accion_boton,c.requiere_accion_id,c.requiere_accion_var_error,c.requiere_agendar,c.tipo,c.validacion,c.valor_default,'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM campo c;");

        //aud_conexion
        $q->execute("CREATE TABLE IF NOT EXISTS aud_conexion AS SELECT cx.id, cx.id AS id_aud,cx.estado_fin_trazabilidad,cx.regla,cx.tarea_id_destino,cx.tarea_id_origen,cx.tipo, 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM conexion cx;");

        //aud_documento
        $q->execute("CREATE TABLE IF NOT EXISTS aud_documento AS SELECT doc.id, doc.id AS id_aud, doc.contenido,doc.firmador_cargo,doc.firmador_imagen,doc.firmador_nombre,doc.firmador_servicio,doc.hsm_configuracion_id,doc.logo,doc.nombre,doc.proceso_id,doc.servicio,doc.servicio_url,doc.subtitulo,doc.tamano,doc.timbre,doc.tipo,doc.titulo,doc.validez,doc.validez_habiles, 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM documento doc;");

        //aud_proceso
        $q->execute("CREATE TABLE IF NOT EXISTS aud_proceso AS SELECT p.id, p.id AS id_aud, p.activo,p.codigo_tramite_ws_grep,p.cuenta_id,p.estado,p.height,p.instanciar_api,p.nombre,p.root,p.version,p.width,'' AS traza, 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM proceso p;");

        //aud_reporte
        $q->execute("CREATE TABLE IF NOT EXISTS aud_reporte AS SELECT r.id, r.id AS id_aud, r.nombre,r.campos,r.grupos_usuarios_permiso,r.proceso_id,r.tipo,r.usuarios_permiso, 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM reporte r;");

        //aud_formulario
        $q->execute("CREATE TABLE IF NOT EXISTS aud_formulario AS SELECT f.id,f.id AS id_aud, f.nombre,f.bloque_id,f.contenedor,f.leyenda,f.proceso_id, 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM formulario f;");

        //aud_pasarela_pago
        $q->execute("CREATE TABLE IF NOT EXISTS aud_pasarela_pago AS SELECT pp.id,pp.id AS id_aud, pp.nombre,pp.activo,pp.cuenta_id,pp.metodo, 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM pasarela_pago pp;");

        //aud_validacion
        $q->execute("CREATE TABLE IF NOT EXISTS aud_validacion AS SELECT v.id,v.id AS id_aud, v.nombre,v.contenido,v.filename,v.proceso_id, 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM validacion v;");

        //aud_ws_operacion_respuesta
        $q->execute("CREATE TABLE IF NOT EXISTS aud_ws_operacion_respuesta AS SELECT wr.id, wr.id AS id_aud, wr.operacion_id,wr.respuesta_id,wr.xslt, 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM ws_operacion_respuesta wr;");

        //aud_ejecutar_validacion
        $q->execute("CREATE TABLE IF NOT EXISTS aud_ejecutar_validacion AS SELECT ev.id,ev.id AS id_aud, ev.instante,ev.paso_id,ev.regla,ev.tarea_id,ev.validacion_id, 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM ejecutar_validacion ev;");

        //aud_evento
        $q->execute("CREATE TABLE IF NOT EXISTS aud_evento AS SELECT e.id,e.id AS id_aud, e.accion_id,e.descripcion_error_soap,e.descripcion_traza,e.etiqueta_traza,e.instanciar_api,e.instante,e.paso_id,e.regla,e.tarea_id,e.tipo_registro_traza,e.traza,e.variable_error_soap,e.visible_traza, 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM evento e;");

        //aud_evento_pago
        $q->execute("CREATE TABLE IF NOT EXISTS aud_evento_pago AS SELECT ep.id,ep.id AS id_aud, ep.accion_ejecutar_id,ep.accion_id,ep.descripcion_error_soap,ep.descripcion_traza,ep.etiqueta_traza,ep.instante,ep.regla,ep.tipo_registro_traza,ep.traza,ep.variable_error_soap,ep.visible_traza, 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM evento_pago ep;");

        //aud_pasarela_pago_antel
        $q->execute("CREATE TABLE IF NOT EXISTS aud_pasarela_pago_antel AS SELECT pa.id,pa.id AS id_aud,pa.pasarela_pago_id, pa.cantidad,pa.referencia_pago,pa.certificado,pa.clave_certificado,pa.codigos_desglose,pa.cuerpo_email_inicio,pa.cuerpo_email_ok,pa.cuerpo_email_pendiente,pa.cuerpo_email_timeout,pa.descripcion_error_traza,pa.descripcion_iniciado_traza,pa.descripcion_pendiente_traza,pa.descripcion_reachazado_traza,pa.descripcion_realizado_traza,pa.descripcion_token_solicita_traza,pa.id_organismo,pa.id_tramite,pa.montos_desglose,pa.operacion,pa.tasa_1,pa.tasa_2,pa.tasa_3,pa.tema_email_inicio,pa.tema_email_ok,pa.tema_email_pendiente,pa.tema_email_timeout,pa.vencimiento,'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM pasarela_pago_antel pa;");

        //aud_pasarela_pago_generica
        $q->execute("CREATE TABLE IF NOT EXISTS aud_pasarela_pago_generica AS SELECT pg.id,pg.id AS id_aud,pg.codigo_operacion_soap,pg.codigo_operacion_soap_consulta,pg.cuerpo_email_inicio,pg.descripciones_estados_traza,pg.mensaje_reimpresion_ticket,pg.metodo_http,pg.pasarela_pago_id,pg.tema_email_inicio,pg.ticket_metodo,pg.ticket_variables,pg.url_redireccion,pg.url_ticket,pg.variable_evaluar,pg.variable_idestado,pg.variable_idsol,pg.variable_redireccion,pg.variables_post,  'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM pasarela_pago_generica pg;");

        //aud_tarea
        $q->execute("CREATE TABLE IF NOT EXISTS aud_tarea AS SELECT t.id,t.id AS id_aud,t.acceso_modo,t.activacion,t.activacion_fin,t.activacion_inicio,t.almacenar_usuario,t.almacenar_usuario_variable,t.asignacion,t.asignacion_notificar,t.asignacion_notificar_mensaje,t.asignacion_usuario,t.automatica,t.escalado_automatico,t.etiqueta_traza,t.grupos_usuarios,t.identificador,t.id_x_tarea,t.inicial,t.nivel_confianza,t.nombre,t.notificar_vencida,t.paso_confirmacion,t.paso_final_completado,t.paso_final_pendiente,t.paso_final_sincontinuacion,t.paso_final_standby,t.posx,t.posy,t.previsualizacion,t.proceso_id,t.texto_boton_generar_pdf,t.texto_boton_paso_final,t.trazabilidad,t.trazabilidad_cabezal,t.trazabilidad_estado,t.trazabilidad_id_oficina,t.trazabilidad_nombre_oficina,t.vencimiento,t.vencimiento_a_partir_de_variable,t.vencimiento_habiles,t.vencimiento_notificar,t.vencimiento_notificar_dias,t.vencimiento_notificar_email,t.vencimiento_unidad,t.vencimiento_valor,t.visible_traza,0 AS 'final', 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM tarea t;");

        //aud_ws_catalogo
        $q->execute("CREATE TABLE IF NOT EXISTS aud_ws_catalogo AS SELECT wc.id,wc.id AS id_aud,wc.activo,wc.autenticacion_basica_cert,wc.autenticacion_basica_cert_pass,wc.autenticacion_basica_pass,wc.autenticacion_basica_user,wc.autenticacion_mutua_client,wc.autenticacion_mutua_client_key,wc.autenticacion_mutua_client_pass,wc.autenticacion_mutua_server,wc.autenticacion_mutua_user,wc.conexion_timeout,wc.endpoint_location,wc.nombre,wc.requiere_autenticacion,wc.requiere_autenticacion_tipo,wc.respuesta_timeout,wc.rol,wc.tipo,wc.url_fisica,wc.url_logica,wc.wsdl, 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM ws_catalogo wc;");

        //aud_ws_operacion
        $q->execute("CREATE TABLE IF NOT EXISTS aud_ws_operacion AS SELECT wo.id,wo.id AS id_aud,wo.ayuda,wo.catalogo_id,wo.codigo,wo.nombre,wo.operacion,wo.respuestas,wo.soap, 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM ws_operacion wo;");

        //aud_paso
        $q->execute("CREATE TABLE IF NOT EXISTS aud_paso AS SELECT p.id, p.id AS id_aud, p.enviar_traza,p.etiqueta_traza,p.formulario_id,p.generar_pdf,p.nombre,p.modo,p.orden,p.regla,p.tarea_id,p.visible_traza, 'MIGRACION 2.0' AS usuario_aud, 'insert' AS tipo_operacion_aud, NOW() AS fecha_aud FROM paso p;");
    }

    public function down() {
        
    }

}
