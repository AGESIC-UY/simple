INSERT INTO `cuenta` (`id`, `nombre`, `nombre_largo`, `mensaje`, `logo`, `api_token`) VALUES
(1, 'default', 'Organismo', '', '', '');

INSERT INTO `usuario_backend` (`id`, `email`, `password`, `nombre`, `apellidos`, `rol`, `salt`, `cuenta_id`, `reset_token`, `usuario`) VALUES
(1, 'admin@admin.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Admin', 'Admin', 'super', '', 1, NULL, 'uy-ci-88888889');

INSERT INTO `usuario_manager` (`id`, `usuario`, `password`, `nombre`, `apellidos`, `salt`) VALUES
(1, 'admin@admin.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Admin', 'Admin', '');

INSERT INTO `ws_catalogo` (`nombre`, `id`, `wsdl`, `endpoint_location`, `activo`, `conexion_timeout`, `respuesta_timeout`) VALUES
('Servicio DNIC', 36, 'http://localhost:9800/dnic/servicioci?wsdl', 'http://localhost:9800/dnic/servicioci', 1, 10, 10);

INSERT INTO `ws_operacion` (`id`, `codigo`, `catalogo_id`, `nombre`, `operacion`, `soap`, `ayuda`, `respuestas`) VALUES
(5, '39I04ckCE5sl', 36, 'Obtener persona por documento', 'ObtPersonaPorDoc', '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsd="http://wsDNIC/">\r\n   <soapenv:Header/>\r\n   <soapenv:Body>\r\n      <wsd:ObtPersonaPorDoc>\r\n         <wsd:paramObtPersonaPorDoc>\r\n            <wsd:Organizacion>DGREC</wsd:Organizacion>\r\n            <wsd:PasswordEntidad>Part1da30082010T</wsd:PasswordEntidad>\r\n            <wsd:Nrodocumento>@@documento</wsd:Nrodocumento>\r\n            <wsd:TipoDocumento>DO</wsd:TipoDocumento>\r\n         </wsd:paramObtPersonaPorDoc>\r\n      </wsd:ObtPersonaPorDoc>\r\n   </soapenv:Body>\r\n</soapenv:Envelope>', 'Para invocar el servicio Web de la DNIC, deber modificar le texto @@documento por el nmero de documento del ciudadano.\r\n\r\nAl invocar este servicio la respuesta se transforma a las siguientes variables:\r\n\r\ndatos: una coleccin JSON que se puede asociar a una grila, con las columnas nombre y apellido.\r\nprimer_nombre: con el Primer Nombre\r\nprimer_apellido: con el Apellido \r\nerror: en caso de error en la invocacin el error', '{"respuestas":[{"id":"5-7ovxzr","key":"datos","xpath":"//*","tipo":"lista"},{"id":"5-f2jxqe","key":"error","xpath":"//*[local-name() = ''Descripcion'']","tipo":"texto"}]}');

INSERT INTO `ws_operacion_respuesta` (`id`, `operacion_id`, `respuesta_id`, `xslt`) VALUES
(69, 5, '5-7ovxzr', '<xsl:stylesheet version=''1.0'' xmlns:xsl=''http://www.w3.org/1999/XSL/Transform''>\r\n<xsl:output method=''xml''/>\r\n<xsl:template match=''/''>\r\n      <elementos>\r\n<xsl:for-each select=''(//*[local-name() = "ObjPersona"])''>\r\n<elemento>\r\n<item>\r\n      <xsl:value-of select=''*[3]''/>\r\n      </item>\r\n<item>\r\n      <xsl:value-of select=''*[5]''/>\r\n      </item>\r\n<item>\r\n      <xsl:value-of select=''*[10]''/>\r\n      </item>\r\n</elemento>\r\n</xsl:for-each>\r\n    </elementos>\r\n</xsl:template>\r\n</xsl:stylesheet>');

INSERT INTO `pasarela_pago` (`id`, `nombre`, `metodo`, `activo`) VALUES
(19, 'Pasarela ANTEL', 'antel', 1);

INSERT INTO `pasarela_pago_antel` (`id`, `pasarela_pago_id`, `id_tramite`, `cantidad`, `tasa_1`, `tasa_2`, `tasa_3`, `operacion`, `vencimiento`, `codigos_desglose`, `montos_desglose`) VALUES
(27, 19, 123456, 1, '10.00', '11.00', '12.00', 'P', '201604010000', 'Cod123', 'Cod456');
