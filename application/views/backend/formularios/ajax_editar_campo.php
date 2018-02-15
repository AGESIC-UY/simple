<script type="text/javascript">
    function updateDependientes() {
      var campos = [];
      var relaciones = [];
      var valores = [];
      var tipos = [];

      // -- Campo 1
      var campo = $('select[name="dependiente_campo_1"]').val();
      var relacion = $('input[name="dependiente_relacion_1"]').val();
      var valor = $('input[name="dependiente_valor_1"]').val();
      var tipo = $('input[name="dependiente_tipo_1"]').val();
      campos.push(campo);
      relaciones.push(relacion);
      valores.push(valor);
      tipos.push(tipo);

      // -- Campo clones agregados
      $('.campoDependientesClones .campoDependientes').each(function() {
        var campo = $(this).find('.dependiente_campo_modelo').val();
        var relacion = $(this).find('.dependiente_relacion_modelo').val();
        var valor = $(this).find('.dependiente_valor_modelo').val();
        var tipo = $(this).find('.dependiente_tipo_modelo').val();

        campos.push(campo);
        relaciones.push(relacion);
        valores.push(valor);
        tipos.push(tipo);
      });

      $('#campoDependientesData input[name="dependiente_campo"]').val(campos.toString());
      $('#campoDependientesData input[name="dependiente_relacion"]').val(relaciones.toString());
      $('#campoDependientesData input[name="dependiente_valor"]').val(valores.toString());
      $('#campoDependientesData input[name="dependiente_tipo"]').val(tipos.toString());
    }

    function updateSelects() {
      // Llenamos el select box de dependientes
      var selected=$("#formEditarCampo select[name=dependiente_campo]").val();
      var html='<option value="">Seleccionar</option>';
      var names=new Array();
      $("#formEditarFormulario :input[name]").each(function(i,el){
          var name=$(el).attr("name");
          if($.inArray(name, names)==-1){
              names.push(name);
              html+='<option value="'+name+'">'+name+'</option>';
          }
      });
      $("#formEditarCampo select[name=dependiente_campo]").html(html);
      $("#formEditarCampo select[name=dependiente_campo]").val(selected);

      // Llenamos el select box de dependientes modelo
      var selected=$("#formEditarCampo .dependiente_campo_modelo").val();
      var html='<option value="">Seleccionar</option>';
      var names=new Array();
      $("#formEditarFormulario :input[name]").each(function(i,el){
          var name=$(el).attr("name");
          if($.inArray(name, names)==-1){
              names.push(name);
              html+='<option value="'+name+'">'+name+'</option>';
          }
      });
      $("#formEditarCampo .dependiente_campo_modelo").html(html);
      $("#formEditarCampo .dependiente_campo_modelo").val(selected);

      // Llenamos el select box de dependientes 1
      var selected=$("#formEditarCampo select[name=dependiente_campo_1]").val();
      var html='<option value="">Seleccionar</option>';
      var names=new Array();
      $("#formEditarFormulario :input[name]").each(function(i,el){
          var name=$(el).attr("name");
          if($.inArray(name, names)==-1){
              names.push(name);
              html+='<option value="'+name+'">'+name+'</option>';
          }
      });
      $("#formEditarCampo select[name=dependiente_campo_1]").html(html);
      $("#formEditarCampo select[name=dependiente_campo_1]").val(selected);

      // Funcionalidad en campo dependientes para seleccionar entre tipo regex y string
      $buttonRegex=$("#formEditarCampo .campoDependientes .buttonRegex");
      $buttonString=$("#formEditarCampo .campoDependientes .buttonString");
      $inputDependienteTipo=$("#formEditarCampo input[name=dependiente_tipo]");
      $inputDependienteTipo1=$("#formEditarCampo input[name=dependiente_tipo_1]");
      $inputDependienteTipoModelo=$(".dependiente_tipo_modelo");
      $buttonString.attr("disabled",$inputDependienteTipo.val()=="string");
      $buttonRegex.attr("disabled",$inputDependienteTipo.val()=="regex");
      $buttonRegex.click(function() {
        $(this).parent().parent().find('.buttonString').prop("disabled", false);
        // $buttonString.prop("disabled",false);
        $(this).parent().parent().find('.buttonRegex').prop("disabled", true);
        // $buttonRegex.prop("disabled",true);
        // $inputDependienteTipo.val("regex");
        if  ($(this).parent().parent().attr('id') == 'campo_dependiente_1'){
          $inputDependienteTipo1.val("regex");
        }else{
          $(this).parent().parent().find('.dependiente_tipo_modelo').val("regex");
        }


        //$inputDependienteTipoModelo.val("regex");
      });
      $buttonString.click(function() {
        $(this).parent().parent().find('.buttonString').prop("disabled", true);
        // $buttonString.prop("disabled",true);
        $(this).parent().parent().find('.buttonRegex').prop("disabled", false);
        // $buttonRegex.prop("disabled",false);
        // $inputDependienteTipo.val("string");
        if  ($(this).parent().parent().attr('id') == 'campo_dependiente_1'){
          $inputDependienteTipo1.val("string");
        }else{
          $(this).parent().parent().find('.dependiente_tipo_modelo').val("string");
        }


        //$inputDependienteTipoModelo.val("string");
      });

      //Funcionalidad en campo dependientes para seleccionar entre tipo igualdad y desigualdad
      $buttonDesigualdad=$("#formEditarCampo .campoDependientes .buttonDesigualdad");
      $buttonIgualdad=$("#formEditarCampo .campoDependientes .buttonIgualdad");
      $inputDependienteRelacion=$("#formEditarCampo input[name=dependiente_relacion]");
      $inputDependienteRelacion1=$("#formEditarCampo input[name=dependiente_relacion_1]");
      $inputDependienteRelacionModelo=$("#formEditarCampo .dependiente_relacion_modelo");
      $buttonIgualdad.attr("disabled",$inputDependienteRelacion.val()=="==");
      $buttonDesigualdad.attr("disabled",$inputDependienteRelacion.val()=="!=");
      $buttonDesigualdad.click(function() {
        $(this).parent().parent().find('.buttonIgualdad').prop("disabled", false);
        //$buttonIgualdad.prop("disabled", false);
        $(this).parent().parent().find('.buttonDesigualdad').prop("disabled", true);
        // $buttonDesigualdad.prop("disabled", true);
        // $inputDependienteRelacion.val("!=");

        if  ($(this).parent().parent().attr('id') == 'campo_dependiente_1'){
          $inputDependienteRelacion1.val("!=");
        }else{
          $(this).parent().parent().find('.dependiente_relacion_modelo').val("!=");
        }


        // $inputDependienteRelacionModelo.val("!=");
      });
      $buttonIgualdad.click(function() {
        $(this).parent().parent().find('.buttonIgualdad').prop("disabled", true);
        // $buttonIgualdad.prop("disabled",true);
        $(this).parent().parent().find('.buttonDesigualdad').prop("disabled", false);
        //$buttonDesigualdad.prop("disabled",false);
        //$inputDependienteRelacion.val("==");
        if  ($(this).parent().parent().attr('id') == 'campo_dependiente_1'){
          $inputDependienteRelacion1.val("==");
        }else{
          $(this).parent().parent().find('.dependiente_relacion_modelo').val("==");
        }


        // $inputDependienteRelacionModelo.val("==");
      });
    }

    function loadDependientesButtons() {
      $buttonRegex=$("#formEditarCampo .campoDependientes .buttonRegex");
      $buttonString=$("#formEditarCampo .campoDependientes .buttonString");
      $inputDependienteTipo=$("#formEditarCampo input[name=dependiente_tipo]");
      $inputDependienteTipo1=$("#formEditarCampo input[name=dependiente_tipo_1]");
      $inputDependienteTipoModelo=$(".dependiente_tipo_modelo");
      $buttonRegex.click(function() {
        $(this).parent().parent().find('.buttonString').prop("disabled", false);
        $(this).parent().parent().find('.buttonRegex').prop("disabled", true);
        if  ($(this).parent().parent().attr('id') == 'campo_dependiente_1'){
          $inputDependienteTipo1.val("regex");
        }else{
          $(this).parent().parent().find('.dependiente_tipo_modelo').val("regex");
        }


      });
      $buttonString.click(function() {
        $(this).parent().parent().find('.buttonString').prop("disabled", true);
        $(this).parent().parent().find('.buttonRegex').prop("disabled", false);
        if  ($(this).parent().parent().attr('id') == 'campo_dependiente_1'){
          $inputDependienteTipo1.val("string");
        }else{
          $(this).parent().parent().find('.dependiente_tipo_modelo').val("string");
        }


      });
      $buttonDesigualdad=$("#formEditarCampo .campoDependientes .buttonDesigualdad");
      $buttonIgualdad=$("#formEditarCampo .campoDependientes .buttonIgualdad");
      $inputDependienteRelacion=$("#formEditarCampo input[name=dependiente_relacion]");
      $inputDependienteRelacion1=$("#formEditarCampo input[name=dependiente_relacion_1]");
      $inputDependienteRelacionModelo=$("#formEditarCampo .dependiente_relacion_modelo");
      $buttonDesigualdad.click(function() {
        $(this).parent().parent().find('.buttonIgualdad').prop("disabled", false);
        $(this).parent().parent().find('.buttonDesigualdad').prop("disabled", true);
        if  ($(this).parent().parent().attr('id') == 'campo_dependiente_1'){
          $inputDependienteRelacion1.val("!=");
        }else{
          $(this).parent().parent().find('.dependiente_relacion_modelo').val("!=");
        }


      });
      $buttonIgualdad.click(function() {
        $(this).parent().parent().find('.buttonIgualdad').prop("disabled", true);
        $(this).parent().parent().find('.buttonDesigualdad').prop("disabled", false);
          if  ($(this).parent().parent().attr('id') == 'campo_dependiente_1'){
            $inputDependienteRelacion1.val("==");
          }else{
            $(this).parent().parent().find('.dependiente_relacion_modelo').val("==");
          }


      });
    }

    function observeChangeDependientes() {
      // -- Actualiza estado de dependientes luego de un cambio
      $('.campoDependientes').change(function() {
        updateDependientes();
      });
      $('.buttonRegex').click(function() {
        updateDependientes();
      });
      $('.buttonString').click(function() {
        updateDependientes();
      });
      $('.buttonIgualdad').click(function() {
        updateDependientes();
      });
      $('.buttonDesigualdad').click(function() {
        updateDependientes();
      });
    }

    $(document).ready(function() {
        updateSelects();

        // -- Carga los campos de validacion guardados
        var cargados_campos_dependientes = $('input[name=dependiente_campo]').val();
            cargados_campos_dependientes = cargados_campos_dependientes.split(',');
        var cargados_relaciones_dependientes = $('input[name=dependiente_relacion]').val();
            cargados_relaciones_dependientes = cargados_relaciones_dependientes.split(',');
        var cargados_valores_dependientes = $('input[name=dependiente_valor]').val();
            cargados_valores_dependientes = cargados_valores_dependientes.split(',');
        var cargados_tipos_dependientes = $('input[name=dependiente_tipo]').val();
            cargados_tipos_dependientes = cargados_tipos_dependientes.split(',');

        indice = 0;
        $(cargados_campos_dependientes).each(function() {
          if(indice == 0) {
            $('select[name=dependiente_campo_1]').val(this.toString());
            $('input[name=dependiente_relacion_1]').val(cargados_relaciones_dependientes[indice]);
            $('input[name=dependiente_valor_1]').val(cargados_valores_dependientes[indice]);
            $('input[name=dependiente_tipo_1]').val(cargados_tipos_dependientes[indice]);

            if(cargados_relaciones_dependientes[indice] == '==') {
              $('select[name=dependiente_campo_1]').parent().find('.buttonIgualdad').prop("disabled", true);
              $('select[name=dependiente_campo_1]').parent().find('.buttonDesigualdad').prop("disabled", false);
            }
            else {
              $('select[name=dependiente_campo_1]').parent().find('.buttonIgualdad').prop("disabled", false);
              $('select[name=dependiente_campo_1]').parent().find('.buttonDesigualdad').prop("disabled", true);
            }

            if(cargados_tipos_dependientes[indice] == 'regex') {
              $('select[name=dependiente_campo_1]').parent().find('.buttonRegex').prop("disabled", true);
              $('select[name=dependiente_campo_1]').parent().find('.buttonString').prop("disabled", false);
            }
            else {
              $('select[name=dependiente_campo_1]').parent().find('.buttonRegex').prop("disabled", false);
              $('select[name=dependiente_campo_1]').parent().find('.buttonString').prop("disabled", true);
            }
          }
          else {
            var nuevo_dependiente = $('.campo_dependiente_modelo').first().clone();
            $(nuevo_dependiente).find('.dependiente_campo_modelo').val(this.toString());
            $(nuevo_dependiente).find('.dependiente_relacion_modelo').val(cargados_relaciones_dependientes[indice]);
            $(nuevo_dependiente).find('.dependiente_valor_modelo').val(cargados_valores_dependientes[indice]);
            $(nuevo_dependiente).find('.dependiente_tipo_modelo').val(cargados_tipos_dependientes[indice]);

            if(cargados_relaciones_dependientes[indice] == '==') {
              $(nuevo_dependiente).find('.buttonIgualdad').prop("disabled", true);
              $(nuevo_dependiente).find('.buttonDesigualdad').prop("disabled", false);
            }
            else {
              $(nuevo_dependiente).find('.buttonIgualdad').prop("disabled", false);
              $(nuevo_dependiente).find('.buttonDesigualdad').prop("disabled", true);
            }

            if(cargados_tipos_dependientes[indice] == 'regex') {
              $(nuevo_dependiente).find('.buttonRegex').prop("disabled", true);
              $(nuevo_dependiente).find('.buttonString').prop("disabled", false);
            }
            else {
              $(nuevo_dependiente).find('.buttonRegex').prop("disabled", false);
              $(nuevo_dependiente).find('.buttonString').prop("disabled", true);
            }

            $(nuevo_dependiente).appendTo('.campoDependientesClones');
            $('.campoDependientesClones').find('.campoDependientes').removeClass('hidden');
          }

          $('.remove_dependiente').click(function() {
            $(this).parent().parent().remove();
            updateDependientes();
          });

          loadDependientesButtons();

          indice++
        });

        // -- Agrega nuevos campos de validacion
        $('#add_dependiente').click(function() {
          $('.campo_dependiente_modelo').first().clone().appendTo('.campoDependientesClones').find('.dependiente_valor_modelo').val('');
          $('.campoDependientesClones').find('.campoDependientes').removeClass('hidden');
          loadDependientesButtons();
          observeChangeDependientes()

          $('.remove_dependiente').click(function() {
            $(this).parent().parent().remove();
            updateDependientes();
          });

          return false;
        });

        observeChangeDependientes();

        $('#requiere_accion').click(function() {
          if($(this).prop('checked')) {
            $('#requiere_accion_elementos').removeClass('hidden').show();
          }
          else {
            $('#requiere_accion_elementos').hide();
          }
        });

        $('.validacion').typeahead({
            mode: "multiple",
            delimiter: "|",
            source: ["required","rut","ci","ci_validacionExtendida","alpha_numeric_ext","not_empty_table","consulta_pago_completo_generico","min_length_table[num]","max_length_table[num]","min_length[num]","max_length[num]","exact_length[num]","greater_than[num]","less_than[num]","alpha","alpha_numeric","alpha_dash","alpha_space","numeric","integer","decimal","is_natural","is_natural_no_zero","valid_email","valid_emails","valid_ip","valid_base64","trim","is_unique[exp]","validar_agenda_sae","validar_campo_ica"]
        });

        //Funcionalidad del llenado de nombre usando el boton de asistencia
        $("#formEditarCampo .asistencia .dropdown-menu a").click(function(){
            var nombre=$(this).text();
            $("#formEditarCampo input[name=nombre]").val(nombre);
        });

        //Llenado automatico del campo nombre
        $("#formEditarCampo input[name=etiqueta]").blur(function(){
            ellipsize($("#formEditarCampo input[name=etiqueta]"),$("#formEditarCampo input[name=nombre]"));
        });
        //Llenado automatico del campo valor
        $("#formEditarCampo").on("blur","input[name$='[etiqueta]']",function(){
            var campoOrigen=$(this);
            var campoDestino=$(this).closest("tr").find("input[name$='[valor]']")
            ellipsize(campoOrigen,campoDestino);
        });

        function ellipsize(campoOrigen, campoDestino){
            if($(campoDestino).val()==""){
                var string=$(campoOrigen).val().trim();
                string=string.toLowerCase();
                string=string.replace(/\s/g,"_");
                string=string.replace(/á/g,"a");
                string=string.replace(/é/g,"e");
                string=string.replace(/í/g,"i");
                string=string.replace(/ó/g,"o");
                string=string.replace(/ú/g,"u");
                string=string.replace(/\W/g,"");
                $(campoDestino).val(string);
            }
        }
    });
</script>

<div class="modal-header">
    <button class="close" data-dismiss="modal">×</button>
    <h3>Edición de Campo</h3>
</div>
<form id="formEditarCampo" class="ajaxForm" method="POST" action="<?= site_url('backend/formularios/editar_campo_form/' . ($edit ? $campo->id : '')) ?>">
  <div class="modal-body">
        <div class="validacion validacion-error"></div>
        <?php if (!$edit): ?>
            <input type="hidden" name="formulario_id" value="<?= $formulario->id ?>" />
            <input type="hidden" name="tipo" value="<?= $campo->tipo ?>" />
        <?php endif; ?>
        <?php if (!$campo->sin_etiqueta): ?>
          <label for="etiqueta">Etiqueta</label>
          <?php if($campo->etiqueta_tamano=='xxlarge'):?>
            <?php if($campo->tipo == 'javascript'):?>
              <textarea id="etiqueta" class="input-xxlarge campo_javascript_codigo" rows="15" name="etiqueta"><?= htmlspecialchars($campo->etiqueta) ?></textarea>
            <?php else: ?>
              <textarea id="etiqueta" class="input-xxlarge" rows="5" name="etiqueta"><?= htmlspecialchars($campo->etiqueta) ?></textarea>
            <?php endif; ?>
          <?php else: ?>
            <input type="text" id="etiqueta" name="etiqueta" value="<?= htmlspecialchars($campo->etiqueta) ?>" />
          <?php endif ?>
        <?php else: ?>
          <input type="hidden" id="etiqueta" name="etiqueta" value="etiqueta" />
        <?php endif; ?>
        <?php if($campo->requiere_nombre):?>
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" value="<?= $campo->nombre ?>" />
        <?php $campos_asistencia=$formulario->Proceso->getNombresDeCampos($campo->tipo,false) ?>
        <?php if(count($campos_asistencia)):?>
        <div class="btn-group asistencia" style="display: inline-block; vertical-align: top;">
            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><span class="icon-th-list"></span><span class="caret"></span></a>
            <ul class="dropdown-menu">
                <?php foreach ($campos_asistencia as $c): ?>
                    <li><a href="#"><?= $c ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif ?>
        <?php else: ?>
        <input type="hidden" name="nombre" value="<?=$campo->nombre?$campo->nombre:uniqid();?>" />
        <?php endif; ?>

        <?php if (!$campo->sin_etiqueta): ?>
          <?php if(!$campo->estatico):?>
          <label for="ayuda">Ayuda contextual (Opcional)</label>
          <input type="text" class="input-xxlarge" id="ayuda" name="ayuda" value="<?=$campo->ayuda?>" />

          <label for="ayuda_ampliada">Texto de ayuda ampliada (Opcional)</label>
          <input type="text" class="input-xxlarge" id="ayuda_ampliada" name="ayuda_ampliada" value="<?= strip_tags($campo->ayuda_ampliada); ?>" />
          <?php endif ?>
        <?php endif ?>

        <?php if (!$campo->sin_etiqueta): ?>
          <?php if (!$campo->estatico): ?>
              <label class="checkbox" for="soloLectura"><input type="checkbox" id="soloLectura" name="readonly" value="1" <?=$campo->readonly?'checked':''?> /> Solo lectura</label>
              <?php if ($campo->tipo == 'text'): ?>
                <label for="documento_tramite"><input class='checkbox' id="documento_tramite" type="checkbox" name="documento_tramite" value="1" <?=$campo->documento_tramite?'checked':''?> /> Número de documento del trámite</label>
                <label for="email_tramite"><input class='checkbox' id="email_tramite" type="checkbox" name="email_tramite" value="1" <?=$campo->email_tramite?'checked':''?> /> Correo electrónico del trámite</label>
              <?php endif ?>
          <?php endif; ?>
        <?php endif; ?>
        <?php if ((!$campo->estatico) && ($campo->requiere_validacion)): ?>
            <label for="validacion">Reglas de validación</label>
            <input class='validacion' id="validacion" type="text" name="validacion" value="<?= $edit ? implode('|', $campo->validacion) : 'required' ?>"/>
        <?php endif; ?>
            <?php if(!$campo->estatico):?>
              <?php if($campo->valor_default_tamano == 'large'): ?>
                <?php if($campo->dialogo):?>
                  <label for="campo_dialogo_titulo">Título</label>
                  <input type="text" id="campo_dialogo_titulo" class="input-xxlarge" />
                  <label for="campo_dialogo_contenido">Contenido</label>
                  <textarea id="campo_dialogo_contenido" class="input-xxlarge"></textarea>
                  <label for="campo_dialogo_titulo_enlace">Título del enlace</label>
                  <input type="text" id="campo_dialogo_titulo_enlace" class="input-xxlarge" />
                  <label for="campo_dialogo_enlace">Enlace</label>
                  <input type="text" id="campo_dialogo_enlace" class="input-xxlarge" />

                  <div class="hidden" id="valor_default_html"><?=htmlspecialchars($campo->valor_default)?></div>
                  <textarea id="valor_default" name="valor_default" class="input-xxlarge hidden"><?=htmlspecialchars($campo->valor_default)?></textarea>
                <?php else: ?>
                  <label for="valor_default">Contenido del diálogo</label>
                  <textarea id="valor_default" name="valor_default" class="input-xxlarge"><?=htmlspecialchars($campo->valor_default)?></textarea>
                <?php endif; ?>
              <?php else: ?>
                <?php if($campo->tipo == 'error'): ?>
                  <label for="valor_default">Variable</label>
                <?php else: ?>
                  <label for="valor_default">Valor por defecto</label>
                <?php endif; ?>
                <input type="text" id="valor_default" name="valor_default" value="<?=htmlspecialchars($campo->valor_default)?>" />
              <?php endif; ?>
            <?php endif ?>

            <?php if($campo->tipo == 'estado_pago'): ?>
              <label for="valor_default">Variable de ID de solicitud</label>
              <input type="text" id="valor_default" name="valor_default" value="<?=htmlspecialchars($campo->valor_default)?>" />
            <?php endif ?>

            <?php if(($campo->tipo != 'fieldset') && ($campo->tipo != 'bloque')):?>
            <label for="lista_de_fieldsets">Fieldset al que pertenece</label>
            <input type="text" id="fieldset" name="fieldset" value="<?=htmlspecialchars($campo->fieldset)?>" />
            <?php endif ?>
            <?php if($campo->tipo == 'bloque'):?>
            <label for="selector_bloques">Tipo de bloque</label>
            <select name="valor_default" id="selector_bloques">
                <option value="" <?= $campo->valor_default == '' ? 'selected' : '' ?>>-- Seleccionar tipo de bloque --</option>
                <?php foreach($bloques as $bloque) { ?>
                    <option value="<?= $bloque->id ?>" <?= $campo->valor_default == '<?= $bloque->id ?>' ? 'selected' : '' ?>><?= $bloque->nombre ?></option>
                <?php } ?>
            </select>
            <?php echo form_error('valor_default'); ?>
            <?php endif ?>
            <?php if($campo->tipo == 'pagos'):?>
              <label for="validacion">Reglas de validación</label>
              <input class='validacion' id="validacion" type="text" name="validacion" value="<?= $edit ? implode('|', $campo->validacion) : '' ?>"/>
            <label for="selector_pagos">Método de pago</label>
            <select name="valor_default" id="selector_pagos">
                <option value="" <?= $campo->valor_default == '' ? 'selected' : '' ?>>-- Seleccionar método de pago --</option>
                <?php foreach($pagos as $pago) { ?>
                    <option value="<?= $pago->id ?>" <?= $campo->valor_default == $pago->id ? 'selected' : '' ?>><?= $pago->nombre ?></option>
                <?php } ?>
            </select>
            <?php echo form_error('valor_default'); ?>
        <?php endif ?>

            <div class="campoDependientes" id="campo_dependiente_1">
                <label for="dependiente_campo_1">Visible solo si</label>
                <select class="input-medium" id="dependiente_campo_1" name="dependiente_campo_1">
                    <option value="<?=$campo->dependiente_campo?>"><?=$campo->dependiente_campo?></option>
                </select>
                <div class="btn-group" style="margin-bottom: 9px;">
                    <button type="button" class="buttonIgualdad btn">=</button><button type="button" class="buttonDesigualdad btn">!=</button>
                </div>
                <input type="hidden" name="dependiente_relacion_1" />
                <span class="input-append">
                  <label class="hidden-accessible" for="dependiente_valor">Valor</label>
                    <input type="text" name="dependiente_valor_1" id="dependiente_valor_1" /><button type="button" class="buttonString btn">String</button><button type="button" class="buttonRegex btn">Regex</button>
                </span>
                <div class="btn-group" style="display: inline-block; vertical-align: top;">
                  <div class="btn" id="add_dependiente"><span class="icon-plus"></span></div>
                </div>
                <input type="hidden" name="dependiente_tipo_1" />
            </div>
            <div class="campoDependientesClones"></div>
            <div class="campoDependientes hidden campo_dependiente_modelo">
                <select class="input-medium dependiente_campo_modelo">
                    <option value="<?=$campo->dependiente_campo?>"><?=$campo->dependiente_campo?></option>
                </select>
                <div class="btn-group" style="margin-bottom: 9px;">
                    <button type="button" class="buttonIgualdad btn">=</button><button type="button" class="buttonDesigualdad btn">!=</button>
                </div>
                <input type="hidden" class="dependiente_relacion_modelo" />
                <span class="input-append">
                  <label class="hidden-accessible">Valor</label>
                    <input type="text" class="dependiente_valor_modelo" /><button type="button" class="buttonString btn">String</button><button type="button" class="buttonRegex btn">Regex</button>
                </span>
                <input type="hidden" class="dependiente_tipo_modelo" />
                <div class="btn-group" style="display: inline-block; vertical-align: top;">
                  <div class="btn remove_dependiente"><span class="icon-minus"></span></div>
                </div>
            </div>

            <div id="campoDependientesData" class="hidden">
              <input type="hidden" name="dependiente_campo" value="<?=$campo->dependiente_campo?>" />
              <input type="hidden" name="dependiente_relacion" value="<?=isset($campo) && $campo->dependiente_relacion? $campo->dependiente_relacion:'==' ?>" />
              <input type="hidden" name="dependiente_valor" value="<?= isset($campo) ? $campo->dependiente_valor : '' ?>" />
              <input type="hidden" name="dependiente_tipo" value="<?=isset($campo) && $campo->dependiente_tipo? $campo->dependiente_tipo:'string' ?>" />
            </div>

            <?php if($campo->tipo == 'text' || $campo->tipo == 'textarea' || $campo->tipo == 'radio' || $campo->tipo == 'select' || $campo->tipo == 'checkbox' || $campo->tipo == 'date' || $campo->tipo == 'paragraph' || $campo->tipo == 'dialogo'):?>
              <label class="checkbox" for="requiere_accion"><input type="checkbox" id="requiere_accion" name="requiere_accion" value="1" <?=$campo->requiere_accion?'checked':''?> /> Requiere ejecutar acción</label>
              <span id="requiere_accion_elementos" class="<?=$campo->requiere_accion ? '' : 'hidden'?>">
                <label for="requiere_accion_id">Acción a ejecutar</label>
                <select class="input-medium" id="requiere_accion_id" name="requiere_accion_id">
                  <?php foreach ($acciones as $a): ?>
                      <option value="<?= $a->id ?>" <?= $campo->requiere_accion_id == $a->id ? 'selected' : '' ?>><?= $a->nombre ?></option>
                  <?php endforeach; ?>
                </select>
                <label for="requiere_accion_boton">Nombre del botón</label>
                <input type="text" id="requiere_accion_boton" name="requiere_accion_boton" value="<?=$campo->requiere_accion_boton?>" />
                <label for="requiere_accion_var_error">Variable con el error</label>
                <input type="text" id="requiere_accion_var_error" name="requiere_accion_var_error" value="<?=$campo->requiere_accion_var_error?>" />
              </span>
            <?php endif; ?>

            <?=$campo->extraForm()?$campo->extraForm():''?>

        <?php if ($campo->requiere_datos): ?>
            <div class="datos">
                <script type="text/javascript">
                    $(document).ready(function(){
                        $('#formEditarCampo .datos .nuevo').click(function() {
                            var pos=$('#formEditarCampo .datos table tbody tr').size();
                            var html='<tr>';
                            html+='<td><label class="hidden-accessible" for="etiqueta'+pos+'">etiqueta'+pos+'</label><input id="etiqueta'+pos+'" type="text" name="datos['+pos+'][etiqueta]" /></td>';
                            html+='<td><label class="hidden-accessible" for="valor'+pos+'">valor'+pos+'</label><input id="valor'+pos+'" type="text" name="datos['+pos+'][valor]" /></td>';
                            html+='<td class="actions"><button type="button" class="btn btn-danger eliminar"><span class="icon-trash icon-white"></span></button></td>';
                            html+='</tr>';

                            $('#formEditarCampo .datos table tbody').append(html);
                        });
                        $('#formEditarCampo .datos').on('click','.eliminar',function(){
                            $(this).closest('tr').remove();
                        });
                    });
                </script>
                <h4>Datos</h4>
                <button class="btn nuevo" type="button"><span class="icon-plus"></span> Nuevo</button>
                <table class="table">
                  <caption class="hidden-accessible">Opciones</caption>
                  <thead>
                      <tr>
                          <th>Etiqueta</th>
                          <th>Valor</th>
                          <th>Acciones</th>
                      </tr>
                  </thead>
                  <tbody>
                    <?php if($campo->datos):?>
                      <?php $i=0 ?>
                      <?php foreach ($campo->datos as $key => $d): ?>
                        <tr>
                          <td><label class="hidden-accessible" for="etiqueta<?= $i ?>">etiqueta<?= $i ?></label><input id="etiqueta<?= $i ?>" type="text" name="datos[<?= $i ?>][etiqueta]" value="<?= $d->etiqueta ?>" /></td>
                          <td><label class="hidden-accessible" for="valor<?= $i ?>">valor<?= $i ?></label><input id="valor<?= $i ?>" type="text" name="datos[<?= $i ?>][valor]" value="<?= $d->valor ?>" /></td>
                          <td class="actions"><button type="button" class="btn btn-danger eliminar"><span class="icon-white icon-trash"></span></button></td>
                        </tr>
                        <?php $i++ ?>
                      <?php endforeach; ?>
                    <?php endif ?>
                  </tbody>
                </table>
            </div>
        <?php endif; ?>

      <?php if($campo->tipo == 'pagos'):?>
        <label for="check_pago_online"><?=TEXTO_CONFIG_PAGO_ONLINE?></label>
        <select id="check_pago_online" name="check_pago_online">

          <?php if($campo->pago_online == 1):?>
            <option value="1" selected>Si</option>
            <option value="0">No</option>
          <?php else: ?>
            <option value="0" selected>No</option>
            <option value="1">Si</option>
          <?php endif; ?>

          </select>
      <?php endif; ?>

      <?php if($campo->tipo == 'agenda' || $campo->tipo == 'agenda_sae'):?>
        <label for="check_requiere_agendar"><?=TEXTO_CONFIG_AGENDAR?></label>
        <select id="check_requiere_agendar" name="check_requiere_agendar">

          <?php if($campo->requiere_agendar == 1):?>
            <option value="1" selected>Si</option>
            <option value="0">No</option>
          <?php else: ?>
            <option value="0" selected>No</option>
            <option value="1">Si</option>
          <?php endif; ?>

          </select>
      <?php endif; ?>

      <?php if($campo->tipo == 'documento'):?>
        <label for="check_firma_electronica"><?=TEXTO_CONFIG_FIRMA_DOCUMENTO?></label>
        <select id="check_firma_electronica" name="check_firma_electronica">

          <?php if($campo->firma_electronica == 1):?>
            <option value="1" selected>Si</option>
            <option value="0">No</option>
          <?php else: ?>
            <option value="0" selected>No</option>
            <option value="1">Si</option>
          <?php endif; ?>

          </select>
      <?php endif; ?>

      <?=$campo->backendExtraFields()?>

  </div>
  <div class="modal-footer">
    <a href="#" data-dismiss="modal" class="btn btn-link">Cerrar</a>
    <button type="submit" class="btn btn-primary">Guardar</button>
  </div>
</form>
