-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 01-05-2016 a las 03:32:49
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campo`
--

CREATE TABLE IF NOT EXISTS `campo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
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
  `extra` text,
  `dependiente_relacion` enum('==','!=') DEFAULT '==',
  `fieldset` varchar(100) NULL,
  PRIMARY KEY (`id`),
  KEY `fk_campo_formulario1` (`formulario_id`),
  KEY `fk_campo_documento1_idx` (`documento_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=966 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=47 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=325 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=204 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=72 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formulario`
--

CREATE TABLE IF NOT EXISTS `formulario` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(128) NOT NULL,
  `proceso_id` int(10) unsigned NOT NULL,
  `bloque_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_formulario_proceso1_idx` (`proceso_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=292 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

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



-- --------------------------------------------------------



-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paso`
--

CREATE TABLE IF NOT EXISTS `paso` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orden` int(10) unsigned NOT NULL,
  `modo` enum('edicion','visualizacion') NOT NULL,
  `regla` varchar(512) NOT NULL,
  `formulario_id` int(10) unsigned NOT NULL,
  `tarea_id` int(10) unsigned NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_paso_formulario1_idx` (`formulario_id`),
  KEY `fk_paso_tarea1_idx` (`tarea_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=50 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=74 ;

-- --------------------------------------------------------


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=139 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=136 ;

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
  `usuario` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_usuario_backend_cuenta1_idx` (`cuenta_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_manager`
--

CREATE TABLE IF NOT EXISTS `usuario_manager` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario` varchar(128) NOT NULL,
  `user` varchar(128) DEFAULT NULL,
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------


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
