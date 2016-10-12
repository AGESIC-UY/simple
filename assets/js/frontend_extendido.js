$(document).ready(function() {
  // -- Prepara el formulario de pasarela de pagos
  if($('#form_pasarela_pago').length) {
    $(".form-action-buttons").find(".btn-primary").remove();
    $('#form_pasarela_pago').wrap("<form method='post' action='"+ $('#form_pasarela_pago').attr('data-action') +"'></form>");
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
