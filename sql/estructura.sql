SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------
--
-- Estructuras de las tablas SIMPLE v 1.5-r3
--

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `accion`
--

CREATE TABLE IF NOT EXISTS `accion` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(128) NOT NULL,
    `tipo` VARCHAR(60) NOT NULL,
    `extra` TEXT,
    `proceso_id` INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_trigger_proceso1_idx` (`proceso_id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `bloque`
--

CREATE TABLE IF NOT EXISTS `bloque` (
  `id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `campo`
--

CREATE TABLE IF NOT EXISTS `campo` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(100) NOT NULL,
    `readonly` TINYINT(1) NOT NULL DEFAULT '0',
    `valor_default` TEXT NOT NULL,
    `posicion` INT(10) UNSIGNED NOT NULL,
    `tipo` VARCHAR(32) NOT NULL,
    `formulario_id` INT(10) UNSIGNED NOT NULL,
    `etiqueta` TEXT NOT NULL,
    `validacion` VARCHAR(128) NOT NULL,
    `ayuda` LONGTEXT DEFAULT '',
    `ayuda_ampliada` TEXT NOT NULL,
    `dependiente_tipo` VARCHAR(200) DEFAULT NULL,
    `dependiente_campo` VARCHAR(64) DEFAULT NULL,
    `dependiente_valor` VARCHAR(256) DEFAULT NULL,
    `datos` TEXT,
    `documento_id` INT(10) UNSIGNED DEFAULT NULL,
    `extra` TEXT,
    `dependiente_relacion` ENUM('==', '!=') DEFAULT '==',
    `fieldset` VARCHAR(400) NULL,
    `documento_tramite` TINYINT(1) DEFAULT NULL,
    `email_tramite` TINYINT(1) DEFAULT NULL,
    `pago_online` TINYINT(1) DEFAULT '1',
    `requiere_agendar` TINYINT(1) DEFAULT '1',
    `firma_electronica` TINYINT(1) DEFAULT '1',
    `requiere_accion` TINYINT(1) DEFAULT '0',
    `requiere_accion_id` TINYINT(1) DEFAULT NULL,
    `requiere_accion_boton` VARCHAR(200) DEFAULT NULL,
    `requiere_accion_var_error` VARCHAR(200) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_campo_formulario1` (`formulario_id`),
    KEY `fk_campo_documento1_idx` (`documento_id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `conexion`
--

CREATE TABLE IF NOT EXISTS `conexion` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `tarea_id_origen` INT(10) UNSIGNED NOT NULL,
    `tarea_id_destino` INT(10) UNSIGNED DEFAULT NULL,
    `tipo` ENUM('secuencial', 'evaluacion', 'paralelo', 'paralelo_evaluacion', 'union') NOT NULL,
    `regla` VARCHAR(256) NOT NULL,
    `estado_fin_trazabilidad` TINYINT(1) DEFAULT '2',
    PRIMARY KEY (`id`),
    UNIQUE KEY `tarea_origen_destino` (`tarea_id_origen` , `tarea_id_destino`),
    KEY `fk_ruta_tarea` (`tarea_id_origen`),
    KEY `fk_ruta_tarea1` (`tarea_id_destino`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `cuenta`
--

CREATE TABLE IF NOT EXISTS `cuenta` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(128) NOT NULL,
    `nombre_largo` VARCHAR(256) NOT NULL,
    `mensaje` TEXT NOT NULL,
    `logo` VARCHAR(128) DEFAULT NULL,
    `api_token` VARCHAR(32) NOT NULL,
    `codigo_analytics` TEXT DEFAULT '',
    `correo_remitente` VARCHAR(255) DEFAULT NULL,
    `envio_guid_automatico` TINYINT(1) DEFAULT '0',
    `asunto_email_guid` VARCHAR(255) DEFAULT '',
    `cuerpo_email_guid` TEXT DEFAULT '',
    PRIMARY KEY (`id`),
    UNIQUE KEY `nombre` (`nombre`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `dato_seguimiento`
--

CREATE TABLE IF NOT EXISTS `dato_seguimiento` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(128) NOT NULL,
    `valor` MEDIUMTEXT NOT NULL,
    `etapa_id` INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `nombre_etapa` (`nombre` , `etapa_id`),
    KEY `fk_dato_seguimiento_etapa1_idx` (`etapa_id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `documento`
--

CREATE TABLE IF NOT EXISTS `documento` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `tipo` ENUM('blanco', 'certificado') NOT NULL DEFAULT 'blanco',
    `nombre` VARCHAR(128) NOT NULL,
    `contenido` TEXT NOT NULL,
    `servicio` VARCHAR(128) NOT NULL,
    `servicio_url` VARCHAR(256) NOT NULL,
    `logo` VARCHAR(256) NOT NULL,
    `timbre` VARCHAR(256) NOT NULL,
    `firmador_nombre` VARCHAR(128) NOT NULL,
    `firmador_cargo` VARCHAR(128) NOT NULL,
    `firmador_servicio` VARCHAR(128) NOT NULL,
    `firmador_imagen` VARCHAR(256) NOT NULL,
    `validez` INT(10) UNSIGNED DEFAULT NULL,
    `hsm_configuracion_id` INT(10) UNSIGNED DEFAULT NULL,
    `proceso_id` INT(10) UNSIGNED NOT NULL,
    `subtitulo` VARCHAR(128) NOT NULL,
    `titulo` VARCHAR(128) NOT NULL,
    `validez_habiles` TINYINT(1) NOT NULL,
    `tamano` ENUM('letter', 'legal') DEFAULT 'letter',
    PRIMARY KEY (`id`),
    KEY `fk_documento_proceso1_idx` (`proceso_id`),
    KEY `fk_documento_hsm_configuracion1_idx` (`hsm_configuracion_id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `etapa`
--

CREATE TABLE IF NOT EXISTS `etapa` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tarea_id` int(10) unsigned NOT NULL,
  `usuario_id` int(10) unsigned DEFAULT NULL,
  `pendiente` tinyint(1) NOT NULL,
  `etapa_ancestro_split_id` int(10) unsigned DEFAULT NULL,
  `vencimiento_at` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `ended_at` datetime DEFAULT NULL,
  `tramite_id` int(10) unsigned NOT NULL,
  `usuario_original_id` int(10)  DEFAULT NULL,
  `usuario_original_historico` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_etapa_tarea1_idx` (`tarea_id`),
  KEY `fk_etapa_usuario1_idx` (`usuario_id`),
  KEY `fk_etapa_tramite1` (`tramite_id`),
  KEY `fk_etapa_etapa1_idx` (`etapa_ancestro_split_id`),
  KEY `index_etapa_pendiente` (`pendiente`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `etapa_historial_ejecuciones`
--

CREATE TABLE IF NOT EXISTS `etapa_historial_ejecuciones` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `etapa_id` INT(10) UNSIGNED NOT NULL,
    `secuencia` INT(10) UNSIGNED NOT NULL,
    `usuario_id` INT(10) UNSIGNED NOT NULL,
    `descripcion` VARCHAR(255) DEFAULT NULL,
    `fecha` VARCHAR(255) DEFAULT NULL,
    `nombre_paso` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_etapa` (`etapa_id`),
    KEY `fk_usuario` (`usuario_id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `evento`
--

CREATE TABLE IF NOT EXISTS `evento` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `regla` varchar(512) NOT NULL,
  `instante` enum('antes','despues') NOT NULL,
  `tarea_id` int(10) unsigned NOT NULL,
  `accion_id` int(10) unsigned NOT NULL,
  `paso_id` int(10) unsigned DEFAULT NULL,
  `instanciar_api` tinyint(1) DEFAULT '0',
  `traza` tinyint(1) DEFAULT '0',
  `tipo_registro_traza` int(2) DEFAULT '0',
  `descripcion_traza` varchar(512) DEFAULT NULL,
  `descripcion_error_soap` varchar(512) DEFAULT NULL,
  `variable_error_soap` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_evento_tarea1_idx` (`tarea_id`),
  KEY `fk_evento_accion1_idx` (`accion_id`),
  KEY `fk_evento_paso1_idx` (`paso_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `feriado`
--

CREATE TABLE IF NOT EXISTS `feriado` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `fecha` DATE NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `fecha_UNIQUE` (`fecha`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `file`
--

CREATE TABLE IF NOT EXISTS `file` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `filename` VARCHAR(255) NOT NULL,
    `tipo` ENUM('dato', 'documento', 'etapa_pdf', 'descarga', 'accion_archivo') NOT NULL,
    `llave` VARCHAR(12) NOT NULL,
    `llave_copia` VARCHAR(40) DEFAULT NULL,
    `llave_firma` VARCHAR(12) DEFAULT NULL,
    `validez` INT(10) UNSIGNED DEFAULT NULL,
    `tramite_id` INT(10) UNSIGNED NOT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `validez_habiles` TINYINT(1) NOT NULL,
    `firmado` TINYINT(1) NOT NULL,
    `etapa_id` INT(10) DEFAULT NULL,
    `file_origen` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `filename_tipo` (`filename` , `tipo`),
    KEY `fk_file_tramite1_idx` (`tramite_id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `formulario`
--

CREATE TABLE IF NOT EXISTS `formulario` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(128) NOT NULL,
    `proceso_id` INT(10) UNSIGNED NOT NULL,
    `bloque_id` BIGINT(20) DEFAULT NULL,
    `leyenda` VARCHAR(400) DEFAULT NULL,
    `contenedor` TINYINT(4) DEFAULT '1',
    PRIMARY KEY (`id`),
    KEY `fk_formulario_proceso1_idx` (`proceso_id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `grupo_usuarios`
--

CREATE TABLE IF NOT EXISTS `grupo_usuarios` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(128) NOT NULL,
    `cuenta_id` INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `grupo_usuarios_UNIQUE` (`cuenta_id` , `nombre`),
    KEY `fk_grupo_usuarios_cuenta1_idx` (`cuenta_id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `grupo_usuarios_has_usuario`
--

CREATE TABLE IF NOT EXISTS `grupo_usuarios_has_usuario` (
    `grupo_usuarios_id` INT(10) UNSIGNED NOT NULL,
    `usuario_id` INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`grupo_usuarios_id` , `usuario_id`),
    KEY `fk_grupo_usuarios_has_usuario_usuario1_idx` (`usuario_id`),
    KEY `fk_grupo_usuarios_has_usuario_grupo_usuarios1_idx` (`grupo_usuarios_id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `hsm_configuracion`
--

CREATE TABLE IF NOT EXISTS `hsm_configuracion` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(128) NOT NULL,
    `cuenta_id` INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `nombre_UNIQUE` (`nombre`),
    KEY `fk_hsm_configuracion_cuenta1_idx` (`cuenta_id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `migration_version`
--

CREATE TABLE IF NOT EXISTS `migration_version` (
    `version` INT(11) DEFAULT NULL
)  ENGINE=INNODB DEFAULT CHARSET=LATIN1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `monitoreo`
--

CREATE TABLE IF NOT EXISTS `monitoreo` (
    `proceso_id` BIGINT(20) DEFAULT NULL,
    `url_web_service` TEXT,
    `fecha` VARCHAR(255) DEFAULT NULL,
    `tipo` VARCHAR(255) DEFAULT NULL,
    `seguridad` TINYINT(1) DEFAULT NULL,
    `rol` VARCHAR(255) DEFAULT NULL,
    `certificado` VARCHAR(255) DEFAULT NULL,
    `error_texto` VARCHAR(255) DEFAULT NULL,
    `error` TINYINT(1) DEFAULT NULL,
    `soap_peticion` LONGTEXT NOT NULL,
    `soap_respuesta` LONGTEXT NOT NULL,
    `catalogo_id` BIGINT(20) DEFAULT NULL,
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`id`)
)  ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `pago`
--

CREATE TABLE IF NOT EXISTS `pago` (
    `id_tramite` VARCHAR(40) NOT NULL,
    `id_solicitud` BIGINT(20) NOT NULL,
    `estado` VARCHAR(14) NOT NULL,
    `fecha_actualizacion` VARCHAR(16) NOT NULL,
    `pasarela` VARCHAR(14) NOT NULL,
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `id_tramite_interno` BIGINT(20) NOT NULL,
    `id_etapa` BIGINT(20) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `id_solicitud_idx` (`id_solicitud`)
)  ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `parametro`
--

CREATE TABLE IF NOT EXISTS `parametro` (
    `cuenta_id` BIGINT(20) NOT NULL,
    `clave` VARCHAR(100) NOT NULL,
    `valor` VARCHAR(200) NOT NULL,
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`id`)
)  ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `pasarela_pago`
--

CREATE TABLE IF NOT EXISTS `pasarela_pago` (
    `nombre` VARCHAR(64) NOT NULL,
    `metodo` VARCHAR(64) NOT NULL,
    `activo` TINYINT(4) NOT NULL DEFAULT '1',
    `cuenta_id` BIGINT(20) NOT NULL,
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`id`)
)  ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `pasarela_pago_antel`
--

CREATE TABLE IF NOT EXISTS `pasarela_pago_antel` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `pasarela_pago_id` BIGINT(20) NOT NULL,
    `id_tramite` BIGINT(20) NOT NULL,
    `cantidad` TINYINT(4) DEFAULT '1',
    `tasa_1` VARCHAR(10) NOT NULL,
    `tasa_2` VARCHAR(10) NOT NULL,
    `tasa_3` VARCHAR(10) NOT NULL,
    `operacion` VARCHAR(1) DEFAULT 'P',
    `vencimiento` VARCHAR(12) NOT NULL,
    `codigos_desglose` TEXT NOT NULL,
    `montos_desglose` TEXT NOT NULL,
    `clave_organismo` VARCHAR(60) NOT NULL,
    `clave_tramite` VARCHAR(128) DEFAULT NULL,
    `certificado` TEXT,
    `clave_certificado` TEXT,
    `pass_clave_certificado` TEXT,
    `id_organismo` VARCHAR(10) DEFAULT NULL,
    `cuerpo_email_inicio` VARCHAR(700) DEFAULT NULL,
    `cuerpo_email_ok` VARCHAR(700) DEFAULT NULL,
    `cuerpo_email_pendiente` VARCHAR(700) DEFAULT NULL,
    `cuerpo_email_timeout` VARCHAR(700) DEFAULT NULL,
    `tema_email_inicio` VARCHAR(300) DEFAULT NULL,
    `tema_email_ok` VARCHAR(300) DEFAULT NULL,
    `tema_email_pendiente` VARCHAR(300) DEFAULT NULL,
    `tema_email_timeout` VARCHAR(300) DEFAULT NULL,
    `descripcion_pendiente_traza` TEXT,
    `descripcion_iniciado_traza` TEXT,
    `descripcion_token_solicita_traza` TEXT,
    `descripcion_realizado_traza` TEXT,
    `descripcion_error_traza` TEXT,
    `descripcion_reachazado_traza` TEXT,
    PRIMARY KEY (`id`)
)  ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `pasarela_pago_generica`
--

CREATE TABLE IF NOT EXISTS `pasarela_pago_generica` (
    `pasarela_pago_id` BIGINT(20) NOT NULL,
    `codigo_operacion_soap` VARCHAR(64) NOT NULL,
    `url_redireccion` VARCHAR(200) NOT NULL,
    `url_ticket` VARCHAR(200) DEFAULT NULL,
    `metodo_http` VARCHAR(4) DEFAULT 'GET',
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `variables_post` VARCHAR(800) DEFAULT NULL,
    `variable_evaluar` VARCHAR(100) DEFAULT NULL,
    `variable_idsol` VARCHAR(100) DEFAULT NULL,
    `codigo_operacion_soap_consulta` VARCHAR(64) DEFAULT NULL,
    `mensaje_reimpresion_ticket` VARCHAR(400) DEFAULT NULL,
    `variable_idestado` VARCHAR(100) DEFAULT NULL,
    `tema_email_inicio` VARCHAR(300) DEFAULT NULL,
    `cuerpo_email_inicio` VARCHAR(700) DEFAULT NULL,
    `variable_redireccion` VARCHAR(100) DEFAULT NULL,
    `ticket_metodo` VARCHAR(4) DEFAULT NULL,
    `ticket_variables` VARCHAR(800) DEFAULT NULL,
    `descripciones_estados_traza` TEXT,
    PRIMARY KEY (`id`)
)  ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `monitoreo_notificaciones`
--

CREATE TABLE IF NOT EXISTS `monitoreo_notificaciones` (
    `email` VARCHAR(255) DEFAULT NULL,
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`id`)
)  ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `paso`
--

CREATE TABLE IF NOT EXISTS `paso` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `orden` INT(10) UNSIGNED NOT NULL,
    `modo` ENUM('edicion', 'visualizacion') NOT NULL,
    `regla` VARCHAR(512) NOT NULL,
    `formulario_id` INT(10) UNSIGNED NOT NULL,
    `tarea_id` INT(10) UNSIGNED NOT NULL,
    `nombre` VARCHAR(255) DEFAULT NULL,
    `generar_pdf` TINYINT(1) DEFAULT '0',
    `enviar_traza` TINYINT(1) DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `fk_paso_formulario1_idx` (`formulario_id`),
    KEY `fk_paso_tarea1_idx` (`tarea_id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `pdi`
--

CREATE TABLE IF NOT EXISTS `pdi` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `cuenta_id` BIGINT(20) NOT NULL,
    `sts` VARCHAR(200) NOT NULL,
    `policy` VARCHAR(200) NOT NULL,
    `certificado_organismo` TEXT NOT NULL,
    `clave_organismo` VARCHAR(200) NOT NULL,
    `certificado_ssl` TEXT NOT NULL,
    `clave_ssl` VARCHAR(200) NOT NULL,
    PRIMARY KEY (`id`)
)  ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `proceso`
--

CREATE TABLE IF NOT EXISTS `proceso` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(128) NOT NULL,
    `width` VARCHAR(8) NOT NULL DEFAULT '100%',
    `height` VARCHAR(8) NOT NULL DEFAULT '800px',
    `cuenta_id` INT(10) UNSIGNED NOT NULL,
    `codigo_tramite_ws_grep` INT(11) DEFAULT NULL,
    `instanciar_api` TINYINT(1) DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `fk_proceso_cuenta1_idx` (`cuenta_id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `proceso_trazabilidad`
--

CREATE TABLE IF NOT EXISTS `proceso_trazabilidad` (
    `proceso_id` BIGINT(20) NOT NULL,
    `organismo_id` VARCHAR(200) NOT NULL,
    `proceso_externo_id` VARCHAR(200) NOT NULL,
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `envio_guid_automatico` TINYINT(1) DEFAULT '1',
    `email_envio_guid` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
)  ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `reporte`
--

CREATE TABLE IF NOT EXISTS `reporte` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(128) NOT NULL,
    `campos` TEXT NOT NULL,
    `proceso_id` INT(10) UNSIGNED NOT NULL,
    `tipo` VARCHAR(20) DEFAULT NULL,
    `grupos_usuarios_permiso` TEXT DEFAULT NULL,
    `usuarios_permiso` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_reporte_proceso1_idx` (`proceso_id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `reporte_satisfaccion`
--

CREATE TABLE IF NOT EXISTS `reporte_satisfaccion` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `usuario_id` BIGINT(20) NOT NULL,
    `fecha` DATETIME NOT NULL,
    `reporte` TEXT NOT NULL,
    PRIMARY KEY (`id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `tarea`
--

CREATE TABLE IF NOT EXISTS `tarea` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `identificador` VARCHAR(32) NOT NULL,
    `inicial` TINYINT(1) NOT NULL DEFAULT '0',
    `nombre` VARCHAR(128) NOT NULL,
    `posx` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `posy` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `asignacion` ENUM('ciclica', 'manual', 'autoservicio', 'usuario') NOT NULL DEFAULT 'ciclica',
    `asignacion_usuario` VARCHAR(128) DEFAULT NULL,
    `asignacion_notificar` TINYINT(1) NOT NULL DEFAULT '0',
    `proceso_id` INT(10) UNSIGNED NOT NULL,
    `almacenar_usuario` TINYINT(1) NOT NULL DEFAULT '0',
    `almacenar_usuario_variable` VARCHAR(128) DEFAULT NULL,
    `acceso_modo` ENUM('grupos_usuarios', 'publico', 'registrados', 'claveunica') NOT NULL DEFAULT 'grupos_usuarios',
    `activacion` ENUM('si', 'entre_fechas', 'no') NOT NULL DEFAULT 'si',
    `activacion_inicio` DATE DEFAULT NULL,
    `activacion_fin` DATE DEFAULT NULL,
    `vencimiento` TINYINT(1) NOT NULL DEFAULT '0',
    `vencimiento_valor` INT(10) UNSIGNED NOT NULL DEFAULT '5',
    `vencimiento_unidad` ENUM('D', 'W', 'M') NOT NULL DEFAULT 'D',
    `vencimiento_habiles` TINYINT(1) NOT NULL DEFAULT '0',
    `vencimiento_notificar` TINYINT(1) NOT NULL DEFAULT '0',
    `vencimiento_notificar_email` VARCHAR(255) NOT NULL,
    `vencimiento_notificar_dias` INT(10) UNSIGNED NOT NULL DEFAULT '1',
    `grupos_usuarios` TEXT,
    `paso_confirmacion` TINYINT(1) NOT NULL DEFAULT '1',
    `previsualizacion` TEXT NOT NULL,
    `trazabilidad` TINYINT(1) NOT NULL DEFAULT '1',
    `asignacion_notificar_mensaje` LONGTEXT DEFAULT NULL,
    `trazabilidad_id_oficina` VARCHAR(64) DEFAULT NULL,
    `automatica` TINYINT(1) DEFAULT NULL,
    `nivel_confianza` VARCHAR(255) DEFAULT NULL,
    `trazabilidad_estado` INT(1) NOT NULL DEFAULT '2',
    `trazabilidad_cabezal` INT(1) DEFAULT '1',
    `paso_final_pendiente` VARCHAR(1000) DEFAULT 'Para confirmar y enviar el formulario a la siguiente etapa haga click en Finalizar.',
    `paso_final_standby` VARCHAR(1000) DEFAULT 'Luego de hacer click en Finalizar esta etapa quedara detenida momentaneamente hasta que se completen el resto de etapas pendientes.',
    `paso_final_completado` VARCHAR(1000) DEFAULT 'El formulario está completo y listo para enviarse, una vez enviado no podrá realizar modificaciones.',
    `paso_final_sincontinuacion` VARCHAR(1000) DEFAULT 'Este trámite no tiene una etapa donde continuar.',
    `texto_boton_paso_final` VARCHAR(1000) DEFAULT 'Finalizar',
    `texto_boton_generar_pdf` VARCHAR(255) DEFAULT 'Imprimir',
    PRIMARY KEY (`id`),
    UNIQUE KEY `identificador_proceso` (`identificador` , `proceso_id`),
    KEY `fk_tarea_proceso1` (`proceso_id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `tarea_has_grupo_usuarios`
--

CREATE TABLE IF NOT EXISTS `tarea_has_grupo_usuarios` (
    `tarea_id` INT(10) UNSIGNED NOT NULL,
    `grupo_usuarios_id` INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`tarea_id` , `grupo_usuarios_id`),
    KEY `fk_tarea_has_grupo_usuarios_grupo_usuarios1_idx` (`grupo_usuarios_id`),
    KEY `fk_tarea_has_grupo_usuarios_tarea1_idx` (`tarea_id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `tramite`
--

CREATE TABLE IF NOT EXISTS `tramite` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `proceso_id` INT(10) UNSIGNED NOT NULL,
    `pendiente` TINYINT(1) NOT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `ended_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_tramite_proceso1_idx` (`proceso_id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `trazabilidad`
--

CREATE TABLE IF NOT EXISTS `trazabilidad` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `id_etapa` BIGINT(20) NOT NULL,
    `id_tramite` BIGINT(20) NOT NULL,
    `num_paso` BIGINT(20) NOT NULL,
    `secuencia` BIGINT(20) NOT NULL,
    `estado` VARCHAR(1) NOT NULL,
    `num_paso_real` BIGINT(20) DEFAULT NULL,
    `id_tarea` BIGINT(20) DEFAULT NULL,
    `enviar_correo` TINYINT(1) DEFAULT '0',
    PRIMARY KEY (`id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `usuario` VARCHAR(128) NOT NULL,
    `password` VARCHAR(256) DEFAULT NULL,
    `rut` VARCHAR(16) DEFAULT NULL,
    `nombres` VARCHAR(128) DEFAULT NULL,
    `apellido_paterno` VARCHAR(128) DEFAULT NULL,
    `apellido_materno` VARCHAR(128) DEFAULT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `registrado` TINYINT(1) NOT NULL DEFAULT '1',
    `vacaciones` TINYINT(1) NOT NULL DEFAULT '0',
    `cuenta_id` INT(10) UNSIGNED DEFAULT NULL,
    `salt` VARCHAR(32) NOT NULL,
    `open_id` TINYINT(1) NOT NULL DEFAULT '0',
    `reset_token` VARCHAR(40) DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `acceso_reportes` TINYINT(1) DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `usuario_unique` (`usuario` , `open_id`),
    KEY `fk_usuario_cuenta1_idx` (`cuenta_id`),
    KEY `email_idx` (`email` , `open_id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `usuario_backend`
--

CREATE TABLE IF NOT EXISTS `usuario_backend` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(128) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `nombre` VARCHAR(128) NOT NULL,
    `apellidos` VARCHAR(128) NOT NULL,
    `rol` VARCHAR(200) DEFAULT NULL,
    `salt` VARCHAR(32) NOT NULL,
    `cuenta_id` INT(10) UNSIGNED NOT NULL,
    `reset_token` VARCHAR(40) DEFAULT NULL,
    `usuario` VARCHAR(128) DEFAULT NULL,
    `seg_alc_control_total` TINYINT(1) NOT NULL DEFAULT '1',
    `seg_alc_grupos_usuarios` VARCHAR(1000) DEFAULT NULL,
    `seg_reasginar` TINYINT(1) NOT NULL DEFAULT '1',
    `seg_reasginar_usu` TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `fk_usuario_backend_cuenta1_idx` (`cuenta_id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `usuario_manager`
--

CREATE TABLE IF NOT EXISTS `usuario_manager` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `usuario` VARCHAR(128) NOT NULL,
    `user` VARCHAR(128) DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `nombre` VARCHAR(128) NOT NULL,
    `apellidos` VARCHAR(128) NOT NULL,
    `salt` VARCHAR(32) NOT NULL,
    PRIMARY KEY (`id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `widget`
--

CREATE TABLE IF NOT EXISTS `widget` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `tipo` VARCHAR(32) NOT NULL,
    `nombre` VARCHAR(128) NOT NULL,
    `posicion` INT(10) UNSIGNED NOT NULL,
    `config` TEXT,
    `cuenta_id` INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_widget_cuenta1_idx` (`cuenta_id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `ws_catalogo`
--

CREATE TABLE IF NOT EXISTS `ws_catalogo` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(64) NOT NULL,
    `wsdl` TEXT NOT NULL,
    `endpoint_location` TEXT NOT NULL,
    `activo` TINYINT(4) DEFAULT '1',
    `conexion_timeout` MEDIUMINT(9) DEFAULT '30',
    `respuesta_timeout` MEDIUMINT(9) DEFAULT '30',
    `url_logica` TEXT NOT NULL,
    `url_fisica` TEXT NOT NULL,
    `rol` VARCHAR(200) NOT NULL,
    `tipo` VARCHAR(40) NOT NULL,
    `requiere_autenticacion` TINYINT(1) DEFAULT NULL,
    `requiere_autenticacion_tipo` VARCHAR(100) DEFAULT NULL,
    `autenticacion_basica_user` VARCHAR(100) DEFAULT NULL,
    `autenticacion_basica_pass` VARCHAR(100) DEFAULT NULL,
    `autenticacion_basica_cert` VARCHAR(100) DEFAULT NULL,
    `autenticacion_basica_cert_pass` VARCHAR(100) DEFAULT NULL,
    `autenticacion_mutua_client` VARCHAR(100) DEFAULT NULL,
    `autenticacion_mutua_client_pass` VARCHAR(100) DEFAULT NULL,
    `autenticacion_mutua_server` VARCHAR(100) DEFAULT NULL,
    `autenticacion_mutua_user` VARCHAR(100) DEFAULT NULL,
    `autenticacion_mutua_pass` VARCHAR(100) DEFAULT NULL,
    `autenticacion_mutua_client_key` VARCHAR(100) DEFAULT NULL,
    PRIMARY KEY (`id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `ws_operacion`
--

CREATE TABLE IF NOT EXISTS `ws_operacion` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `codigo` VARCHAR(12) DEFAULT NULL,
    `catalogo_id` BIGINT(20) NOT NULL,
    `nombre` VARCHAR(100) NOT NULL,
    `operacion` VARCHAR(100) NOT NULL,
    `soap` LONGTEXT NOT NULL,
    `ayuda` LONGTEXT NOT NULL,
    `respuestas` LONGTEXT NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `codigo` (`codigo`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `ws_operacion_respuesta`
--

CREATE TABLE IF NOT EXISTS `ws_operacion_respuesta` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `operacion_id` BIGINT(20) NOT NULL,
    `respuesta_id` VARCHAR(20) NOT NULL,
    `xslt` LONGTEXT,
    PRIMARY KEY (`id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8 AUTO_INCREMENT=1;

-- --------------------------------------------------------


-- --------------------------------------------------------
--
-- Filtros para las tablas descargadas (dump)
--

--
-- Filtros para la tabla `accion`
--
ALTER TABLE `accion`
  ADD CONSTRAINT `fk_trigger_proceso1` FOREIGN KEY (`proceso_id`) REFERENCES `proceso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `campo`
--
ALTER TABLE `campo`
  ADD CONSTRAINT `campo_ibfk_1` FOREIGN KEY (`formulario_id`) REFERENCES `formulario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_campo_documento1` FOREIGN KEY (`documento_id`) REFERENCES `documento` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `conexion`
--
ALTER TABLE `conexion`
  ADD CONSTRAINT `conexion_ibfk_1` FOREIGN KEY (`tarea_id_origen`) REFERENCES `tarea` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `conexion_ibfk_2` FOREIGN KEY (`tarea_id_destino`) REFERENCES `tarea` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `dato_seguimiento`
--
ALTER TABLE `dato_seguimiento`
  ADD CONSTRAINT `fk_dato_seguimiento_etapa1` FOREIGN KEY (`etapa_id`) REFERENCES `etapa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `documento`
--
ALTER TABLE `documento`
  ADD CONSTRAINT `fk_documento_hsm_configuracion1` FOREIGN KEY (`hsm_configuracion_id`) REFERENCES `hsm_configuracion` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_documento_proceso1` FOREIGN KEY (`proceso_id`) REFERENCES `proceso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `etapa`
--
ALTER TABLE `etapa`
  ADD CONSTRAINT `etapa_ibfk_1` FOREIGN KEY (`tramite_id`) REFERENCES `tramite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_etapa_etapa1` FOREIGN KEY (`etapa_ancestro_split_id`) REFERENCES `etapa` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_etapa_tarea1` FOREIGN KEY (`tarea_id`) REFERENCES `tarea` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_etapa_usuario1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `evento`
--
ALTER TABLE `evento`
  ADD CONSTRAINT `fk_evento_accion1` FOREIGN KEY (`accion_id`) REFERENCES `accion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_evento_paso1` FOREIGN KEY (`paso_id`) REFERENCES `paso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_evento_tarea1` FOREIGN KEY (`tarea_id`) REFERENCES `tarea` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `file`
--
ALTER TABLE `file`
  ADD CONSTRAINT `fk_file_tramite1` FOREIGN KEY (`tramite_id`) REFERENCES `tramite` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `formulario`
--
ALTER TABLE `formulario`
  ADD CONSTRAINT `fk_formulario_proceso1` FOREIGN KEY (`proceso_id`) REFERENCES `proceso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `grupo_usuarios`
--
ALTER TABLE `grupo_usuarios`
  ADD CONSTRAINT `fk_grupo_usuarios_cuenta1` FOREIGN KEY (`cuenta_id`) REFERENCES `cuenta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `grupo_usuarios_has_usuario`
--
ALTER TABLE `grupo_usuarios_has_usuario`
  ADD CONSTRAINT `fk_grupo_usuarios_has_usuario_grupo_usuarios1` FOREIGN KEY (`grupo_usuarios_id`) REFERENCES `grupo_usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_grupo_usuarios_has_usuario_usuario1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `hsm_configuracion`
--
ALTER TABLE `hsm_configuracion`
  ADD CONSTRAINT `fk_hsm_configuracion_cuenta1` FOREIGN KEY (`cuenta_id`) REFERENCES `cuenta` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `paso`
--
ALTER TABLE `paso`
  ADD CONSTRAINT `fk_paso_formulario1` FOREIGN KEY (`formulario_id`) REFERENCES `formulario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_paso_tarea1` FOREIGN KEY (`tarea_id`) REFERENCES `tarea` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `proceso`
--
ALTER TABLE `proceso`
  ADD CONSTRAINT `fk_proceso_cuenta1` FOREIGN KEY (`cuenta_id`) REFERENCES `cuenta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `reporte`
--
ALTER TABLE `reporte`
  ADD CONSTRAINT `fk_reporte_proceso1` FOREIGN KEY (`proceso_id`) REFERENCES `proceso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tarea`
--
ALTER TABLE `tarea`
  ADD CONSTRAINT `tarea_ibfk_1` FOREIGN KEY (`proceso_id`) REFERENCES `proceso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tarea_has_grupo_usuarios`
--
ALTER TABLE `tarea_has_grupo_usuarios`
  ADD CONSTRAINT `fk_tarea_has_grupo_usuarios_grupo_usuarios1` FOREIGN KEY (`grupo_usuarios_id`) REFERENCES `grupo_usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tarea_has_grupo_usuarios_tarea1` FOREIGN KEY (`tarea_id`) REFERENCES `tarea` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tramite`
--
ALTER TABLE `tramite`
  ADD CONSTRAINT `fk_tramite_proceso1` FOREIGN KEY (`proceso_id`) REFERENCES `proceso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_cuenta1` FOREIGN KEY (`cuenta_id`) REFERENCES `cuenta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario_backend`
--
ALTER TABLE `usuario_backend`
  ADD CONSTRAINT `fk_usuario_backend_cuenta1` FOREIGN KEY (`cuenta_id`) REFERENCES `cuenta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `widget`
--
ALTER TABLE `widget`
  ADD CONSTRAINT `fk_widget_cuenta1` FOREIGN KEY (`cuenta_id`) REFERENCES `cuenta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `dato_seguimiento`
--
ALTER TABLE `etapa_historial_ejecuciones`
  ADD CONSTRAINT `fk_etapa` FOREIGN KEY (`etapa_id`) REFERENCES `etapa` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;


-- --------------------------------------------------------
--
-- Vistas  (dump)
--


-- --------------------------------------------------------
--
-- Estructura de vista para la vista `tally_10`
--

CREATE OR REPLACE VIEW tally_10 AS
    SELECT 0 AS N 
    UNION ALL SELECT 1 
    UNION ALL SELECT 2 
    UNION ALL SELECT 3 
    UNION ALL SELECT 4 
    UNION ALL SELECT 5 
    UNION ALL SELECT 6 
    UNION ALL SELECT 7 
    UNION ALL SELECT 8 
    UNION ALL SELECT 9;
-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de vista para la vista `tally_100`
--
                      
CREATE OR REPLACE VIEW tally_100 AS
    SELECT 
        a.N + b.N * 10 + 1 n
    FROM
        tally_10 a,
        tally_10 b
    ORDER BY n;

-- --------------------------------------------------------

-- --------------------------------------------------------
--
-- Estructura de vista para la vista `tarea_grupos_view`
--
                        
CREATE OR REPLACE VIEW tarea_grupos_view AS
    SELECT 
        t.id,
        t.proceso_id,
        t.nombre,
        t.acceso_modo,
        SUBSTRING_INDEX(SUBSTRING_INDEX(t.grupos_usuarios, ',', n.n),
                ',',
                - 1) AS grupos_usuarios
    FROM
        tarea t
            CROSS JOIN
        tally_100 n
    WHERE
        n.n <= 1 + (LENGTH(t.grupos_usuarios) - LENGTH(REPLACE(t.grupos_usuarios, ',', '')))
            AND t.grupos_usuarios LIKE '%@@%'
            AND t.acceso_modo = 'grupos_usuarios';

-- --------------------------------------------------------

--
-- FIN - Estructura de SIMPLE
--