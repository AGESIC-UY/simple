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
