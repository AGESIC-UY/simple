$(document).on('focus', '.datepicker',  function() {
  $(this).datepicker({dateFormat: 'dd-mm-yy'});
});

$(document).on('keydown', '.datepicker',    function() {
  $.datepicker.customKeyPress(event);
});

$.extend($.datepicker, {
    customKeyPress: function (event) {
        var inst = $.datepicker._getInst(event.target);
        var isRTL = inst.dpDiv.is(".ui-datepicker-rtl");
        switch (event.keyCode) {
            case 37:    // LEFT --> -1 day
                $('body').css('overflow','hidden');
            $.datepicker._adjustDate(event.target, (isRTL ? +1 : -1), "D");
            break;
        case 38:    // UPP --> -7 day
            $('body').css('overflow','hidden');
            $.datepicker._adjustDate(event.target, -7, "D");
            break;
        case 39:    // RIGHT --> +1 day
            $('body').css('overflow','hidden');
            $.datepicker._adjustDate(event.target, (isRTL ? -1 : +1), "D");
            break;
        case 40:    // DOWN --> +7 day
            $('body').css('overflow','hidden');
            $.datepicker._adjustDate(event.target, +7, "D");
            break;
        }
        $('body').css('overflow','visible');
    }
});

$.datepicker.regional['es'] = {
         closeText: 'Cerrar',
         prevText: 'Anterior',
         nextText: 'Siguiente',
         currentText: 'Hoy',
         monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
         monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
         dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
         dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
         dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
         weekHeader: 'Sm',
         dateFormat: 'dd-mm-yy',
         firstDay: 1,
         isRTL: false,
         showMonthAfterYear: false,
         yearSuffix: ''
         };
$.datepicker.setDefaults($.datepicker.regional['es']);

$(function() {
  $( ".datepicker" ).datepicker({dateFormat: 'dd-mm-yy'});
});

$('.datepicker').each(function() {
  $(document).on('keydown', this, function() {
    $.datepicker.customKeyPress(event);
  });
})
