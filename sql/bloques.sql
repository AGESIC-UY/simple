INSERT INTO `proceso` (`id`, `nombre`, `width`, `height`, `cuenta_id`) VALUES
(8851, 'BLOQUE', '100%', '800px', 1),
(8852, 'BLOQUE', '100%', '800px', 1),
(8853, 'BLOQUE', '100%', '800px', 1),
(8854, 'BLOQUE', '100%', '800px', 1),
(8855, 'BLOQUE', '100%', '800px', 1);

INSERT INTO `formulario` (`id`, `nombre`, `proceso_id`, `bloque_id`) VALUES
(88245, 'Formulario', 8851, 8836),
(88246, 'Formulario', 8852, 8837),
(88247, 'Formulario', 8853, 8838),
(88248, 'Formulario', 8854, 8839),
(88249, 'Formulario', 8855, 8840);

INSERT INTO `bloque` (`id`, `nombre`) VALUES
(8839, 'Identificación UY'),
(8836, 'DatosDeContacto'),
(8837, 'Domicilio'),
(8838, 'Identificación extranjeros'),
(8840, 'Personas jurídicas');

INSERT INTO `campo` (`id`, `nombre`, `readonly`, `valor_default`, `posicion`, `tipo`, `formulario_id`, `etiqueta`, `validacion`, `ayuda`, `dependiente_tipo`, `dependiente_campo`, `dependiente_valor`, `datos`, `documento_id`, `fieldset`, `extra`, `dependiente_relacion`) VALUES
(88798, 'datos_de_contacto', 0, '', 1, 'fieldset', 88245, 'Datos de contacto', '', '', 'string', '', '', NULL, NULL, '', NULL, '=='),
(88799, 'telefono', 0, '', 2, 'text', 88245, 'Teléfono', 'required|numeric|', '', 'string', '', '', NULL, NULL, 'datos_de_contacto', NULL, '=='),
(88800, 'otro_telefono', 0, '', 3, 'text', 88245, 'Otro teléfono', 'required|numeric|', '', 'string', '', '', NULL, NULL, 'datos_de_contacto', NULL, '=='),
(88801, 'correo_electronico', 0, '', 4, 'text', 88245, 'Correo electrónico', 'required|valid_email|', '', 'string', '', '', NULL, NULL, 'datos_de_contacto', NULL, '=='),
(88802, 'domicilio', 0, '', 0, 'fieldset', 88246, 'Domicilio', '', '', 'string', '', '', NULL, NULL, '', NULL, '=='),
(88803, 'departamento', 0, '', 1, 'select', 88246, 'Departamento', 'required', '', 'string', '', '', '[{"etiqueta":"Artigas","valor":"artigas"},{"etiqueta":"Canelones","valor":"canelones"},{"etiqueta":"Cerro Largo","valor":"cerro_largo"},{"etiqueta":"Colonia","valor":"colonia"},{"etiqueta":"Durazno","valor":"durazno"},{"etiqueta":"Flores","valor":"flores"},{"etiqueta":"Florida","valor":"florida"},{"etiqueta":"Lavalleja","valor":"lavalleja"},{"etiqueta":"Maldonado","valor":"maldonado"},{"etiqueta":"Montevideo","valor":"montevideo"},{"etiqueta":"Paysand\\u00fa","valor":"paysandu"},{"etiqueta":"Rio Negro","valor":"rio_negro"},{"etiqueta":"Rivera","valor":"rivera"},{"etiqueta":"Rocha","valor":"rocha"},{"etiqueta":"Salto","valor":"salto"},{"etiqueta":"San Jos\\u00e9","valor":"san_jose"},{"etiqueta":"Soriano","valor":"soriano"},{"etiqueta":"Tacuaremb\\u00f3","valor":"tacuarembo"},{"etiqueta":"Treinta y Tres","valor":"treinta_y_tres"}]', NULL, 'domicilio', NULL, '=='),
(88804, 'localidad', 0, '', 2, 'text', 88246, 'Localidad', 'required', '', 'string', '', '', NULL, NULL, 'domicilio', NULL, '=='),
(88805, 'calle', 0, '', 3, 'text', 88246, 'Calle', 'required', '', 'string', '', '', NULL, NULL, 'domicilio', NULL, '=='),
(88806, 'numero', 0, '', 4, 'text', 88246, 'Número', 'required', '', 'string', '', '', NULL, NULL, 'domicilio', NULL, '=='),
(88807, 'otros_datos', 0, '', 5, 'text', 88246, 'Otros datos', '', '', 'string', '', '', NULL, NULL, 'domicilio', NULL, '=='),
(88809, 'datos_personales', 0, '', 1, 'fieldset', 88247, 'Datos personales', '', '', 'string', '', '', NULL, NULL, '', NULL, '=='),
(88810, 'tipo_de_documento', 0, '', 2, 'select', 88247, 'Documento de identidad', 'required', '', 'string', '', '', '[{"etiqueta":"C.I.","valor":"ci"},{"etiqueta":"Pasaporte","valor":"pasaporte"}]', NULL, 'datos_personales', NULL, '=='),
(88811, 'numero_de_documento', 0, '', 3, 'text', 88247, 'Número de documento (incluir dígito verificador)', 'required', '', 'string', '', '', NULL, NULL, 'datos_personales', NULL, '=='),
(88812, 'pais', 0, '', 4, 'paises', 88247, 'País emisor', 'required', '', 'string', '', '', NULL, NULL, 'datos_personales', NULL, '=='),
(88813, 'apellidos', 0, '', 5, 'text', 88247, 'Apellidos', 'required', '', 'string', '', '', NULL, NULL, 'datos_personales', NULL, '=='),
(88814, 'nombres', 0, '', 6, 'text', 88247, 'Nombres', 'required', '', 'string', '', '', NULL, NULL, 'datos_personales', NULL, '=='),
(88815, 'datos_personales', 0, '', 1, 'fieldset', 88248, 'Datos personales', '', '', 'string', '', '', NULL, NULL, '', NULL, '=='),
(88816, 'tipo_de_documento', 0, '', 2, 'select', 88248, 'Documento de identidad', 'required', '', 'string', '', '', '[{"etiqueta":"C.I.","valor":"ci"},{"etiqueta":"Pasaporte","valor":"pasaporte"}]', NULL, 'datos_personales', NULL, '=='),
(88817, 'numero_de_documento', 0, '', 3, 'text', 88248, 'Número de documento (incluir dígito verificador)', 'required', '', 'string', '', '', NULL, NULL, 'datos_personales', NULL, '=='),
(88818, 'apellidos', 0, '', 4, 'text', 88248, 'Apellidos', 'required', '', 'string', '', '', NULL, NULL, 'datos_personales', NULL, '=='),
(88819, 'nombres', 0, '', 5, 'text', 88248, 'Nombres', 'required', '', 'string', '', '', NULL, NULL, 'datos_personales', NULL, '=='),
(88820, 'empresa', 0, '', 0, 'fieldset', 88249, 'Empresa', '', '', 'string', '', '', NULL, NULL, '', NULL, '=='),
(88821, 'rut', 0, '', 1, 'text', 88249, 'RUT', 'required|numeric', '', 'string', '', '', NULL, NULL, 'empresa', NULL, '=='),
(88822, 'razon_social', 0, '', 2, 'text', 88249, 'Razón social', 'required', '', 'string', '', '', NULL, NULL, 'empresa', NULL, '=='),
(88823, 'rol', 0, '', 3, 'text', 88249, 'Rol', 'required', '', 'string', '', '', NULL, NULL, 'empresa', NULL, '=='),
(88824, 'validacion', 0, '', 4, 'radio', 88249, 'Validación', 'required', '', 'string', '', '', '[{"etiqueta":"Verificar en registro de DGI\\/DGREC","valor":"verificar_en_registro_de_dgidgrec"},{"etiqueta":"Presentar documentaci\\u00f3n en oficinas del organismo o PAC","valor":"presentar_documentacion_en_oficinas_del_organismo_o_pac"},{"etiqueta":"Adjuntar certificado notarial electr\\u00f3nico (PDF max 45KB)","valor":"adjuntar_certificado_notarial_electronico_pdf_max_45kb"}]', NULL, 'empresa', NULL, '=='),
(88825, 'certificado', 0, '', 5, 'file', 88249, 'Certificado', '', '', 'string', '', '', NULL, NULL, 'empresa', '{"filetypes":["pdf"]}', '=='),
(88826, '56ead35f64d18', 1, '', 6, 'javascript', 88249, '$(document).ready(function(){ \r\n  $(''input[name="certificado"]'').parent().hide();\r\n  $(''span:contains("Certificado (Opcional)")'').hide();\r\n\r\n  setTimeout(function() {\r\n    $(''input:radio'').change(function() {\r\n      if($(''#adjuntar_certificado_notarial_electronico_pdf_max_45kb'').is('':checked'')) {\r\n        $(''input[name="certificado"]'').parent().show();\r\n        $(''span:contains("Certificado (Opcional)")'').show();\r\n      }\r\n      else {\r\n        $(''input[name="certificado"]'').parent().hide();\r\n        $(''span:contains("Certificado (Opcional)")'').hide();\r\n      }\r\n    });\r\n  }, 400);\r\n});', '', '', 'string', '', '', NULL, NULL, NULL, '==', '');


/* Clausula de consentimiento informado */
INSERT INTO `bloque` (`nombre`,`id`) VALUES ('Clausula de consentimiento informado', NULL);
SET @bloque_id_1 = LAST_INSERT_ID();

INSERT INTO `proceso` (`id`,`nombre`,`width`,`height`,`cuenta_id`) VALUES (NULL, 'BLOQUE', '100%', '800px', 1);
SET @proceso_id_1 = LAST_INSERT_ID();

INSERT INTO `formulario` (`id`,`nombre`,`proceso_id`,`bloque_id`,`leyenda`,`contenedor`) VALUES (NULL, 'Formulario', @proceso_id_1, @bloque_id_1, NULL, 0);
SET @formulario_id_1 = LAST_INSERT_ID();

INSERT INTO `campo` (`id`,`nombre`,`readonly`,`valor_default`,`posicion`,`tipo`,`formulario_id`,`etiqueta`,`validacion`,`ayuda`,`dependiente_tipo`,`dependiente_campo`,`dependiente_valor`,`datos`,`documento_id`,`extra`,`dependiente_relacion`,`fieldset`) VALUES (NULL,'clausula_de_consentimiento_informado',0,'',1,'fieldset',@formulario_id_1,'Cláusula de consentimiento informado','','','string','','',NULL,NULL,NULL,'==','');
INSERT INTO `campo` (`id`,`nombre`,`readonly`,`valor_default`,`posicion`,`tipo`,`formulario_id`,`etiqueta`,`validacion`,`ayuda`,`dependiente_tipo`,`dependiente_campo`,`dependiente_valor`,`datos`,`documento_id`,`extra`,`dependiente_relacion`,`fieldset`) VALUES (NULL,'57a8c159bd71e',1,'',2,'paragraph',@formulario_id_1,'<p>\"De conformidad con la Ley N° 18.331, de 11 de agosto de 2008, de Protección de Datos Personales y Acción de Habeas Data (LPDP), los datos suministrados por usted quedarán incorporados en una base de datos, la cual será procesada exclusivamente para la siguiente finalidad: **Objetivo del formulario**.</p>\r\n\r\n<p>Los datos personales serán tratados con el grado de protección adecuado, tomándose las medidas de seguridad necesarias para evitar su alteración, pérdida, tratamiento o acceso no autorizado por parte de terceros que lo puedan utilizar para finalidades distintas para las que han sido solicitadas al usuario.</p>\r\n\r\n<p>El responsable de la base de datos es **Titular de la base** y la dirección donde podrá ejercer los derechos de acceso, rectificación, actualización, inclusión o supresión, es **Dirección del organismo**, según lo establecido en la LPDP\".</p>\r\n<br />','','','string','','',NULL,NULL,NULL,'==','clausula_de_consentimiento_informado');
INSERT INTO `campo` (`id`,`nombre`,`readonly`,`valor_default`,`posicion`,`tipo`,`formulario_id`,`etiqueta`,`validacion`,`ayuda`,`dependiente_tipo`,`dependiente_campo`,`dependiente_valor`,`datos`,`documento_id`,`extra`,`dependiente_relacion`,`fieldset`) VALUES (NULL,'terminos_de_la_clausula',0,'',3,'radio',@formulario_id_1,'Términos de la cláusula','required','','string','','','[{\"etiqueta\":\"Acepto los t\\u00e9rminos\",\"valor\":\"acepto\"},{\"etiqueta\":\"No acepto los t\\u00e9rminos. (No se enviar\\u00e1 el mensaje)\",\"valor\":\"\"}]',NULL,NULL,'==','clausula_de_consentimiento_informado');

/* Valoracion */
INSERT INTO `bloque` (`nombre`,`id`) VALUES ('Valoracion', NULL);
SET @bloque_id_2 = LAST_INSERT_ID();

INSERT INTO `proceso` (`id`,`nombre`,`width`,`height`,`cuenta_id`) VALUES (NULL,'BLOQUE','100%','800px',1);
SET @proceso_id_2 = LAST_INSERT_ID();

INSERT INTO `formulario` (`id`,`nombre`,`proceso_id`,`bloque_id`,`leyenda`,`contenedor`) VALUES (NULL,'Formulario',@proceso_id_2, @bloque_id_2,NULL,0);
SET @formulario_id_2 = LAST_INSERT_ID();

INSERT INTO `campo` (`id`,`nombre`,`readonly`,`valor_default`,`posicion`,`tipo`,`formulario_id`,`etiqueta`,`validacion`,`ayuda`,`dependiente_tipo`,`dependiente_campo`,`dependiente_valor`,`datos`,`documento_id`,`extra`,`dependiente_relacion`,`fieldset`) VALUES (NULL,'ayudanos_a_mejorar',0,'',0,'fieldset',@formulario_id_2,'Ayúdanos a mejorar','','','string','','',NULL,NULL,NULL,'==','valoracion.');
INSERT INTO `campo` (`id`,`nombre`,`readonly`,`valor_default`,`posicion`,`tipo`,`formulario_id`,`etiqueta`,`validacion`,`ayuda`,`dependiente_tipo`,`dependiente_campo`,`dependiente_valor`,`datos`,`documento_id`,`extra`,`dependiente_relacion`,`fieldset`) VALUES (NULL,'valoracion',0,'',2,'radio',@formulario_id_2,'¿Cómo calificarías esta gestión?','required','','string','','','[{\"etiqueta\":\"Excelente\",\"valor\":\"5\"},{\"etiqueta\":\"Muy Buena\",\"valor\":\"4\"},{\"etiqueta\":\"Buena\",\"valor\":\"3\"},{\"etiqueta\":\"Regular\",\"valor\":\"2\"},{\"etiqueta\":\"Mala\",\"valor\":\"1\"}]',NULL,NULL,'==','ayudanos_a_mejorar');
INSERT INTO `campo` (`id`,`nombre`,`readonly`,`valor_default`,`posicion`,`tipo`,`formulario_id`,`etiqueta`,`validacion`,`ayuda`,`dependiente_tipo`,`dependiente_campo`,`dependiente_valor`,`datos`,`documento_id`,`extra`,`dependiente_relacion`,`fieldset`) VALUES (NULL,'comentarios',0,'',3,'textarea',@formulario_id_2,'Comentarios','required','','string','','',NULL,NULL,NULL,'==','ayudanos_a_mejorar');
