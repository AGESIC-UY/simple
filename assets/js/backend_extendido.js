$(document).ready(function () {

    // -- En en caso de que se este mostrando el componente de pago en el editor de formularios...
    if ($('.vista_componente_pago_generico').length && $('.edicionFormulario').length) {
        if (!$('#form_pago_submit_generico').length) {
            $('.vista_componente_pago_generico').removeClass('hidden').show();
        }
    }
    $('#btn_buscar_filtro_auditoria').click(function (e) {
        var url = $('#url_auditoria').val();
        var desde = $('#busqueda_modificacion_desde').val();
        var hasta = $('#busqueda_modificacion_hasta').val();
        var op = $('#tipo_operacion_auditoria').val();
        $.ajax({
            type: 'post',
            dataType: 'html',
            url: url,
            data: {
                'desde': desde,
                'hasta': hasta,
                'operacion': op,
            },
            async: true,
            complete: function (data) {
                //alert(data);
                var tabla = $(data.responseText).find('#mainTable');
                $('#mainTable').html(tabla.html());
                if ($('#mainTable tr').length == 0) {
                    $('#mainTable').html('No hay trámites con los filtros seleccionados.');
                }
            }
        });
    });
    $('#generar_reporte_completo').click(function (e) {
        e.preventDefault();

        /*var grupo = $('#filtro_grupo').val();
         var usuario = $('#filtro_usuario').val();
         var desde = $('#filtro_desde').val();
         var hasta = $('#filtro_hasta').val();
         var reporte_id =  $('#filtro_reporte_id').val();
         window.location.href = site_url + "backend/reportes/ver/" + reporte_id + "?filtro_grupo=" + grupo + "&filtro_usuario=" + usuario + "&filtro_desde=" + desde + "&filtro_hasta=" + hasta;
         */

        var grupo = $('#filtro_grupo').val();
        var usuario = $('#filtro_usuario').val();
        var desde = $('#filtro_desde').val();
        var hasta = $('#filtro_hasta').val();
        var reporte_id = $('#filtro_reporte_id').val();
        var desdeCambio = $('#fechaInicialCompleto').val();
        var hastaCambio = $('#fechaFinalCompleto').val();
        var estado = $('input[name="pendiente_completo"]:checked').val();

        var form = $('.ajaxForm.dynaForm');
        $.ajax({
            type: 'post',
            url: site_url + 'backend/reportes/generar_completo/' + reporte_id
                    + "?filtro_grupo=" + grupo
                    + "&filtro_usuario=" + usuario
                    + "&filtro_desde=" + desde
                    + "&filtro_hasta=" + hasta
                    + "&desdeCambio=" + desdeCambio
                    + "&hastaCambio=" + hastaCambio
                    + "&estado=" + estado,
            success: function (response) {
                resultado = $.parseJSON(response);
                if (resultado.error) {
                    $('#error').remove();
                    $('#mensaje_limite_completo').append("<label id='error'>" + resultado.msg + "</label>");
                    $('#mensaje_limite_completo').removeAttr("style");
                    $('#mensaje_limite_completo').addClass("error");
                    return true;
                } else if (resultado.email) {
                    $('#mensaje_limite_completo').hide();
                    $('#error').remove();
                    $('#modal_email').modal();
                    $('#modal_email').height($('#modal_filtro').height());
                } else {
                    $('#mensaje_limite_completo').hide();
                    $('#error').remove();
                    window.location.href = site_url + "backend/reportes/ver/" + reporte_id
                            + "?filtro_grupo=" + grupo
                            + "&filtro_usuario=" + usuario
                            + "&filtro_desde=" + desde
                            + "&filtro_hasta=" + hasta
                            + "&desdeCambio=" + desdeCambio
                            + "&hastaCambio=" + hastaCambio
                            + "&estado=" + estado;
                }
            }
        });

    });

    $('#filtro_hasta').change(function () {

        var desde = $("#filtro_desde").datepicker('getDate');
        var hasta = $("#filtro_hasta").datepicker('getDate');

        if (desde > hasta) {
            $("#filtro_fechas").addClass("error");
            document.getElementById("generar_reporte_completo").disabled = true;
            var contenedor = document.getElementById("mesage");
            contenedor.style.display = "block";
            return true;
        } else {
            $("#filtro_fechas").removeClass("error");
            document.getElementById("generar_reporte_completo").disabled = false;
            var contenedor = document.getElementById("mesage");
            contenedor.style.display = "none";
            return true;
        }
    });

    $('#generar_reporte_basico').click(function (e) {
        e.preventDefault();

        var desde = $('#filtro_desde_basico').val();
        var hasta = $('#filtro_hasta_basico').val();
        var reporte_id = $('#filtro_reporte_id_basico').val();
        var desdeCambio = $('#fechaInicialBasico').val();
        var hastaCambio = $('#fechaFinalBasico').val();
        var estado = $('input[name="pendiente"]:checked').val();

        if (desde == "" || hasta == "") {
            $("#filtro_fechas_basico").addClass("error");
            var contenedor = document.getElementById("mensaje_fechas_requeridas");
            contenedor.style.display = "block";
        } else {
            $("#filtro_fechas_basico").removeClass("error");
            var contenedor = document.getElementById("mensaje_fechas_requeridas");
            contenedor.style.display = "none";

            var form = $('.ajaxForm.dynaForm');
            $.ajax({
                type: 'post',
                url: site_url + 'backend/reportes/generar_basico/' + reporte_id
                        + "?&filtro_desde=" + desde
                        + "&filtro_hasta=" + hasta
                        + "&desdeCambio=" + desdeCambio
                        + "&hastaCambio=" + hastaCambio
                        + "&estado=" + estado,
                success: function (response) {
                    resultado = $.parseJSON(response);
                    if (resultado.error) {
                        $('#error').remove();
                        $('#mensaje_limite_basico').append("<label id='error'>" + resultado.msg + "</label>");
                        $('#mensaje_limite_basico').removeAttr("style");
                        $('#mensaje_limite_basico').addClass("error");
                        return true;
                    } else
                    if (resultado.email) {
                        $('#mensaje_limite_basico').hide();
                        $('#error').remove();
                        $('#modal_email_basico').modal();
                        $('#modal_email_basico').height($('#modal_filtro_basico').height());
                    } else {
                        $('#mensaje_limite_basico').hide();
                        $('#error').remove();
                        window.location.href = site_url + "backend/reportes/ver/" + reporte_id
                                + "?&filtro_desde=" + desde
                                + "&filtro_hasta=" + hasta
                                + "&desdeCambio=" + desdeCambio
                                + "&hastaCambio=" + hastaCambio
                                + "&estado=" + estado;
                    }
                }
            });
        }

    });

    $("#close_completo").click(function (e) {
        $('#mensaje_limite_completo').hide();
        $('#error').remove();
    });
    $("#close_basico").click(function (e) {
        $('#mensaje_limite_basico').hide();
        $('#error').remove();
    });

    $('#filtro_hasta_basico').change(function () {
        var desde = $("#filtro_desde_basico").datepicker('getDate');
        var hasta = $("#filtro_hasta_basico").datepicker('getDate');

        if (desde > hasta) {
            $("#filtro_fechas_basico").addClass("error");
            document.getElementById("generar_reporte_basico").disabled = true;
            var contenedor = document.getElementById("mensaje_fechas_invalidas");
            contenedor.style.display = "block";
            return true;
        } else {
            $("#filtro_fechas_basico").removeClass("error");
            document.getElementById("generar_reporte_basico").disabled = false;
            var contenedor = document.getElementById("mensaje_fechas_invalidas");
            contenedor.style.display = "none";
            return true;
        }
    });

    $('#generar_reporte_completo_email').click(function (e) {
        e.preventDefault();
        var grupo = $('#filtro_grupo').val();
        var usuario = $('#filtro_usuario').val();
        var desde = $('#filtro_desde').val();
        var hasta = $('#filtro_hasta').val();
        var email = $('#email_text').val();
        var reporte_id = $('#filtro_reporte_id').val();

        $.ajax({
            type: 'post',
            url: site_url + 'backend/reportes/ver_completo_email/' + reporte_id + "?filtro_grupo=" + grupo + "&filtro_usuario=" + usuario + "&filtro_desde=" + desde + "&filtro_hasta=" + hasta + "&email=" + email,
            success: function (response) {
            }
        });
        $('#modal_filtro').modal("hide");
        $('#modal_email').modal("hide");

    });

    $('#generar_reporte_basico_email').click(function (e) {
        e.preventDefault();
        var desde = $('#filtro_desde_basico').val();
        var hasta = $('#filtro_hasta_basico').val();
        var email = $('#email_text_basico').val();
        var reporte_id = $('#filtro_reporte_id_basico').val();

        $.ajax({
            type: 'post',
            url: site_url + 'backend/reportes/ver_basico_email/' + reporte_id + "?filtro_desde=" + desde + "&filtro_hasta=" + hasta + "&email=" + email,
            success: function (response) {
            }
        });
        $('#modal_filtro_basico').modal("hide");
        $('#modal_email_basico').modal("hide");

    });

    $('#generar_reporte_usuario').click(function (e) {
        e.preventDefault();

        var grupo = $('#filtro_grupo').val();
        var usuario = $('#filtro_usuario').val();
        var created_at_desde = $('#desde').val();
        var created_at_hasta = $('#hasta').val();
        var updated_at_desde = $('#fechaInicial').val();
        var updated_at_hasta = $('#fechaFinal').val();
        var estado = $('input[name="pendiente"]:checked').val();

        window.location.href = site_url + "backend/reportes/ver_reporte_usuario?filtro_grupo=" + grupo
                + "&filtro_usuario=" + usuario
                + "&created_at_desde=" + created_at_desde
                + "&created_at_hasta=" + created_at_hasta
                + "&updated_at_desde=" + updated_at_desde
                + "&updated_at_hasta=" + updated_at_hasta
                + "&pendiente=" + estado;
    });

    $('.solicita_filtro').click(function () {
        var reporte_id = $(this).attr('data-reporte');
        $('form #filtro_reporte_id').val(reporte_id);
        $('#modal_filtro').modal();
    });

    $('.solicita_filtro_basico').click(function () {
        var reporte_id = $(this).attr('data-reporte');
        $('form #filtro_reporte_id_basico').val(reporte_id);
        $('#modal_filtro_basico').modal();
    });

    $('#add_parametro_configuracion').click(function () {
        var num = parseInt($('#total_parametros').val()) + 1;
        $('.lista_parametros.controls').append('<span class="campo_parametro"><input class="parametro_id" type="hidden" name="parametro[' + num + '][id]" value="null" /> <input type="text" name="parametro[' + num + '][clave]" class="input-large" placeholder="Clave" /> <input type="text" name="parametro[' + num + '][valor]" class="input-medium" placeholder="Valor" /> <span class="remove_parametro_configuracion icon-minus btn"></span><br /><br /></span>');
        $('#total_parametros').val(num);

        $('.remove_parametro_configuracion').click(function () {
            $(this).parent().remove();
            var count = $('.campo_parametro').size();
            count = (count - 1);
            $('#total_parametros').val(count);

            var param_id = $(this).parent().find('.parametro_id').first().val();
            $.ajax({
                type: 'post',
                url: site_url + 'backend/configuracion/eliminar_parametro',
                data: {parametro_id: param_id},
                success: function (data) {
                }
            });
        });
    });

    $('.remove_parametro_configuracion').click(function () {
        $(this).parent().remove();
        var count = $('.campo_parametro').size();
        count = (count - 1);
        $('#total_parametros').val(count);

        var param_id = $(this).parent().find('.parametro_id').first().val();
        $.ajax({
            type: 'post',
            url: site_url + 'backend/configuracion/eliminar_parametro',
            data: {parametro_id: param_id},
            success: function (data) {
            }
        });
    });

    $('#add_etiqueta_configuracion').click(function () {
        var num = parseInt($('#total_etiquetas').val()) + 1;
        $('.lista_etiquetas.controls').append('<span class="campo_etiqueta"><input class="etiqueta_id" type="hidden" name="etiqueta[' + num + '][id]" value="null" /> <input type="text" name="etiqueta[' + num + '][etiqueta]" class="input-medium" placeholder="Etiqueta" /> <input type="text" name="etiqueta[' + num + '][descripcion]" class="input-large" placeholder="Descripción" /> <span class="remove_etiqueta_configuracion icon-minus btn"></span><br /><br /></span>');
        $('#total_etiquetas').val(num);

        $('.remove_etiqueta_configuracion').click(function () {
            $(this).parent().remove();
            var count = $('.campo_etiqueta').size();
            count = (count - 1);
            $('#total_etiquetas').val(count);

            var etiq_id = $(this).parent().find('.etiqueta_id').first().val();
            $.ajax({
                type: 'post',
                url: site_url + 'backend/configuracion/eliminar_etiquetas',
                data: {etiqueta_id: etiq_id},
                success: function (data) {
                }
            });
        });
    });

    $('.remove_etiqueta_configuracion').click(function () {
        $(this).parent().remove();
        var count = $('.campo_etiqueta').size();
        count = (count - 1);
        $('#total_etiquetas').val(count);

        var etq_id = $(this).parent().find('.etiqueta_id').first().val();
        $.ajax({
            type: 'post',
            url: site_url + 'backend/configuracion/eliminar_etiquetas',
            data: {etiqueta_id: etq_id},
            success: function (data) {
            }
        });
    });

    $('#add_imagen_documento').click(function () {
        var num = parseInt($('#total_imagenes').val()) + 1;
        $('.controls > .lista_imagen').append('<span class="campo_imagen"><input class="campo_imagen input-medium" placeholder="@@variable[contenido]" type="text" name="imagen[' + num + '][variable]" value="" /> <input class="campo_imagen input-small number" placeholder="alto" type="number" min="25" max="500" name="imagen[' + num + '][alto]" value="" /> <input class="campo_imagen input-small number" placeholder="ancho" type="number" min="25" max="500" name="imagen[' + num + '][ancho]" value="" /> <input class="campo_imagen input-large" placeholder="descripción" type="text" name="imagen[' + num + '][descripcion]" value="" /> <span class="remove_imagen_documento icon-minus btn"></span><br /><br /></span>');
        $('#total_imagenes').val(num);

        $('.remove_imagen_documento').click(function () {
            $(this).parent().remove();
            var count = $('.campo_imagen').size();
            count = (count - 1);
            $('#total_imagenes').val(count);
        });
    });

    $('.remove_imagen_documento').click(function () {
        $(this).parent().remove();
        var count = $('.campo_imagen').size();
        count = (count - 1);
        $('#total_imagenes').val(count);
    });

    $('#add_pdf_documento').click(function () {
        console.log('fsfs');
        var num = parseInt($('#total_pdf').val()) + 1;
        $('.controls > .lista_pdf').append('<span class="campo_pdf"><input class="campo_pdf input-medium" placeholder="@@variable" type="text" name="lista_pdf[' + num + '][variable]" value="" /> <span class="remove_pdf_documento icon-minus btn"></span><br /><br /></span>');
        $('#total_pdf').val(num);

        $('.remove_pdf_documento').click(function () {
            $(this).parent().remove();
            var count = $('.campo_pdf').size();
            count = (count - 1);
            $('#total_pdf').val(count);
        });
    });

    $('.remove_pdf_documento').click(function () {
        $(this).parent().remove();
        var count = $('.campo_imagen').size();
        count = (count - 1);
        $('#total_imagenes').val(count);
    });

    $('#add_pasarela_metodo_generico_ticket_variables').click(function () {
        var num = parseInt($('#total_ticket_variables').val()) + 1;
        $('#pasarela_metodo_generico_ticket_variables .controls').append('<input type="text" name="pasarela_metodo_generico_ticket_variables[' + num + '][nombre]" class="input-medium" placeholder="Nombre" /> <input type="text" name="pasarela_metodo_generico_ticket_variables[' + num + '][valor]" class="input-medium" placeholder="Valor" /><br /><br />');
        $('#total_ticket_variables').val(num);
    });

    $('#pasarela_metodo_generico_ticket_metodo').on('change', function () {
        if (this.value == 'post') {
            if (!$.trim($('#pasarela_metodo_generico_ticket_variables .controls').html())) {
                $('#pasarela_metodo_generico_ticket_variables .controls').append('<input type="text" name="pasarela_metodo_generico_ticket_variables[1][nombre]" class="input-medium" placeholder="Nombre" /> <input type="text" name="pasarela_metodo_generico_ticket_variables[1][valor]" class="input-medium" placeholder="Valor" /> <input type="hidden" id="total_ticket_variables" value="1" /> <span id="add_pasarela_metodo_generico_ticket_variables" class="icon-plus btn"></span><br /><br />');
                $('#add_pasarela_metodo_generico_ticket_variables').click(function () {
                    var num = parseInt($('#total_ticket_variables').val()) + 1;
                    $('#pasarela_metodo_generico_ticket_variables .controls').append('<input type="text" name="pasarela_metodo_generico_ticket_variables[' + num + '][nombre]" class="input-medium" placeholder="Nombre" /> <input type="text" name="pasarela_metodo_generico_ticket_variables[' + num + '][valor]" class="input-medium" placeholder="Valor" /><br /><br />');
                    $('#total_ticket_variables').val(num);
                });
            }

            $('#pasarela_metodo_generico_ticket_variables').removeClass('hidden').show();
        } else {
            $('#pasarela_metodo_generico_ticket_variables').hide();
        }
    });

    $('#add_pasarela_metodo_generico_metodo_http_variable').click(function () {
        var num = parseInt($('#total_variables').val()) + 1;
        $('#pasarela_metodo_generico_variables_post .controls').append('<input type="text" name="pasarela_metodo_generico_metodo_http_variable[' + num + '][nombre]" class="input-medium" placeholder="Nombre" /> <input type="text" name="pasarela_metodo_generico_metodo_http_variable[' + num + '][valor]" class="input-medium" placeholder="Valor" /><br /><br />');
        $('#total_variables').val(num);
    });

    $('#add_pasarela_metodo_generico_descripciones_estados_traza').click(function () {
        var num = parseInt($('#total_descripciones_estados_traza').val()) + 1;
        $('#pasarela_metodo_generico_descripciones_estados_traza .controls').append('<input type="text" name="pasarela_metodo_generico_descripciones_estados_traza[' + num + '][codigo]" class="input-medium" placeholder="Código" /> <input type="text" name="pasarela_metodo_generico_descripciones_estados_traza[' + num + '][valor]" class="input-medium" placeholder="Valor" /><br /><br />');
        $('#total_descripciones_estados_traza').val(num);
    });

    $('#pasarela_metodo_generico_metodo_http').on('change', function () {
        if (this.value == 'post') {
            if (!$.trim($('#pasarela_metodo_generico_variables_post .controls').html())) {
                $('#pasarela_metodo_generico_variables_post .controls').append('<input type="text" name="pasarela_metodo_generico_metodo_http_variable[1][nombre]" class="input-medium" placeholder="Nombre" /> <input type="text" name="pasarela_metodo_generico_metodo_http_variable[1][valor]" class="input-medium" placeholder="Valor" /> <input type="hidden" id="total_variables" value="1" /> <span id="add_pasarela_metodo_generico_metodo_http_variable" class="icon-plus btn"></span><br /><br />');
                $('#add_pasarela_metodo_generico_metodo_http_variable').click(function () {
                    var num = parseInt($('#total_variables').val()) + 1;
                    $('#pasarela_metodo_generico_variables_post .controls').append('<input type="text" name="pasarela_metodo_generico_metodo_http_variable[' + num + '][nombre]" class="input-medium" placeholder="Nombre" /> <input type="text" name="pasarela_metodo_generico_metodo_http_variable[' + num + '][valor]" class="input-medium" placeholder="Valor" /><br /><br />');
                    $('#total_variables').val(num);
                });
            }

            $('#pasarela_metodo_generico_variables_post').removeClass('hidden').show();
        } else {
            $('#pasarela_metodo_generico_variables_post').hide();
        }
    });

    if ($('.edicionFormulario').length && $('.mensaje_estado_pago').length) {
        $('.mensaje_estado_pago').parent().html('<div class="well text-left"><i class="icon-tasks" / > Componente de pago</div>');
    }

    $('input[name="requiere_autenticacion_tipo"]').click(function () {
        switch ($(this).val()) {
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

    $('#requiere_autenticacion').click(function () {
        $('input[name="requiere_aut_tipo"]').prop('checked', false);

        if ($(this).prop('checked')) {
            $('.tipo_autenticacion').removeClass('hidden').show();

            $('input[name="requiere_autenticacion_tipo"]').click(function () {
                switch ($(this).val()) {
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
        } else {
            $('.tipo_autenticacion').addClass('hidden').hide();
            $('.autenticacion_soap').addClass('hidden').hide();
        }
    });

    $(".toolbar-formulario .btn, .botones-edit .btn-primary").click(function () {
        setTimeout(function () {
            if ($(".dialogos_titulo").size()) {
                var contenido_default = $('#valor_default_html').html($("#valor_default").val());

                document.content = $('#valor_default_html');

                $("#campo_dialogo_titulo").val($('#valor_default_html').find('.dialogos_titulo').text());
                $("#campo_dialogo_contenido").val($('#valor_default_html').find('.dialogos_contenido').html());
                $("#campo_dialogo_titulo_enlace").val($('#valor_default_html').find('.dialogos_enlace').text());
                $("#campo_dialogo_enlace").val($('#valor_default_html').find('.dialogos_enlace').attr('href'));
            }

            $("#campo_dialogo_titulo, #campo_dialogo_contenido, #campo_dialogo_enlace, #campo_dialogo_titulo_enlace").on("change", function () {
                var titulo = "<h3 class='dialogos_titulo'>" + $("#campo_dialogo_titulo").val() + "</h3>";
                var contenido = "<div class='dialogos_contenido'>" + $("#campo_dialogo_contenido").val() + "</div>";
                var enlace = "<a class='dialogos_enlace' href='" + $("#campo_dialogo_enlace").val() + "' target='_blank'>" + $("#campo_dialogo_titulo_enlace").val() + "</a>";
                $("#valor_default").html(titulo + contenido + enlace);
            });
        }, 400);
    });


    $('#verificar_existe_usuario').click(function () {
        var usuario = $('input[name="usuario"]').val();

        // -- Consulta si existe el usuario
        $.ajax({
            type: 'post',
            url: 'usuario_existe',
            data: {usuario: usuario},
            success: function (data) {
                $('.validacion').html('').hide();

                if (data) {
                    var usuario = JSON.parse(data);

                    if (usuario.error) {
                        $('.validacion').html('<div class="alert alert-error">' + usuario.error + '</div>').show();
                    } else {
                        $('input[name="password"]').parent().parent().remove();
                        $('input[name="password_confirm"]').parent().parent().remove();
                        $('input[name="nombres"]').val(usuario.nombres).attr({'readonly': 'readonly'});
                        $('input[name="apellido_paterno"]').val(usuario.apellido_paterno).attr({'readonly': 'readonly'});
                        $('input[name="apellido_materno"]').val(usuario.apellido_materno).attr({'readonly': 'readonly'});
                        $('input[name="email"]').val(usuario.email);

                        var form_action = $('form').attr('action');
                        $('form').attr({'action': form_action + '/' + usuario.usuario_id});
                    }
                }
            }
        });

        var existe_usuario = true;
        if (existe_usuario) {

        } else {

        }
    });

    if ($('#servicio_tipo_pdi:checked').length) {
        $('#form_soap').hide();
        $('#form_pdi').show();
    } else {
        $('#form_soap').show();
        $('#form_pdi').hide();
    }

    $('#servicio_tipo_pdi').click(function () {
        $('#form_soap').hide();
        $('#form_pdi').show();
    });

    $('#servicio_tipo_soap').click(function () {
        $('#form_pdi').hide();
        $('#form_soap').show().removeClass('hidden');
    });

    setTimeout(function () {
        $(".controls").find(".ht_master.handsontable").not(':first').hide();
    }, 200);

    // -- Muestra campo Error si se esta visualizando desde el modelador de formularios
    $('#areaFormulario .campo_error').show().removeClass('hidden');

    // -- Manejador de checkbox de webservice
    $('#servicio_activo').on('click', function () {
        if ($('#servicio_activo:checked')) {
            $('#servicio_activo:checked').val(1);
        }
    });

    // -- Selección de metodo de pago en pasarela
    $('#pasarela_metodo').on('change', function () {
        $('.pasarela_metodo_form').hide();

        if ($('#pasarela_metodo_' + this.value).length) {
            $('.pasarela_metodo_form').hide();
            $('#pasarela_metodo_' + this.value).removeClass('hidden').show();
        }
    });

    // -- Mostrar si selecciona acción SOAP
    $('#main_action_selector').live('change', function () {
        $('#formAgregarAccion_button_services').hide();
        $('#formAgregarAccion_button_operations').hide();
        $('#formAgregarAccion_button_pasarela_pagos').hide();

        $('#formAgregarAccion_services').hide();
        $('#formAgregarAccion_operations').hide();
        $('#formAgregarAccion_pasarela_pagos').hide();
        $('#servicios_operaciones').addClass('hidden');

        $('#formAgregarAccion_variable_obn').hide();
        $('#formAgregarAccion_button_variable_obn').hide();
        $('#formAgregarAccion_button_variable_obn_tipo').hide();
        $('#formAgregarAccion_variable_obn_tipo').hide();
        $('#formAgregarAccion_variable_obn_tipo_consulta').hide();
        $('#formAgregarAccion_button_variable_obn_tipo_consulta').hide();

        $('#formAgregarAccion_button').show().removeClass('hidden');

        // -- Mostrar pasarelas disponibles en acciones
        if (this.value == 'pasarela_pago') {
            $('#formAgregarAccion_pasarela_pagos').show().removeClass('hidden');

            $('#formAgregarAccion_variable_obn').hide();
            $('#formAgregarAccion_button_variable_obn').hide();
            $('#formAgregarAccion_button_variable_obn_tipo').hide();
            $('#formAgregarAccion_variable_obn_tipo').hide();
            $('#formAgregarAccion_variable_obn_tipo_consulta').hide();
            $('#formAgregarAccion_button_variable_obn_tipo_consulta').hide();

            $('#formAgregarAccion_services').hide();
            $('#formAgregarAccion_button').hide();
            $('#formAgregarAccion_operations').hide();
            $('#formAgregarAccion_button_operations').hide();
            $('#formAgregarAccion_button_services').hide();
            $('#formAgregarAccion_button_pasarela_pagos').show().removeClass('hidden');

            $('#pasarela_pagos_action_selector').live('change', function () {
                $('#formAgregarAccion_button').hide();
                $('#formAgregarAccion_button_operations').hide();
                $('#formAgregarAccion_button_services').hide();
                $('#formAgregarAccion_button_pasarela_pagos').show().removeClass('hidden');
            });
        }

        // -- Servicios disponibles
        if (this.value == 'webservice') {
            $('#formAgregarAccion_services').show().removeClass('hidden');

            $('#formAgregarAccion_button').hide();
            $('#formAgregarAccion_operations').hide();
            $('#formAgregarAccion_pasarela_pagos').hide();
            $('#formAgregarAccion_button_operations').hide();
            $('#formAgregarAccion_button_pasarela_pagos').hide();
            $('#formAgregarAccion_button_services').show().removeClass('hidden');

            $('#formAgregarAccion_variable_obn').hide();
            $('#formAgregarAccion_button_variable_obn').hide();
            $('#formAgregarAccion_button_variable_obn_tipo').hide();
            $('#formAgregarAccion_variable_obn_tipo').hide();
            $('#formAgregarAccion_variable_obn_tipo_consulta').hide();
            $('#formAgregarAccion_button_variable_obn_tipo_consulta').hide();

            $('#services_action_selector').live('change', function () {
                $('#operations_action_selector').val('');

                $('#formAgregarAccion_operations').hide();
                $('#formAgregarAccion_button_services').show().removeClass('hidden');
                $('#formAgregarAccion_button_operations').hide();

                $('.servicios_operaciones').addClass('hidden');
                $('#servicios_operacion_' + this.value).removeClass('hidden');

                if (this.value != 'webservice') {
                    $('#servicios_operacion_' + this.value).removeClass('hidden');

                    $('#formAgregarAccion_operations').show().removeClass('hidden');
                    $('#formAgregarAccion_button_services').hide();
                    $('#formAgregarAccion_button_operations').show().removeClass('hidden');

                    $('#operations_action_selector').live('change', function () {
                        var operacion_id = $('#operations_action_selector option:selected').attr('data-operacion-id');
                        var form_accion = $('#formAgregarAccion_operations').attr('action');
                        $('#formAgregarAccion_operations').attr({'action': form_accion + '/' + operacion_id});
                    });
                }
            });
        }

        if (this.value == 'variable_obn') {
            $('#formAgregarAccion_variable_obn').show().removeClass('hidden');
            $('#formAgregarAccion_button_variable_obn').show().removeClass('hidden');
            $('#variable_obn_tipo_action_selector').val("");
            $('#variable_obn_action_selector').val("");
            $('#formAgregarAccion_services').hide();

            $('#formAgregarAccion_button').hide();
            $('#formAgregarAccion_operations').hide();
            $('#formAgregarAccion_pasarela_pagos').hide();
            $('#formAgregarAccion_button_operations').hide();
            $('#formAgregarAccion_button_pasarela_pagos').hide();
            $('#formAgregarAccion_button_services').hide();
            $('#formAgregarAccion_variable_obn_tipo').hide();
            $('#formAgregarAccion_variable_obn_tipo_consulta').hide();
            $('#formAgregarAccion_button_variable_obn_tipo_consulta').hide();

            $('#variable_obn_action_selector').live('change', function () {

                $('#formAgregarAccion_variable_obn_tipo').show().removeClass('hidden');
                $('#formAgregarAccion_button_variable_obn_tipo').show().removeClass('hidden');
                $('#formAgregarAccion_button_variable_obn').hide();
                $('#formAgregarAccion_button_variable_obn_tipo_consulta').hide();
                $('#formAgregarAccion_variable_obn_tipo_consulta').hide();
                $('#variable_obn_tipo_action_selector').val("");
                $('#variable_obn_query_action_selector').val("");
                $('#variable_obn_tipo_consulta').removeAttr('checked');
                var obn_id = $('#variable_obn_action_selector option:selected').attr('data-obn-id');
                var form_accion = $('#formAgregarAccion').attr('action');
                $('#formAgregarAccion_variable_obn_tipo').attr({'action': form_accion + '/' + obn_id});
                $('#variable_obn_tipo_action_selector').live('change', function () {
                    $('#formAgregarAccion_button_variable_obn').hide();
                    $('#formAgregarAccion_button_variable_obn_tipo').show().removeClass('hidden');
                    $('#formAgregarAccion_button_variable_obn_tipo_consulta').hide();
                    $('#formAgregarAccion_variable_obn_tipo_consulta').hide();
                    var tipo = $('#main_action_selector option:selected').val();
                    $('#var_obn_tipo_action_selector').val(tipo);
                    var obn_id = $('#variable_obn_action_selector option:selected').attr('data-obn-id');
                    var form_accion = $('#formAgregarAccion').attr('action');
                    $('#formAgregarAccion_variable_obn_tipo').attr({'action': form_accion + '/' + obn_id});
                });
            });
        }
    });

    // -- Manejador de fieldsets para editor de formulario
    $('#formEditarFormulario .btn').on('click', function () {
        setTimeout(function () {
            try {
                document.lista_de_fieldsets = $('.custom-fieldset');

                var lista_de_fieldsets = '<select id="lista_de_fieldsets" name="fieldset">';
                lista_de_fieldsets += '<option value="">-- Seleccionar fieldset --</option>';
                $(document.lista_de_fieldsets).each(function () {
                    if ($(this).attr('name')) {
                        var fieldset = $(this).attr('name').replace('BLOQUE_', '');
                        var selected = '';

                        if ($('#formEditarCampo input[name="fieldset"]').length) {
                            if (($('#formEditarCampo input[name="fieldset"]').val().length >= 1) && ($('#formEditarCampo input[name="fieldset"]').val() == fieldset)) {
                                selected = 'selected';
                            }
                        }

                        lista_de_fieldsets += '<option value="' + fieldset + '" ' + selected + '>' + fieldset + '</option>';
                    }
                });
                lista_de_fieldsets += '</select>';

                $('#formEditarCampo input[name="fieldset"]').replaceWith(lista_de_fieldsets);
            } catch (error) {
                console.log(error);
            }
        }, 500);
    });

    $('.btn-group .btn').on('click', function () {
        setTimeout(function () {
            try {
                document.lista_de_fieldsets = $('.custom-fieldset');

                var lista_de_fieldsets = '<select id="lista_de_fieldsets" name="fieldset">';
                lista_de_fieldsets += '<option value="">-- Seleccionar fieldset --</option>';
                $(document.lista_de_fieldsets).each(function () {
                    var fieldset = $(this).attr('name').replace('BLOQUE_', '');
                    var selected = '';

                    if (($('#formEditarCampo input[name="fieldset"]').val().length >= 1) && ($('#formEditarCampo input[name="fieldset"]').val() == fieldset)) {
                        selected = 'selected';
                    }

                    lista_de_fieldsets += '<option value="' + fieldset + '" ' + selected + '>' + fieldset + '</option>';
                });
                lista_de_fieldsets += '</select>';

                $('#formEditarCampo input[name="fieldset"]').replaceWith(lista_de_fieldsets);
            } catch (error) {
                console.log(error);
            }
        }, 500);
    });

    // -- Setea campos como "No requeridos"
    $('.campo_no_requerido').on('click', function () {
        setTimeout(function () {
            $('.modal input[name="validacion"]').attr({'value': ''}).hide();
            $('.modal input[name="readonly"]').attr({'value': '1'}).parent().hide();
            $('.modal input[name="validacion"]').prev().hide();
        }, 200);
    });

    // -- Maneja respuestas de operacion Catalogo de Servicios
    $('#agregar_respuestas').on('click', function () {
        // -- Genera un nuevo ID de respuesta
        var id = $('#operacion_id').val() + '-';
        var posibles = 'abcdefghijklmnopqrstuvwxyz0123456789';
        for (var i = 0; i < 6; i++) {
            id += posibles.charAt(Math.floor(Math.random() * posibles.length));
        }

        $('#respuestas_visual').append('<div class="well respuestas_visual_hijos"><input type="hidden" class="respuesta_id" value="' + id + '" /><div class="row-fluid"><div class="span3"><label for="nombreResp' + id + '">Nombre</label><input id="nombreResp' + id + '" type="text" class="respuestas_campos_key" /></div><div class="span5"><label for="XPath' + id + '">XPath</label><input id="XPath' + id + '" type="text" class="input-xxlarge respuestas_campos_xpath" /></div><div class="span3"><label for="tipo' + id + '">Tipo</label><select id="tipo' + id + '" class="respuestas_campos_tipo"><option value="texto">Texto</option><option value="lista">Lista</option><option value="xslt">XSLT</option></select></div><div class="span1"><div class="btn btn-danger respuestas_campos_eliminar"><span class="icon-trash icon-white" /></div></div></div></div>');

        $('.respuesta_id[value="' + id + '"]').parent().find('.respuestas_campos_key').focus();

        document.operacion_id = $('#operacion_id').val();

        $('.respuestas_campos_tipo').on('change', function () {
            if (this.value == 'lista') {
                if (!$(this).parent().parent().parent().find('.respuestas_campos_xslt').length) {
                    $(this).parent().parent().parent().append('<div class="margen"><label for="xsl' + document.operacion_id + '">XSL</label><textarea id="xsl' + document.operacion_id + '" name="xslt[' + document.operacion_id + '][' + $(this).parent().parent().parent().find('.respuesta_id').val() + ']" spellcheck="false" class="large-textarea respuestas_campos_xslt" placeholder="Ingrese el XSLT...">' + $('#xsl_example').val() + '</textarea></div>');
                } else {
                    $(this).parent().parent().parent().find('.margen').show();
                    $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr({'name': $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr('name').replace('_xslt', 'xslt')});
                }
            } else if (this.value == 'xslt') {
                if (!$(this).parent().parent().parent().find('.respuestas_campos_xslt').length) {
                    $(this).parent().parent().parent().append('<div class="margen"><label for="xsl' + document.operacion_id + '">XSLT</label><textarea id="xsl' + document.operacion_id + '" name="xslt[' + document.operacion_id + '][' + $(this).parent().parent().parent().find('.respuesta_id').val() + ']" spellcheck="false" class="large-textarea respuestas_campos_xslt" placeholder="Ingrese el XSLT..."></textarea></div>');
                } else {
                    $(this).parent().parent().parent().find('.margen').show();
                    $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr({'name': $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr('name').replace('_xslt', 'xslt')});
                }
            } else {
                if ($(this).parent().parent().parent().find('.respuestas_campos_xslt').length) {
                    $(this).parent().parent().parent().find('.margen').hide();
                    $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr({'name': $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr('name').replace('xslt', '_xslt')});
                }
            }
        });

        $('.respuestas_campos_eliminar').on('click', function () {
            $(this).parent().parent().parent().remove();
        });
    });

    if ($('#respuestas').length) {
        document.operacion_id = $('#operacion_id').val();

        if ($('#respuestas').val().length > 0) {
            var respuestas = $('#respuestas').val();
            respuestas = $.parseJSON(respuestas);

            $(respuestas.respuestas).each(function (i) {
                var options = '';

                if ($(respuestas.respuestas)[i].tipo == 'texto') {
                    options = '<option value="texto" selected>Texto</option><option value="lista">Lista</option><option value="xslt">XSLT</option>';
                } else if ($(respuestas.respuestas)[i].tipo == 'lista') {
                    options = '<option value="texto">Texto</option><option value="lista" selected>Lista</option><option value="xslt">XSLT</option>';
                } else if ($(respuestas.respuestas)[i].tipo == 'xslt') {
                    options = '<option value="texto">Texto</option><option value="lista">Lista</option><option value="xslt" selected>XSLT</option>';
                } else {
                    options = '<option value="texto" selected>Texto</option><option value="lista">Lista</option><option value="xslt">XSLT</option>';
                }

                $('#respuestas_visual').append('<div class="well respuestas_visual_hijos"><input type="hidden" class="respuesta_id" value="' + $(respuestas.respuestas)[i].id + '" /><div class="row-fluid"><div class="span3"><label for="nombreResp' + $(respuestas.respuestas)[i].id + '">Nombre</label><input id="nombreResp' + $(respuestas.respuestas)[i].id + '" type="text" class="respuestas_campos_key" value="' + $(respuestas.respuestas)[i].key + '" /></div><div class="span5"><label for="XPath' + $(respuestas.respuestas)[i].id + '">XPath</label><input id="XPath' + $(respuestas.respuestas)[i].id + '" type="text" class="input-xxlarge respuestas_campos_xpath" value="' + $(respuestas.respuestas)[i].xpath + '" /></div><div class="span3"><label for="tipo' + $(respuestas.respuestas)[i].id + '">Tipo</label><select id="tipo' + $(respuestas.respuestas)[i].id + '" class="respuestas_campos_tipo">' + options + '</select></div><div class="span1"><div class="btn btn-danger respuestas_campos_eliminar"><span class="icon-trash icon-white" /></div></div></div></div>');

                if ($(respuestas.respuestas)[i].tipo == 'lista') {
                    $('#respuestas_creadas .respuestas_campos_xslt').each(function () {
                        document.campo_xslt = $(this);
                        $('.respuesta_id').filter(function () {
                            return this.value == $(document.campo_xslt).attr('data-respuesta-id')
                        }).parent().append($(document.campo_xslt).parent());
                    });
                }

                if ($(respuestas.respuestas)[i].tipo == 'xslt') {
                    $('#respuestas_creadas .respuestas_campos_xslt').each(function () {
                        document.campo_xslt = $(this);
                        $('.respuesta_id').filter(function () {
                            return this.value == $(document.campo_xslt).attr('data-respuesta-id')
                        }).parent().append($(document.campo_xslt).parent());
                    });
                }

                $('.respuestas_campos_tipo').on('change', function () {
                    if (this.value == 'lista') {
                        if (!$(this).parent().parent().parent().find('.respuestas_campos_xslt').length) {
                            $(this).parent().parent().parent().append('<div class="margen"><label for="xsl' + document.operacion_id + '">XSL</label><textarea id="xsl' + document.operacion_id + '" name="xslt[' + document.operacion_id + '][' + $(this).parent().parent().parent().find('.respuesta_id').val() + ']" spellcheck="false" class="large-textarea respuestas_campos_xslt" placeholder="Ingrese el XSLT...">' + $('#xsl_example').val() + '</textarea></div>');
                        } else {
                            $(this).parent().parent().parent().find('.margen').show();
                            $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr({'name': $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr('name').replace('_xslt', 'xslt')});
                        }
                    } else if (this.value == 'xslt') {
                        if (!$(this).parent().parent().parent().find('.respuestas_campos_xslt').length) {
                            $(this).parent().parent().parent().append('<div class="margen"><label for="xsl' + document.operacion_id + '">XSLT</label><textarea id="xsl' + document.operacion_id + '" name="xslt[' + document.operacion_id + '][' + $(this).parent().parent().parent().find('.respuesta_id').val() + ']" spellcheck="false" class="large-textarea respuestas_campos_xslt" placeholder="Ingrese el XSLT..."></textarea></div>');
                        } else {
                            $(this).parent().parent().parent().find('.margen').show();
                            $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr({'name': $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr('name').replace('_xslt', 'xslt')});
                        }
                    } else {
                        if ($(this).parent().parent().parent().find('.respuestas_campos_xslt').length) {
                            $(this).parent().parent().parent().find('.margen').hide();
                            $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr({'name': $(this).parent().parent().parent().find('.respuestas_campos_xslt').attr('name').replace('xslt', '_xslt')});
                        }
                    }
                });
            });

            $('.respuestas_campos_eliminar').on('click', function () {
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

    $('#guardar_operacion').click(function () {
        var respuestas = Array();
        $('#respuestas_visual .respuestas_visual_hijos').each(function () {
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

        $('.respuestas_campos_xslt').each(function () {
            var xslt = $(this).val();
            var new_xslt = xslt.replace('version=', '%version%=');
            $(this).val(new_xslt);
        });

        $('form').submit();
    });

    // -- Datepicker para vencimiento de método de pago de pasarela
    if ($('#pasarela_pago_vencimiento_muestra').length) {
        $('#pasarela_pago_vencimiento_button').click(function () {
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

    $('#areaFormulario a').click(function () {
        setTimeout(function () {
            $('#contenedor').click(function () {
                if ($(this).is(':checked')) {
                    $(this).val(1);
                    $('#leyenda_contenedor').removeClass('hidden').show();
                } else {
                    $('#leyenda_contenedor').addClass('hidden').hide();
                }
            });
        }, 400);
    });

    //pasareka de pago ITC para que se visualice el estado en seguimiento

    if ($("input[name='IdSol']").length > 0) {

        $.ajax({
            type: 'post',
            url: document.Constants.host + '/pagos/consulta_estado_directo',
            data: {'IdSol': $('input[name="IdSol"]').val(), 'IdEtapa': $('input[name="IdEtapa"]').val(), 'IdPasarela': $('#pasarela_generica_id_antel').val()},
            complete: function (resultado) {
                resultado = $.parseJSON(resultado.responseText);
                var mensaje = '';

                switch (resultado.estado) {
                    case 'timeout':
                        mensaje += '<div class="dialogo validacion-error"><h3 class="dialogos_titulo">' + resultado.titulo + '</h3><div>' + resultado.mensaje + '<br /><br /><a class="btn-link" href="' + window.location.href + '">Consultar</a></div></div>';
                        $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');
                        break;
                    case 'error':

                        mensaje += '<div class="text-center"><p>' + $('input[name="MsgPago"]').val() + '</p><input type="button" value="Realizar pago" class="btn btn-primary" id="boton_reload_token" /></div>';
                        $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');


                        break;
                    case 'ok':
                        mensaje += '<div class="dialogo validacion-success"><h3 class="dialogos_titulo">' + resultado.titulo + '</h3><div>' + resultado.mensaje + '</div></div>';
                        $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');

                        break;
                    case 'rc':
                        mensaje += '<div class="dialogo validacion-warning"><h3 class="dialogos_titulo">' + resultado.titulo + '</h3><div>' + resultado.mensaje + '<br><br><a target="_blank" href="' + document.Constants.host + '/pagos/generar_ticket?t=' + $('input[name="IdSol"]').val() + '&e=' + $('input[name="IdEtapa"]').val() + '" class="btn-link">Imprimir ticket</a></div></div>';
                        $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');
                        break;
                    case 'pendiente':
                        if (resultado.forma_pago == 'online') {
                            mensaje += '<div class="dialogo validacion-warning"><h3 class="dialogos_titulo">' + resultado.titulo + '</h3><div>' + resultado.mensaje_1 + '<br /><br /><a class="btn-link" href="' + window.location.href + '">Consultar</a><br /><br />' + resultado.mensaje_2 + '<br><br><input type="button" value="Realizar pago" class="btn-link" id="boton_reload_token" /></div></div>';
                            $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');
                        } else {
                            mensaje += '<div class="text-center"><p>' + $('input[name="MsgPago"]').val() + '</p><input type="button" value="Realizar pago" class="btn btn-primary" id="boton_reload_token" /></div>';
                            $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');
                        }
                        break;
                    case 'reversado':
                        if (resultado.forma_pago == 'online') {
                            mensaje += '<p>' + resultado.mensaje + '<br><br><input type="button" value="Realizar pago" class="btn btn-primary" id="boton_nuevo_pago" /></p>';
                            $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');
                        } else {
                            mensaje += '<div class="dialogo validacion-warning"><h3 class="dialogos_titulo">' + resultado.titulo + '</h3><div>' + resultado.mensaje_1 + '<br>' + resultado.mensaje_2 + '<br><br><a href="#" class="btn-link" id="boton_reload_token">Realizar pago</a></div></div>';
                            $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');
                        }
                        break;
                    default:
                        if (resultado.forma_pago == 'online') {
                            mensaje += '<div class="dialogo validacion-warning"><h3 class="dialogos_titulo">' + resultado.titulo + '</h3><div>En caso de ya haber efectuado el pago, el mismo no está confirmado, por favor vuelva a consultar en unos minutos.<br>En caso de no haber efectuado el pago, puede realizarlo haciendo clic en el siguiente botón.<br><br><input type="button" value="Realizar pago" class="btn-link" id="boton_reload_token" /><br><br><br>Si desea volver a consultar el estado de su pago, puede hacerlo accediendo a la siguiente URL:<br> <a class="btn-link" href="' + window.location.href + '" target="_blank">' + window.location.href + '</a></div></div>';
                            $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');


                        } else {
                            mensaje += '<p>' + resultado.mensaje + '<br><br><input type="button" value="Realizar pago" class="btn btn-primary" id="boton_nuevo_pago" /></p>';
                            $('.mensaje_estado_pago').html(mensaje).removeClass('hidden');
                        }
                }
            }
        });
    }

    // verifica pago generico par que se visualice el estado en seguimiento
    if ($('#id_solicitud_pago_generico').length) {

        $.ajax({
            type: 'post',
            url: document.Constants.host + '/pagos/consulta_estado_directo_generico',
            data: {'IdSol': $('#id_solicitud_pago_generico').val(), 'IdEtapa': $('#etapa_id').val(), 'IdPasarela': $('#pasarela_generica_id').val()},
            complete: function (resultado) {
                var data = $.parseJSON(resultado.responseText);

                if (!data) {
                    mensaje = '<div class="dialogo validacion-error"><h3 class="dialogos_titulo">Mensaje</h3><div class="alert alert-error">No se ha podido obtener el estado de pago.</div></div>';
                    $('.mensaje_estado_pago_generico').html(mensaje).removeClass('hidden').show();
                    return;
                }

                if (data['estado'] == 'timeout') {
                    mensaje = '<div class="dialogo validacion-error"><h3 class="dialogos_titulo">Mensaje</h3><div class="alert alert-error">No se ha podido obtener el estado de pago.</div></div>';
                    $('.mensaje_estado_pago_generico').html(mensaje).removeClass('hidden').show();
                    return;
                } else if (data['estado'] == 'ok') {
                    resultado = data['data'];
                    $('.imprimir_ticket_generico').removeClass('hidden').show();
                    $('.cuerpo_componente_pago_generico').removeClass('hidden').show();

                    var mensaje = '';

                    var estado_mensaje = resultado[2][0];
                    estado_mensaje = estado_mensaje.split('=');
                    if (estado_mensaje[0] == 'OK') {
                        mensaje += '<div class="dialogo validacion-success"><h3 class="dialogos_titulo">Mensaje</h3><div class="alert alert-success">' + estado_mensaje[1] + '</div></div>';
                    } else if (estado_mensaje[0] == 'ALERTA') {
                        mensaje += '<div class="dialogo validacion-warning"><h3 class="dialogos_titulo">Mensaje</h3><div class="alert alert-warning">' + estado_mensaje[1] + '</div></div>';
                    } else if (estado_mensaje[0] == 'ERROR') {
                        mensaje += '<div class="dialogo validacion-error"><h3 class="dialogos_titulo">Mensaje</h3><div class="alert alert-error">' + estado_mensaje[1] + '</div></div>';
                    }

                    $('#form_pago_submit_generico').hide();
                    $('#form_pago_etiqueta').hide();
                    $('.mensaje_estado_pago_generico').html(mensaje).removeClass('hidden').show();

                    $('.imprimir_ticket_generico_post_button').click(function () {
                        $('#form_pasarela_pago_generica').replaceWith(function () {
                            return $('<div/>', {
                                html: this.innerHTML
                            });
                        });

                        $('form.ajaxForm.dynaForm.form-horizontal').replaceWith(function () {
                            return $('<div/>', {
                                html: this.innerHTML
                            });
                        });

                        $('.imprimir_ticket_generico_post').wrap("<form id='imprimir_ticket_generico_post_form' method='post' action='" + $('.imprimir_ticket_generico_post').attr('data-action') + "'></form>");
                        $('#imprimir_ticket_generico_post_form input').each(function () {
                            var name = $(this).attr('name');
                            name = name.replace('__ticket', '');
                            $(this).attr({'name': name});
                        });

                        $('#imprimir_ticket_generico_post_form').submit();
                    });
                }
            }
        });
    }

    if (window.location.href.split('?')[0] === document.Constants.host + '/backend/configuracion/monitoreo_pasarelas_ajax') {
        $.ajax({
            dataType: 'html',
            async: true,
            url: document.Constants.host + '/backend/configuracion/monitoreo_pasarelas',
            complete: function (data) {
                var contenido_monitoreo_pasarela = $(data.responseText).find('#contenido_monitoreo_pasarela');
                $('#contenido_monitoreo_pasarela').css("display", "none");
                $('#contenido_monitoreo_pasarela').html(contenido_monitoreo_pasarela.html()).fadeIn("slow");
                ;
            }
        });
    }

    if (window.location.href.split('?')[0] === document.Constants.host + '/backend/configuracion/monitoreo_trazabilidad_ajax') {
        $.ajax({
            dataType: 'html',
            async: true,
            url: document.Constants.host + '/backend/configuracion/monitoreo_trazabilidad',
            complete: function (data) {
                var contenido_monitoreo_trazabilidad = $(data.responseText).find('#contenido_monitoreo_trazabilidad');
                $('#contenido_monitoreo_trazabilidad').css("display", "none");
                $('#contenido_monitoreo_trazabilidad').html(contenido_monitoreo_trazabilidad.html()).fadeIn("slow");
            }
        });
    }

    $('#busqueda_filtro_monitoreo_toggle').click(function () {
        $("#busqueda_monitoreo_filtro").slideToggle();
    });

    $('#btn_buscar_monitoreo_filtro').click(function (e) {
        e.preventDefault();

        $('#error_filtro').html('');
        $('#filtros_aplicados').html('');

        if ($('#tabla_monitoreo tr').length == 0) {
            $('#tabla_monitoreo').html('');
        }

        var busqueda_tipo_servicio = $('#busqueda_tipo_servicio').val();
        var busqueda_nombre_servicio = $('#busqueda_nombre_servicio').val();
        var busqueda_fecha_desde = $('#busqueda_fecha_desde').val();
        var busqueda_fecha_hasta = $('#busqueda_fecha_hasta').val();
        var busqueda_id_tramite = $('#busqueda_id_tramite').val();
        var busqueda_id_etapa = $('#busqueda_id_etapa').val();

        if (busqueda_tipo_servicio || busqueda_nombre_servicio || busqueda_fecha_desde || busqueda_fecha_hasta || busqueda_id_tramite || busqueda_id_etapa) {

            $.blockUI({
                message: '<img src="' + document.Constants.host + '/assets/img/ajax-loader.gif"></img>',
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

            $.ajax({
                type: 'GET',
                dataType: 'html',
                url: site_url + 'backend/configuracion/monitoreo_estado_soap_pdi',
                data: {
                    'busqueda_tipo_servicio': busqueda_tipo_servicio,
                    'busqueda_nombre_servicio': busqueda_nombre_servicio,
                    'busqueda_fecha_desde': busqueda_fecha_desde,
                    'busqueda_fecha_hasta': busqueda_fecha_hasta,
                    'busqueda_id_tramite': busqueda_id_tramite,
                    'busqueda_id_etapa': busqueda_id_etapa
                },
                async: true,
                complete: function (data) {
                    $.unblockUI();
                    var contenido = $(data.responseText).find('#contenido');
                    $('#contenido').html(contenido.html());
                    $('#paginado_div').html('');
                    $('#paginado_div').html($(data.responseText).find('#paginado_div').html());

                    if ($('#tabla_monitoreo tr').length == 1) {
                        var html_tabla = '<tbody><tr><td>';
                        var html_tabla = html_tabla + '<p><strong>No hay datos para los fitros de búsqueda avanzada seleccionados:</strong></p>';

                        if (busqueda_tipo_servicio) {
                            var html_tabla = html_tabla + '<p>* Tipo de servicio: ' + busqueda_tipo_servicio.toUpperCase() + '</p>';
                        }
                        if (busqueda_nombre_servicio) {
                            var html_tabla = html_tabla + '<p>* Nombre del servicio: ' + busqueda_nombre_servicio + '</p>';
                        }
                        if (busqueda_fecha_desde) {
                            var html_tabla = html_tabla + '<p>* Fecha de ejecución a partir de: ' + busqueda_fecha_desde + '</p>';
                        }
                        if (busqueda_fecha_hasta) {
                            var html_tabla = html_tabla + '<p>* Fecha de ejecución a hasta: ' + busqueda_fecha_hasta + '</p>';
                        }
                        if (busqueda_id_tramite) {
                            var html_tabla = html_tabla + '<p>* ID trámite: ' + busqueda_id_tramite + '</p>';
                        }
                        if (busqueda_id_etapa) {
                            var html_tabla = html_tabla + '<p>* ID etapa: ' + busqueda_id_etapa + '</p>';
                        }

                        var html_tabla = html_tabla + '</td></tr></tbody>';
                        $('#tabla_monitoreo').html(html_tabla);

                        $('#filtros_aplicados').html('');
                        $('#paginado_div').html('');
                    } else {
                        $('#filtros_aplicados').html('<p>* Filtros búsqueda avanzada aplicados</p>');
                    }
                }
            });
        } else {
            $('#error_filtro').html('<p style="color:red">Seleccione por lo menos 1 filtro de búsqueda</p>').fadeIn();
        }
    });

    $("#busqueda_fecha_desde").datepicker($.extend({
        onSelect: function () {
            var minDate = $(this).datepicker('getDate');
            minDate.setDate(minDate.getDate());
            $("#busqueda_fecha_hasta").datepicker("option", "minDate", minDate);
        }
    }));

    $("#busqueda_fecha_hasta").datepicker($.extend({
        onSelect: function () {
            var maxDate = $(this).datepicker('getDate');
            maxDate.setDate(maxDate.getDate());
            $("#busqueda_fecha_desde").datepicker("option", "maxDate", maxDate);
        }
    }));

    $("#busqueda_fecha_hasta").keyup(function () {
        $("#busqueda_fecha_desde").datepicker('destroy');
        $("#busqueda_fecha_desde").datepicker($.extend({
            onSelect: function () {
                var minDate = $(this).datepicker('getDate');
                minDate.setDate(minDate.getDate());
                $("#busqueda_fecha_hasta").datepicker("option", "minDate", minDate);
            }
        }));

        $("#busqueda_fecha_hasta").datepicker('destroy');
        $("#busqueda_fecha_hasta").datepicker($.extend({
            onSelect: function () {
                var maxDate = $(this).datepicker('getDate');
                maxDate.setDate(maxDate.getDate());
                $("#busqueda_fecha_desde").datepicker("option", "maxDate", maxDate);
            }
        }));
    });

    $("#proc_arch_id").change(function () {
        var proceso_id = $("#proc_arch_id").val();
        console.log("Id proceso seleccionado: " + proceso_id);
        $("#procArchivadoForm").attr('action', site_url + 'backend/procesos/editar/' + proceso_id);
        console.log("Submit action: " + $("#procArchivadoForm").attr('action'));
        $('#procArchivadoForm').submit();
        //javascript:$('#procArchivadoForm').submit();
    });
    $("#proc_arch_id_seg").change(function () {
        var proceso_id = $("#proc_arch_id_seg").val();
        console.log("Id proceso seleccionado: " + proceso_id);
        $("#procArchivadoForm").attr('action', proceso_id);
        console.log("Submit action: " + $("#procArchivadoForm").attr('action'));
        $('#procArchivadoForm').submit();
        //javascript:$('#procArchivadoForm').submit();
    });
});

// -- Revuelve un color aleatorio en hexadecimal
function getRandomColor() {
    return randomColor({hue: 'blue'});
}

function editarBloque(bloqueId) {
    $("#modal").load(site_url + "backend/bloques/ajax_editar/" + bloqueId);
    $("#modal").modal({backdrop: 'static', keyboard: false});
    return false;
}

function jsonFormat(json_1) {
    var json_code = $('#' + json_1).val();
    if (json_code && json_code != 'null') {
        var json = JSON.parse(json_code);
        $('#modal_peticion').modal('show').scrollTop(0);
        $('#json_code_html').html(JSON.stringify(json, null, 2));
    } else {
        $('#modal_peticion').modal('show').scrollTop(0);
        $('#json_code_html').html('No hay datos que mostrar');
    }
}