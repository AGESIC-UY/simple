// -- Muestra los mensajes de error en su campo correspondiente
function muestraErrores () {
  document.errors = [];

  $('.error').removeClass('error');
  $('.mensaje_error_campo').remove();
  document.ayuda = [];
  $('.validacion > .alert').each(function() {
    $this = $(this);

    var error_reference_original = $this.find('strong').text();
    error_reference_original = error_reference_original.split("@");

    //var error_reference = $this.find('strong').text().toLowerCase();
    var error_reference = error_reference_original[0];

    if((($('form *[name="'+ error_reference +'"]').length) || ($('form *[name="'+ error_reference +'[]"]').length)) && ($.inArray(error_reference, document.errors) == -1)) {
      // -- $('form *[name="'+ error_reference +'"]').wrap('<div class="error_element_wrap"></div>'); /*comentado por mariana*/

      var element = $('form *[name="'+ error_reference +'"]');
      if(!element.length) {
        element = $('form *[name="'+ error_reference +'[]"]');
      }

      var error_regexp = /"[a-zA-Z0-9_-]*"/
      var error_text = $this.text().replace('×', '').replace(error_regexp, '');

      if($(element).parent().parent().is('fieldset')) {
        var label = $(element).parent().prev().first();
        $(element).parent().prev().remove();
        var nuevo_element = $(element).parent().wrap('<div class="control-group error"></div>');
        $(nuevo_element).parent().prepend('<label class="control-label">'+ $(label).text() +'</label>');
      }
      else {
        if($(element).parent().is('label')) {
          if($(element).attr('type') == 'checkbox') {
            $(element).parent().parent().parent().addClass('error');

            var element = $(element).parent().last();
          }
          else {
            $(element).parent().parent().parent().addClass('error');
            var element = $(element).parent().last();
          }
        }
        else {
          $(element).parent().parent().addClass('error');
        }
      }

      error_text = error_text.replace(error_reference+"@", "");

      $('<div class="mensaje_error_campo">'+ error_text +'</div>').insertAfter(element);

      // -- Reemplazando el mensaje de error de este formato: NOMBRE@ETIQUETA por ETIQUETA.
      //  Se necesita obtener el formato NOMBRE@ETIQUETA de parte de la libreria de validacion
      // ya que se deben mostrar los errores tanto de forma de lista como local.
      var mensaje_completo = $('.validacion').html();
      mensaje_completo = mensaje_completo.replace(error_reference+"@", "");
      $('.validacion').html(mensaje_completo);

      document.errors.push(error_reference);

      if($(element).parent().find('.help-block')) {
        var ayuda_contextual = $(element).parent().find('.help-block').first();
        if(typeof ayuda_contextual[0] != 'undefined') {
          $(ayuda_contextual[0]).insertAfter($(element));
        }
      }
    }
  });

  // -- Si hay mas de un error se muestran agrupados, de lo contrario se muestra de forma local.
  if(document.errors.length > 1) {
    $('.validacion').prepend('<span class="dialog-title">Hay <strong>' + document.errors.length + ' errores</strong> en el formulario</span>');

    $('.validacion').show();
    $('body').scrollTo('.validacion', 500);
  }
  else if (document.errors.length == 1) {
    $('.controls').find('.help-block').insertBefore('.mensaje_error_campo');
    $('.validacion').hide();
  }
}

$(document).ready(function(){
    $("[data-toggle=popover]").popover();

    $(".chosen").chosen();

    $(".preventDoubleRequest").one("click", function() {
        $(this).click(function () { return false; });
    });

    $(".datepicker:not([readonly])")
    .datepicker({
        format: "dd-mm-yyyy",
        weekStart: 1,
        autoclose: true,
        language: "es"
    });


    $(".file-uploader").each(function(i,el){
        var $parentDiv=$(el).parent();
        new qq.FileUploader({
            element: el,
            action: $(el).data("action"),
            onComplete: function(id,filename,respuesta){
                if(!respuesta.error){
                    $parentDiv.find("input[type=hidden]").val(respuesta.file_name);
                    $parentDiv.find(".qq-upload-list").empty();
                    $parentDiv.find(".link").html("<a target='blank' href='"+site_url+"uploader/datos_get/"+respuesta.id+"?token="+respuesta.llave+"'>"+respuesta.file_name+"</a> (<a href='#' class='remove'>X</a>)")
                }
            }
        });
    });
    $(".file-uploader").parent().on("click","a.remove",function(){
        var $parentDiv=$(this).closest("div");
        $parentDiv.find("input[type=hidden]").val("");
        $parentDiv.find(".link").empty();
        $parentDiv.find(".qq-upload-list").empty();
    });


    $(".ajaxForm :submit").attr("disabled",false);
    $(document).on("submit",".ajaxForm",function() {
        var form=this;
        if(!form.submitting){
            form.submitting=true;
            $(form).find(":submit").attr("disabled",true);
            $(form).append("<div class='ajaxLoader'>Cargando</div>");
            var ajaxLoader=$(form).find(".ajaxLoader");
            $(ajaxLoader).css({
                left: ($(form).width()/2 - $(ajaxLoader).width()/2)+"px",
                top: ($(form).height()/2 - $(ajaxLoader).height()/2)+"px"
                });
            $.ajax({
                url: form.action,
                data: $(form).serialize(),
                type: form.method,
                dataType: "json",
                success: function(response) {
                  if(response.validacion) {
                    if(response.redirect) {
                      window.location=response.redirect;
                    }
                    else {
                      var f=window[$(form).data("onsuccess")];
                      f(form);
                    }
                  }
                  else {
                    form.submitting=false;
                    $(ajaxLoader).remove();
                    $(form).find(":submit").attr("disabled", false);

                    if(response) {
                      if($(".validacion").html(response.errores)) {
                        muestraErrores();
                      }

                      if(response.error_paso_final) {
                				$(".validacion").html(response.error_paso_final);
                				$(".validacion-error").show();
                			}
                    }

                    /*
                    $('html, body').animate({
                        scrollTop: $(".validacion").offset().top-10
                    });
                    */
                  }
                },
                error: function(){
                    form.submitting=false;
                    $(ajaxLoader).remove();
                    $(form).find(":submit").attr("disabled",false);
                }
            });
        }
        return false;
    });

    //Para manejar los input dependientes en dynaforms
    function prepareDynaForm(form){
        $(form).find(":input[readonly]").prop("disabled",false);

        $(form).find(".campo[data-dependiente-campo]").each(function(i,el){
            var tipo=$(el).data("dependiente-tipo");
            var relacion=$(el).data("dependiente-relacion");
            var campo=$(el).data("dependiente-campo");
            var valor=$(el).data("dependiente-valor");

            var items=$(form).find(":input:not(:hidden)").serializeArray();

            var existe=false;
            for(var i in items){
                if(items[i].name==campo){
                    if(tipo=="regex"){
                        var regex=new RegExp(valor);
                        if(regex.test(items[i].value))
                            existe=true;
                    }else{
                        if(items[i].value==valor)
                            existe=true;
                    }
                    if(relacion=="!=")
                        existe=!existe;
                }
            }
            if(existe){
                if($(form).hasClass("debugForm"))
                    $(el).css("opacity","1.0");
                else
                    $(el).show();

                if(!$(el).data("readonly"))
                    $(el).find(":input").prop("disabled",false);

            }
            else{
                if($(form).hasClass("debugForm"))
                    $(el).css("opacity","0.5");
                else
                    $(el).hide();

                $(el).find(":input").prop("disabled",true);
            }
        });

        $(form).find(":input[readonly]").prop("disabled",true);
    }
    prepareDynaForm(".dynaForm");
    $(".dynaForm").on("change",":input",function(event){
        prepareDynaForm($(event.target).closest(".dynaForm"))
    });

    // -- Guarda paso sin avanzar
    $('#save_step').click(function(){
        $('#no_advance').val(1);
    });

    // -- Organiza los campos dentro de un fieldset.
    if(($('form').size() > 0) && ($('#areaFormulario').size() < 1)) {
      setTimeout(function() {
        $('fieldset').each(function() {
            var nombre = $(this).attr('name');
            if(typeof(nombre) !== 'undefined') {
              var nombre_bloque = nombre;
              nombre = nombre.replace('BLOQUE_', '');

              var elementos = [];
              $('*[data-fieldset="'+ nombre +'"]').each(function() {
                if($(this).parent().parent().attr('data-dependiente-campo')) {
                  elementos.push($(this).parent().parent());
                }
                else {
                  elementos.push($(this).parent().parent());
                }
              });

              $(elementos).detach().appendTo('fieldset[name="'+ nombre_bloque +'"]');
            }
        });
      }, 100);
    }
    else if(($('form').size() > 0) && ($('#areaFormulario'))) {
        $('fieldset').each(function() {
            var nombre = $(this).attr('name');
            if(typeof(nombre) !== 'undefined') {
              var nombre_bloque = nombre;
              nombre = nombre.replace('BLOQUE_', '');
              var elementos = $('*[data-fieldset="'+ nombre +'"]').parent().parent().parent().parent().parent();
              $('*[data-fieldset="'+ nombre +'"]').parent().parent().parent().parent().parent().remove();
              $('fieldset[name="'+ nombre_bloque +'"]').append(elementos);

              if(denegar_remover_campos_bloques) {
                var patron = new RegExp("BLOQUE_");
                if(patron.test(nombre_bloque)) {
                  $('fieldset[name="'+ nombre_bloque +'"]').find('.botones-edit .btn.btn-danger').remove();
                }
              }
            }
        });
    }
});
