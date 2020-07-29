<script type="text/javascript">
    $(document).ready(function () {
        $('#formEditarObn .nav-tabs a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
        $("#tipo_attr").change(function () {
            $("#mult_attr").removeAttr("checked");
            $("#mult_attr").removeAttr("disabled");
            $("#clogica_attr").removeAttr("disabled");
            if ($(this).val() == "obn") {
                $("#clogica_attr").removeAttr("checked");
                $("#clogica_attr").attr("disabled", "");
            } else {
                $("#clogica_attr").removeAttr("disabled");
            }
            if ($(this).val() == "obn") {
                var select_obn = "<select id='valores_attr'>";
<?php foreach ($lista_obn as $value): ?>
                    select_obn += "<option value='<?= $value->identificador ?>'><?= $value->identificador ?></option>";
<?php endforeach; ?>
                select_obn += "</select>";
                $("#valores_attr").parent("td").html(select_obn);
            } else if ($(this).val() == "varchar") {
                var input = '<input class="input-large" id="valores_attr" name="valores_attr" min="1"  value="255" placeholder="Tamaño de la cadena" type="number">';
                $("#valores_attr").parent("td").html(input);
            } else if ($(this).val() == "enum") {
                var input = '<input class="input-large" id="valores_attr" name="valores_attr" value="" placeholder="valor1:etiqueta1, valor2:etiqueta2" type="text">';
                $("#valores_attr").parent("td").html(input);
                $("#mult_attr").attr("disabled", "");
            }
            else {
                var input = '<input class="input-large" id="valores_attr" name="valores_attr" value="" placeholder="" type="text">';
                $("#valores_attr").parent("td").html(input);
            }

            if ($(this).val() == "bool" || $(this).val() == "char" || $(this).val() == "int" || $(this).val() == "double" || $(this).val() == "date" || $(this).val() == "time") {
                $("#valores_attr").attr("disabled", "");
            } else {
                $("#valores_attr").removeAttr("disabled");
            }

            return false;
        });
        $("#mult_attr").change(function () {
            if ($(this).attr('checked')) {
                $("#clogica_attr").attr("disabled", "");
                $("#clogica_attr").removeAttr("checked");
            } else if ($("#tipo_attr").val() != "obn") {
                $("#clogica_attr").removeAttr("disabled");
            }
            return false;
        });
        //Permite borrar Atributos
        $(".tab-attr").on("click", ".delete", function () {
            $(this).closest("tr").remove();
            return false;
        });
        //Permite agregar nuevos Atributos
        $(".tab-attr .form-agregar-attr button").click(function () {
            var $form = $(".tab-attr .form-agregar-attr");
            var pos = 1 + $(".tab-attr table tbody tr").size();
            var nombre = $form.find("#nombre_attr").val();
            var patt = /^[a-zA-Z_][a-zA-Z_0-9]*$/;
            var result = patt.test(nombre);
            if (!result) {
                alert("El campo nombre del Atributo no es válido");
            } else {

                var tipo = $form.find("#tipo_attr option:selected").val();
                if ((tipo == "obn" || tipo == "enum" || tipo == "varchar") && $form.find("#valores_attr").val() == "") {
                    alert("El campo valores es requerido para este tipo de atributo");
                } else {
                    var tipo_nombre = $form.find("#tipo_attr option:selected").text();
                    var valores_attr = $form.find("#valores_attr").val();
                    var mult_text = $form.find("#mult_attr").attr('checked');
                    var mult = $form.find("#mult_attr").attr('checked') ? 1 : 0;
                    var clogica_text = $form.find("#clogica_attr").attr('checked');
                    var clogica = $form.find("#clogica_attr").attr('checked') ? 1 : 0;
                    var html = "<tr>";
                    html += "<td class='hidden'>" + pos + "</td>";
                    html += "<td>" + nombre + "</td>";
                    html += "<td>" + tipo_nombre + "</td>";
                    html += "<td>" + valores_attr + "</td>";
                    html += "<td><input disabled type='checkbox' " + mult_text + "/></td>";
                    html += "<td><input disabled type='checkbox' " + clogica_text + "/></td>";
                    html += '<td>';
                    html += '<input type="hidden" name="attr[' + pos + '][nombre]" value="' + nombre + '" />';
                    html += '<input type="hidden" name="attr[' + pos + '][tipo]" value="' + tipo + '" />';
                    html += '<input type="hidden" name="attr[' + pos + '][valores]" value="' + valores_attr + '" />';
                    html += '<input type="hidden" name="attr[' + pos + '][multiple]" value="' + mult + '" />';
                    html += '<input type="hidden" name="attr[' + pos + '][clave_logica]" value="' + clogica + '" />';

                    html += '<a class="delete" title="Eliminar" href="#"><span class="icon-trash"></span></a>';

                    html += '</td>';
                    html += "</tr>";
                    $(".tab-attr table tbody").append(html);
                }
                return false;
            }
        });
        //Permite borrar Atributos
        $(".tab-query").on("click", ".delete", function () {
            $(this).closest("tr").remove();
            return false;
        });

        //Permite agregar nuevas Query
        $(".tab-query .form-agregar-query button").click(function () {
            var $form = $(".tab-query .form-agregar-query");
            var pos = 1 + $(".tab-query table tbody tr").size();
            var nombre = $form.find("#nombre_query").val();
            var patt = /^[a-zA-Z_][a-zA-Z_0-9]*$/;
            var result = patt.test(nombre);
            if (!result) {
                alert("El campo nombre de la Pestaña Consulta no es válido");
            } else {
                var tipo = $form.find("#tipo_query option:selected").val();
                var tipo_nombre = $form.find("#tipo_query option:selected").text();
                var consulta_query = $form.find("#consulta_query").val();
                if (consulta_query == "") {
                    alert("El campo consulta de la Pestaña Consulta es obligatorio");
                } else{
                    var html = "<tr>";
                html += "<td class='hidden'>" + pos + "</td>";
                html += "<td>" + nombre + "</td>";
                html += "<td>" + tipo_nombre + "</td>";
                html += "<td>" + consulta_query + "</td>";
                html += '<td>';
                html += '<input type="hidden" name="query[' + pos + '][nombre]" value="' + nombre + '" />';
                html += '<input type="hidden" name="query[' + pos + '][tipo]" value="' + tipo + '" />';
                html += '<input type="hidden" name="query[' + pos + '][consulta]" value="' + consulta_query + '" />';
                html += '<a class="delete" title="Eliminar" href="#"><span class="icon-trash"></span></a>';
                html += '</td>';
                html += "</tr>";
                $(".tab-query table tbody").append(html);
                return false;
            }
        }
        });
    });
</script>

<ul class="breadcrumb">
    <li>
        <a href="<?= site_url('backend/obns/listar') ?>">Lista de Objetos</a> <span class="divider">/</span>
    </li>
    <li class="active"><?= $edit ? strtoupper($obn->identificador) : 'Nuevo Objeto de negocio' ?></li>
</ul>



<form class="ajaxForm" id="formEditarObn" method="POST" action="<?= site_url('backend/obns/editar_form/' . ($edit ? $obn->id : '')) ?>">
    <div class="titulo-form">
        <h3><?= $edit ? strtoupper($obn->identificador) : 'Nuevo Objeto de negocio' ?></h3>
    </div>
    <div class="validacion validacion-error"></div>

    <fieldset>
        <legend>Objeto de negocio</legend>
        <div class="form-horizontal">
            <div class="control-group">
                <label for="identificador" class="control-label">Identificador</label>
                <div class="controls">
                    <input type="text" id="identificador" name="identificador" class="input-large" <?= $edit ? 'readonly' : '' ?> value="<?= $edit ? strtoupper($obn->identificador) : '' ?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="descripcion">Descripción</label>
                <div class="controls">
                    <textarea id="descripcion" name="descripcion" class="input-large" rows="1"><?= $edit ? $obn->descripcion : '' ?></textarea>
                </div>
            </div>
        </div>
    </fieldset>
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1">Atributos</a></li>
            <li><a href="#tab2">Consultas de acceso</a></li>    
        </ul>
        <div class="tab-content">
            <div class="tab-attr tab-pane active" id="tab1">
                <div class="control-group">
                    <div class="controls">
                        <table name="tab_attrs" class="table table_condensed">
                            <caption class="hide-text">Atributos</caption>
                            <thead>
                                <tr class="form-agregar-attr">
                                    <td class="hidden"></td>
                                    <td>
                                        <input class="input-large" id="nombre_attr" name="nombre_attr" type="text" value="" placeholder="Nombre"/>
                                    </td>
                                    <td>
                                        <select id="tipo_attr" name="tipo_attr">
                                            <optgroup label="Cadena">   
                                                <option value="varchar">Cadena de caracteres</option>
                                                <option value="bool">Lógico</option>
                                                <option value="char">Caracter</option>                                                
                                            </optgroup>
                                            <optgroup label="Numéricos">                                         
                                                <option value="int">Entero</option>
                                                <option value="double">Real</option>
                                            </optgroup >
                                            <optgroup label="Tiempo">                                        
                                                <option value="date">Fecha</option>
                                                <option value="time">Hora</option>
                                            </optgroup>
                                            <optgroup label="Objeto">                                        
                                                <option value="obn">OBN</option>
                                                <!--option value="enum">ENUM</option-->
                                            </optgroup>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="input-large"  id="valores_attr" name="valores_attr" type="number" min="1" value="255" placeholder="Tamaño de la cadena"/>
                                    </td>  
                                    <td>
                                        <input id="mult_attr" name="mult_attr" type="checkbox"/>
                                    </td> 
                                    <td>
                                        <input id="clogica_attr" name="clogica_attr" type="checkbox"/>
                                    </td> 
                                    <td>
                                        <button type="button" class="btn" title="Agregar"><span class="icon-plus"></span></button>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="hidden">#</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Valores</th>
                                    <th>Múltiple</th>
                                    <th>Clave lógica</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($attr)): ?>
                                    <?php foreach ($attr as $value): ?>
                                        <tr>
                                            <td class="hidden"><label><?= $value->id ?></label></td>
                                            <td><label><?= $value->nombre ?></label></td>
                                            <td><label><?php
                                                    switch ($value->tipo) {
                                                        case "varchar":
                                                            echo "Cadena de caracteres";
                                                            break;
                                                        case "bool":
                                                            echo "Lógico";
                                                            break;
                                                        case "char":
                                                            echo "Caracter";
                                                            break;
                                                        case "int":
                                                            echo "Entero";
                                                            break;
                                                        case "double":
                                                            echo "Real";
                                                            break;
                                                        case "date":
                                                            echo "Fecha";
                                                            break;
                                                        case "time":
                                                            echo "Hora";
                                                            break;
                                                        case "obn":
                                                            echo "OBN";
                                                            break;
                                                        case "enum":
                                                            echo "ENUM";
                                                            break;
                                                        default :
                                                            echo $value->tipo;
                                                            break;
                                                    }
                                                    ?></label></td>
                                            <td><label><?= $value->valores ?></label></td>
                                            <td><input disabled="" type='checkbox' <?= ($value->multiple) ? "checked" : "" ?>/></td>
                                            <td><input disabled="" type='checkbox' <?= ($value->clave_logica) ? "checked" : "" ?>/></td>
                                            <td>
                                                <input type="hidden" name="attr[<?= $value->id ?>][nombre]" value="<?= $value->nombre ?>" />
                                                <input type="hidden" name="attr[<?= $value->id ?>][tipo]" value="<?= $value->tipo ?>" />
                                                <input type="hidden" name="attr[<?= $value->id ?>][valores]" value="<?= $value->valores ?>" />
                                                <input type="hidden" name="attr[<?= $value->id ?>][multiple]" value="<?= $value->multiple ?>" />
                                                <input type="hidden" name="attr[<?= $value->id ?>][clave_logica]" value="<?= $value->clave_logica ?>" />
                                                <?php if ($instancias == 0): ?>
                                                    <a class="delete" title="Eliminar" href="#"><span class="icon-trash"></span></a>
                                                <?php endif; ?>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-query tab-pane" id="tab2">
                <div class="control-group">
                    <div class="controls">
                        <table name="tab_querys" class="table table_condensed">
                            <caption class="hide-text">Consultas de acceso</caption>
                            <thead>
                                <tr class="form-agregar-query">
                                    <td class="hidden"></td>
                                    <td>
                                        <input class="input-large" id="nombre_query" name="nombre_query" type="text" value="" placeholder="Nombre"/>
                                    </td>
                                    <td>
                                        <select id="tipo_query" name="tipo_query" >
                                            <option value="count">Contador</option>
                                            <option value="query">Obtener</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input class="input-large"  id="consulta_query" name="consulta_query" type="text" value="" placeholder="Consulta"/>
                                    </td> 
                                    <td>
                                        <button type="button" class="btn" title="Agregar"><span class="icon-plus"></span></button>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="hidden">#</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Consulta</th>                           
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($query)): ?>
                                    <?php foreach ($query as $value): ?>
                                        <tr>
                                            <td class="hidden"><label><?= $value->id ?></label></td>
                                            <td><label><?= $value->nombre ?></label></td>
                                            <td><label><?= $value->tipo ?></label></td>
                                            <td><label><?= $value->consulta ?></label></td>
                                            <td>
                                                <input type="hidden" name="query[<?= $value->id ?>][nombre]" value="<?= $value->nombre ?>" />
                                                <input type="hidden" name="query[<?= $value->id ?>][tipo]" value="<?= $value->tipo ?>" />
                                                <input type="hidden" name="query[<?= $value->id ?>][consulta]" value="<?= $value->consulta ?>" />
                                                <a class="delete" title="Eliminar" href="#"><span class="icon-trash"></span></a>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <ul class="form-action-buttons">
        <li class="action-buttons-primary">
            <ul>
                <li>
                    <input class="btn btn-primary btn-lg" type="submit" value="Guardar" />
                </li>
            </ul>
        </li>
    </ul>
</form>
