$(document).ready(function() {
  // -- En en caso de que se este mostrando el componente de pago en el editor de formularios...
  if($('.vista_componente_pago_generico').length && $('.edicionFormulario').length ) {
    if(!$('#form_pago_submit_generico').length) {
      $('.vista_componente_pago_generico').removeClass('hidden').show();
    }
  }

  $('#generar_reporte_completo').click(function(e) {
    e.preventDefault();

    var grupo = $('#filtro_grupo').val();
    var usuario = $('#filtro_usuario').val();
    var desde = $('#filtro_desde').val();
    var hasta = $('#filtro_hasta').val();
    var reporte_id =  $('#filtro_reporte_id').val();

    window.location.href = site_url + "backend/reportes/ver/" + reporte_id + "?filtro_grupo=" + grupo + "&filtro_usuario=" + usuario + "&filtro_desde=" + desde + "&filtro_hasta=" + hasta;
  });

  $('#generar_reporte_usuario').click(function(e) {
    e.preventDefault();

    var grupo = $('#filtro_grupo').val();
    var usuario = $('#filtro_usuario').val();
    var desde = $('#filtro_desde').val();
    var hasta = $('#filtro_hasta').val();

    window.location.href = site_url + "backend/reportes/ver_reporte_usuario?filtro_grupo=" + grupo + "&filtro_usuario=" + usuario + "&filtro_desde=" + desde + "&filtro_hasta=" + hasta;
  });

  $('.solicita_filtro').click(function(){
    var reporte_id = $(this).attr('data-reporte');
    $('form #filtro_reporte_id').val(reporte_id);
    $('#modal_filtro').modal();
  });

  $('#add_parametro_configuracion').click(function() {
    var num = parseInt($('#total_parametros').val()) + 1;
    $('.lista_parametros.controls').append('<span class="campo_parametro"><input class="parametro_id" type="hidden" name="parametro['+num+'][id]" value="null" /> <input type="text" name="parametro['+num+'][clave]" class="input-large" placeholder="Clave" /> <input type="text" name="parametro['+num+'][valor]" class="input-medium" placeholder="Valor" /> <span class="remove_parametro_configuracion icon-minus btn"></span><br /><br /></span>');
    $('#total_parametros').val(num);

    $('.remove_parametro_configuracion').click(function() {
      $(this).parent().remove();
      var count = $('.campo_parametro').size();
      count = (count - 1);
      $('#total_parametros').val(count);

      var param_id = $(this).parent().find('.parametro_id').first().val();
      $.ajax({
        type: 'post',
        url: site_url+'/backend/configuracion/eliminar_parametro',
        data: {parametro_id: param_id},
        success: function(data) {
        }
      });
    });
  });

  $('.remove_parametro_configuracion').click(function() {
    $(this).parent().remove();
    var count = $('.campo_parametro').size();
    count = (count - 1);
    $('#total_parametros').val(count);

    var param_id = $(this).parent().find('.parametro_id').first().val();
    $.ajax({
      type: 'post',
      url: site_url+'/backend/configuracion/eliminar_parametro',
      data: {parametro_id: param_id},
      success: function(data) {
      }
    });
  });

  $('#add_pasarela_metodo_generico_ticket_variables').click(function() {
    var num = parseInt($('#total_ticket_variables').val()) + 1;
    $('#pasarela_metodo_generico_ticket_variables .controls').append('<input type="text" name="pasarela_metodo_generico_ticket_variables['+num+'][nombre]" class="input-medium" placeholder="Nombre" /> <input type="text" name="pasarela_metodo_generico_ticket_variables['+num+'][valor]" class="input-medium" placeholder="Valor" /><br /><br />');
    $('#total_ticket_variables').val(num);
  });

  $('#pasarela_metodo_generico_ticket_metodo').on('change', function() {
    if(this.value == 'post') {
      if(!$.trim($('#pasarela_metodo_generico_ticket_variables .controls').html())) {
        $('#pasarela_metodo_generico_ticket_variables .controls').append('<input type="text" name="pasarela_metodo_generico_ticket_variables[1][nombre]" class="input-medium" placeholder="Nombre" /> <input type="text" name="pasarela_metodo_generico_ticket_variables[1][valor]" class="input-medium" placeholder="Valor" /> <input type="hidden" id="total_ticket_variables" value="1" /> <span id="add_pasarela_metodo_generico_ticket_variables" class="icon-plus btn"></span><br /><br />');
        $('#add_pasarela_metodo_generico_ticket_variables').click(function() {
          var num = parseInt($('#total_ticket_variables').val()) + 1;
          $('#pasarela_metodo_generico_ticket_variables .controls').append('<input type="text" name="pasarela_metodo_generico_ticket_variables['+num+'][nombre]" class="input-medium" placeholder="Nombre" /> <input type="text" name="pasarela_metodo_generico_ticket_variables['+num+'][valor]" class="input-medium" placeholder="Valor" /><br /><br />');
          $('#total_ticket_variables').val(num);
        });
      }

      $('#pasarela_metodo_generico_ticket_variables').removeClass('hidden').show();
    }
    else {
      $('#pasarela_metodo_generico_ticket_variables').hide();
    }
  });

  $('#add_pasarela_metodo_generico_metodo_http_variable').click(function() {
    var num = parseInt($('#total_variables').val()) + 1;
    $('#pasarela_metodo_generico_variables_post .controls').append('<input type="text" name="pasarela_metodo_generico_metodo_http_variable['+num+'][nombre]" class="input-medium" placeholder="Nombre" /> <input type="text" name="pasarela_metodo_generico_metodo_http_variable['+num+'][valor]" class="input-medium" placeholder="Valor" /><br /><br />');
    $('#total_variables').val(num);
  });

  $('#pasarela_metodo_generico_metodo_http').on('change', function() {
    if(this.value == 'post') {
      if(!$.trim($('#pasarela_metodo_generico_variables_post .controls').html())) {
        $('#pasarela_metodo_generico_variables_post .controls').append('<input type="text" name="pasarela_metodo_generico_metodo_http_variable[1][nombre]" class="input-medium" placeholder="Nombre" /> <input type="text" name="pasarela_metodo_generico_metodo_http_variable[1][valor]" class="input-medium" placeholder="Valor" /> <input type="hidden" id="total_variables" value="1" /> <span id="add_pasarela_metodo_generico_metodo_http_variable" class="icon-plus btn"></span><br /><br />');
        $('#add_pasarela_metodo_generico_metodo_http_variable').click(function() {
          var num = parseInt($('#total_variables').val()) + 1;
          $('#pasarela_metodo_generico_variables_post .controls').append('<input type="text" name="pasarela_metodo_generico_metodo_http_variable['+num+'][nombre]" class="input-medium" placeholder="Nombre" /> <input type="text" name="pasarela_metodo_generico_metodo_http_variable['+num+'][valor]" class="input-medium" placeholder="Valor" /><br /><br />');
          $('#total_variables').val(num);
        });
      }

      $('#pasarela_metodo_generico_variables_post').removeClass('hidden').show();
    }
    else {
      $('#pasarela_metodo_generico_variables_post').hide();
    }
  });

  if($('.edicionFormulario').length && $('.mensaje_estado_pago').length) {
    $('.mensaje_estado_pago').parent().html('<div class="well text-left"><i class="icon-tasks" / > Componente de pago</div>');
  }

  $('input[name="requiere_autenticacion_tipo"]').click(function() {
    switch($(this).val()) {
      case 'autenticacion_basica':
        $('.tipo_autenticacion_mutua').addClass('hidden').hide();
        $('.tipo_autenticacion_token').addClass('hidden').hide();
        $('.tipo_autenticacion_basica').removeClass('hidden').show();
        break;
      case 'autenticacion_mutua':
        $('.tipo_autenticacion_basica').addClass('hidden').hide();
        $('.tipo_autenticacion_token').addClass('hidden').hide();
        $('.tipo_autenticacion_mutua').removeClass('hidden').show();
        break;
      case 'autenticacion_token':
        $('.tipo_autenticacion_basica').addClass('hidden').hide();
        $('.tipo_autenticacion_mutua').addClass('hidden').hide();
        $('.tipo_autenticacion_token').removeClass('hidden').show();
        break;
    }
  });

  $('#requiere_autenticacion').click(function() {
    $('input[name="requiere_aut_tipo"]').prop('checked', false);

    if($(this).prop('checked')) {
      $('.tipo_autenticacion').removeClass('hidden').show();

      $('input[name="requiere_autenticacion_tipo"]').click(function() {
        switch($(this).val()) {
          case 'autenticacion_basica':
            $('.tipo_autenticacion_mutua').addClass('hidden').hide();
            $('.tipo_autenticacion_token').addClass('hidden').hide();
            $('.tipo_autenticacion_basica').removeClass('hidden').show();
            break;
          case 'autenticacion_mutua':
            $('.tipo_autenticacion_basica').addClass('hidden').hide();
            $('.tipo_autenticacion_token').addClass('hidden').hide();
            $('.tipo_autenticacion_mutua').removeClass('hidden').show();
            break;
          case 'autenticacion_token':
            $('.tipo_autenticacion_basica').addClass('hidden').hide();
            $('.tipo_autenticacion_mutua').addClass('hidden').hide();
            $('.tipo_autenticacion_token').removeClass('hidden').show();
            break;
        }
      });
    }
    else {
      $('.tipo_autenticacion').addClass('hidden').hide();
      $('.autenticacion_soap').addClass('hidden').hide();
    }
  });

  $(".toolbar-formulario .btn, .botones-edit .btn-primary").click(function() {
    setTimeout(function() {
      if($(".dialogos_titulo").size()) {
        var contenido_default = $('#valor_default_html').html($("#valor_default").val());

        document.content = $('#valor_default_html');

        $("#campo_dialogo_titulo").val($('#valor_default_html').find('.dialogos_titulo').text());
        $("#campo_dialogo_contenido").val($('#valor_default_html').find('.dialogos_contenido').html());
        $("#campo_dialogo_titulo_enlace").val($('#valor_default_html').find('.dialogos_enlace').text());
        $("#campo_dialogo_enlace").val($('#valor_default_html').find('.dialogos_enlace').attr('href'));
      }

      $("#campo_dialogo_titulo, #campo_dialogo_contenido, #campo_dialogo_enlace, #campo_dialogo_titulo_enlace").on("change", function() {
        var titulo = "<h3 class='dialogos_titulo'>" + $("#campo_dialogo_titulo").val() + "</h3>";
        var contenido = "<div class='dialogos_contenido'>" + $("#campo_dialogo_contenido").val() + "</div>";
        var enlace = "<a class='dialogos_enlace' href='"+ $("#campo_dialogo_enlace").val() +"' target='_blank'>" + $("#campo_dialogo_titulo_enlace").val() + "</a>";
        $("#valor_default").html(titulo + contenido + enlace);
      });
    }, 400);
  });


  $('#verificar_existe_usuario').click(function() {
    var usuario = $('input[name="usuario"]').val();

    // -- Consulta si existe el usuario
    $.ajax({
      type: 'post',
      url: 'usuario_existe',
      data: {usuario: usuario},
      success: function(data) {
        $('.validacion').html('').hide();

        if(data) {
          var usuario = JSON.parse(data);

          if(usuario.error) {
            $('.validacion').html('<div class="alert alert-error">'+usuario.error+'</div>').show();
          }
          else {
            $('input[name="password"]').parent().parent().remove();
            $('input[name="password_confirm"]').parent().parent().remove();
            $('input[name="nombres"]').val(usuario.nombres).attr({'readonly':'readonly'});
            $('input[name="apellido_paterno"]').val(usuario.apellido_paterno).attr({'readonly':'readonly'});
            $('input[name="apellido_materno"]').val(usuario.apellido_materno).attr({'readonly':'readonly'});
            $('input[name="email"]').val(usuario.email);

            var form_action = $('form').attr('action');
            $('form').attr({'action': form_action + '/' + usuario.usuario_id});
          }
        }
      }
    });

    var existe_usuario = true;
    if(existe_usuario) {

    }
    else {

    }
  });

  if($('#servicio_tipo_pdi:checked').length) {
    $('#form_soap').hide();
    $('#form_pdi').show();
  }
  else {
    $('#form_soap').show();
    $('#form_pdi').hide();
  }

  $('#servicio_tipo_pdi').click(function() {
    $('#form_soap').hide();
    $('#form_pdi').show();
  });

  $('#servicio_tipo_soap').click(function() {
    $('#form_pdi').hide();
    $('#form_soap').show().removeClass('hidden');
  });

  setTimeout(function() {$(".controls").find(".ht_master.handsontable").not(':first').hide();}, 200);

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

          $('#pasarela_pagos_action_selector').live('change', function() {
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
        try {
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
        }
        catch(error) {
          console.log(error);
        }
      }, 500);
  });

  $('.btn-group .btn').on('click', function() {
      setTimeout(function() {
        try {
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
        }
        catch(error) {
          console.log(error);
        }
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

      $('#respuestas_visual').append('<div class="well respuestas_visual_hijos"><input type="hidden" class="respuesta_id" value="'+ id +'" /><div class="row-fluid"><div class="span3"><label for="nombreResp'+ id +'">Nombre</label><input id="nombreResp'+ id +'" type="text" class="respuestas_campos_key" /></div><div class="span5"><label for="XPath'+ id +'">XPath</label><input id="XPath'+ id +'" type="text" class="input-xxlarge respuestas_campos_xpath" /></div><div class="span3"><label for="tipo'+ id +'">Tipo</label><select id="tipo'+ id +'" class="respuestas_campos_tipo"><option value="texto">Texto</option><option value="lista">Lista</option><option value="xslt">XSLT</option></select></div><div class="span1"><div class="btn btn-danger respuestas_campos_eliminar"><span class="icon-trash icon-white" /></div></div></div></div>');

      $('.respuesta_id[value="'+ id +'"]').parent().find('.respuestas_campos_key').focus();

      document.operacion_id = $('#operacion_id').val();

      $('.respuestas_campos_tipo').on('change', function() {
          if(this.value == 'lista') {
              if(!$(this).parent().parent().parent().find('.respuestas_campos_xslt').length) {
                  $(this).parent().parent().parent().append('<div class="margen"><label for="xsl'+ document.operacion_id +'">XSL</label><textarea id="xsl'+ document.operacion_id +'" name="xslt['+ document.operacion_id +']['+$(this).parent().parent().parent().find('.respuesta_id').val() +']" spellcheck="false" class="large-textarea respuestas_campos_xslt" placeholder="Ingrese el XSLT...">'+ $('#xsl_example').val() +'</textarea></div>');
              }
              else {
                  $(this).parent().parent().parent().find('.margen').show();
                  $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr({'name': $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr('name').replace('_xslt', 'xslt')});
              }
          }
          else if(this.value == 'xslt') {
              if(!$(this).parent().parent().parent().find('.respuestas_campos_xslt').length) {
                  $(this).parent().parent().parent().append('<div class="margen"><label for="xsl'+ document.operacion_id +'">XSLT</label><textarea id="xsl'+ document.operacion_id +'" name="xslt['+ document.operacion_id +']['+$(this).parent().parent().parent().find('.respuesta_id').val() +']" spellcheck="false" class="large-textarea respuestas_campos_xslt" placeholder="Ingrese el XSLT..."></textarea></div>');
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
                  options = '<option value="texto" selected>Texto</option><option value="lista">Lista</option><option value="xslt">XSLT</option>';
              }
              else if($(respuestas.respuestas)[i].tipo == 'lista') {
                  options = '<option value="texto">Texto</option><option value="lista" selected>Lista</option><option value="xslt">XSLT</option>';
              }
              else if($(respuestas.respuestas)[i].tipo == 'xslt') {
                  options = '<option value="texto">Texto</option><option value="lista">Lista</option><option value="xslt" selected>XSLT</option>';
              }
              else {
                  options = '<option value="texto" selected>Texto</option><option value="lista">Lista</option><option value="xslt">XSLT</option>';
              }

              $('#respuestas_visual').append('<div class="well respuestas_visual_hijos"><input type="hidden" class="respuesta_id" value="'+ $(respuestas.respuestas)[i].id +'" /><div class="row-fluid"><div class="span3"><label for="nombreResp'+ $(respuestas.respuestas)[i].id +'">Nombre</label><input id="nombreResp'+ $(respuestas.respuestas)[i].id +'" type="text" class="respuestas_campos_key" value="'+ $(respuestas.respuestas)[i].key +'" /></div><div class="span5"><label for="XPath'+ $(respuestas.respuestas)[i].id +'">XPath</label><input id="XPath'+ $(respuestas.respuestas)[i].id +'" type="text" class="input-xxlarge respuestas_campos_xpath" value="'+ $(respuestas.respuestas)[i].xpath +'" /></div><div class="span3"><label for="tipo'+ $(respuestas.respuestas)[i].id +'">Tipo</label><select id="tipo'+ $(respuestas.respuestas)[i].id +'" class="respuestas_campos_tipo">'+ options +'</select></div><div class="span1"><div class="btn btn-danger respuestas_campos_eliminar"><span class="icon-trash icon-white" /></div></div></div></div>');

              if($(respuestas.respuestas)[i].tipo == 'lista') {
                  $('#respuestas_creadas .respuestas_campos_xslt').each(function() {
                      document.campo_xslt = $(this);
                      $('.respuesta_id').filter(function(){return this.value == $(document.campo_xslt).attr('data-respuesta-id')}).parent().append($(document.campo_xslt).parent());
                  });
              }

              if($(respuestas.respuestas)[i].tipo == 'xslt') {
                  $('#respuestas_creadas .respuestas_campos_xslt').each(function() {
                      document.campo_xslt = $(this);
                      $('.respuesta_id').filter(function(){return this.value == $(document.campo_xslt).attr('data-respuesta-id')}).parent().append($(document.campo_xslt).parent());
                  });
              }

              $('.respuestas_campos_tipo').on('change', function() {
                  if(this.value == 'lista') {
                      if(!$(this).parent().parent().parent().find('.respuestas_campos_xslt').length) {
                          $(this).parent().parent().parent().append('<div class="margen"><label for="xsl'+ document.operacion_id +'">XSL</label><textarea id="xsl'+ document.operacion_id +'" name="xslt['+ document.operacion_id +']['+$(this).parent().parent().parent().find('.respuesta_id').val() +']" spellcheck="false" class="large-textarea respuestas_campos_xslt" placeholder="Ingrese el XSLT...">'+ $('#xsl_example').val() +'</textarea></div>');
                      }
                      else {
                          $(this).parent().parent().parent().find('.margen').show();
                          $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr({'name': $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr('name').replace('_xslt', 'xslt')});
                      }
                  }
                  else if(this.value == 'xslt') {
                      if(!$(this).parent().parent().parent().find('.respuestas_campos_xslt').length) {
                          $(this).parent().parent().parent().append('<div class="margen"><label for="xsl'+ document.operacion_id +'">XSLT</label><textarea id="xsl'+ document.operacion_id +'" name="xslt['+ document.operacion_id +']['+$(this).parent().parent().parent().find('.respuesta_id').val() +']" spellcheck="false" class="large-textarea respuestas_campos_xslt" placeholder="Ingrese el XSLT..."></textarea></div>');
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
  if($('#pasarela_pago_vencimiento_muestra').length) {
    $('#pasarela_pago_vencimiento_button').click(function() {
      $('#pasarela_pago_vencimiento').datepicker('show');
    });
      $('#pasarela_pago_vencimiento').datepicker({
          format: 'yymmdd'
      }).on('changeDate', function (e) {
          $(this).datepicker('hide');

          var month = e.date.getMonth() + 1;
          $('#pasarela_pago_vencimiento').val(e.date.getFullYear() + '' + (month < 10 ? '0' : '') + month + '' + (e.date.getDate() < 10 ? '0' : '') + e.date.getDate() + '0000');
          $('#pasarela_pago_vencimiento_muestra_texto').text((e.date.getDate() < 10 ? '0' : '') + e.date.getDate() + '/' + (month < 10 ? '0' : '') + month + '/' + e.date.getFullYear() + ' 00:00');
      });
  }

  $('#areaFormulario a').click(function() {
    setTimeout(function() {
      $('#contenedor').click(function() {
        if($(this).is(':checked')) {
          $(this).val(1);
          $('#leyenda_contenedor').removeClass('hidden').show();
        }
        else {
          $('#leyenda_contenedor').addClass('hidden').hide();
        }
      });
    }, 400);
  });
});

// -- Revuelve un color aleatorio en hexadecimal
function getRandomColor() {
  return randomColor({hue: 'blue'});
}

function editarBloque(bloqueId){
    $("#modal").load(site_url+"backend/bloques/ajax_editar/"+bloqueId);
    $("#modal").modal({backdrop: 'static', keyboard: false});
    return false;
}
