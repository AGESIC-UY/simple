

$(document).ready(function() {
      //pro problema en text area que no permite estirar el contenido en algun
      //navegador si el componente es disbaled
      $("textarea:disabled").removeAttr("disabled");


      // -- Para que el tooltip funcione en los contenidos cargados con ajax
      $(document).on("hover",".tt",function(){
          $(this).tooltip({
              html: true,
              trigger: "manual"
          }).tooltip('toggle');
      });


  $('.requiere_accion_disparador').click(function(event) {
    event.preventDefault();
    var form = $('.ajaxForm.dynaForm');
    //$(form).append("<div class='ajaxLoader'>Cargando</div>");
    //var ajaxLoader = $(form).find(".ajaxLoader");
    //$(ajaxLoader).css({left: ($(form).width()/2 - $(ajaxLoader).width()/2)+"px", top: ($(form).height()/2 - $(ajaxLoader).height()/2)+"px"});
    $.blockUI({
       message: '<img src="'+ document.Constants.host + '/assets/img/ajax-loader.gif"></img>',
        css: {
          width: '70px',
          height: '60px',
           border: 'none',
           padding: '15px',
           backgroundColor: '#000',
           textAlign: 'center',
           color: '#fff',
           top: '40%',
           left: '50%',
      }});

    var form_action = $(form).attr('action');
    var require_accion_campo_id = $(this).data('campo');

    $.ajax({
      type: 'post',
      dataType: 'json',
      url: form_action,
      data: $(form).serialize() + '&no_advance=1&require_accion=1&require_accion_campo_id=' + require_accion_campo_id,
      async: true,
      complete: function(resultado) {
        var resultado = JSON.parse(resultado.responseText);
        if (resultado.error){
          $("<div title='Ha ocurrido un error' class='validacion-popup validacion-error'><div class='dialog-icon'><span class='icn icn-circle-error-lg'></span></div>" + resultado.error+"</div>").dialog(
                        {modal: true,draggable: false,
                          resizable: false,
                          dialogClass: 'ui-dialog-tabla',
                          buttons: {
                            'Cerrar': function() {
                              $(this).dialog("close");
                          }
                        }
                  });
          //$(ajaxLoader).remove();
          $.unblockUI();
        }else{
          //$(ajaxLoader).remove();
          $.unblockUI();
          window.location.reload();
        }


      }
    });
  });

  /*$('#generar_reporte_completo').click(function(e) {
    e.preventDefault();

    var grupo = $('#filtro_grupo').val();
    var usuario = $('#filtro_usuario').val();
    var desde = $('#filtro_desde').val();
    var hasta = $('#filtro_hasta').val();
    var reporte_id =  $('#filtro_reporte_id').val();

    window.location.href = site_url + "tramites/ver_reporte/" + reporte_id + "?filtro_grupo=" + grupo + "&filtro_usuario=" + usuario + "&filtro_desde=" + desde + "&filtro_hasta=" + hasta;
  });*/

  $('#generar_reporte_completo').click(function(e) {
    e.preventDefault();



    var grupo = $('#filtro_grupo').val();
    var usuario = $('#filtro_usuario').val();
    var desde = $('#filtro_desde').val();
    var hasta = $('#filtro_hasta').val();
    var reporte_id =  $('#filtro_reporte_id').val();

    var form = $('.ajaxForm.dynaForm');
    $.ajax({
      type: 'post',
      url: site_url+ 'tramites/generar_completo/' + reporte_id + "?filtro_grupo=" + grupo + "&filtro_usuario=" + usuario + "&filtro_desde=" + desde + "&filtro_hasta=" + hasta,
      success: function(response) {
        resultado = $.parseJSON(response);
        if (resultado.email){
          //$('#modal_filtro').height();

          $('#modal_email').modal();
          $('#modal_email').height($('#modal_filtro').height());
        }else{
            window.location.href = site_url + "tramites/ver_reporte/" + reporte_id + "?filtro_grupo=" + grupo + "&filtro_usuario=" + usuario + "&filtro_desde=" + desde + "&filtro_hasta=" + hasta;
        }
      }
    });

  });

  $('#filtro_hasta').change(function() {

      var desde = $("#filtro_desde").datepicker('getDate');
      var hasta = $("#filtro_hasta").datepicker('getDate');

      if (desde > hasta){
          $("#filtro_fechas").addClass("error");
          document.getElementById("generar_reporte_completo").disabled = true;
          var contenedor = document.getElementById("mesage");
          contenedor.style.display = "block";
          return true;
      }else {
          $("#filtro_fechas").removeClass("error");
          document.getElementById("generar_reporte_completo").disabled = false;
          var contenedor = document.getElementById("mesage");
          contenedor.style.display = "none";
          return true;
      }
  });

  $('#generar_reporte_basico').click(function(e) {
    e.preventDefault();

    var desde = $('#filtro_desde_basico').val();
    var hasta = $('#filtro_hasta_basico').val();
    var reporte_id =  $('#filtro_reporte_id_basico').val();

    if(desde == "" || hasta == ""){
      $("#filtro_fechas_basico").addClass("error");
      var contenedor = document.getElementById("mensaje_fechas_requeridas");
      contenedor.style.display = "block";
    }
    else{
      $("#filtro_fechas_basico").removeClass("error");
      var contenedor = document.getElementById("mensaje_fechas_requeridas");
      contenedor.style.display = "none";

      var form = $('.ajaxForm.dynaForm');
      $.ajax({
        type: 'post',
        url: site_url+'tramites/generar_basico/' + reporte_id + "?&filtro_desde=" + desde + "&filtro_hasta=" + hasta,
        success: function(response) {
          resultado = $.parseJSON(response);
          if (resultado.email){
            //$('#modal_filtro').height();

            $('#modal_email_basico').modal();
            $('#modal_email_basico').height($('#modal_filtro_basico').height());
          }else{
            window.location.href = site_url + "tramites/ver_reporte/" + reporte_id + "?&filtro_desde=" + desde + "&filtro_hasta=" + hasta;
          }
        }
      });
    }

  });


  $('#filtro_hasta_basico').change(function() {
      var desde = $("#filtro_desde_basico").datepicker('getDate');
      var hasta = $("#filtro_hasta_basico").datepicker('getDate');

      if (desde > hasta){
          $("#filtro_fechas_basico").addClass("error");
          document.getElementById("generar_reporte_basico").disabled = true;
          var contenedor = document.getElementById("mensaje_fechas_invalidas");
          contenedor.style.display = "block";
          return true;
      }else {
          $("#filtro_fechas_basico").removeClass("error");
          document.getElementById("generar_reporte_basico").disabled = false;
          var contenedor = document.getElementById("mensaje_fechas_invalidas");
          contenedor.style.display = "none";
          return true;
      }
  });

  $('#generar_reporte_completo_email').click(function(e) {
    e.preventDefault();
    var grupo = $('#filtro_grupo').val();
    var usuario = $('#filtro_usuario').val();
    var desde = $('#filtro_desde').val();
    var hasta = $('#filtro_hasta').val();
    var email = $('#email_text').val();
    var reporte_id =  $('#filtro_reporte_id').val();

    $.ajax({
      type: 'post',
      url: site_url+'tramites/ver_completo_email/' + reporte_id + "?filtro_grupo=" + grupo + "&filtro_usuario=" + usuario + "&filtro_desde=" + desde + "&filtro_hasta=" + hasta+ "&email=" + email,
      success: function(response) {
      }
    });
    $('#modal_filtro').modal("hide");
    $('#modal_email').modal("hide");

  });

  $('#generar_reporte_basico_email').click(function(e) {
    e.preventDefault();
    var desde = $('#filtro_desde_basico').val();
    var hasta = $('#filtro_hasta_basico').val();
    var email = $('#email_text_basico').val();
    var reporte_id =  $('#filtro_reporte_id_basico').val();

    $.ajax({
      type: 'post',
      url: site_url+'tramites/ver_basico_email/' + reporte_id + "?filtro_desde=" + desde + "&filtro_hasta=" + hasta+ "&email=" + email,
      success: function(response) {
      }
    });
    $('#modal_filtro_basico').modal("hide");
    $('#modal_email_basico').modal("hide");

  });

  $('.solicita_filtro').click(function(){
    var reporte_id = $(this).attr('data-reporte');
    $('form #filtro_reporte_id').val(reporte_id);
    $('#modal_filtro').modal();
  });

  $('.solicita_filtro_basico').click(function(){
    var reporte_id = $(this).attr('data-reporte');
    $('form #filtro_reporte_id_basico').val(reporte_id);
    $('#modal_filtro_basico').modal();
  });

  $('.tooltip_help_click').click(function() {
  var elem = $(this).parent().find('.tooltip_help_line').first();
  if($(elem).is(':visible')) {
    $(elem).hide();
  }
  else {
    $(elem).removeClass('hidden').show();
  }
});


$('.tooltip_help_click_radio').click(function() {
  var elem = $(this).parent().parent().children('.tooltip_help_line');
  if($(elem).is(':visible')) {
    $(elem).hide();
  }
  else {
    $(elem).removeClass('hidden').show();
  }
});



  if(window.location.href.match(/filtro=([0-9]+)/)) {
    $("#busqueda_filtro").slideToggle();
  }

  if (window.location.search.indexOf('xbp') == 1) {
    setTimeout(function() {
      $('#form_pago_submit').trigger('click');
    }, 300);
  }

  if (window.location.search.indexOf('xbpg') == 1) {
    setTimeout(function() {
      if($('#form_pago_submit_real_generico').attr('href')) {
        window.location.href =  $('#form_pago_submit_real_generico').attr('href');
      }
      else {
        $('#form_pago_submit_generico_real').trigger('click');
      }
    }, 300);
  }

  $("#busqueda_modificacion_desde").datepicker($.extend({
    onSelect: function() {
      var minDate = $(this).datepicker('getDate');
      minDate.setDate(minDate.getDate());
      $("#busqueda_modificacion_hasta").datepicker( "option", "minDate", minDate);
    }
  }));

  $("#busqueda_modificacion_hasta").datepicker($.extend({
    onSelect: function() {
      var maxDate = $(this).datepicker('getDate');
      maxDate.setDate(maxDate.getDate());
      $("#busqueda_modificacion_desde").datepicker( "option", "maxDate", maxDate);
    }
  }));

  $("#busqueda_modificacion_hasta").keyup(function() {
    $("#busqueda_modificacion_desde").datepicker('destroy');
    $("#busqueda_modificacion_desde").datepicker($.extend({
      onSelect: function() {
        var minDate = $(this).datepicker('getDate');
        minDate.setDate(minDate.getDate());
        $("#busqueda_modificacion_hasta").datepicker( "option", "minDate", minDate);
      }
    }));

    $("#busqueda_modificacion_hasta").datepicker('destroy');
    $("#busqueda_modificacion_hasta").datepicker($.extend({
      onSelect: function() {
        var maxDate = $(this).datepicker('getDate');
        maxDate.setDate(maxDate.getDate());
        $("#busqueda_modificacion_desde").datepicker( "option", "maxDate", maxDate);
      }
    }));
  });

  $('#busqueda_filtro_toggle').click(function() {
    $("#busqueda_filtro").slideToggle();
  });

  //-- Oculto/Muestro campos de busqueda segun URL
  if(window.location.href.split('?')[0] === document.Constants.host + '/tramites/busqueda_filtros_participados'){
    $('#busqueda_filtro').css('display','block');
  }
  else if(window.location.href.split('?')[0] === document.Constants.host + '/etapas/busqueda_filtros_pendientes'){
    $('#busqueda_filtro').css('display','block');
  }
  else if(window.location.href.split('?')[0] === document.Constants.host + '/etapas/busqueda_filtros_sinasignar'){
    $('#busqueda_filtro').css('display','block');
  }
  else {
    $('#busqueda_filtro').css('display','none');
  }


  //-- Seteo variables de URL para ajax boton btn_buscar_filtro
  if(window.location.href.split('?')[0].indexOf('/tramites/participados') >=0 ||
    window.location.href.split('?')[0].indexOf('/tramites/busqueda_filtros_participados') >=0){
    var url_datos = '/tramites/busqueda_filtros_participados';
  }
  else if (window.location.href.split('?')[0].indexOf('/etapas/inbox') >=0 ||
      window.location.href.split('?')[0].indexOf('/etapas/busqueda_filtros_pendientes') >=0){
    var url_datos = '/etapas/busqueda_filtros_pendientes';
  }
  else{
    var url_datos = '/etapas/busqueda_filtros_sinasignar';
  }

  //-- Nuevos Filtros por base de datos
  $('#btn_buscar_filtro').click(function(e) {
    e.preventDefault();

    var busqueda_id_tramite = $('#busqueda_id_tramite').val();
    var busqueda_etapa = $('#busqueda_etapa').val();
    var busqueda_grupo = $('#busqueda_grupo').val();
    var busqueda_termino = $('#busqueda_termino').val();
    var busqueda_nombre = $('#busqueda_nombre').val();
    var busqueda_documento = $('#busqueda_documento').val();
    var busqueda_modificacion_desde = $('#busqueda_modificacion_desde').val();
    var busqueda_modificacion_hasta = $('#busqueda_modificacion_hasta').val();

      $.ajax({
        type: 'get',
        dataType: 'html',
        url: document.Constants.host + url_datos,
        data: {
          'busqueda_id_tramite' : busqueda_id_tramite,
          'busqueda_etapa' : busqueda_etapa,
          'busqueda_grupo' : busqueda_grupo,
          'busqueda_nombre' : busqueda_nombre,
          'busqueda_documento' : busqueda_documento,
          'busqueda_modificacion_desde' : busqueda_modificacion_desde,
          'busqueda_modificacion_hasta' : busqueda_modificacion_hasta,
          'termino' : busqueda_termino
        },
        async: true,
        complete: function(data) {
          var tabla = $(data.responseText).find('#mainTable');
          $('#mainTable').html(tabla.html());
          $('#paginado_div').html('');
          $('#paginado_div').html($(data.responseText).find('#paginado_div').html());

          if($('#mainTable tr').length == 0){
              $('#mainTable').html('No hay trámites con los filtros seleccionados.');
              $('#paginado_div').html('');
          }
        }
      });
  });

  // -- Filtros Viejos
  $('#busqueda_filtro_inbox').click(function() {
    if($('#busqueda_termino').val()) {
      var termino = $('#busqueda_termino').val();

      $.ajax({
        type: 'post',
        dataType: 'html',
        url: document.Constants.host + '/etapas/busqueda_termino',
        data: {'termino': termino},
        async: true,
        complete: function(data) {
          document.busqueda_termino = true;
          var tabla = $(data.responseText).find('#mainTable');
          $('#mainTable').html(tabla.html());
          filtro();
        }
      });
    }
    else {
      // --  Verifica si antes se hizo una busqueda con termino
      if(document.busqueda_termino) {
        document.busqueda_termino = null;

        // -- Si esta en la pagina de INBOX recarga la lista primero
        if($('#pagina_inbox').length) {
          $.ajax({
            type: 'post',
            dataType: 'html',
            url: document.Constants.host + '/etapas/inbox',
            async: true,
            complete: function(data) {
              var tabla = $(data.responseText).find('#mainTable');
              $('#mainTable').html(tabla.html());
              filtro();
            }
          });
        }
        // -- Si esta en la pagina de SINASIGNAR recarga la lista primero
        else if($('#pagina_sinasignar').length) {
          document.busqueda_termino = null;

          $.ajax({
            type: 'post',
            dataType: 'html',
            url: document.Constants.host + '/etapas/sinasignar',
            async: true,
            complete: function(data) {
              var tabla = $(data.responseText).find('#mainTable');
              $('#mainTable').html(tabla.html());
              filtro();
            }
          });
        }
        // -- Si esta en la pagina de MISTRAMITES recarga la lista primero
        else if($('#pagina_mistramites').length) {
          document.busqueda_termino = null;

          $.ajax({
            type: 'post',
            dataType: 'html',
            url: document.Constants.host + '/tramites/participados',
            async: true,
            complete: function(data) {
              var tabla = $(data.responseText).find('#mainTable');
              $('#mainTable').html(tabla.html());
              filtro();
            }
          });
        }
      }
      else {
        filtro();
      }
    }
  });


  if ((navigator.appVersion.toString().indexOf(".NET") > 0) || (navigator.appVersion.toString().indexOf("Edge") > 0)) {
    setTimeout(function() {
      $('#form_pago_submit').click(function(e) {
        $('#form_pago_submit').val(' Espere por favor... ').attr({'disabled': true});

        $.ajax({
          type: 'post',
          url: document.Constants.host + '/pagos/envia_email_inicio',
          data: {'url': window.location.href, 'idSol':$('input[name="IdSol"]').val()},
          complete: function(resultado) {
            $('#form_pago_submit_real').trigger('click');
          }
        });
      });
    }, 1000);
  }
  else {
    $('#form_pago_submit').click(function(e) {
      $('#form_pago_submit').val(' Espere por favor... ').attr({'disabled': true});

      $.ajax({
        type: 'post',
        url: document.Constants.host + '/pagos/envia_email_inicio',
        data: {'url': window.location.href, 'idSol':$('input[name="IdSol"]').val()},
        complete: function(resultado) {
          $('#form_pago_submit_real').trigger('click');
        }
      });
    });
  }



  if ((navigator.appVersion.toString().indexOf(".NET") > 0) || (navigator.appVersion.toString().indexOf("Edge") > 0)) {
    setTimeout(function() {
      $('#form_pago_submit_generico').click(function(e) {
        $('#form_pago_submit_generico').val(' Espere por favor... ').attr({'disabled': true});
        $.ajax({
          type: 'post',
          url: document.Constants.host + '/pagos/envia_email_inicio_generico',
          data: {'url': window.location.href, 'idPasarela':$('#pasarela_generica_id').val()},
          complete: function(resultado) {
            $('#form_pago_submit_generico_real').trigger('click');
          }
        });
      });
    }, 1000);
  }
  else {
    $('#form_pago_submit_generico').click(function(e) {
      $('#form_pago_submit_generico').val(' Espere por favor... ').attr({'disabled': true});
      $.ajax({
        type: 'post',
        url: document.Constants.host + '/pagos/envia_email_inicio_generico',
        data: {'url': window.location.href, 'idPasarela':$('#pasarela_generica_id').val()},
        complete: function(resultado) {
          $('#form_pago_submit_generico_real').trigger('click');
        }
      });
    });
  }

  if($('#form_pago_submit_generico').length) {
    $(".form-action-buttons").find(".btn-primary").hide();
  }

  // -- Prepara el formulario de pasarela de pagos
  if($('#form_pasarela_pago').length) {
    $(".form-action-buttons").find(".btn-primary").remove();
    $('#form_pasarela_pago').wrap("<form id='form_pago' method='post' action='"+ $('#form_pasarela_pago').attr('data-action') +"'></form>");

    if ((navigator.appVersion.toString().indexOf(".NET") > 0) || (navigator.appVersion.toString().indexOf("Edge") > 0)) {
      $('form').first().replaceWith('<div>'+$('form').first().html()+'</div>');
    }
  }

  // -- Prepara el formulario de pasarela de pagos
  if($('#form_pasarela_pago_generica').length) {
    if($('#form_pasarela_pago_generica .validacion-error').length) {
      $(".form-action-buttons").find(".btn-primary").remove();
    }

    $('#form_pasarela_pago_generica').wrap("<form id='form_pago_generica' method='post' action='"+ $('#form_pasarela_pago_generica').attr('data-action') +"'></form>");

    if ((navigator.appVersion.toString().indexOf(".NET") > 0) || (navigator.appVersion.toString().indexOf("Edge") > 0)) {
      if(!$('#verificar_estado_pago_generico').length) {
        $ ('form').first().replaceWith('<div>'+$('form').first().html()+'</div>');
      }
    }
  }

  // -- Previene que el dropdown de login se cierre al hacer click en elementos contenidos
  $('.dropdown-menu input, .dropdown-menu label, .dropdown-menu button').click(function(e) {
    e.stopPropagation();
  });

  // -- Limpia el formulario de elementos vacios del modelador
  if($('.step-boxes').length) {
    $('.control-group').each(function() {
      if(!$(this).find('.campo_error, .campo_alerta, .campo_exito').length) {
        if($(this).text().trim().length == 0) {
          $(this).remove();
        }
      }
    });
  }

  // -- Consulta y muestra estado de pago
  $('.consulta_estado_pago').click(function(e) {
    e.preventDefault();

    document.texto_boton_consulta_estado_pago = $('.consulta_estado_pago').text();
    $('.consulta_estado_pago').attr({'disabled': true});
    $('.consulta_estado_pago').text('Consultando...');

    $.ajax({
      type: 'post',
      url: document.Constants.host + '/pagos/consulta_estado',
      data: {'IdSol': $('input[name="IdSol"]').val(), 'IdTramite': $('input[name="IdTramite"]').val(), 'IdEtapa': $('input[name="IdEtapa"]').val()},
      complete: function(resultado) {
        $('.consulta_estado_pago').text(document.texto_boton_consulta_estado_pago);
        $('.consulta_estado_pago').attr({'disabled': false});

        resultado = $.parseJSON(resultado.responseText);
        var mensaje = '';

        switch(resultado.estado) {
          case 'error':
            mensaje += '<div class="dialogo validacion-error"><h3 class="dialogos_titulo">'+ resultado.titulo +'</h3><div class="alert alert-error">'+ resultado.mensaje +'</div></div>';
            break;
          case 'alerta':
            mensaje += '<div class="dialogo validacion-warning"><h3 class="dialogos_titulo">'+ resultado.titulo +'</h3><div class="alert alert-warning">'+ resultado.mensaje +'</div></div>';
            break;
          case 'ok':
            mensaje += '<div class="dialogo validacion-success"><h3 class="dialogos_titulo">'+ resultado.titulo +'</h3><div class="alert alert-success">'+ resultado.mensaje +'</div></div>';
            break;
          default:
            mensaje += '<div class="dialogo validacion-error"><h3 class="dialogos_titulo">'+ resultado.titulo +'</h3><div class="alert alert-error">'+ resultado.mensaje +'</div></div>';
        }

        $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');
      }
    });
  });

  // -- Consulta el estado de un pago en componente pago
  $('#boton_reload_token').click(function() {
    pagos_reload_token($('input[name="IdEtapa"]').val());
  });

  //para visualizar
  if($('#verificar_estado_pago_visualizacion').size() > 0) {
    $.ajax({
      type: 'post',
      url: document.Constants.host + '/pagos/consulta_estado_directo',
      data: {'IdSol': $('input[name="IdSol"]').val(), 'IdEtapa': $('input[name="IdEtapa"]').val(), 'IdPasarela': $('#pasarela_generica_id_antel').val()},
      complete: function(resultado) {
        resultado = $.parseJSON(resultado.responseText);
        var mensaje = '';

        switch(resultado.estado) {
          case 'timeout':
            mensaje += '<div class="dialogo validacion-error"><h3 class="dialogos_titulo">'+ resultado.titulo +'</h3><div>'+ resultado.mensaje +'<br /><br /><a class="btn-link" href="'+ window.location.href +'">Consultar</a></div></div>';
            $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');
            break;
          case 'error':
            mensaje += '<div class="text-center"><p>'+ $('input[name="MsgPago"]').val() +'</p><input type="button" value="Realizar pago" class="btn btn-primary" id="boton_reload_token" /></div>';
            $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');

            break;
          case 'ok':
            mensaje += '<div class="dialogo validacion-success"><h3 class="dialogos_titulo">'+ resultado.titulo +'</h3><div>'+ resultado.mensaje +'</div></div>';
            $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');
            break;
          case 'rc':
            mensaje += '<div class="dialogo validacion-warning"><h3 class="dialogos_titulo">'+ resultado.titulo +'</h3><div>'+ resultado.mensaje +'<br><br><a target="_blank" href="'+ document.Constants.host +'/pagos/generar_ticket?t='+ $('input[name="IdSol"]').val() +'&e='+ $('input[name="IdEtapa"]').val() +'" class="btn-link">Imprimir ticket</a></div></div>';
            $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');

            break;
          case 'pendiente':
            if(resultado.forma_pago == 'online') {
              mensaje += '<div class="dialogo validacion-warning"><h3 class="dialogos_titulo">'+ resultado.titulo +'</h3><div>'+ resultado.mensaje_1 +'<br /><br /><a class="btn-link" href="'+ window.location.href +'">Consultar</a><br /><br />'+ resultado.mensaje_2 +'<br><br><input type="button" value="Realizar pago" class="btn-link" id="boton_reload_token" /></div></div>';
              $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');

            }
            else {
              mensaje += '<div class="text-center"><p>'+ $('input[name="MsgPago"]').val() +'</p><input type="button" value="Realizar pago" class="btn btn-primary" id="boton_reload_token" /></div>';
              $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');
            }
            break;
          case 'reversado':

            if(resultado.forma_pago == 'online') {
              mensaje += '<p>'+ resultado.mensaje + '<br><br><input type="button" value="Realizar pago" class="btn btn-primary" id="boton_nuevo_pago" /></p>';
              $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');

            }
            else {
              mensaje += '<div class="dialogo validacion-warning"><h3 class="dialogos_titulo">'+ resultado.titulo +'</h3><div>'+ resultado.mensaje_1 +'<br>'+ resultado.mensaje_2 +'<br><br><a href="#" class="btn-link" id="boton_reload_token">Realizar pago</a></div></div>';
              $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');
            }
          break;
          default:
            if(resultado.forma_pago == 'online') {
              mensaje += '<div class="dialogo validacion-warning"><h3 class="dialogos_titulo">'+ resultado.titulo +'</h3><div>En caso de ya haber efectuado el pago, el mismo no está confirmado, por favor vuelva a consultar en unos minutos.<br>En caso de no haber efectuado el pago, puede realizarlo haciendo clic en el siguiente botón.<br><br><input type="button" value="Realizar pago" class="btn-link" id="boton_reload_token" /><br><br><br>Si desea volver a consultar el estado de su pago, puede hacerlo accediendo a la siguiente URL:<br> <a class="btn-link" href="'+ window.location.href +'" target="_blank">' + window.location.href + '</a></div></div>';
              $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');
            }
            else {
              mensaje += '<p>'+ resultado.mensaje + '<br><br><input type="button" value="Realizar pago" class="btn btn-primary" id="boton_nuevo_pago" /></p>';
              $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');
            }
        }
      }
    });
  }


  if($('#verificar_estado_pago').size() > 0) {
    var boton_continuar = $(".form-action-buttons").html();
    $(".form-action-buttons").find(".btn-primary").remove();

    $.ajax({
      type: 'post',
      url: document.Constants.host + '/pagos/consulta_estado_directo',
      data: {'IdSol': $('input[name="IdSol"]').val(), 'IdEtapa': $('input[name="IdEtapa"]').val()},
      complete: function(resultado) {
        resultado = $.parseJSON(resultado.responseText);
        var mensaje = '';

        switch(resultado.estado) {
          case 'timeout':
            mensaje += '<div class="dialogo validacion-error"><h3 class="dialogos_titulo">'+ resultado.titulo +'</h3><div>'+ resultado.mensaje +'<br /><br /><a class="btn-link" href="'+ window.location.href +'">Consultar</a></div></div>';
            $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');
            break;
          case 'error':
            $(".form-action-buttons .action-buttons-primary").remove();
            mensaje += '<div class="text-center"><p>'+ $('input[name="MsgPago"]').val() +'</p><input type="button" value="Realizar pago" class="btn btn-primary" id="boton_reload_token" /></div>';
            $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');

            $('#boton_reload_token').click(function() {
              pagos_reload_token($('input[name="IdEtapa"]').val());
            });
            break;
          case 'ok':
            mensaje += '<div class="dialogo validacion-success"><h3 class="dialogos_titulo">'+ resultado.titulo +'</h3><div>'+ resultado.mensaje +'</div></div>';
            $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');
            setTimeout(function() {
              $(".form-action-buttons").html(boton_continuar);
            }, 500);
            break;
          case 'rc':
            // $('.ajaxForm').attr({'action': ''});
            $(".form-action-buttons .action-buttons-primary").remove();
            mensaje += '<div class="dialogo validacion-warning"><h3 class="dialogos_titulo">'+ resultado.titulo +'</h3><div>'+ resultado.mensaje +'<br><br><a target="_blank" href="'+ document.Constants.host +'/pagos/generar_ticket?t='+ $('input[name="IdSol"]').val() +'&e='+ $('input[name="IdEtapa"]').val() +'" class="btn-link">Imprimir ticket</a></div></div>';
            $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');

            setTimeout(function() {
              $(".form-action-buttons").html(boton_continuar);
            }, 500);
            break;
          case 'pendiente':
            $('.ajaxForm').attr({'action': ''});
            $(".form-action-buttons .action-buttons-primary").remove();
            if(resultado.forma_pago == 'online') {
              mensaje += '<div class="dialogo validacion-warning"><h3 class="dialogos_titulo">'+ resultado.titulo +'</h3><div>'+ resultado.mensaje_1 +'<br /><br /><a class="btn-link" href="'+ window.location.href +'">Consultar</a><br /><br />'+ resultado.mensaje_2 +'<br><br><input type="button" value="Realizar pago" class="btn-link" id="boton_reload_token" /></div></div>';
              $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');

              $('#boton_reload_token').click(function() {
                pagos_reload_token($('input[name="IdEtapa"]').val());
              });
            }
            else {
              mensaje += '<div class="text-center"><p>'+ $('input[name="MsgPago"]').val() +'</p><input type="button" value="Realizar pago" class="btn btn-primary" id="boton_reload_token" /></div>';
              $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');

              $('#boton_reload_token').click(function() {
                pagos_reload_token($('input[name="IdEtapa"]').val());
              });
            }
            break;
          case 'reversado':
            $('.ajaxForm').attr({'action': ''});
            $(".form-action-buttons .action-buttons-primary").remove();
            if(resultado.forma_pago == 'online') {
              mensaje += '<p>'+ resultado.mensaje + '<br><br><input type="button" value="Realizar pago" class="btn btn-primary" id="boton_nuevo_pago" /></p>';
              $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');

              $('#boton_nuevo_pago').click(function() {
                pagos_reload_token($('input[name="IdEtapa"]').val());
              });
            }
            else {
              mensaje += '<div class="dialogo validacion-warning"><h3 class="dialogos_titulo">'+ resultado.titulo +'</h3><div>'+ resultado.mensaje_1 +'<br>'+ resultado.mensaje_2 +'<br><br><a href="#" class="btn-link" id="boton_reload_token">Realizar pago</a></div></div>';
              $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');

              $('#boton_reload_token').click(function() {
                pagos_reload_token($('input[name="IdEtapa"]').val());
              });
            }
          break;
          default:
            $('.ajaxForm').attr({'action': ''});
            $(".form-action-buttons .action-buttons-primary").remove();
            if(resultado.forma_pago == 'online') {
              mensaje += '<div class="dialogo validacion-warning"><h3 class="dialogos_titulo">'+ resultado.titulo +'</h3><div>En caso de ya haber efectuado el pago, el mismo no está confirmado, por favor vuelva a consultar en unos minutos.<br>En caso de no haber efectuado el pago, puede realizarlo haciendo clic en el siguiente botón.<br><br><input type="button" value="Realizar pago" class="btn-link" id="boton_reload_token" /><br><br><br>Si desea volver a consultar el estado de su pago, puede hacerlo accediendo a la siguiente URL:<br> <a class="btn-link" href="'+ window.location.href +'" target="_blank">' + window.location.href + '</a></div></div>';
              $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');

              $('#boton_reload_token').click(function() {
                pagos_reload_token($('input[name="IdEtapa"]').val());
              });
            }
            else {
              mensaje += '<p>'+ resultado.mensaje + '<br><br><input type="button" value="Realizar pago" class="btn btn-primary" id="boton_nuevo_pago" /></p>';
              $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');

              $('#boton_nuevo_pago').click(function() {
                pagos_reload_token($('input[name="IdEtapa"]').val());
              });
            }
        }
      }
    });
  }

  //para visualizar en mis tramites
  if($('#verificar_estado_pago_generico_visualizacion').length) {

        //modo visualizacion el boton de realizar pago no se ve
        $("#boton_reload_token_generico").hide();
        $('#form_pago_submit_generico').hide();
        $('#form_pago_etiqueta').hide();

        $.ajax({
          type: 'post',
          url: document.Constants.host + '/pagos/consulta_estado_directo_generico',
          data: {'IdSol': $('#id_solicitud_pago_generico').val(), 'IdEtapa': $('#etapa_id').val(), 'IdPasarela': $('#pasarela_generica_id').val()},
          complete: function(resultado) {
            var data = $.parseJSON(resultado.responseText);

            if(!data) {
              mensaje = '<div class="dialogo validacion-error"><h3 class="dialogos_titulo">Mensaje</h3><div class="alert alert-error">No se ha podido obtener el estado de pago.</div></div>';
              $('.mensaje_estado_pago_generico').html(mensaje).removeClass('hidden').show();
              return;
            }

            if(data['estado'] == 'timeout') {
              mensaje = '<div class="dialogo validacion-error"><h3 class="dialogos_titulo">Mensaje</h3><div class="alert alert-error">No se ha podido obtener el estado de pago.</div></div>';
              $('.mensaje_estado_pago_generico').html(mensaje).removeClass('hidden').show();
              return;
            }
            else if(data['estado'] == 'ok'){
              resultado = data['data'];
              $('.imprimir_ticket_generico').removeClass('hidden').show();
              $('.cuerpo_componente_pago_generico').removeClass('hidden').show();

              var mensaje = '';

              var estado_mensaje = resultado[2][0];
                  estado_mensaje = estado_mensaje.split('=');
              if(estado_mensaje[0] == 'OK') {
                mensaje += '<div class="dialogo validacion-success"><h3 class="dialogos_titulo">Mensaje</h3><div class="alert alert-success">'+  estado_mensaje[1] +'</div></div>';
              }
              else if(estado_mensaje[0] == 'ALERTA') {
                mensaje += '<div class="dialogo validacion-warning"><h3 class="dialogos_titulo">Mensaje</h3><div class="alert alert-warning">'+  estado_mensaje[1] +'</div></div>';
              }
              else if(estado_mensaje[0] == 'ERROR') {
                mensaje += '<div class="dialogo validacion-error"><h3 class="dialogos_titulo">Mensaje</h3><div class="alert alert-error">'+  estado_mensaje[1] +'</div></div>';
              }
              //se despliega el mensaje con el estado del pago
              $('.mensaje_estado_pago_generico').html(mensaje).removeClass('hidden').show();
              //se muestra el boton continuar
              $(".form-action-buttons").find(".btn-primary").removeClass('hidden').show();
            }
          }
        });
  }


  // verifica pago generico
  if($('#verificar_estado_pago_generico').length) {
    //el boton siguiente y volver
    var boton_continuar = $(".form-action-buttons").html();
    $(".form-action-buttons").find(".btn-primary").remove();
    $(".form-action-buttons .action-buttons-primary").remove();

    //ocultamos el boton de pago mientras se consulta el estado.
    $("#boton_reload_token_generico").hide();
    $('#form_pago_submit_generico').hide();
    $('#form_pago_etiqueta').hide();

    $.ajax({
      type: 'post',
      url: document.Constants.host + '/pagos/consulta_estado_directo_generico',
      data: {'IdSol': $('#id_solicitud_pago_generico').val(), 'IdEtapa': $('#etapa_id').val(), 'IdPasarela': $('#pasarela_generica_id').val()},
      complete: function(resultado) {
        var data = $.parseJSON(resultado.responseText);

        if(!data) {
          mensaje = '<div class="dialogo validacion-error"><h3 class="dialogos_titulo">Mensaje</h3><div class="alert alert-error">No se ha podido obtener el estado de pago.</div></div>';
          $('.mensaje_estado_pago_generico').html(mensaje).removeClass('hidden').show();
          return;
        }

        if(data['estado'] == 'timeout') {
          mensaje = '<div class="dialogo validacion-error"><h3 class="dialogos_titulo">Mensaje</h3><div class="alert alert-error">No se ha podido obtener el estado de pago.</div></div>';
          $('.mensaje_estado_pago_generico').html(mensaje).removeClass('hidden').show();
          return;
        }
        else if(data['estado'] == 'ok'){
          resultado = data['data'];
          $('.imprimir_ticket_generico').removeClass('hidden').show();
          $('.cuerpo_componente_pago_generico').removeClass('hidden').show();

          var mensaje = '';

          var estado_mensaje = resultado[2][0];
              estado_mensaje = estado_mensaje.split('=');
          if(estado_mensaje[0] == 'OK') {
            mensaje += '<div class="dialogo validacion-success"><h3 class="dialogos_titulo">Mensaje</h3><div class="alert alert-success">'+  estado_mensaje[1] +'</div></div>';
          }
          else if(estado_mensaje[0] == 'ALERTA') {
            mensaje += '<div class="dialogo validacion-warning"><h3 class="dialogos_titulo">Mensaje</h3><div class="alert alert-warning">'+  estado_mensaje[1] +'</div></div>';
          }
          else if(estado_mensaje[0] == 'ERROR') {
            mensaje += '<div class="dialogo validacion-error"><h3 class="dialogos_titulo">Mensaje</h3><div class="alert alert-error">'+  estado_mensaje[1] +'</div></div>';
          }

          // Verifica primer estado para saber si continua o no
          switch(resultado[1][0]) {
            // Habilitado para volver a pagar
            case 'HABILITADO':
              $('.mensaje_estado_pago_generico').hide();

              mensaje += '<div class="text-center"><p>'+ $('input[name="MsgPagoGenerico"]').val() +'</p><input type="button" value="Realizar pago" class="btn btn-primary" id="boton_reload_token_generico" /></div>';
              $('#form_pago_submit_generico').hide();
              $('#form_pago_etiqueta').hide();
              $('.mensaje_estado_pago_generico').html(mensaje).removeClass('hidden').show();
              $('#form_pago_generica').replaceWith(function() {
                  return $('<div/>', {
                      html: this.innerHTML
                  });
              });

              $('#boton_reload_token_generico').click(function() {
                $('#boton_reload_token_generico').val(' Espere por favor... ').attr({'disabled': true});
                pagos_reload_token_generico($('#etapa_id').val(), $('#var_idsol').val());
              });

              if(resultado[0][0] == 'CONTINUAR') {
                setTimeout(function() {
                  $('#form_pasarela_pago_generica').replaceWith(function() {
                      return $('<div/>', {
                          html: this.innerHTML
                      });
                  });
                  $(".form-action-buttons").html(boton_continuar);
                  $(".form-action-buttons").find(".btn-primary").show();
                }, 500);
              }
              break;
            // No habilitado para volver a pagar
            case 'NO_HABILITADO':
              $('.mensaje_estado_pago_generico').hide();
              $('#form_pago_submit_generico').hide();
              $('#form_pago_etiqueta').hide();

              if(mensaje != '') {
                $('.mensaje_estado_pago_generico').html(mensaje).removeClass('hidden').show();
              }

              if(resultado[0][0] == 'CONTINUAR') {
                setTimeout(function() {
                  $(".form-action-buttons").html(boton_continuar);
                  $(".form-action-buttons").find(".btn-primary").show();
                }, 500);
              }
              break;
            default:
              $('.mensaje_estado_pago_generico').hide();
          }

          $('.imprimir_ticket_generico_post_button').click(function() {
            $('#form_pasarela_pago_generica').replaceWith(function() {
                return $('<div/>', {
                    html: this.innerHTML
                });
            });

            $('form.ajaxForm.dynaForm.form-horizontal').replaceWith(function() {
                return $('<div/>', {
                    html: this.innerHTML
                });
            });

            $('.imprimir_ticket_generico_post').wrap("<form id='imprimir_ticket_generico_post_form' method='post' action='" + $('.imprimir_ticket_generico_post').attr('data-action') + "'></form>");
            $('#imprimir_ticket_generico_post_form input').each(function() {
              var name = $(this).attr('name');
              name = name.replace('__ticket', '');
              $(this).attr({'name':name});
            });

            $('#imprimir_ticket_generico_post_form').submit();
          });
        }
      }
    });
  }

	// -- Gestiona encuesta de satisfacción
  if($('#encuesta_satisfaccion_form').length) {
    $('button[type="submit"]').not('.login-btn').hide();
    $('#encuesta_satisfaccion_form_enviar').wrap('<li class="action-buttons-primary"></li>');
    $('#encuesta_satisfaccion_form_enviar').parent().prependTo('.form-action-buttons .action-buttons-primary ul');
    $('.form-action-buttons #encuesta_satisfaccion_form_enviar').removeClass('hidden');

    function enviarEncuesta() {
      var reporte = $('#encuesta_satisfaccion_form *').serializeObject();

      $.ajax({
        type: 'post',
        url: document.Constants.host + '/encuesta_satisfaccion/crear',
        data: {reporte: reporte},
        complete: function(resultado) {

          switch(resultado.responseText) {
              case '0':
                // $('#encuesta_satisfaccion_form').hide();
                $('.form-action-buttons #encuesta_satisfaccion_form_enviar').hide();
                //$('<div id="encuesta_satisfaccion_form_mensaje" class="alert alert-success">Se ha completado la encuesta. Muchas gracias.</div>').insertAfter('#encuesta_satisfaccion_form');
                $('button[type="submit"]').show();

                setTimeout(function() {
                  $('button[type="submit"]').trigger('click');
                }, 300);
                break;
              case '-1':
                $('#encuesta_satisfaccion_form').hide();
                $('.form-action-buttons #encuesta_satisfaccion_form_enviar').hide();
                $('<div id="encuesta_satisfaccion_form_mensaje" class="alert alert-error">No se ha podido enviar, por favor vuelva a intentarlo más tarde. <a href="#" class="" id="encuesta_satisfaccion_form_refrescar">Reintentar</a></div>').insertAfter('#encuesta_satisfaccion_form');
                break;
          }

          $('#encuesta_satisfaccion_form_refrescar').on('click', function() {
            $('#encuesta_satisfaccion_form_mensaje').hide();
            $('#encuesta_satisfaccion_form').show();

            $('#encuesta_satisfaccion_form_enviar').show();
          });

          return;
        }
      });
    }

    $('#encuesta_satisfaccion_form').keypress(function(e) {
      if(e.which == 13) {
        e.preventDefault();
        enviarEncuesta();}
    });

    $('#encuesta_satisfaccion_form_enviar').on('click', function() {
      enviarEncuesta();
    });
  }

});

function pagos_reload_token(etapa_id) {
  $.ajax({
    type: 'post',
    url: document.Constants.host + '/pagos/limpiar_sesion',
    data: {etapa_id: etapa_id},
    complete: function(resultado) {
      var url = window.location.href;
      url = url.replace('?xbp', '');
      url = url.replace('#?', '');
      window.location.href = url + '?xbp';
    }
  });
}

// Funcion que al recibir llamada del boton de volvera intentar pago, hace un back para volver a
// cargar la accion de pasarela de pagos.
function pagos_reload_token_generico(etapa_id, variable_idsol) {
  $.ajax({
    type: 'post',
    url: document.Constants.host + '/pagos/limpiar_sesion_generico',
    data: {etapa_id: etapa_id, variable_idsol: variable_idsol},
    complete: function(resultado) {
      var url = window.location.href;
      url = url.replace('?xbpg', '');
      url = url.replace('#', '');
      window.location.href = url + '?xbpg';
    }
  });
}

function paginador() {
  $('table.paginar').each(function() {
    var currentPage = 0;
    var numPerPage = document.Constants.max_paginado;
    var $table = $(this);
    $table.bind('repaginate', function() {
      $table.find('tbody tr[visible!="0"]').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
      $(this).parent().addClass('active');
    });
    $table.trigger('repaginate');
    var numRows = $table.find('tbody tr[visible!="0"]').length;
    var numPages = Math.ceil(numRows / numPerPage);
    var $pager = $('<ul class="inner-pagination"></ul>');
    for (var page = 0; page < numPages; page++) {
        $('<a href="#" class="page-number"></a>').text(page + 1).bind('click', {
            newPage: page
        }, function(event) {
            currentPage = event.data['newPage'];
            $table.trigger('repaginate');
            $(this).addClass('active').siblings().removeClass('active');
        }).appendTo($pager).addClass('clickable');
    }
    $pager.insertAfter($table).find('a.page-number:first').addClass('active');
    $('.inner-pagination').wrap('<div class="pagination"></div>');
    $('a.page-number').wrap('<li></li>');
  });
}

function filtro() {
  $('#busqueda_id_tramite').val($.trim($('#busqueda_id_tramite').val()));
  $('#busqueda_etapa').val($.trim($('#busqueda_etapa').val()));
  $('#busqueda_nombre').val($.trim($('#busqueda_nombre').val()));
  $('#busqueda_documento').val($.trim($('#busqueda_documento').val()));

  var desde, hasta = null;
  var meses = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
  var hoy = new Date();
  var dd = hoy.getDate();
  var mm = hoy.getMonth() + 1;
  var yyyy = hoy.getFullYear();

  if(dd <= 9) {
    dd = '0' + dd;
  }

  if(mm <= 9) {
    mm = '0' + mm;
  }

  if($('#busqueda_modificacion_desde').val()) {
    desde = $('#busqueda_modificacion_desde').val();
    var fecha = desde.split('-');

    if(fecha[1] <= 9) {
      fecha[1] = '0' + fecha[1];
    }

    if(fecha[0] <= 9) {
      fecha[0] = '0' + fecha[0];
    }
    var desde_num = fecha[1] + '/' + fecha[0] + '/' + fecha[2];

  }
  else {
    hasta = meses[mm] + '.' + dd + '.' + yyyy;
    var desde_num = mm + '/' + dd + '/' + yyyy;
  }

  if($('#busqueda_modificacion_hasta').val()) {
    hasta = $('#busqueda_modificacion_hasta').val();
    var fecha = hasta.split('-');

    if(fecha[1] <= 9) {
        fecha[1] = '0' + fecha[1];
    }

    if(fecha[0] <= 9) {
      fecha[0] = '0' + fecha[0];
    }
    var hasta_num = fecha[1] + '/' + fecha[0] + '/' + fecha[2];
  }
  else {
    hasta = meses[mm] + '.' + dd + '.' + yyyy
    var hasta_num = mm + '/' + dd + '/' + yyyy;
  }

  $('.filter').multifilter({
    'target': $('#mainTable')
  });
  setTimeout(function() {
    $('#mainTable tr:hidden').attr({'visible': 0});
  }, 400);

  var start = new Date(desde_num);
  var end = new Date(hasta_num);

  var fechas = [];
  while(start <= end) {
   var dd = start.getDate();
   var mm = start.getMonth();
   var yyyy = start.getFullYear();
   if (dd<=9){
     dd = '0'+dd;
   }
   fechas.push(dd + '.' + meses[mm] + '.' + yyyy);
   start.setDate(start.getDate() + 1);
  }

  document.fechas = fechas;

  //si se ingresaron fechas se filtra por fecha el resultado del filtro multifilter
  if($('#busqueda_modificacion_desde').val() || $('#busqueda_modificacion_hasta').val()) {
    if($(document.fechas).size() > 0) {
      $('#mainTable td.list_modificacion').show();
      $('#mainTable td.list_modificacion').each(function() {
        var val = $(this).text();
        val = val.split(' ');
        if($.inArray(val[0], document.fechas) == '-1') {
          $(this).parent().hide();
        }
      });
    }
  }

  $('.pagination').remove();
  setTimeout(function() {
    paginador();
  }, 400);
}


function ir_agenda_externa() {
  $.ajax({
    type: 'post',
    url: document.Constants.host + '/agenda/ir_agenda_externa',
    data: {},
    complete: function(resultado) {
      var url = resultado.responseText;
      window.location.href = url;
    }
  });
}
