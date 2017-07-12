<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

ERROR - 07-06-2017 00:10:47 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.etapa_id = e.id AND d2.etapa_id = e.id AND d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "7675675")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:10:49 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.etapa_id = e.id AND d2.etapa_id = e.id AND d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "7675675")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:11:50 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.etapa_id = e.id AND d2.etapa_id = e.id AND d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "7675675")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:12:08 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.etapa_id = e.id AND d2.etapa_id = e.id AND d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "7675675")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:12:18 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.etapa_id = e.id AND d2.etapa_id = e.id AND d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "7675675")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:13:25 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.etapa_id = e.id AND d2.etapa_id = e.id AND d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "7675675")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:14:11 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.etapa_id = e.id AND d2.etapa_id = e.id AND d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "7675675")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:14:35 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.etapa_id = e.id AND d2.etapa_id = e.id AND d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "7675675")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:14:51 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.etapa_id = e.id AND d2.etapa_id = e.id AND d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "21312")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:18:45 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.etapa_id = e.id AND d2.etapa_id = e.id AND d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "21312")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:19:06 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.etapa_id = e.id AND d2.etapa_id = e.id AND d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "21312")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:19:24 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.etapa_id = e.id AND d2.etapa_id = e.id AND d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "231123")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:19:53 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.etapa_id = e.id AND d2.etapa_id = e.id AND d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "231123")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:24:39 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:28:12 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "48037336")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:31:29 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = ""48037336"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:31:47 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = ""48037336"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:32:38 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"48037336\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:33:13 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:33:22 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:33:26 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:33:38 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"48037336\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:35:01 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:54:30 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND  AND d.etapa_id = d2.etapa_id                     d1.nombre = replace(d.valor, '"', '') AND d2.valor = "\"48037336\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:55:42 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT  FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"48037336\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:56:02 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT  FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"48037336\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:56:26 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT  FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"48037336\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:57:31 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT  FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"48037336\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 00:58:33 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT  FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"48037336\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:00:13 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"48037336\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:00:30 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"48037336\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:01:40 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:02:10 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (e.id IN (SELECT d.etapa_id AS d__etapa_id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"48037336\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:10:45 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"48037336\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:10:52 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:10:59 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"123456\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:11:04 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:11:21 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:11:27 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:11:42 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"123456\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:11:57 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:12:58 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ? AND e.updated_at >= ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:13:03 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ? AND e.updated_at >= ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:13:08 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ? AND e.updated_at >= ? AND e.updated_at <= ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:13:12 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id LEFT JOIN tarea t3 ON e.tarea_id = t3.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ? AND t3.nombre LIKE ? AND e.updated_at >= ? AND e.updated_at <= ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:13:14 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id LEFT JOIN tarea t3 ON e.tarea_id = t3.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ? AND t3.nombre LIKE ? AND e.updated_at >= ? AND e.updated_at <= ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:13:18 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id LEFT JOIN tarea t3 ON e.tarea_id = t3.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ? AND t3.nombre LIKE ? AND e.updated_at >= ? AND e.updated_at <= ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:13:25 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ? AND t.id = ? AND e.updated_at >= ? AND e.updated_at <= ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:13:30 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id LEFT JOIN tarea t3 ON e.tarea_id = t3.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ? AND (t3.acceso_modo = ? AND (t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ?)) AND e.updated_at >= ? AND e.updated_at <= ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:13:33 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id LEFT JOIN tarea t3 ON e.tarea_id = t3.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ? AND (t3.acceso_modo = ? AND (t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ?)) AND e.updated_at >= ? AND e.updated_at <= ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:13:36 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ? AND e.updated_at >= ? AND e.updated_at <= ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:13:41 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ? AND e.updated_at >= ? AND e.updated_at <= ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:16:39 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ? AND e.updated_at >= ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:17:19 --> 404 Page Not Found --> favicon.ico
ERROR - 07-06-2017 01:17:27 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/autenticacion.php 136
ERROR - 07-06-2017 01:17:27 --> Severity: Notice  --> Undefined offset: 2 /var/www/html/simple/application/models/widget_tramite_etapas.php 41
ERROR - 07-06-2017 01:18:56 --> Severity: Notice  --> Undefined variable: etapa /var/www/html/simple/application/models/campo_text.php 21
ERROR - 07-06-2017 01:18:56 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/models/campo_text.php 21
ERROR - 07-06-2017 01:19:00 --> Severity: Notice  --> Undefined variable: etapa /var/www/html/simple/application/models/campo_text.php 21
ERROR - 07-06-2017 01:19:00 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/models/campo_text.php 21
ERROR - 07-06-2017 01:19:04 --> Severity: Notice  --> Undefined variable: etapa /var/www/html/simple/application/models/campo_text.php 21
ERROR - 07-06-2017 01:19:04 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/models/campo_text.php 21
ERROR - 07-06-2017 01:19:08 --> Severity: Notice  --> Undefined variable: etapa /var/www/html/simple/application/models/campo_text.php 21
ERROR - 07-06-2017 01:19:08 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/models/campo_text.php 21
ERROR - 07-06-2017 01:19:15 --> Severity: Notice  --> Undefined variable: etapa /var/www/html/simple/application/models/campo_text.php 21
ERROR - 07-06-2017 01:19:15 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/models/campo_text.php 21
ERROR - 07-06-2017 01:19:19 --> Severity: Notice  --> Undefined variable: etapa /var/www/html/simple/application/models/campo_text.php 21
ERROR - 07-06-2017 01:19:19 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/models/campo_text.php 21
ERROR - 07-06-2017 01:20:31 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:21:33 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:21:39 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id LEFT JOIN tarea t3 ON e.tarea_id = t3.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ? AND (t3.acceso_modo = ? AND (t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ?))) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:21:40 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id LEFT JOIN tarea t3 ON e.tarea_id = t3.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ? AND (t3.acceso_modo = ? AND (t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ?))) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:21:49 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN tarea t3 ON e.tarea_id = t3.id WHERE (e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ? AND (t3.acceso_modo = ? AND (t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ?)) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:21:51 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN tarea t3 ON e.tarea_id = t3.id WHERE (e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ? AND (t3.acceso_modo = ? AND (t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ?)) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:21:52 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN tarea t3 ON e.tarea_id = t3.id WHERE (e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ? AND (t3.acceso_modo = ? AND (t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ?)) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:22:05 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id LEFT JOIN tarea t3 ON e.tarea_id = t3.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ? AND (t3.acceso_modo = ? AND (t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ?))) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:22:09 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:22:36 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN tarea t3 ON e.tarea_id = t3.id WHERE (e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ? AND (t3.acceso_modo = ? AND (t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ?)) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:22:48 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 358
ERROR - 07-06-2017 01:22:48 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 358
ERROR - 07-06-2017 01:23:35 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN tarea t3 ON e.tarea_id = t3.id WHERE (e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ? AND (t3.acceso_modo = ? AND (t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ?)) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 01:25:05 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 358
ERROR - 07-06-2017 01:25:05 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:25:05 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:25:05 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:25:05 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:25:05 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:25:05 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:25:05 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:25:05 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:25:05 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:25:05 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:25:05 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:11 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 358
ERROR - 07-06-2017 01:26:11 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:11 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:11 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:11 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:11 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:11 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:11 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:11 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:11 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:11 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:11 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:19 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 358
ERROR - 07-06-2017 01:26:19 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 358
ERROR - 07-06-2017 01:26:37 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 358
ERROR - 07-06-2017 01:26:37 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:37 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:37 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:37 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:37 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:37 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:37 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:37 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:37 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:37 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 298
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 825
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:26:37 --> Severity: Warning  --> assert(): Assertion failed /var/www/html/simple/application/libraries/sphinxclient.php 128
ERROR - 07-06-2017 01:27:23 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 358
ERROR - 07-06-2017 01:27:38 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 358
ERROR - 07-06-2017 01:27:53 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 358
ERROR - 07-06-2017 01:28:20 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 358
ERROR - 07-06-2017 01:28:35 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 358
ERROR - 07-06-2017 01:28:35 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 358
ERROR - 07-06-2017 01:28:37 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 358
ERROR - 07-06-2017 01:28:37 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 358
ERROR - 07-06-2017 01:36:37 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 308
ERROR - 07-06-2017 01:36:37 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 308
ERROR - 07-06-2017 01:37:11 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 308
ERROR - 07-06-2017 01:37:11 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 308
ERROR - 07-06-2017 01:37:33 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 308
ERROR - 07-06-2017 01:37:33 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 308
ERROR - 07-06-2017 01:37:42 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 308
ERROR - 07-06-2017 01:37:42 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 308
ERROR - 07-06-2017 01:42:48 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 304
ERROR - 07-06-2017 01:43:07 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 304
ERROR - 07-06-2017 01:44:59 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and (d.nombre = CONCAT('documento_tramite_inicial__e', e.tramite_id)  and d1.nombre = replace(d.valor,'"', '') and d1.valor = :busqueda_documento) 
              and t.id  IN (
                       SELECT t.id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", t.id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and t.id  IN (
                       SELECT t.id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", t.id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta  and (d.nombre = CONCAT('documento_tramite_inicial__e', e.tramite_id)  and d1.nombre = replace(d.valor,'"', '') and d1.valor = :busqueda_documento) 
              )) as a  order by a.updated_at limit 0,50
ERROR - 07-06-2017 01:44:59 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:45:47 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and t.id  IN (
                       SELECT t.id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", t.id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and t.id  IN (
                       SELECT t.id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", t.id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at limit 0,50
ERROR - 07-06-2017 01:45:47 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:45:47 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and t.id  IN (
                       SELECT t.id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", t.id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and t.id  IN (
                       SELECT t.id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", t.id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 01:45:47 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:45:57 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and t.id  IN (
                       SELECT t.id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", t.id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and t.id  IN (
                       SELECT t.id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", t.id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at limit 0,50
ERROR - 07-06-2017 01:45:57 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:45:57 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and t.id  IN (
                       SELECT t.id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", t.id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and t.id  IN (
                       SELECT t.id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", t.id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 01:45:57 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:46:06 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and t.id  IN (
                       SELECT t.id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", t.id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and t.id  IN (
                       SELECT t.id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", t.id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at limit 0,50
ERROR - 07-06-2017 01:46:06 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:46:06 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and t.id  IN (
                       SELECT t.id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", t.id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and t.id  IN (
                       SELECT t.id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", t.id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 01:46:06 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:46:22 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and t.id  IN (
                       SELECT t.id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", t.id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and t.id  IN (
                       SELECT t.id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", t.id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at limit 0,50
ERROR - 07-06-2017 01:46:22 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:46:22 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and t.id  IN (
                       SELECT t.id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", t.id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and t.id  IN (
                       SELECT t.id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", t.id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 01:46:22 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:51:58 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at limit 0,50
ERROR - 07-06-2017 01:51:58 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:51:58 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 01:51:58 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:55:17 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 01:55:17 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:55:17 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 01:55:17 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:56:38 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:56:38 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:56:41 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:56:41 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:58:06 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:58:06 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:58:12 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
              )) as a  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 01:58:12 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:58:12 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"36947729\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
              )) as a 
ERROR - 07-06-2017 01:58:12 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:58:17 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"peñarol\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"peñarol\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
              )) as a  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 01:58:17 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:58:17 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"peñarol\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"peñarol\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
              )) as a 
ERROR - 07-06-2017 01:58:17 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:58:18 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"peñarol\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"peñarol\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
              )) as a  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 01:58:18 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:58:18 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"peñarol\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"peñarol\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
              )) as a 
ERROR - 07-06-2017 01:58:18 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:58:33 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"peñarol\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"peñarol\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 01:58:33 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 01:58:33 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"peñarol\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\"peñarol\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 01:58:33 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 307
ERROR - 07-06-2017 02:05:50 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\""pe\u00f1arol"\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\""pe\u00f1arol"\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 02:05:50 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:07:10 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\""pe\u00f1arol"\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\""pe\u00f1arol"\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 02:07:10 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:07:40 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\""pe\u00f1arol"\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\""pe\u00f1arol"\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 02:07:40 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:09:19 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\""pe\u00f1arol"\"")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "\""pe\u00f1arol"\"")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 02:09:19 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:09:30 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "pe\u00f1arol")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "pe\u00f1arol")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 02:09:30 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:09:35 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "pe\u00f1arol")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "pe\u00f1arol")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 02:09:35 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:09:45 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:09:45 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:10:02 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "36947729")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "36947729")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 02:10:02 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:10:17 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "36947729")
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = "36947729")
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 02:10:17 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:11:59 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"36947729"')
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"36947729"')
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 02:11:59 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:12:00 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"36947729"')
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"36947729"')
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 02:12:00 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:12:13 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"pe\u00f1arol"')
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"pe\u00f1arol"')
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 02:12:13 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:12:14 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"pe\u00f1arol"')
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"pe\u00f1arol"')
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 02:12:14 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:12:47 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"pe\u00f1arol"')
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"pe\u00f1arol"')
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 02:12:47 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:15:49 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"pe\u00f1arol"')
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"pe\u00f1arol"')
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 02:15:49 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:16:01 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"36947729"')
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"36947729"')
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 02:16:01 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:16:01 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"36947729"')
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"36947729"')
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 02:16:01 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:16:09 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"36947729"')
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"36947729"')
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde 
              )) as a  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 02:16:09 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:16:09 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"36947729"')
              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"36947729"')
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde 
              )) as a 
ERROR - 07-06-2017 02:16:09 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:16:24 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:16:24 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:16:37 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:16:37 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:16:44 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:16:44 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:16:51 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:16:51 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:16:57 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:16:57 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 303
ERROR - 07-06-2017 02:21:47 --> SELECT t.id AS t__id, t.pendiente AS t__pendiente, t.proceso_id AS t__proceso_id, t.created_at AS t__created_at, t.updated_at AS t__updated_at, t.ended_at AS t__ended_at, p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, c.id AS c__id, c.nombre AS c__nombre, c.nombre_largo AS c__nombre_largo, c.mensaje AS c__mensaje, c.logo AS c__logo, c.api_token AS c__api_token, c.codigo_analytics AS c__codigo_analytics, c.correo_remitente AS c__correo_remitente, e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at, u.id AS u__id, u.usuario AS u__usuario, u.password AS u__password, u.rut AS u__rut, u.nombres AS u__nombres, u.apellido_paterno AS u__apellido_paterno, u.apellido_materno AS u__apellido_materno, u.email AS u__email, u.vacaciones AS u__vacaciones, u.cuenta_id AS u__cuenta_id, u.salt AS u__salt, u.open_id AS u__open_id, u.registrado AS u__registrado, u.reset_token AS u__reset_token, u.acceso_reportes AS u__acceso_reportes, u.created_at AS u__created_at, u.updated_at AS u__updated_at, d.id AS d__id, d.nombre AS d__nombre, d.valor AS d__valor, d.etapa_id AS d__etapa_id, d2.id AS d2__id, d2.nombre AS d2__nombre, d2.valor AS d2__valor, d2.etapa_id AS d2__etapa_id FROM tramite t LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN etapa e ON t.id = e.tramite_id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN dato_seguimiento d ON e.id = d.etapa_id LEFT JOIN dato_seguimiento d2 ON e.id = d2.etapa_id WHERE t.id IN ('3309', '3308') AND (u.id = ? AND e.pendiente = 0 AND c.nombre = ? AND d.nombre = CONCAT('documento_tramite_inicial__e', t.id) AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = '"36947729"') ORDER BY t.updated_at desc
ERROR - 07-06-2017 02:29:48 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 02:29:52 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 02:29:59 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"123456\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 02:43:22 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"123456\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 03:01:12 --> SELECT t.id AS t__id, t.pendiente AS t__pendiente, t.proceso_id AS t__proceso_id, t.created_at AS t__created_at, t.updated_at AS t__updated_at, t.ended_at AS t__ended_at, p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, c.id AS c__id, c.nombre AS c__nombre, c.nombre_largo AS c__nombre_largo, c.mensaje AS c__mensaje, c.logo AS c__logo, c.api_token AS c__api_token, c.codigo_analytics AS c__codigo_analytics, c.correo_remitente AS c__correo_remitente, e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at, u.id AS u__id, u.usuario AS u__usuario, u.password AS u__password, u.rut AS u__rut, u.nombres AS u__nombres, u.apellido_paterno AS u__apellido_paterno, u.apellido_materno AS u__apellido_materno, u.email AS u__email, u.vacaciones AS u__vacaciones, u.cuenta_id AS u__cuenta_id, u.salt AS u__salt, u.open_id AS u__open_id, u.registrado AS u__registrado, u.reset_token AS u__reset_token, u.acceso_reportes AS u__acceso_reportes, u.created_at AS u__created_at, u.updated_at AS u__updated_at FROM tramite t LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN etapa e ON t.id = e.tramite_id LEFT JOIN usuario u ON e.usuario_id = u.id WHERE t.id IN ('3309', '3308', '3307', '3262', '3261', '3254', '3253', '3252', '2891', '2890', '2889', '2744', '2742', '2740') AND (u.id = ? AND e.pendiente = 0 AND c.nombre = ? AND p.nombre LIKE ?) ORDER BY t.updated_at desc
ERROR - 07-06-2017 03:03:29 --> SELECT t.id AS t__id, t.pendiente AS t__pendiente, t.proceso_id AS t__proceso_id, t.created_at AS t__created_at, t.updated_at AS t__updated_at, t.ended_at AS t__ended_at, p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, c.id AS c__id, c.nombre AS c__nombre, c.nombre_largo AS c__nombre_largo, c.mensaje AS c__mensaje, c.logo AS c__logo, c.api_token AS c__api_token, c.codigo_analytics AS c__codigo_analytics, c.correo_remitente AS c__correo_remitente, e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at, u.id AS u__id, u.usuario AS u__usuario, u.password AS u__password, u.rut AS u__rut, u.nombres AS u__nombres, u.apellido_paterno AS u__apellido_paterno, u.apellido_materno AS u__apellido_materno, u.email AS u__email, u.vacaciones AS u__vacaciones, u.cuenta_id AS u__cuenta_id, u.salt AS u__salt, u.open_id AS u__open_id, u.registrado AS u__registrado, u.reset_token AS u__reset_token, u.acceso_reportes AS u__acceso_reportes, u.created_at AS u__created_at, u.updated_at AS u__updated_at FROM tramite t LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN etapa e ON t.id = e.tramite_id LEFT JOIN usuario u ON e.usuario_id = u.id WHERE t.id IN ('3309', '3308', '3307', '3262', '3261', '3254', '3253', '3252', '2891', '2890', '2889', '2744', '2742', '2740') AND (u.id = ? AND e.pendiente = 0 AND c.nombre = ? AND p.nombre LIKE ?) ORDER BY t.updated_at desc
ERROR - 07-06-2017 03:03:40 --> SELECT t.id AS t__id, t.pendiente AS t__pendiente, t.proceso_id AS t__proceso_id, t.created_at AS t__created_at, t.updated_at AS t__updated_at, t.ended_at AS t__ended_at, p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, c.id AS c__id, c.nombre AS c__nombre, c.nombre_largo AS c__nombre_largo, c.mensaje AS c__mensaje, c.logo AS c__logo, c.api_token AS c__api_token, c.codigo_analytics AS c__codigo_analytics, c.correo_remitente AS c__correo_remitente, e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at, u.id AS u__id, u.usuario AS u__usuario, u.password AS u__password, u.rut AS u__rut, u.nombres AS u__nombres, u.apellido_paterno AS u__apellido_paterno, u.apellido_materno AS u__apellido_materno, u.email AS u__email, u.vacaciones AS u__vacaciones, u.cuenta_id AS u__cuenta_id, u.salt AS u__salt, u.open_id AS u__open_id, u.registrado AS u__registrado, u.reset_token AS u__reset_token, u.acceso_reportes AS u__acceso_reportes, u.created_at AS u__created_at, u.updated_at AS u__updated_at FROM tramite t LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN etapa e ON t.id = e.tramite_id LEFT JOIN usuario u ON e.usuario_id = u.id WHERE t.id IN ('3309', '3308', '3307', '3262', '3261', '3254', '3253', '3252', '2891', '2890', '2889', '2744', '2742', '2740') AND (u.id = ? AND e.pendiente = 0 AND c.nombre = ? AND p.nombre LIKE ?) ORDER BY t.updated_at desc
ERROR - 07-06-2017 03:03:57 --> SELECT t.id AS t__id FROM tramite t LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) AND c.nombre = ?) ORDER BY t.updated_at desc LIMIT 50
ERROR - 07-06-2017 03:13:58 --> Severity: Notice  --> Undefined variable: orderby /var/www/html/simple/application/models/tramite_table.php 88
ERROR - 07-06-2017 03:13:58 --> Severity: Notice  --> Undefined variable: direction /var/www/html/simple/application/models/tramite_table.php 88
ERROR - 07-06-2017 03:14:09 --> Severity: Notice  --> Undefined variable: orderby /var/www/html/simple/application/models/tramite_table.php 88
ERROR - 07-06-2017 03:14:09 --> Severity: Notice  --> Undefined variable: direction /var/www/html/simple/application/models/tramite_table.php 88
ERROR - 07-06-2017 03:15:11 --> Severity: Notice  --> Undefined variable: orderby /var/www/html/simple/application/models/tramite_table.php 88
ERROR - 07-06-2017 03:15:11 --> Severity: Notice  --> Undefined variable: direction /var/www/html/simple/application/models/tramite_table.php 88
ERROR - 07-06-2017 03:15:11 --> SELECT e.id AS e__id, t.id AS t__id, t.pendiente AS t__pendiente, t.proceso_id AS t__proceso_id, t.created_at AS t__created_at, t.updated_at AS t__updated_at, t.ended_at AS t__ended_at FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) AND (e.pendiente = 0 AND u.id = ?) AND c.nombre = ?) ORDER BY   LIMIT 50
ERROR - 07-06-2017 03:15:22 --> Severity: Notice  --> Undefined variable: orderby /var/www/html/simple/application/models/tramite_table.php 88
ERROR - 07-06-2017 03:15:22 --> Severity: Notice  --> Undefined variable: direction /var/www/html/simple/application/models/tramite_table.php 88
ERROR - 07-06-2017 03:15:22 --> SELECT e.id AS e__id, t.id AS t__id, t.pendiente AS t__pendiente, t.proceso_id AS t__proceso_id, t.created_at AS t__created_at, t.updated_at AS t__updated_at, t.ended_at AS t__ended_at FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) AND (e.pendiente = 0 AND u.id = ?) AND c.nombre = ?) ORDER BY   LIMIT 50
ERROR - 07-06-2017 03:20:26 --> SELECT e.id AS e__id, t.id AS t__id, t.pendiente AS t__pendiente, t.proceso_id AS t__proceso_id, t.created_at AS t__created_at, t.updated_at AS t__updated_at, t.ended_at AS t__ended_at FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) AND (e.pendiente = 0 AND u.id = ?) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 03:21:19 --> SELECT e.id AS e__id, e.tramite AS e__tramite FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) AND (e.pendiente = 0 AND u.id = ?) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 03:21:32 --> SELECT e.id AS e__id, e.tramite AS e__tramite FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 0 AND u.id = ?) AND c.nombre = ? AND e.updated_at >= ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 03:21:46 --> SELECT e.id AS e__id, e.tramite AS e__tramite FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 0 AND u.id = ?) AND c.nombre = ? AND e.updated_at >= ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 03:22:31 --> SELECT e.id AS e__id, e.tramite AS e__tramite FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 0 AND u.id = ?) AND c.nombre = ? AND e.updated_at >= ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 03:22:40 --> SELECT e.id AS e__id, e.tramite AS e__tramite FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 0 AND u.id = ?) AND c.nombre = ? AND e.updated_at >= ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 03:23:27 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 0 AND u.id = ?) AND c.nombre = ? AND e.updated_at >= ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 03:29:12 --> SELECT e.id AS e__id, t2.id AS t2__id, t2.pendiente AS t2__pendiente, t2.proceso_id AS t2__proceso_id, t2.created_at AS t2__created_at, t2.updated_at AS t2__updated_at, t2.ended_at AS t2__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 0 AND u.id = ?) AND c.nombre = ? AND e.updated_at >= ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 03:29:23 --> SELECT e.id AS e__id, t2.id AS t2__id, t2.pendiente AS t2__pendiente, t2.proceso_id AS t2__proceso_id, t2.created_at AS t2__created_at, t2.updated_at AS t2__updated_at, t2.ended_at AS t2__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 0 AND u.id = ?) AND c.nombre = ? AND e.updated_at >= ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 03:34:20 --> SELECT t.id AS t__id, t.pendiente AS t__pendiente, t.proceso_id AS t__proceso_id, t.created_at AS t__created_at, t.updated_at AS t__updated_at, t.ended_at AS t__ended_at, p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, c.id AS c__id, c.nombre AS c__nombre, c.nombre_largo AS c__nombre_largo, c.mensaje AS c__mensaje, c.logo AS c__logo, c.api_token AS c__api_token, c.codigo_analytics AS c__codigo_analytics, c.correo_remitente AS c__correo_remitente, e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at, u.id AS u__id, u.usuario AS u__usuario, u.password AS u__password, u.rut AS u__rut, u.nombres AS u__nombres, u.apellido_paterno AS u__apellido_paterno, u.apellido_materno AS u__apellido_materno, u.email AS u__email, u.vacaciones AS u__vacaciones, u.cuenta_id AS u__cuenta_id, u.salt AS u__salt, u.open_id AS u__open_id, u.registrado AS u__registrado, u.reset_token AS u__reset_token, u.acceso_reportes AS u__acceso_reportes, u.created_at AS u__created_at, u.updated_at AS u__updated_at FROM tramite t LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN etapa e ON t.id = e.tramite_id LEFT JOIN usuario u ON e.usuario_id = u.id WHERE t.id IN ('3309', '3308') AND (u.id = ? AND e.pendiente = 0 AND c.nombre = ? AND e.updated_at >= ?) ORDER BY t.updated_at desc
ERROR - 07-06-2017 03:34:33 --> SELECT t.id AS t__id, t.pendiente AS t__pendiente, t.proceso_id AS t__proceso_id, t.created_at AS t__created_at, t.updated_at AS t__updated_at, t.ended_at AS t__ended_at, p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, c.id AS c__id, c.nombre AS c__nombre, c.nombre_largo AS c__nombre_largo, c.mensaje AS c__mensaje, c.logo AS c__logo, c.api_token AS c__api_token, c.codigo_analytics AS c__codigo_analytics, c.correo_remitente AS c__correo_remitente, e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at, u.id AS u__id, u.usuario AS u__usuario, u.password AS u__password, u.rut AS u__rut, u.nombres AS u__nombres, u.apellido_paterno AS u__apellido_paterno, u.apellido_materno AS u__apellido_materno, u.email AS u__email, u.vacaciones AS u__vacaciones, u.cuenta_id AS u__cuenta_id, u.salt AS u__salt, u.open_id AS u__open_id, u.registrado AS u__registrado, u.reset_token AS u__reset_token, u.acceso_reportes AS u__acceso_reportes, u.created_at AS u__created_at, u.updated_at AS u__updated_at FROM tramite t LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN etapa e ON t.id = e.tramite_id LEFT JOIN usuario u ON e.usuario_id = u.id WHERE t.id IN ('3309', '3308', '3307', '3270', '3262', '3261', '3254', '3258', '3253', '3252', '2891', '3230', '3094', '3093', '3092', '3091', '3090', '3089', '3087', '3085', '3084', '3083', '3081', '3076', '2890', '2889', '2744', '2848', '2847', '2846', '2845', '2844', '2843', '2842', '2841', '2839', '2838', '2837', '2836', '2835', '2833', '2832', '2640', '2824', '2823', '2822', '2757', '2709', '2756', '2755') AND (u.id = ? AND e.pendiente = 0 AND c.nombre = ?) ORDER BY t.updated_at desc
ERROR - 07-06-2017 07:55:31 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/autenticacion.php 136
ERROR - 07-06-2017 07:57:50 --> SELECT t.id AS t__id, t.pendiente AS t__pendiente, t.proceso_id AS t__proceso_id, t.created_at AS t__created_at, t.updated_at AS t__updated_at, t.ended_at AS t__ended_at, p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, c.id AS c__id, c.nombre AS c__nombre, c.nombre_largo AS c__nombre_largo, c.mensaje AS c__mensaje, c.logo AS c__logo, c.api_token AS c__api_token, c.codigo_analytics AS c__codigo_analytics, c.correo_remitente AS c__correo_remitente FROM tramite t LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE (t.id IN (SELECT t.id AS t__id FROM etapa e WHERE (e.tramite_id = t.id AND e.pendiente = 0 AND e.usuario_id = 25043)) AND c.nombre = ?) ORDER BY t.updated_at desc LIMIT 50
ERROR - 07-06-2017 08:00:45 --> SELECT t.id AS t__id, t.pendiente AS t__pendiente, t.proceso_id AS t__proceso_id, t.created_at AS t__created_at, t.updated_at AS t__updated_at, t.ended_at AS t__ended_at, p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, c.id AS c__id, c.nombre AS c__nombre, c.nombre_largo AS c__nombre_largo, c.mensaje AS c__mensaje, c.logo AS c__logo, c.api_token AS c__api_token, c.codigo_analytics AS c__codigo_analytics, c.correo_remitente AS c__correo_remitente FROM tramite t LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE (t.id IN (SELECT t.id AS t__id FROM etapa e WHERE (e.tramite_id = t.id AND e.pendiente = 0 AND e.usuario_id = 25043)) AND c.nombre = ?) ORDER BY t.updated_at desc LIMIT 50*******85
ERROR - 07-06-2017 08:43:12 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 08:44:59 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 141
ERROR - 07-06-2017 08:45:15 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 08:45:48 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 142
ERROR - 07-06-2017 08:49:22 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 08:49:50 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 142
ERROR - 07-06-2017 08:50:43 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/tramites.php 192
ERROR - 07-06-2017 08:50:43 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/tramites.php 193
ERROR - 07-06-2017 08:50:56 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 08:50:56 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 142
ERROR - 07-06-2017 08:50:56 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/tramites.php 192
ERROR - 07-06-2017 08:50:56 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/tramites.php 193
ERROR - 07-06-2017 08:52:04 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 08:52:04 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 142
ERROR - 07-06-2017 08:52:04 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/tramites.php 192
ERROR - 07-06-2017 08:52:04 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/tramites.php 193
ERROR - 07-06-2017 08:52:14 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 08:52:14 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 142
ERROR - 07-06-2017 08:52:14 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = :usuario_id  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 08:52:14 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/tramites.php 192
ERROR - 07-06-2017 08:52:14 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/tramites.php 193
ERROR - 07-06-2017 09:03:37 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:03:52 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 142
ERROR - 07-06-2017 09:05:06 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = :usuario_id  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 09:05:50 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:06:14 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 142
ERROR - 07-06-2017 09:06:30 --> select count(distinct t.id)  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = :usuario_id  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde 
ERROR - 07-06-2017 09:07:21 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:07:21 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 142
ERROR - 07-06-2017 09:07:21 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = :usuario_id  and c.nombre = :cuenta  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 09:07:25 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:07:25 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 142
ERROR - 07-06-2017 09:07:25 --> select count(distinct t.id)  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = :usuario_id  and c.nombre = :cuenta 
ERROR - 07-06-2017 09:08:15 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:08:15 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 142
ERROR - 07-06-2017 09:08:15 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = :usuario_id  and c.nombre = :cuenta  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 09:08:20 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:08:20 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 142
ERROR - 07-06-2017 09:08:20 --> select count(distinct t.id)  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = :usuario_id  and c.nombre = :cuenta 
ERROR - 07-06-2017 09:11:01 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:11:01 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 142
ERROR - 07-06-2017 09:11:01 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = :usuario_id  and c.nombre = :cuenta  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:11:01 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:11:01 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 142
ERROR - 07-06-2017 09:11:01 --> select count(distinct t.id)  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = :usuario_id  and c.nombre = :cuenta 
ERROR - 07-06-2017 09:12:27 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:12:27 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 142
ERROR - 07-06-2017 09:12:27 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = :usuario_id  and c.nombre = :cuenta  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:15:47 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:15:47 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:15:47 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:15:47 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:15:47 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:15:47 --> select count(distinct t.id)  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta 
ERROR - 07-06-2017 09:15:53 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:15:53 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 142
ERROR - 07-06-2017 09:15:53 --> select count(distinct t.id)  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = :usuario_id  and c.nombre = :cuenta 
ERROR - 07-06-2017 09:16:17 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:16:17 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:16:17 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:18:23 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:18:23 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:18:23 --> select count(distinct t.id)  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta 
ERROR - 07-06-2017 09:19:02 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:19:02 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:19:02 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:19:02 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:19:02 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:19:02 --> select count(distinct t.id)  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde 
ERROR - 07-06-2017 09:19:10 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:19:10 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:19:10 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:19:10 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:19:10 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:19:10 --> select count(distinct t.id)  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde 
ERROR - 07-06-2017 09:19:14 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:19:14 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:19:14 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and t.id = :busqueda_id_tramite  and e.updated_at >= :busqueda_modificacion_desde  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:19:14 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:19:14 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:19:14 --> select count(distinct t.id)  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and t.id = :busqueda_id_tramite  and e.updated_at >= :busqueda_modificacion_desde 
ERROR - 07-06-2017 09:19:29 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:19:29 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:19:29 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:19:29 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:19:29 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:19:29 --> select count(distinct t.id)  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta 
ERROR - 07-06-2017 09:19:36 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:19:36 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:19:36 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and p.nombre LIKE :busqueda_nombre  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:19:36 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:19:36 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:19:36 --> select count(distinct t.id)  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and p.nombre LIKE :busqueda_nombre 
ERROR - 07-06-2017 09:19:49 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:19:49 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:19:49 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and tar.nombre LIKE :busqueda_etapa  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:19:49 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:19:49 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:19:49 --> select count(distinct t.id)  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and tar.nombre LIKE :busqueda_etapa 
ERROR - 07-06-2017 09:19:58 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:19:58 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:19:58 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and tar.nombre LIKE :busqueda_etapa  order by e.updated_at desc limit 50,50
ERROR - 07-06-2017 09:19:58 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:19:58 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:19:58 --> select count(distinct t.id)  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and tar.nombre LIKE :busqueda_etapa 
ERROR - 07-06-2017 09:20:05 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:20:05 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:20:05 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and tar.nombre LIKE :busqueda_etapa  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:20:05 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:20:05 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:20:05 --> select count(distinct t.id)  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and tar.nombre LIKE :busqueda_etapa 
ERROR - 07-06-2017 09:20:22 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:20:22 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:20:22 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and tar.acceso_modo = :acceso_modo and (tar.grupos_usuarios REGEXP  :busqueda_grupo_1
           OR
           tar.grupos_usuarios REGEXP :busqueda_grupo_2
           OR
           tar.grupos_usuarios REGEXP :busqueda_grupo_3
           OR
           tar.grupos_usuarios REGEXP :busqueda_grupo_4)  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:20:22 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:20:22 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:20:22 --> select count(distinct t.id)  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and tar.acceso_modo = :acceso_modo and (tar.grupos_usuarios REGEXP  :busqueda_grupo_1
           OR
           tar.grupos_usuarios REGEXP :busqueda_grupo_2
           OR
           tar.grupos_usuarios REGEXP :busqueda_grupo_3
           OR
           tar.grupos_usuarios REGEXP :busqueda_grupo_4) 
ERROR - 07-06-2017 09:20:27 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:20:27 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:20:27 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and tar.acceso_modo = :acceso_modo and (tar.grupos_usuarios REGEXP  :busqueda_grupo_1
           OR
           tar.grupos_usuarios REGEXP :busqueda_grupo_2
           OR
           tar.grupos_usuarios REGEXP :busqueda_grupo_3
           OR
           tar.grupos_usuarios REGEXP :busqueda_grupo_4)  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:20:27 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:20:27 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:20:27 --> select count(distinct t.id)  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and tar.acceso_modo = :acceso_modo and (tar.grupos_usuarios REGEXP  :busqueda_grupo_1
           OR
           tar.grupos_usuarios REGEXP :busqueda_grupo_2
           OR
           tar.grupos_usuarios REGEXP :busqueda_grupo_3
           OR
           tar.grupos_usuarios REGEXP :busqueda_grupo_4) 
ERROR - 07-06-2017 09:20:48 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:20:48 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:20:48 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:20:48 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:20:48 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 140
ERROR - 07-06-2017 09:20:48 --> select count(distinct t.id)  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta 
ERROR - 07-06-2017 09:26:41 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:26:41 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 163
ERROR - 07-06-2017 09:26:41 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and e.tramite_id  IN (
                                SELECT e.tramite_id
                                  FROM dato_seguimiento d1, dato_seguimiento d2
                                    WHERE
                                      d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                                      AND d1.etapa_id = d2.etapa_id
                                      AND d2.nombre = replace(d1.valor,'"', '')
                                      AND d2.valor = '36947729')  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:27:48 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:27:48 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 163
ERROR - 07-06-2017 09:27:48 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and e.tramite_id  IN (
                                SELECT e.tramite_id
                                  FROM dato_seguimiento d1, dato_seguimiento d2
                                    WHERE
                                      d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                                      AND d1.etapa_id = d2.etapa_id
                                      AND d2.nombre = replace(d1.valor,'"', '')
                                      AND d2.valor = '36947729')  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:27:59 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:28:19 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 163
ERROR - 07-06-2017 09:28:19 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and e.tramite_id  IN (
                                SELECT e.tramite_id
                                  FROM dato_seguimiento d1, dato_seguimiento d2
                                    WHERE
                                      d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                                      AND d1.etapa_id = d2.etapa_id
                                      AND d2.nombre = replace(d1.valor,'"', '')
                                      AND d2.valor = '36947729')  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:30:06 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:30:06 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 163
ERROR - 07-06-2017 09:30:06 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and e.tramite_id  IN (
                                SELECT e.tramite_id
                                  FROM dato_seguimiento d1, dato_seguimiento d2
                                    WHERE
                                      d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                                      AND d1.etapa_id = d2.etapa_id
                                      AND d2.nombre = replace(d1.valor,'"', '')
                                      AND d2.valor = '36947729')  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:30:18 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:32:56 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 163
ERROR - 07-06-2017 09:32:56 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and e.tramite_id  IN (
                                SELECT e.tramite_id
                                  FROM dato_seguimiento d1, dato_seguimiento d2
                                    WHERE
                                      d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                                      AND d1.etapa_id = d2.etapa_id
                                      AND d2.nombre = replace(d1.valor,'"', '')
                                      AND d2.valor = '36947729')  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:33:02 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:33:05 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 167
ERROR - 07-06-2017 09:33:05 --> select distinct t.id  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and e.tramite_id  IN (
                                SELECT e.tramite_id
                                  FROM dato_seguimiento d1, dato_seguimiento d2
                                    WHERE
                                      d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                                      AND d1.etapa_id = d2.etapa_id
                                      AND d2.nombre = replace(d1.valor,'"', '')
                                      AND d2.valor = '"36947729"')  order by e.updated_at desc limit 0,50
ERROR - 07-06-2017 09:33:05 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 09:33:08 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 167
ERROR - 07-06-2017 09:33:08 --> select count(distinct t.id)  from tramite t
                 left join proceso p on t.proceso_id = p.id
                 left join cuenta c on p.cuenta_id = c.id
                 left join etapa e on e.tramite_id = t.id
                 left join tarea tar on tar.id  =  e.tarea_id
                 where e.pendiente = 0 and e.usuario_id = 25043 and c.nombre = :cuenta  and e.tramite_id  IN (
                                SELECT e.tramite_id
                                  FROM dato_seguimiento d1, dato_seguimiento d2
                                    WHERE
                                      d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                                      AND d1.etapa_id = d2.etapa_id
                                      AND d2.nombre = replace(d1.valor,'"', '')
                                      AND d2.valor = '"36947729"') 
ERROR - 07-06-2017 09:33:26 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 09:33:26 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 09:33:38 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 09:33:38 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 09:33:45 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 09:33:45 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 09:33:52 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 09:33:52 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 09:33:59 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 09:33:59 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 09:34:06 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 09:34:06 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 09:34:14 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 09:34:14 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 09:34:18 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 09:34:18 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 09:34:22 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 09:34:22 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 10:35:25 --> Severity: Notice  --> Undefined variable: usuario /var/www/html/simple/application/controllers/portada.php 19
ERROR - 07-06-2017 10:35:25 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/portada.php 19
ERROR - 07-06-2017 10:37:01 --> Severity: Notice  --> Undefined variable: usuario /var/www/html/simple/application/controllers/portada.php 19
ERROR - 07-06-2017 10:37:01 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/portada.php 19
ERROR - 07-06-2017 10:56:11 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/autenticacion.php 136
ERROR - 07-06-2017 11:01:32 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:01:32 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:01:37 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:01:37 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:01:45 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:01:46 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:01:54 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:01:54 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:01:59 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:01:59 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:02:04 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:02:04 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:02:10 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:02:10 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:02:23 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:02:23 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:02:34 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:02:34 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:02:49 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:02:50 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:02:59 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:02:59 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:03:06 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:03:06 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:03:31 --> Severity: Notice  --> Indirect modification of overloaded property CampoError::$extra has no effect /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 11:03:31 --> Severity: Warning  --> Creating default object from empty value /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 11:03:31 --> Severity: Notice  --> Indirect modification of overloaded property CampoError::$extra has no effect /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 11:03:31 --> Severity: Warning  --> Creating default object from empty value /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 11:03:51 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 11:03:57 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 11:05:11 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:05:11 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:05:17 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ? AND t2.id = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 11:05:33 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:05:33 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:05:36 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:05:36 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:06:57 --> Severity: Notice  --> Indirect modification of overloaded property CampoError::$extra has no effect /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 11:06:57 --> Severity: Warning  --> Creating default object from empty value /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 11:06:57 --> Severity: Notice  --> Indirect modification of overloaded property CampoError::$extra has no effect /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 11:06:57 --> Severity: Warning  --> Creating default object from empty value /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 11:07:14 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:07:14 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:07:17 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:07:17 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:07:23 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:07:23 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:07:57 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE (t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"18871449\"")) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 11:07:59 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 11:08:18 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:08:18 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:08:23 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:08:23 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:08:27 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:08:27 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:08:31 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:08:32 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:08:51 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:08:51 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:09:23 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/autenticacion.php 136
ERROR - 07-06-2017 11:10:14 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:10:15 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:10:20 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:10:20 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:10:37 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:10:38 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:11:14 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:11:14 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:12:05 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:12:05 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:12:13 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:12:13 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:12:19 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:12:19 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:12:22 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:12:23 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:12:28 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:12:28 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:12:41 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:12:41 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:12:47 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:12:47 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:12:53 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:12:53 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:13:06 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:13:06 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:19:53 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:19:53 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:19:57 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:19:57 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:20:11 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:20:12 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:20:36 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:20:36 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:20:40 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:20:41 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 301
ERROR - 07-06-2017 11:24:48 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/autenticacion.php 136
ERROR - 07-06-2017 11:39:56 --> SELECT (SELECT COUNT(t.id) AS t__0 FROM tramite t WHERE (t.proceso_id = p.id) LIMIT 1) AS p__0 FROM proceso p LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE (p.nombre != ?) ORDER BY p.nombre asc
ERROR - 07-06-2017 11:41:25 --> SELECT p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, (SELECT COUNT(t.id) AS t__0 FROM tramite t WHERE (t.proceso_id = p.id) LIMIT 1) AS p__0 FROM proceso p LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE (p.nombre != ?) ORDER BY p.nombre asc
ERROR - 07-06-2017 11:42:58 --> SELECT p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, (SELECT COUNT(t.id) AS t__0 FROM tramite t WHERE (t.proceso_id = p.id) LIMIT 1) AS p__0 FROM proceso p LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE (p.nombre != ?) ORDER BY p.nombre asc
ERROR - 07-06-2017 11:43:04 --> SELECT p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, (SELECT COUNT(t.id) AS t__0 FROM tramite t WHERE (t.proceso_id = p.id) LIMIT 1) AS p__0 FROM proceso p LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE (p.nombre != ?) ORDER BY p.nombre asc
ERROR - 07-06-2017 11:43:18 --> SELECT p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, (SELECT COUNT(t.id) AS t__0 FROM tramite t WHERE (t.proceso_id = p.id) LIMIT 1) AS p__0 FROM proceso p LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE (p.nombre != ?) ORDER BY p.nombre asc
ERROR - 07-06-2017 11:43:22 --> SELECT p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, (SELECT COUNT(t.id) AS t__0 FROM tramite t WHERE (t.proceso_id = p.id) LIMIT 1) AS p__0 FROM proceso p LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE (p.nombre != ?) ORDER BY p.nombre asc
ERROR - 07-06-2017 11:43:30 --> SELECT p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, (SELECT COUNT(t.id) AS t__0 FROM tramite t WHERE (t.proceso_id = p.id) LIMIT 1) AS p__0 FROM proceso p LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE (p.nombre != ?) ORDER BY p.nombre asc
ERROR - 07-06-2017 11:43:35 --> SELECT p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, (SELECT COUNT(t.id) AS t__0 FROM tramite t WHERE (t.proceso_id = p.id) LIMIT 1) AS p__0 FROM proceso p LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE (p.nombre != ?) ORDER BY p.nombre asc
ERROR - 07-06-2017 11:53:28 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 308
ERROR - 07-06-2017 11:53:28 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee
                                 WHERE
                                   ee.tramite_id =  e.tramite_id and
                                   ee.usuario_id like  '%"45347661"')

              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,10
ERROR - 07-06-2017 11:53:30 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 308
ERROR - 07-06-2017 11:53:30 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee
                                 WHERE
                                   ee.tramite_id =  e.tramite_id and
                                   ee.usuario_id like  '%"45347661"')

              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 11:53:42 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 308
ERROR - 07-06-2017 11:53:42 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee
                                 WHERE
                                   ee.tramite_id =  e.tramite_id and
                                   ee.usuario_id like  '%"45347661"')

              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,10
ERROR - 07-06-2017 11:53:43 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 308
ERROR - 07-06-2017 11:53:43 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee
                                 WHERE
                                   ee.tramite_id =  e.tramite_id and
                                   ee.usuario_id like  '%"45347661"')

              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              where e.usuario_id is NULL
              and e.tramite_id IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 11:54:44 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 11:54:44 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee
                                 WHERE
                                   ee.tramite_id =  e.tramite_id and
                                   ee.usuario_id like  '%"45347661"'))

              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              where e.usuario_id is NULL
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee
                                 WHERE
                                   ee.tramite_id =  e.tramite_id and
                                   ee.usuario_id like  '%"45347661"'))
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,10
ERROR - 07-06-2017 11:54:45 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 11:54:45 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee
                                 WHERE
                                   ee.tramite_id =  e.tramite_id and
                                   ee.usuario_id like  '%"45347661"'))

              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              where e.usuario_id is NULL
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee
                                 WHERE
                                   ee.tramite_id =  e.tramite_id and
                                   ee.usuario_id like  '%"45347661"'))
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 11:55:06 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 11:55:06 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee
                                 WHERE
                                   ee.tramite_id =  e.tramite_id and
                                   ee.usuario_id like  '%"45347661"'))

              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              where e.usuario_id is NULL
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee
                                 WHERE
                                   ee.tramite_id =  e.tramite_id and
                                   ee.usuario_id like  '%"45347661"'))
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,10
ERROR - 07-06-2017 11:55:07 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 11:55:07 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee
                                 WHERE
                                   ee.tramite_id =  e.tramite_id and
                                   ee.usuario_id like  '%"45347661"'))

              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              where e.usuario_id is NULL
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee
                                 WHERE
                                   ee.tramite_id =  e.tramite_id and
                                   ee.usuario_id like  '%"45347661"'))
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 11:55:12 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 11:55:12 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 0,10
ERROR - 07-06-2017 11:55:12 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 11:55:12 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 11:55:16 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 11:55:16 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee
                                 WHERE
                                   ee.tramite_id =  e.tramite_id and
                                   ee.usuario_id like  '%"45347661"'))

              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              where e.usuario_id is NULL
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee
                                 WHERE
                                   ee.tramite_id =  e.tramite_id and
                                   ee.usuario_id like  '%"45347661"'))
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,10
ERROR - 07-06-2017 11:55:16 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 11:55:16 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee
                                 WHERE
                                   ee.tramite_id =  e.tramite_id and
                                   ee.usuario_id like  '%"45347661"'))

              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              where e.usuario_id is NULL
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee
                                 WHERE
                                   ee.tramite_id =  e.tramite_id and
                                   ee.usuario_id like  '%"45347661"'))
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 12:00:30 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:30 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee, usuario uu
                                 WHERE
                                   ee.usuario_id = uu.id and
                                   ee.tramite_id =  e.tramite_id and
                                   uu.usuario like  '%45347661'))

              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              where e.usuario_id is NULL
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee, usuario uu
                                 WHERE
                                   ee.usuario_id = uu.id and
                                   ee.tramite_id =  e.tramite_id and
                                   uu.usuario like  '%45347661'))
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,10
ERROR - 07-06-2017 12:00:31 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:31 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee, usuario uu
                                 WHERE
                                   ee.usuario_id = uu.id and
                                   ee.tramite_id =  e.tramite_id and
                                   uu.usuario like  '%45347661'))

              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              where e.usuario_id is NULL
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"45347661"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee, usuario uu
                                 WHERE
                                   ee.usuario_id = uu.id and
                                   ee.tramite_id =  e.tramite_id and
                                   uu.usuario like  '%45347661'))
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 12:00:38 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:38 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 0,10
ERROR - 07-06-2017 12:00:38 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:38 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:00:40 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:40 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 6560,10
ERROR - 07-06-2017 12:00:40 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:40 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:00:42 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:42 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 6550,10
ERROR - 07-06-2017 12:00:42 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:42 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:00:45 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:45 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 6540,10
ERROR - 07-06-2017 12:00:45 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:45 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:00:48 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:48 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 6530,10
ERROR - 07-06-2017 12:00:48 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:48 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:00:51 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:51 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 6520,10
ERROR - 07-06-2017 12:00:51 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:51 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:00:53 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:53 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 6500,10
ERROR - 07-06-2017 12:00:53 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:53 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:00:56 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:56 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 6480,10
ERROR - 07-06-2017 12:00:56 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:56 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:00:58 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:58 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 6460,10
ERROR - 07-06-2017 12:00:58 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:00:58 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:01:00 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:00 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 6440,10
ERROR - 07-06-2017 12:01:01 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:01 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:01:03 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:03 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 6420,10
ERROR - 07-06-2017 12:01:03 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:03 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:01:06 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:06 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 6400,10
ERROR - 07-06-2017 12:01:06 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:06 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:01:11 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:11 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"39375960"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee, usuario uu
                                 WHERE
                                   ee.usuario_id = uu.id and
                                   ee.tramite_id =  e.tramite_id and
                                   uu.usuario like  '%39375960'))

              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              where e.usuario_id is NULL
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"39375960"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee, usuario uu
                                 WHERE
                                   ee.usuario_id = uu.id and
                                   ee.tramite_id =  e.tramite_id and
                                   uu.usuario like  '%39375960'))
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,10
ERROR - 07-06-2017 12:01:11 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:11 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"39375960"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee, usuario uu
                                 WHERE
                                   ee.usuario_id = uu.id and
                                   ee.tramite_id =  e.tramite_id and
                                   uu.usuario like  '%39375960'))

              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              where e.usuario_id is NULL
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"39375960"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee, usuario uu
                                 WHERE
                                   ee.usuario_id = uu.id and
                                   ee.tramite_id =  e.tramite_id and
                                   uu.usuario like  '%39375960'))
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 12:01:26 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:26 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 0,10
ERROR - 07-06-2017 12:01:26 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:26 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:01:32 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:32 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 10,10
ERROR - 07-06-2017 12:01:33 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:33 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:01:35 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:35 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 20,10
ERROR - 07-06-2017 12:01:35 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:35 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:01:38 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:38 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 30,10
ERROR - 07-06-2017 12:01:38 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:38 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:01:41 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:41 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 40,10
ERROR - 07-06-2017 12:01:41 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:41 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:01:43 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:43 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 50,10
ERROR - 07-06-2017 12:01:43 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:43 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:01:46 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:46 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 60,10
ERROR - 07-06-2017 12:01:46 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:46 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:01:49 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:49 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 70,10
ERROR - 07-06-2017 12:01:49 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:49 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:01:51 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:51 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a  order by a.updated_at desc limit 90,10
ERROR - 07-06-2017 12:01:52 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:52 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta 
                )) as a 
ERROR - 07-06-2017 12:01:57 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:57 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"42310564"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee, usuario uu
                                 WHERE
                                   ee.usuario_id = uu.id and
                                   ee.tramite_id =  e.tramite_id and
                                   uu.usuario like  '%42310564'))

              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              where e.usuario_id is NULL
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"42310564"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee, usuario uu
                                 WHERE
                                   ee.usuario_id = uu.id and
                                   ee.tramite_id =  e.tramite_id and
                                   uu.usuario like  '%42310564'))
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,10
ERROR - 07-06-2017 12:01:57 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:01:57 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"42310564"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee, usuario uu
                                 WHERE
                                   ee.usuario_id = uu.id and
                                   ee.tramite_id =  e.tramite_id and
                                   uu.usuario like  '%42310564'))

              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 377819
              where e.usuario_id is NULL
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"42310564"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee, usuario uu
                                 WHERE
                                   ee.usuario_id = uu.id and
                                   ee.tramite_id =  e.tramite_id and
                                   uu.usuario like  '%42310564'))
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 12:03:12 --> Severity: Notice  --> Indirect modification of overloaded property CampoError::$extra has no effect /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 12:03:12 --> Severity: Warning  --> Creating default object from empty value /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 12:03:12 --> Severity: Notice  --> Indirect modification of overloaded property CampoError::$extra has no effect /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 12:03:12 --> Severity: Warning  --> Creating default object from empty value /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 12:03:54 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:03:54 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.tramite_id = :busqueda_id_tramite 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and e.tramite_id = :busqueda_id_tramite 
                )) as a  order by a.updated_at desc limit 0,10
ERROR - 07-06-2017 12:03:54 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:03:54 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.tramite_id = :busqueda_id_tramite 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and e.tramite_id = :busqueda_id_tramite 
                )) as a 
ERROR - 07-06-2017 12:04:00 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:04:00 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and t.nombre LIKE :busqueda_etapa

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and t.nombre LIKE :busqueda_etapa
                )) as a  order by a.updated_at desc limit 0,10
ERROR - 07-06-2017 12:04:00 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:04:00 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and t.nombre LIKE :busqueda_etapa

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and t.nombre LIKE :busqueda_etapa
                )) as a 
ERROR - 07-06-2017 12:04:14 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:04:14 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
                )) as a  order by a.updated_at desc limit 0,10
ERROR - 07-06-2017 12:04:14 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:04:14 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
                )) as a 
ERROR - 07-06-2017 12:04:17 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:04:17 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
                )) as a  order by a.updated_at desc limit 20,10
ERROR - 07-06-2017 12:04:17 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:04:17 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
                )) as a 
ERROR - 07-06-2017 12:04:20 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:04:20 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
                )) as a  order by a.updated_at desc limit 30,10
ERROR - 07-06-2017 12:04:20 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:04:20 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
                )) as a 
ERROR - 07-06-2017 12:04:23 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:04:23 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
                )) as a  order by a.updated_at desc limit 40,10
ERROR - 07-06-2017 12:04:23 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:04:23 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
                )) as a 
ERROR - 07-06-2017 12:04:27 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:04:27 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
                )) as a  order by a.updated_at desc limit 0,10
ERROR - 07-06-2017 12:04:27 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:04:27 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and e.updated_at >= :busqueda_modificacion_desde  and e.updated_at <=  :busqueda_modificacion_hasta 
                )) as a 
ERROR - 07-06-2017 12:04:50 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:04:50 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and p.nombre LIKE :busqueda_nombre

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and p.nombre LIKE :busqueda_nombre
                )) as a  order by a.updated_at desc limit 0,10
ERROR - 07-06-2017 12:04:50 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:04:50 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and p.nombre LIKE :busqueda_nombre

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and p.nombre LIKE :busqueda_nombre
                )) as a 
ERROR - 07-06-2017 12:04:53 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:04:53 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and p.nombre LIKE :busqueda_nombre

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and p.nombre LIKE :busqueda_nombre
                )) as a  order by a.updated_at desc limit 6450,10
ERROR - 07-06-2017 12:04:53 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:04:53 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and p.nombre LIKE :busqueda_nombre

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and p.nombre LIKE :busqueda_nombre
                )) as a 
ERROR - 07-06-2017 12:04:57 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:04:57 --> select distinct id from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and p.nombre LIKE :busqueda_nombre

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and p.nombre LIKE :busqueda_nombre
                )) as a  order by a.updated_at desc limit 0,10
ERROR - 07-06-2017 12:04:57 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:04:57 --> select count(distinct id) from ( select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
                where e.usuario_id is NULL
                and t.acceso_modo="grupos_usuarios"
                and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta  and p.nombre LIKE :busqueda_nombre

                union (
                select e.id as id, e.updated_at
                from etapa e
                join tarea t on t.id=e.tarea_id
                join proceso p on p.id=t.proceso_id
                join cuenta c on c.id = p.cuenta_id
                join usuario u on u.id = 377819
                where e.usuario_id is NULL
                and (  (t.acceso_modo="publico")
                       or (t.acceso_modo="registrados" and u.registrado=1)
                       or (t.acceso_modo = "claveunica" AND u.open_id=1)
                    )
                  and c.nombre = :cuenta  and p.nombre LIKE :busqueda_nombre
                )) as a 
ERROR - 07-06-2017 12:11:33 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"18871449\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like %18871449))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 10
ERROR - 07-06-2017 12:11:55 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"18871449\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like %18871449))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 10
ERROR - 07-06-2017 12:12:38 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"18871449\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%18871449"))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 10
ERROR - 07-06-2017 12:12:45 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 10
ERROR - 07-06-2017 12:12:49 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"42310564\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%42310564"))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 10
ERROR - 07-06-2017 12:12:53 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 10
ERROR - 07-06-2017 12:13:21 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id LEFT JOIN proceso p2 ON t.proceso_id = p2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"42310564\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%42310564"))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ? AND p2.nombre LIKE ?) ORDER BY e.updated_at desc LIMIT 10
ERROR - 07-06-2017 12:13:26 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id LEFT JOIN proceso p2 ON t.proceso_id = p2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"42310564\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%42310564"))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ? AND t.id = ? AND p2.nombre LIKE ?) ORDER BY e.updated_at desc LIMIT 10
ERROR - 07-06-2017 12:13:29 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id LEFT JOIN proceso p2 ON t.proceso_id = p2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"42310564\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%42310564"))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ? AND t.id = ? AND p2.nombre LIKE ?) ORDER BY e.updated_at desc LIMIT 10
ERROR - 07-06-2017 12:13:37 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ? AND t2.id = ?) ORDER BY e.updated_at desc LIMIT 10
ERROR - 07-06-2017 12:13:44 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 10
ERROR - 07-06-2017 12:19:05 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/autenticacion.php 136
ERROR - 07-06-2017 12:19:16 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:19:16 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:19:19 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:19:19 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:19:27 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:19:27 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:20:34 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:20:34 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:21:10 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:21:10 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:21:16 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:21:16 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:21:22 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:21:22 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:21:28 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:21:28 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:21:45 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:21:45 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:29:38 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%7675675""))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 12:30:17 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%7675675""))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 12:32:46 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%7675675"))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 12:32:53 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%7675675"))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 12:33:01 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%7675675"))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 12:34:30 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%7675675" AND (NOT (exists (SELECT eee.id FROM Etapa eee where eee.tramite_id = ee.tramite_id and eee.id < ee.id)))))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 12:34:38 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 12:34:42 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%7675675" AND (NOT (exists (SELECT eee.id FROM Etapa eee where eee.tramite_id = ee.tramite_id and eee.id < ee.id)))))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 12:34:50 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%7675675" AND (NOT (exists (SELECT eee.id FROM Etapa eee where eee.tramite_id = ee.tramite_id and eee.id < ee.id)))))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 12:37:34 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%7675675" AND (NOT (exists (SELECT eee.id FROM etapa eee where eee.tramite_id = ee.tramite_id and eee.id < ee.id)))))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 12:37:42 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%7675675" AND (NOT (exists (SELECT eee.id FROM etapa eee where eee.tramite_id = ee.tramite_id and eee.id < ee.id)))))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 12:39:15 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 272
ERROR - 07-06-2017 12:39:15 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:40:47 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 272
ERROR - 07-06-2017 12:40:47 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:41:04 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 272
ERROR - 07-06-2017 12:41:04 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 12:42:43 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"7675675\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%7675675" AND e2.id IN (SELECT e3.id AS e3__id FROM etapa e3 WHERE (e3.tramite_id = e2.tramite_id AND e3.id < e2.id))))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 12:42:53 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 12:42:58 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50 OFFSET 50
ERROR - 07-06-2017 12:43:00 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 13:02:07 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%36947729" AND e2.id IN (SELECT e3.id AS e3__id FROM etapa e3 WHERE (e3.tramite_id = e2.tramite_id AND e3.id < e2.id))))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 13:02:53 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%36947729" AND e2.id IN (SELECT e3.id AS e3__id FROM etapa e3 WHERE (e3.tramite_id = e2.tramite_id AND e3.id < e2.id))))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 13:04:13 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"333222111\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%333222111" AND e2.id IN (SELECT e3.id AS e3__id FROM etapa e3 WHERE (e3.tramite_id = e2.tramite_id AND e3.id < e2.id))))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 13:04:17 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 13:10:59 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 13:10:59 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 13:11:04 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 13:11:04 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 13:11:12 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 13:11:12 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 13:12:06 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 13:12:06 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 13:12:08 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 13:12:08 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 13:12:08 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 13:12:08 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 13:12:08 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 13:12:08 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 13:12:13 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 13:12:13 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 315
ERROR - 07-06-2017 13:14:39 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 321
ERROR - 07-06-2017 13:14:39 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 321
ERROR - 07-06-2017 13:15:19 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 321
ERROR - 07-06-2017 13:15:19 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 321
ERROR - 07-06-2017 13:17:09 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 321
ERROR - 07-06-2017 13:17:09 --> select distinct id from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"36947729"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee, usuario uu
                                 WHERE
                                   ee.usuario_id = uu.id and
                                   ee.tramite_id =  e.tramite_id and
                                   trim(uu.usuario) like  '%36947729'
                                   and not exists (select 1 from etapa eee where eee.tramite_id = ee.tramite_id and eee.id < ee.id
                                   and not exists (select 1 from dato_seguimiento d3 where d3.nombre = CONCAT("documento_tramite_inicial__e", ee.tramite_id) )
                                   ) ))

              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"36947729"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee, usuario uu
                                 WHERE
                                   ee.usuario_id = uu.id and
                                   ee.tramite_id =  e.tramite_id and
                                   trim(uu.usuario) like  '%36947729'
                                   and not exists (select 1 from etapa eee where eee.tramite_id = ee.tramite_id and eee.id < ee.id
                                   and not exists (select 1 from dato_seguimiento d3 where d3.nombre = CONCAT("documento_tramite_inicial__e", ee.tramite_id) )
                                   ) ))
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a  order by a.updated_at desc limit 0,50
ERROR - 07-06-2017 13:17:09 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 321
ERROR - 07-06-2017 13:17:09 --> select count(distinct id) from ( select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              join grupo_usuarios_has_usuario gu on gu.usuario_id=u.id
              where e.usuario_id is NULL
              and t.acceso_modo="grupos_usuarios"
              and FIND_IN_SET(gu.grupo_usuarios_id , t.grupos_usuarios)  and c.nombre = :cuenta 
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"36947729"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee, usuario uu
                                 WHERE
                                   ee.usuario_id = uu.id and
                                   ee.tramite_id =  e.tramite_id and
                                   trim(uu.usuario) like  '%36947729'
                                   and not exists (select 1 from etapa eee where eee.tramite_id = ee.tramite_id and eee.id < ee.id
                                   and not exists (select 1 from dato_seguimiento d3 where d3.nombre = CONCAT("documento_tramite_inicial__e", ee.tramite_id) )
                                   ) ))

              union (
              select e.id as id, e.updated_at
              from etapa e
              join tarea t on t.id=e.tarea_id
              join proceso p on p.id=t.proceso_id
              join cuenta c on c.id = p.cuenta_id
              join usuario u on u.id = 25043
              where e.usuario_id is NULL
              and (e.tramite_id  IN (
                       SELECT e.tramite_id
                       FROM dato_seguimiento d1, dato_seguimiento d2
                       WHERE
                        d1.nombre = CONCAT("documento_tramite_inicial__e", e.tramite_id)
                        AND d1.etapa_id = d2.etapa_id
                        AND d2.nombre = replace(d1.valor,'"', '')
                        AND d2.valor = '"36947729"')
                        or e.tramite_id  IN (
                                 SELECT ee.tramite_id
                                 FROM etapa ee, usuario uu
                                 WHERE
                                   ee.usuario_id = uu.id and
                                   ee.tramite_id =  e.tramite_id and
                                   trim(uu.usuario) like  '%36947729'
                                   and not exists (select 1 from etapa eee where eee.tramite_id = ee.tramite_id and eee.id < ee.id
                                   and not exists (select 1 from dato_seguimiento d3 where d3.nombre = CONCAT("documento_tramite_inicial__e", ee.tramite_id) )
                                   ) ))
              and ((t.acceso_modo="publico")
              or (t.acceso_modo="registrados" and u.registrado=1)
              or (t.acceso_modo = "claveunica" AND u.open_id=1))
                and c.nombre = :cuenta 
              )) as a 
ERROR - 07-06-2017 13:18:38 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:18:38 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:18:47 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:18:47 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:18:53 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:18:53 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:19:01 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:19:01 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:19:34 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:19:34 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:19:34 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:19:34 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:19:42 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:19:42 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:21:40 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:21:40 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:22:32 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:22:32 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:22:39 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:22:39 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:23:35 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%36947729" AND e2.id NOT IN (SELECT e3.id AS e3__id FROM etapa e3 WHERE (e3.tramite_id = e2.tramite_id AND e3.id < e2.id))))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 13:24:58 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND u2.usuario like "%36947729" AND e2.id IN (SELECT MIN(e3.id) AS e3__0 FROM etapa e3 WHERE (e3.tramite_id = e2.tramite_id))))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 13:25:13 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 13:25:14 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 13:25:41 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND TRIM(u2.usuario) like "%36947729" AND e2.id IN (SELECT MIN(e3.id) AS e3__0 FROM etapa e3 WHERE (e3.tramite_id = e2.tramite_id))))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 13:26:01 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND TRIM(u2.usuario) like "%36947729" AND e2.id IN (SELECT MIN(e3.id) AS e3__0 FROM etapa e3 WHERE (e3.tramite_id = e2.tramite_id))))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 13:29:03 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 13:29:16 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:29:16 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 314
ERROR - 07-06-2017 13:39:26 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:39:26 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:39:33 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:39:33 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:39:48 --> SELECT e.id AS e__id FROM etapa e LEFT JOIN tramite t ON e.tramite_id = t.id LEFT JOIN proceso p ON t.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tarea t2 ON e.tarea_id = t2.id WHERE ((t.id IN (SELECT t.id AS t__id FROM dato_seguimiento d, dato_seguimiento d2 WHERE (d.nombre = CONCAT("documento_tramite_inicial__e", t.id) AND d.etapa_id = d2.etapa_id AND d2.nombre = replace(d.valor, '"', '') AND d2.valor = "\"36947729\"")) OR t.id IN (SELECT e2.tramite_id AS e2__tramite_id FROM etapa e2 LEFT JOIN usuario u2 ON e2.usuario_id = u2.id WHERE (e2.tramite_id = e.tramite_id AND TRIM(u2.usuario) like "%36947729" AND e2.id IN (SELECT MIN(e3.id) AS e3__0 FROM etapa e3 WHERE (e3.tramite_id = e2.tramite_id))))) AND (e.pendiente = 1 AND u.id = ?) AND 1 != (t2.activacion="no" OR (t2.activacion="entre_fechas" AND ((t2.activacion_inicio IS NOT NULL AND t2.activacion_inicio>NOW()) OR (t2.activacion_fin IS NOT NULL AND NOW()>t2.activacion_fin)))) AND c.nombre = ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 13:43:46 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN tarea t3 ON e.tarea_id = t3.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ? AND t3.nombre LIKE ?) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 13:43:58 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:43:58 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:44:03 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:44:03 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:44:06 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:44:06 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:44:20 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:44:20 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:44:26 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:44:26 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:44:30 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:44:30 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:44:39 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:44:39 --> Severity: Warning  --> fsockopen(): unable to connect to localhost:9312 (Connection refused) /var/www/html/simple/application/libraries/sphinxclient.php 591
ERROR - 07-06-2017 13:45:04 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:45:10 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:45:20 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:45:24 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:45:24 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:45:50 --> Severity: Notice  --> Indirect modification of overloaded property CampoError::$extra has no effect /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 13:45:50 --> Severity: Warning  --> Creating default object from empty value /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 13:45:52 --> Web service SOAP Body:<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:con="http://tempuri.org/wsorg/Consultas"><soap:Header/>
               <soap:Body>
                  <con:ObtenerDatosTransaccion>
                     <con:pIdSolicitud>869</con:pIdSolicitud>
                     <con:pIdTramite>188</con:pIdTramite>
                     <con:pClave>123</con:pClave>
                  </con:ObtenerDatosTransaccion>
               </soap:Body>
            </soap:Envelope> - response:<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<HTML><HEAD><TITLE>The page requires a client certificate</TITLE>
<META HTTP-EQUIV="Content-Type" Content="text/html; charset=Windows-1252">
<STYLE type="text/css">
  BODY { font: 8pt/12pt verdana }
  H1 { font: 13pt/15pt verdana }
  H2 { font: 8pt/12pt verdana }
  A:link { color: red }
  A:visited { color: maroon }
</STYLE>
</HEAD><BODY><TABLE width=500 border=0 cellspacing=10><TR><TD>

<h1>The page requires a client certificate</h1>
The page you are attempting to access requires your browser to have a Secure Sockets Layer (SSL) client certificate that the Web server will recognize. The client certificate is used for identifying you as a valid user of the resource.
<hr>
<p>Please try the following:</p>
<ul>
<li>Contact the Web site administrator if you believe you should be able to view this directory or page without a client certificate, or to obtain a client certificate.</li>
<li>If you already have a client certificate, use your Web browser's security features to ensure that your client certificate is installed properly. (Some Web browsers refer
 to client certificates as browser or personal certificates.)</li>
</ul>
<h2>HTTP Error 403.7 - Forbidden: SSL client certificate is required.<br>Internet Information Services (IIS)</h2>
<hr>
<p>Technical Information (for support personnel)</p>
<ul>
<li>Go to <a href="http://go.microsoft.com/fwlink/?linkid=8180">Microsoft Product Support Services</a> and perform a title search for the words <b>HTTP</b> and <b>403</b>.</li>
<li>Open <b>IIS Help</b>, which is accessible in IIS Manager (inetmgr),
 and search for topics titled <b>About Certificates</b>, <b>Using Certificate Trust Lists</b>, <b>Enabling Client Certificates</b>, and <b>About Custom Error Messages</b>.</li>
</ul>

</TD></TR></TABLE></BODY></HTML>

	


 httpcode:403 curlerrno:0 curlerror:NSS: client certificate not found: /var/www/html/simple/uploads/pasarela/
ERROR - 07-06-2017 13:46:00 --> Severity: Notice  --> Indirect modification of overloaded property CampoError::$extra has no effect /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 13:46:00 --> Severity: Warning  --> Creating default object from empty value /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 13:46:01 --> Web service SOAP Body:<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:con="http://tempuri.org/wsorg/Consultas"><soap:Header/>
               <soap:Body>
                  <con:ObtenerDatosTransaccion>
                     <con:pIdSolicitud>879</con:pIdSolicitud>
                     <con:pIdTramite>188</con:pIdTramite>
                     <con:pClave>123</con:pClave>
                  </con:ObtenerDatosTransaccion>
               </soap:Body>
            </soap:Envelope> - response:<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<HTML><HEAD><TITLE>The page requires a client certificate</TITLE>
<META HTTP-EQUIV="Content-Type" Content="text/html; charset=Windows-1252">
<STYLE type="text/css">
  BODY { font: 8pt/12pt verdana }
  H1 { font: 13pt/15pt verdana }
  H2 { font: 8pt/12pt verdana }
  A:link { color: red }
  A:visited { color: maroon }
</STYLE>
</HEAD><BODY><TABLE width=500 border=0 cellspacing=10><TR><TD>

<h1>The page requires a client certificate</h1>
The page you are attempting to access requires your browser to have a Secure Sockets Layer (SSL) client certificate that the Web server will recognize. The client certificate is used for identifying you as a valid user of the resource.
<hr>
<p>Please try the following:</p>
<ul>
<li>Contact the Web site administrator if you believe you should be able to view this directory or page without a client certificate, or to obtain a client certificate.</li>
<li>If you already have a client certificate, use your Web browser's security features to ensure that your client certificate is installed properly. (Some Web browsers refer
 to client certificates as browser or personal certificates.)</li>
</ul>
<h2>HTTP Error 403.7 - Forbidden: SSL client certificate is required.<br>Internet Information Services (IIS)</h2>
<hr>
<p>Technical Information (for support personnel)</p>
<ul>
<li>Go to <a href="http://go.microsoft.com/fwlink/?linkid=8180">Microsoft Product Support Services</a> and perform a title search for the words <b>HTTP</b> and <b>403</b>.</li>
<li>Open <b>IIS Help</b>, which is accessible in IIS Manager (inetmgr),
 and search for topics titled <b>About Certificates</b>, <b>Using Certificate Trust Lists</b>, <b>Enabling Client Certificates</b>, and <b>About Custom Error Messages</b>.</li>
</ul>

</TD></TR></TABLE></BODY></HTML>

	


 httpcode:403 curlerrno:0 curlerror:NSS: client certificate not found: /var/www/html/simple/uploads/pasarela/
ERROR - 07-06-2017 13:46:29 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:46:29 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:46:34 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:46:34 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:46:39 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:46:39 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:46:50 --> Severity: Notice  --> Undefined offset: 2 /var/www/html/simple/application/models/widget_tramite_etapas.php 41
ERROR - 07-06-2017 13:48:44 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:48:44 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:49:21 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN tarea t3 ON e.tarea_id = t3.id WHERE (e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ? AND (t3.acceso_modo = ? AND (t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ? OR t3.grupos_usuarios REGEXP ?)) ORDER BY e.updated_at desc LIMIT 50
ERROR - 07-06-2017 13:50:10 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:50:10 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:50:28 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:50:28 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:50:36 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:50:36 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:50:41 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:50:41 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:50:48 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:50:48 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:50:52 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:50:52 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:50:58 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:50:58 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:51:05 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:51:05 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:51:10 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:51:10 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 13:54:29 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 13:54:29 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 167
ERROR - 07-06-2017 13:54:29 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 13:54:29 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 167
ERROR - 07-06-2017 13:54:32 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 13:54:32 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 167
ERROR - 07-06-2017 13:54:32 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 13:54:32 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 167
ERROR - 07-06-2017 13:54:40 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 13:54:40 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 167
ERROR - 07-06-2017 13:54:40 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 13:54:40 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 167
ERROR - 07-06-2017 13:55:34 --> Severity: Notice  --> Undefined property: stdClass::$firmar /var/www/html/simple/application/controllers/etapas.php 782
ERROR - 07-06-2017 13:56:11 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 13:56:11 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 167
ERROR - 07-06-2017 13:56:11 --> Severity: Notice  --> Undefined variable: opciones_busqueda /var/www/html/simple/application/models/tramite_table.php 76
ERROR - 07-06-2017 13:56:11 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/tramite_table.php 167
ERROR - 07-06-2017 13:56:24 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 462
ERROR - 07-06-2017 13:56:24 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 465
ERROR - 07-06-2017 13:56:24 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 465
ERROR - 07-06-2017 13:56:24 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 465
ERROR - 07-06-2017 13:56:24 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 465
ERROR - 07-06-2017 13:56:24 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 465
ERROR - 07-06-2017 13:56:24 --> Severity: Notice  --> Trying to get property of non-object /var/www/html/simple/application/controllers/etapas.php 465
ERROR - 07-06-2017 13:58:22 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN tarea t3 ON e.tarea_id = t3.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ? AND t3.nombre LIKE ?) ORDER BY e.updated_at desc LIMIT 10
ERROR - 07-06-2017 13:59:44 --> SELECT e.id AS e__id, e.tarea_id AS e__tarea_id, e.tramite_id AS e__tramite_id, e.usuario_id AS e__usuario_id, e.pendiente AS e__pendiente, e.etapa_ancestro_split_id AS e__etapa_ancestro_split_id, e.vencimiento_at AS e__vencimiento_at, e.created_at AS e__created_at, e.updated_at AS e__updated_at, e.ended_at AS e__ended_at FROM etapa e LEFT JOIN tarea t ON e.tarea_id = t.id LEFT JOIN usuario u ON e.usuario_id = u.id LEFT JOIN tramite t2 ON e.tramite_id = t2.id LEFT JOIN proceso p ON t2.proceso_id = p.id LEFT JOIN cuenta c ON p.cuenta_id = c.id LEFT JOIN tarea t3 ON e.tarea_id = t3.id WHERE ((e.pendiente = 1 AND u.id = ?) AND 1 != (t.activacion="no" OR (t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin)))) AND c.nombre = ? AND t3.nombre LIKE ?) ORDER BY e.updated_at desc LIMIT 10
ERROR - 07-06-2017 14:11:19 --> SELECT p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, (SELECT COUNT(t.id) AS t__0 FROM tramite t WHERE (t.proceso_id = p.id) LIMIT 1) AS p__0 FROM proceso p LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE (p.nombre != ?) ORDER BY p.nombre asc
ERROR - 07-06-2017 14:11:20 --> SELECT p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, (SELECT COUNT(t.id) AS t__0 FROM tramite t WHERE (t.proceso_id = p.id) LIMIT 1) AS p__0 FROM proceso p LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE (p.nombre != ?) ORDER BY p.nombre asc
ERROR - 07-06-2017 14:12:06 --> SELECT p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, (SELECT COUNT(t.id) AS t__0 FROM tramite t WHERE (t.proceso_id = p.id) LIMIT 1) AS p__0 FROM proceso p LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE (c.id = ? AND p.nombre != ?) ORDER BY p.nombre asc
ERROR - 07-06-2017 14:12:12 --> SELECT p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, (SELECT COUNT(t.id) AS t__0 FROM tramite t WHERE (t.proceso_id = p.id) LIMIT 1) AS p__0 FROM proceso p LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE (c.id = ? AND p.nombre != ?) ORDER BY p.nombre asc
ERROR - 07-06-2017 14:12:16 --> SELECT p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, (SELECT COUNT(t.id) AS t__0 FROM tramite t WHERE (t.proceso_id = p.id) LIMIT 1) AS p__0 FROM proceso p LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE (c.id = ? AND p.nombre != ?) ORDER BY p.nombre asc
ERROR - 07-06-2017 14:12:20 --> SELECT p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, (SELECT COUNT(t.id) AS t__0 FROM tramite t WHERE (t.proceso_id = p.id) LIMIT 1) AS p__0 FROM proceso p LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE (c.id = ? AND p.nombre != ?) ORDER BY p.nombre asc
ERROR - 07-06-2017 14:12:25 --> SELECT p.id AS p__id, p.nombre AS p__nombre, p.width AS p__width, p.height AS p__height, p.cuenta_id AS p__cuenta_id, (SELECT COUNT(t.id) AS t__0 FROM tramite t WHERE (t.proceso_id = p.id) LIMIT 1) AS p__0 FROM proceso p LEFT JOIN cuenta c ON p.cuenta_id = c.id WHERE (c.id = ? AND p.nombre != ?) ORDER BY p.nombre asc
ERROR - 07-06-2017 14:14:22 --> Severity: Notice  --> Indirect modification of overloaded property CampoError::$extra has no effect /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 14:14:22 --> Severity: Warning  --> Creating default object from empty value /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 14:14:42 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1713
ERROR - 07-06-2017 14:14:42 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1745
ERROR - 07-06-2017 14:14:42 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1765
ERROR - 07-06-2017 14:14:42 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1885
ERROR - 07-06-2017 14:14:42 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1905
ERROR - 07-06-2017 14:14:42 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1915
ERROR - 07-06-2017 14:14:43 --> Severity: Notice  --> Indirect modification of overloaded property CampoError::$extra has no effect /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 14:14:43 --> Severity: Warning  --> Creating default object from empty value /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 14:14:44 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1105
ERROR - 07-06-2017 14:14:44 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1137
ERROR - 07-06-2017 14:14:44 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1157
ERROR - 07-06-2017 14:14:44 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1277
ERROR - 07-06-2017 14:14:44 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1297
ERROR - 07-06-2017 14:14:44 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1307
ERROR - 07-06-2017 14:15:25 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 14:15:25 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 14:15:32 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 14:15:32 --> Severity: Notice  --> Indirect modification of overloaded property Cuenta::$nombre has no effect /var/www/html/simple/application/models/etapa_table.php 313
ERROR - 07-06-2017 14:15:40 --> Severity: Notice  --> Indirect modification of overloaded property CampoError::$extra has no effect /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 14:15:40 --> Severity: Warning  --> Creating default object from empty value /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 14:15:43 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1713
ERROR - 07-06-2017 14:15:43 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1745
ERROR - 07-06-2017 14:15:43 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1765
ERROR - 07-06-2017 14:15:43 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1885
ERROR - 07-06-2017 14:15:43 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1905
ERROR - 07-06-2017 14:15:43 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1915
ERROR - 07-06-2017 14:16:12 --> Severity: Notice  --> Indirect modification of overloaded property CampoError::$extra has no effect /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 14:16:12 --> Severity: Warning  --> Creating default object from empty value /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 14:16:14 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1105
ERROR - 07-06-2017 14:16:14 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1137
ERROR - 07-06-2017 14:16:14 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1157
ERROR - 07-06-2017 14:16:14 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1277
ERROR - 07-06-2017 14:16:14 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1297
ERROR - 07-06-2017 14:16:14 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1307
ERROR - 07-06-2017 14:16:23 --> Severity: Notice  --> Indirect modification of overloaded property CampoError::$extra has no effect /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 14:16:23 --> Severity: Warning  --> Creating default object from empty value /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 14:16:26 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1713
ERROR - 07-06-2017 14:16:27 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1745
ERROR - 07-06-2017 14:16:27 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1765
ERROR - 07-06-2017 14:16:27 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1885
ERROR - 07-06-2017 14:16:27 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1905
ERROR - 07-06-2017 14:16:27 --> Severity: Notice  --> Undefined offset: 0 /var/www/html/simple/application/controllers/pagos.php 1915
ERROR - 07-06-2017 14:16:31 --> Severity: Notice  --> Indirect modification of overloaded property CampoError::$extra has no effect /var/www/html/simple/application/models/campo_error.php 22
ERROR - 07-06-2017 14:16:31 --> Severity: Warning  --> Creating default object from empty value /var/www/html/simple/application/models/campo_error.php 22
