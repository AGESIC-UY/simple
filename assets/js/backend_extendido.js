$(document).ready(function() {
  // -- Muestra campo Error si se esta visualizando desde el modelador de formularios
  $('#areaFormulario .campo_error').show().removeClass('hidden');

  // -- Manejador de checkbox de webservice
  $('#servicio_activo').on('click', function() {
      if($('#servicio_activo:checked')) {
          $('#servicio_activo:checked').val(1);
      }
  });

  // -- Selección de metodo de pago en pasarela
  $('#pasarela_metodo').on('change', function() {
      $('.pasarela_metodo_form').hide();

      if($('#pasarela_metodo_' + this.value).length) {
          $('.pasarela_metodo_form').hide();
          $('#pasarela_metodo_' + this.value).removeClass('hidden').show();
      }
  });

  // -- Mostrar si selecciona acción SOAP
  $('#main_action_selector').live('change', function(){
      $('#formAgregarAccion_button_services').hide();
      $('#formAgregarAccion_button_operations').hide();
      $('#formAgregarAccion_button_pasarela_pagos').hide();

      $('#formAgregarAccion_services').hide();
      $('#formAgregarAccion_operations').hide();
      $('#formAgregarAccion_pasarela_pagos').hide();
      $('#servicios_operaciones').addClass('hidden');

      $('#formAgregarAccion_button').show().removeClass('hidden');

      // -- Mostrar pasarelas disponibles en acciones
      if(this.value == 'pasarela_pago') {
          $('#formAgregarAccion_pasarela_pagos').show().removeClass('hidden');

          $('#formAgregarAccion_services').hide();
          $('#formAgregarAccion_button').hide();
          $('#formAgregarAccion_operations').hide();
          $('#formAgregarAccion_button_operations').hide();
          $('#formAgregarAccion_button_services').hide();
          $('#formAgregarAccion_button_pasarela_pagos').show().removeClass('hidden');

          $('#pasarela_pagos_action_selector').live('change', function(){
              $('#formAgregarAccion_button').hide();
              $('#formAgregarAccion_button_operations').hide();
              $('#formAgregarAccion_button_services').hide();
              $('#formAgregarAccion_button_pasarela_pagos').show().removeClass('hidden');
          });
      }

      // -- Servicios disponibles
  	if(this.value == 'webservice') {
        $('#formAgregarAccion_services').show().removeClass('hidden');

        $('#formAgregarAccion_button').hide();
        $('#formAgregarAccion_operations').hide();
        $('#formAgregarAccion_pasarela_pagos').hide();
        $('#formAgregarAccion_button_operations').hide();
        $('#formAgregarAccion_button_pasarela_pagos').hide();
        $('#formAgregarAccion_button_services').show().removeClass('hidden');

        $('#services_action_selector').live('change', function() {
          $('#operations_action_selector').val('');

          $('#formAgregarAccion_operations').hide();
          $('#formAgregarAccion_button_services').show().removeClass('hidden');
          $('#formAgregarAccion_button_operations').hide();

          $('.servicios_operaciones').addClass('hidden');
          $('#servicios_operacion_' + this.value).removeClass('hidden');

          if(this.value != 'webservice') {
              $('#servicios_operacion_' + this.value).removeClass('hidden');

              $('#formAgregarAccion_operations').show().removeClass('hidden');
              $('#formAgregarAccion_button_services').hide();
              $('#formAgregarAccion_button_operations').show().removeClass('hidden');

              $('#operations_action_selector').live('change', function() {
                  var operacion_id = $('#operations_action_selector option:selected').attr('data-operacion-id');
                  var form_accion = $('#formAgregarAccion_operations').attr('action');
                  $('#formAgregarAccion_operations').attr({'action': form_accion + '/' + operacion_id});
              });
          }
        });
      }
  });

  // -- Manejador de fieldsets para editor de formulario
  $('#formEditarFormulario .btn').on('click', function() {
      setTimeout(function() {
          document.lista_de_fieldsets = $('.custom-fieldset');

          var lista_de_fieldsets = '<select id="lista_de_fieldsets" name="fieldset">';
              lista_de_fieldsets += '<option value="">-- Seleccionar fieldset --</option>';
          $(document.lista_de_fieldsets).each(function() {
              var fieldset = $(this).attr('name').replace('BLOQUE_', '');
              var selected = '';

              if($('#formEditarCampo input[name="fieldset"]').length) {
                  if(($('#formEditarCampo input[name="fieldset"]').val().length >= 1) && ($('#formEditarCampo input[name="fieldset"]').val() == fieldset)) {
                      selected = 'selected';
                  }
              }

              lista_de_fieldsets += '<option value="'+ fieldset +'" '+ selected +'>'+ fieldset +'</option>';
          });
          lista_de_fieldsets += '</select>';

          $('#formEditarCampo input[name="fieldset"]').replaceWith(lista_de_fieldsets);
      }, 200);
  });

  $('.btn-group .btn').on('click', function() {
      setTimeout(function() {
          document.lista_de_fieldsets = $('.custom-fieldset');

          var lista_de_fieldsets = '<select id="lista_de_fieldsets" name="fieldset">';
              lista_de_fieldsets += '<option value="">-- Seleccionar fieldset --</option>';
          $(document.lista_de_fieldsets).each(function() {
              var fieldset = $(this).attr('name').replace('BLOQUE_', '');
              var selected = '';

              if(($('#formEditarCampo input[name="fieldset"]').val().length >= 1) && ($('#formEditarCampo input[name="fieldset"]').val() == fieldset)) {
                  selected = 'selected';
              }

              lista_de_fieldsets += '<option value="'+ fieldset +'" '+ selected +'>'+ fieldset +'</option>';
          });
          lista_de_fieldsets += '</select>';

          $('#formEditarCampo input[name="fieldset"]').replaceWith(lista_de_fieldsets);
      }, 500);
  });

  // -- Setea campos como "No requeridos"
  $('.campo_no_requerido').on('click', function() {
      setTimeout(function() {
          $('.modal input[name="validacion"]').attr({'value': ''}).hide();
          $('.modal input[name="readonly"]').attr({'value': '1'}).parent().hide();
          $('.modal input[name="validacion"]').prev().hide();
      }, 200);
  });

  // -- Maneja respuestas de operacion Catalogo de Servicios
  $('#agregar_respuestas').on('click', function() {
      // -- Genera un nuevo ID de respuesta
      var id = $('#operacion_id').val() + '-';
      var posibles = 'abcdefghijklmnopqrstuvwxyz0123456789';
      for(var i = 0; i < 6; i++) {id += posibles.charAt(Math.floor(Math.random() * posibles.length));}

      $('#respuestas_visual').append('<div class="well respuestas_visual_hijos"><input type="hidden" class="respuesta_id" value="'+ id +'" /><div class="row-fluid"><div class="span3"><label for="nombreResp'+ id +'">Nombre</label><input id="nombreResp'+ id +'" type="text" class="respuestas_campos_key" /></div><div class="span5"><label for="XPath'+ id +'">XPath</label><input id="XPath'+ id +'" type="text" class="input-xxlarge respuestas_campos_xpath" /></div><div class="span3"><label for="tipo'+ id +'">Tipo</label><select id="tipo'+ id +'" class="respuestas_campos_tipo"><option value="texto">Texto</option><option value="lista">Lista</option></select></div><div class="span1"><div class="btn btn-danger respuestas_campos_eliminar"><span class="icon-trash icon-white" /></div></div></div></div>');

      $('.respuesta_id[value="'+ id +'"]').parent().find('.respuestas_campos_key').focus();

      document.operacion_id = $('#operacion_id').val();

      $('.respuestas_campos_tipo').on('change', function() {
          if(this.value == 'lista') {
              if(!$(this).parent().parent().parent().find('.respuestas_campos_xslt').length) {
                  $(this).parent().parent().parent().append('<div class="margen"><label for="xsl'+ document.operacion_id +'">XSL</label><textarea id="xsl'+ document.operacion_id +'" name="xslt['+ document.operacion_id +']['+$(this).parent().parent().parent().find('.respuesta_id').val() +']" spellcheck="false" class="large-textarea respuestas_campos_xslt" placeholder="Ingrese el XSL...">'+ $('#xsl_example').val() +'</textarea></div>');
              }
              else {
                  $(this).parent().parent().parent().find('.margen').show();
                  $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr({'name': $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr('name').replace('_xslt', 'xslt')});
              }
          }
          else {
              if($(this).parent().parent().parent().find('.respuestas_campos_xslt').length) {
                  $(this).parent().parent().parent().find('.margen').hide();
                  $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr({'name': $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr('name').replace('xslt', '_xslt')});
              }
          }
      });

      $('.respuestas_campos_eliminar').on('click', function() {
          $(this).parent().parent().parent().remove();
      });
  });

  if($('#respuestas').length) {
      document.operacion_id = $('#operacion_id').val();

      if($('#respuestas').val().length > 0) {
          var respuestas = $('#respuestas').val();
              respuestas = $.parseJSON(respuestas);

          $(respuestas.respuestas).each(function(i) {
              var options = '';

              if($(respuestas.respuestas)[i].tipo == 'texto') {
                  options = '<option value="texto" selected>Texto</option><option value="lista">Lista</option>';
              }
              else if($(respuestas.respuestas)[i].tipo == 'lista') {
                  options = '<option value="texto">Texto</option><option value="lista" selected>Lista</option>';
              }
              else {
                  options = '<option value="texto" selected>Texto</option><option value="lista">Lista</option>';
              }

              $('#respuestas_visual').append('<div class="well respuestas_visual_hijos"><input type="hidden" class="respuesta_id" value="'+ $(respuestas.respuestas)[i].id +'" /><div class="row-fluid"><div class="span3"><label for="nombreResp'+ $(respuestas.respuestas)[i].id +'">Nombre</label><input id="nombreResp'+ $(respuestas.respuestas)[i].id +'" type="text" class="respuestas_campos_key" value="'+ $(respuestas.respuestas)[i].key +'" /></div><div class="span5"><label for="XPath'+ $(respuestas.respuestas)[i].id +'">XPath</label><input id="XPath'+ $(respuestas.respuestas)[i].id +'" type="text" class="input-xxlarge respuestas_campos_xpath" value="'+ $(respuestas.respuestas)[i].xpath +'" /></div><div class="span3"><label for="tipo'+ $(respuestas.respuestas)[i].id +'">Tipo</label><select id="tipo'+ $(respuestas.respuestas)[i].id +'" class="respuestas_campos_tipo">'+ options +'</select></div><div class="span1"><div class="btn btn-danger respuestas_campos_eliminar"><span class="icon-trash icon-white" /></div></div></div></div>');

              if($(respuestas.respuestas)[i].tipo == 'lista') {
                  $('#respuestas_creadas .respuestas_campos_xslt').each(function() {
                      document.campo_xslt = $(this);
                      $('.respuesta_id').filter(function(){return this.value == $(document.campo_xslt).attr('data-respuesta-id')}).parent().append($(document.campo_xslt).parent());
                  });
              }

              $('.respuestas_campos_tipo').on('change', function() {
                  if(this.value == 'lista') {
                      if(!$(this).parent().parent().parent().find('.respuestas_campos_xslt').length) {
                          $(this).parent().parent().parent().append('<div class="margen"><label for="xsl'+ document.operacion_id +'">XSL</label><textarea id="xsl'+ document.operacion_id +'" name="xslt['+ document.operacion_id +']['+$(this).parent().parent().parent().find('.respuesta_id').val() +']" spellcheck="false" class="large-textarea respuestas_campos_xslt" placeholder="Ingrese el XSL...">'+ $('#xsl_example').val() +'</textarea></div>');
                      }
                      else {
                          $(this).parent().parent().parent().find('.margen').show();
                          $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr({'name': $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr('name').replace('_xslt', 'xslt')});
                      }
                  }
                  else {
                      if($(this).parent().parent().parent().find('.respuestas_campos_xslt').length) {
                          $(this).parent().parent().parent().find('.margen').hide();
                          $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr({'name': $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr('name').replace('xslt', '_xslt')});
                      }
                  }
              });
          });

          $('.respuestas_campos_eliminar').on('click', function() {
              var nombre = $(this).parent().parent().parent().find('.respuestas_campos_key');
              var respuestas_todas = $('#respuestas').val();
                  respuestas_todas = $.parseJSON(respuestas_todas);
                  respuestas_todas = respuestas_todas.respuestas;
              var index = $.inArray(nombre, respuestas_todas);
              respuestas_todas.splice(index, 1);

              $('#respuestas').val(JSON.stringify({'respuestas': respuestas_todas}));

              $(this).parent().parent().parent().remove();
          });
      }
  }

  $('#guardar_operacion').click(function() {
      var respuestas = Array();
      $('#respuestas_visual .respuestas_visual_hijos').each(function() {
          var key = $(this).find('.respuestas_campos_key').val();
          var xpath = $(this).find('.respuestas_campos_xpath').val();
          var tipo = $(this).find('.respuestas_campos_tipo').val();
          var id = $(this).find('.respuesta_id').val();

          var respuesta = {
              "id": id,
              "key": key,
              "xpath": xpath,
              "tipo": tipo
          };

          respuestas.push(respuesta);
      });

      var respuestas = JSON.stringify({'respuestas': respuestas});
      $('#respuestas').val(respuestas);

      $('.respuestas_campos_xslt').each(function() {
        var xslt = $(this).val();
        var new_xslt = xslt.replace('version=', '%version%=');
        $(this).val(new_xslt);
      });

      $('form').submit();
  });

  // -- Datepicker para vencimiento de método de pago de pasarela
  if($('#pasarela_metodo').length) {
      $('#pasarela_pago_vencimiento_button').datepicker({
          format: 'yyyymmdd',
          startDate: '1d'
      }).on('changeDate', function (e) {
          $(this).datepicker('hide');

          var month = e.date.getMonth() + 1;
          $('#pasarela_pago_vencimiento').val(e.date.getFullYear() + '' + (month < 10 ? '0' : '') + month + '' + (e.date.getDate() < 10 ? '0' : '') + e.date.getDate() + '0000');
          $('#pasarela_pago_vencimiento_muestra_texto').text((e.date.getDate() < 10 ? '0' : '') + e.date.getDate() + '/' + (month < 10 ? '0' : '') + month + '/' + e.date.getFullYear() + ' 00:00');
      });
  }
});

// -- Revuelve un color aleatorio en hexadecimal
function getRandomColor() {
  return randomColor({hue: 'blue', luminosity: 'light', count: 1});
}
