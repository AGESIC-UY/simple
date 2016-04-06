-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 08-03-2016 a las 13:56:19
-- Versión del servidor: 5.1.37
-- Versión de PHP: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de datos: `simple_bpm`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accion`
--

CREATE TABLE IF NOT EXISTS `accion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(128) NOT NULL,
  `tipo` varchar(60) NOT NULL,
  `extra` text,
  `proceso_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_trigger_proceso1_idx` (`proceso_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=171 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bloque`
--

CREATE TABLE IF NOT EXISTS `bloque` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campo`
--

CREATE TABLE IF NOT EXISTS `campo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(32) NOT NULL,
  `readonly` tinyint(1) NOT NULL DEFAULT '0',
  `valor_default` text NOT NULL,
  `posicion` int(10) unsigned NOT NULL,
  `tipo` varchar(32) NOT NULL,
  `formulario_id` int(10) unsigned NOT NULL,
  `etiqueta` text NOT NULL,
  `validacion` varchar(128) NOT NULL,
  `ayuda` text NOT NULL,
  `dependiente_tipo` enum('string','regex') DEFAULT 'string',
  `dependiente_campo` varchar(64) DEFAULT NULL,
  `dependiente_valor` varchar(256) DEFAULT NULL,
  `datos` text,
  `documento_id` int(10) unsigned DEFAULT NULL,
  `fieldset` varchar(30) NOT NULL,
  `extra` text,
  `dependiente_relacion` enum('==','!=') DEFAULT '==',
  PRIMARY KEY (`id`),
  KEY `fk_campo_formulario1` (`formulario_id`),
  KEY `fk_campo_documento1_idx` (`documento_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=788 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conexion`
--

CREATE TABLE IF NOT EXISTS `conexion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tarea_id_origen` int(10) unsigned NOT NULL,
  `tarea_id_destino` int(10) unsigned DEFAULT NULL,
  `tipo` enum('secuencial','evaluacion','paralelo','paralelo_evaluacion','union') NOT NULL,
  `regla` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tarea_origen_destino` (`tarea_id_origen`,`tarea_id_destino`),
  KEY `fk_ruta_tarea` (`tarea_id_origen`),
  KEY `fk_ruta_tarea1` (`tarea_id_destino`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=154 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta`
--

CREATE TABLE IF NOT EXISTS `cuenta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(128) NOT NULL,
  `nombre_largo` varchar(256) NOT NULL,
  `mensaje` text NOT NULL,
  `logo` varchar(128) DEFAULT NULL,
  `api_token` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dato_seguimiento`
--

CREATE TABLE IF NOT EXISTS `dato_seguimiento` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(128) NOT NULL,
  `valor` text NOT NULL,
  `etapa_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre_etapa` (`nombre`,`etapa_id`),
  KEY `fk_dato_seguimiento_etapa1_idx` (`etapa_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2224 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento`
--

CREATE TABLE IF NOT EXISTS `documento` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tipo` enum('blanco','certificado') NOT NULL DEFAULT 'blanco',
  `nombre` varchar(128) NOT NULL,
  `contenido` text NOT NULL,
  `servicio` varchar(128) NOT NULL,
  `servicio_url` varchar(256) NOT NULL,
  `logo` varchar(256) NOT NULL,
  `timbre` varchar(256) NOT NULL,
  `firmador_nombre` varchar(128) NOT NULL,
  `firmador_cargo` varchar(128) NOT NULL,
  `firmador_servicio` varchar(128) NOT NULL,
  `firmador_imagen` varchar(256) NOT NULL,
  `validez` int(10) unsigned DEFAULT NULL,
  `hsm_configuracion_id` int(10) unsigned DEFAULT NULL,
  `proceso_id` int(10) unsigned NOT NULL,
  `subtitulo` varchar(128) NOT NULL,
  `titulo` varchar(128) NOT NULL,
  `validez_habiles` tinyint(1) NOT NULL,
  `tamano` enum('letter','legal') DEFAULT 'letter',
  PRIMARY KEY (`id`),
  KEY `fk_documento_proceso1_idx` (`proceso_id`),
  KEY `fk_documento_hsm_configuracion1_idx` (`hsm_configuracion_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=63 ;

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
  PRIMARY KEY (`id`),
  KEY `fk_etapa_tarea1_idx` (`tarea_id`),
  KEY `fk_etapa_usuario1_idx` (`usuario_id`),
  KEY `fk_etapa_tramite1` (`tramite_id`),
  KEY `fk_etapa_etapa1_idx` (`etapa_ancestro_split_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=867 ;

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
  PRIMARY KEY (`id`),
  KEY `fk_evento_tarea1_idx` (`tarea_id`),
  KEY `fk_evento_accion1_idx` (`accion_id`),
  KEY `fk_evento_paso1_idx` (`paso_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=189 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `feriado`
--

CREATE TABLE IF NOT EXISTS `feriado` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fecha_UNIQUE` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `file`
--

CREATE TABLE IF NOT EXISTS `file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `tipo` enum('dato','documento') NOT NULL,
  `llave` varchar(12) NOT NULL,
  `llave_copia` varchar(40) DEFAULT NULL,
  `llave_firma` varchar(12) DEFAULT NULL,
  `validez` int(10) unsigned DEFAULT NULL,
  `tramite_id` int(10) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `validez_habiles` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `filename_tipo` (`filename`,`tipo`),
  KEY `fk_file_tramite1_idx` (`tramite_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formulario`
--

CREATE TABLE IF NOT EXISTS `formulario` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(128) NOT NULL,
  `proceso_id` int(10) unsigned NOT NULL,
  `bloque_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_formulario_proceso1_idx` (`proceso_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=238 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo_usuarios`
--

CREATE TABLE IF NOT EXISTS `grupo_usuarios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(128) NOT NULL,
  `cuenta_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `grupo_usuarios_UNIQUE` (`cuenta_id`,`nombre`),
  KEY `fk_grupo_usuarios_cuenta1_idx` (`cuenta_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo_usuarios_has_usuario`
--

CREATE TABLE IF NOT EXISTS `grupo_usuarios_has_usuario` (
  `grupo_usuarios_id` int(10) unsigned NOT NULL,
  `usuario_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`grupo_usuarios_id`,`usuario_id`),
  KEY `fk_grupo_usuarios_has_usuario_usuario1_idx` (`usuario_id`),
  KEY `fk_grupo_usuarios_has_usuario_grupo_usuarios1_idx` (`grupo_usuarios_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hsm_configuracion`
--

CREATE TABLE IF NOT EXISTS `hsm_configuracion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(128) NOT NULL,
  `cuenta_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre_UNIQUE` (`nombre`),
  KEY `fk_hsm_configuracion_cuenta1_idx` (`cuenta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migration_version`
--

CREATE TABLE IF NOT EXISTS `migration_version` (
  `version` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pasarela_pago`
--

CREATE TABLE IF NOT EXISTS `pasarela_pago` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(64) NOT NULL,
  `metodo` varchar(64) NOT NULL,
  `activo` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pasarela_pago_antel`
--

CREATE TABLE IF NOT EXISTS `pasarela_pago_antel` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pasarela_pago_id` int(10) NOT NULL,
  `id_tramite` int(10) NOT NULL,
  `cantidad` tinyint(1) NOT NULL,
  `tasa_1` decimal(6,2) NOT NULL,
  `tasa_2` decimal(6,2) NOT NULL,
  `tasa_3` decimal(6,2) NOT NULL,
  `operacion` varchar(1) NOT NULL,
  `vencimiento` varchar(12) NOT NULL,
  `codigos_desglose` varchar(450) NOT NULL,
  `montos_desglose` varchar(450) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paso`
--

CREATE TABLE IF NOT EXISTS `paso` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orden` int(10) unsigned NOT NULL,
  `modo` enum('edicion','visualizacion') NOT NULL,
  `regla` varchar(512) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `formulario_id` int(10) unsigned NOT NULL,
  `tarea_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_paso_formulario1_idx` (`formulario_id`),
  KEY `fk_paso_tarea1_idx` (`tarea_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=242 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proceso`
--

CREATE TABLE IF NOT EXISTS `proceso` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(128) NOT NULL,
  `width` varchar(8) NOT NULL DEFAULT '100%',
  `height` varchar(8) NOT NULL DEFAULT '800px',
  `cuenta_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_proceso_cuenta1_idx` (`cuenta_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=46 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proceso_trazabilidad`
--

CREATE TABLE IF NOT EXISTS `proceso_trazabilidad` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `proceso_id` int(10) NOT NULL,
  `organismo_id` varchar(255) NOT NULL,
  `proceso_externo_id` varchar(255) NOT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reporte`
--

CREATE TABLE IF NOT EXISTS `reporte` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(128) NOT NULL,
  `campos` text NOT NULL,
  `proceso_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reporte_proceso1_idx` (`proceso_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reporte_satisfaccion`
--

CREATE TABLE IF NOT EXISTS `reporte_satisfaccion` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(10) NOT NULL,
  `fecha` datetime NOT NULL,
  `reporte` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=94 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarea`
--

CREATE TABLE IF NOT EXISTS `tarea` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identificador` varchar(32) NOT NULL,
  `inicial` tinyint(1) NOT NULL DEFAULT '0',
  `nombre` varchar(128) NOT NULL,
  `posx` int(10) unsigned NOT NULL DEFAULT '0',
  `posy` int(10) unsigned NOT NULL DEFAULT '0',
  `asignacion` enum('ciclica','manual','autoservicio','usuario') NOT NULL DEFAULT 'ciclica',
  `asignacion_usuario` varchar(128) DEFAULT NULL,
  `asignacion_notificar` tinyint(1) NOT NULL DEFAULT '0',
  `proceso_id` int(10) unsigned NOT NULL,
  `almacenar_usuario` tinyint(1) NOT NULL DEFAULT '0',
  `almacenar_usuario_variable` varchar(128) DEFAULT NULL,
  `acceso_modo` enum('grupos_usuarios','publico','registrados','claveunica') NOT NULL DEFAULT 'grupos_usuarios',
  `activacion` enum('si','entre_fechas','no') NOT NULL DEFAULT 'si',
  `activacion_inicio` date DEFAULT NULL,
  `activacion_fin` date DEFAULT NULL,
  `vencimiento` tinyint(1) NOT NULL DEFAULT '0',
  `vencimiento_valor` int(10) unsigned NOT NULL DEFAULT '5',
  `vencimiento_unidad` enum('D','W','M') NOT NULL DEFAULT 'D',
  `vencimiento_habiles` tinyint(1) NOT NULL DEFAULT '0',
  `vencimiento_notificar` tinyint(1) NOT NULL DEFAULT '0',
  `vencimiento_notificar_email` varchar(255) NOT NULL,
  `vencimiento_notificar_dias` int(10) unsigned NOT NULL DEFAULT '1',
  `grupos_usuarios` text,
  `paso_confirmacion` tinyint(1) NOT NULL DEFAULT '1',
  `previsualizacion` text NOT NULL,
  `trazabilidad` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `identificador_proceso` (`identificador`,`proceso_id`),
  KEY `fk_tarea_proceso1` (`proceso_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=141 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarea_has_grupo_usuarios`
--

CREATE TABLE IF NOT EXISTS `tarea_has_grupo_usuarios` (
  `tarea_id` int(10) unsigned NOT NULL,
  `grupo_usuarios_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`tarea_id`,`grupo_usuarios_id`),
  KEY `fk_tarea_has_grupo_usuarios_grupo_usuarios1_idx` (`grupo_usuarios_id`),
  KEY `fk_tarea_has_grupo_usuarios_tarea1_idx` (`tarea_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tramite`
--

CREATE TABLE IF NOT EXISTS `tramite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `proceso_id` int(10) unsigned NOT NULL,
  `pendiente` tinyint(1) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `ended_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tramite_proceso1_idx` (`proceso_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=735 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario` varchar(128) NOT NULL,
  `password` varchar(256) DEFAULT NULL,
  `rut` varchar(16) DEFAULT NULL,
  `nombres` varchar(128) DEFAULT NULL,
  `apellido_paterno` varchar(128) DEFAULT NULL,
  `apellido_materno` varchar(128) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `registrado` tinyint(1) NOT NULL DEFAULT '1',
  `vacaciones` tinyint(1) NOT NULL DEFAULT '0',
  `cuenta_id` int(10) unsigned DEFAULT NULL,
  `salt` varchar(32) NOT NULL,
  `open_id` tinyint(1) NOT NULL DEFAULT '0',
  `reset_token` varchar(40) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_unique` (`usuario`,`open_id`),
  KEY `fk_usuario_cuenta1_idx` (`cuenta_id`),
  KEY `email_idx` (`email`,`open_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3073 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_backend`
--

CREATE TABLE IF NOT EXISTS `usuario_backend` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(128) NOT NULL,
  `apellidos` varchar(128) NOT NULL,
  `rol` enum('super','modelamiento','operacion','seguimiento','gestion','desarrollo','configuracion') DEFAULT NULL,
  `salt` varchar(32) NOT NULL,
  `cuenta_id` int(10) unsigned NOT NULL,
  `reset_token` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_usuario_backend_cuenta1_idx` (`cuenta_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_manager`
--

CREATE TABLE IF NOT EXISTS `usuario_manager` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario` varchar(128) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(128) NOT NULL,
  `apellidos` varchar(128) NOT NULL,
  `salt` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `widget`
--

CREATE TABLE IF NOT EXISTS `widget` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tipo` varchar(32) NOT NULL,
  `nombre` varchar(128) NOT NULL,
  `posicion` int(10) unsigned NOT NULL,
  `config` text,
  `cuenta_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_widget_cuenta1_idx` (`cuenta_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ws_catalogo`
--

CREATE TABLE IF NOT EXISTS `ws_catalogo` (
  `nombre` varchar(64) NOT NULL,
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `wsdl` varchar(255) NOT NULL,
  `endpoint_location` varchar(255) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `conexion_timeout` int(3) NOT NULL,
  `respuesta_timeout` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=51 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ws_operacion`
--

CREATE TABLE IF NOT EXISTS `ws_operacion` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(12) NOT NULL,
  `catalogo_id` int(10) NOT NULL,
  `nombre` varchar(155) NOT NULL,
  `operacion` varchar(155) NOT NULL,
  `soap` longtext NOT NULL,
  `ayuda` longtext NOT NULL,
  `respuestas` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ws_operacion_respuesta`
--

CREATE TABLE IF NOT EXISTS `ws_operacion_respuesta` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `operacion_id` int(10) NOT NULL,
  `respuesta_id` varchar(19) NOT NULL,
  `xslt` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=77 ;
