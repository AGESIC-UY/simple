<ul class="breadcrumb">
    <li>
        <a href="<?= site_url('backend/procesos') ?>">Listado de Procesos</a> <span class="divider">/</span>
    </li>
    <li>
        <a href="<?= site_url('backend/validaciones/listar/' . $proceso->id) ?>"><?= $proceso->nombre ?></a> <span class="divider">/</span>
    </li>
    <li class="active"><?= $edit ? $validacion->nombre : '' ?></li>
</ul>

<?php $this->load->view('backend/proceso_descripcion') ?>
<ul class="nav nav-tabs">
    <li><a href="<?= site_url('backend/procesos/editar/' . $proceso->id) ?>">Diseñador</a></li>
    <li><a href="<?= site_url('backend/formularios/listar/comun/' . $proceso->id) ?>">Formularios</a></li>
    <li><a href="<?= site_url('backend/formularios/listar/obn/' . $proceso->id) ?>">Formularios para Tablas de Datos</a></li>
    <li ><a href="<?= site_url('backend/documentos/listar/' . $proceso->id) ?>">Documentos</a></li>
    <li class="active"><a href="<?= site_url('backend/validaciones/listar/' . $proceso->id) ?>">Validaciones</a></li>
    <li><a href="<?= site_url('backend/acciones/listar/' . $proceso->id) ?>">Acciones</a></li>
    <li><a href="<?= site_url('backend/trazabilidad/listar/' . $proceso->id) ?>">Trazabilidad</a></li>
    <li><a href="<?= site_url('backend/procesos/editar_codigo_tramite_ws_grep/' . $proceso->id) ?>">Código tramites.gub.uy</a></li>
    <li><a href="<?= site_url('backend/procesos/editar_api/' . $proceso->id) ?>">API</a></li>

</ul>


<form class="ajaxForm" method="POST" action="<?= site_url('backend/validaciones/editar_form/' . ($edit ? $validacion->id : '')) ?>">
    <div class="titulo-form">
        <h3><?= $edit ? $validacion->nombre : 'Validación' ?></h3>
    </div>
    <div class="validacion validacion-error"></div>
    <?php if (!$edit): ?>
        <input type="hidden" name="proceso_id" value="<?= $proceso->id ?>" />
    <?php endif; ?>
    <fieldset>
        <legend>Datos generales</legend>
        <div class="form-horizontal">
            <div class="control-group">
                <label for="nombre" class="control-label">Nombre</label>
                <div class="controls">
                    <input type="text" id="nombre" name="nombre" value="<?= $edit ? $validacion->nombre : '' ?>" />
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend>Otros datos</legend>          
        <div class="form-horizontal">            
            <div class="control-group">
                <label class="control-label" for="contenido">Código de la validación</label>
                <div class="controls">
                    <textarea id="contenido" name="contenido" class="input-xxlarge" rows="20"><?= $edit ? $validacion->contenido : '' ?></textarea>

                </div>
            </div>
            <div class="help-block">
                <ul>
                    <li>EL acceso a las variables con su valor y campo será de la forma: variables.NOMBRE_VARIABLE.valor y  variables.NOMBRE_VARIABLE.campo</li>
                    <li>EL modelador en la respuesta deberá formar un JSON de la forma: print({\"resultado\": \"ERROR\" o \"OK\", \"errores\": [Colección de errores],\"variables_seguimiento\":[{Colección de variables}]});</li>
                    <li>La colección de errores es de la forma {\"campo\": \"mensaje de error\"}</li>
                    <li>La colección de variables es de la forma {\"nombreVariable\": \"valorVariable\"}</li>
                    <li>Si la variable es de tipo Tabla el valor de esta es un JSON  Tabla:[["Nombre1","CI1","Teléfono1"],["Nombre2","CI2","Teléfono2"]]</li>
                    <li>Si la variable es de tipo Checkbox el valor de esta es un JSON  Checkbox:["si"]</li>
                    <li>Ejemplo:
<pre>//valida un campo de nombre apellido
if (variables.apellido.valor == ''){
    print("{\"resultado\": \"ERROR\",\"errores\":[{\""+ variables.apellido.campo +"\": \"El apellido es incorrecto\"}],\"variables_seguimiento\":[{\"error_validacion_apellido\":\""+ variables.apellido.campo +"\"}]}");
} else {
    print("{\"resultado\": \"OK\"}");
}</pre>
                    </li>
                </ul>
            </div>
        </div>
    </fieldset>
    <ul class="form-action-buttons">
        <li class="action-buttons-primary">
            <ul>
                <li>
                    <input class="btn btn-primary btn-lg" type="submit" value="Guardar" />
                </li>
            </ul>
        </li>
        <li class="action-buttons-second">
            <ul>
                <li class="float-left">
                    <a class="btn btn-link btn-lg" href="<?= site_url('backend/validaciones/listar/' . $proceso->id) ?>">Cancelar</a>
                </li>
            </ul>
        </li>
    </ul>
</form>




</div>
