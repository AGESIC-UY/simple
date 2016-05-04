<script src="<?= base_url() ?>assets/js/modelador-formularios.js" type="text/javascript"></script>

<script type="text/javascript">
    var formularioId=<?= $formulario->id ?>;
</script>

<ul class="breadcrumb">
    <li>
        <a href="<?= site_url('backend/procesos') ?>">Listado de Procesos</a> <span class="divider">/</span>
    </li>
    <li>
        <a href="<?= site_url('backend/formularios/listar/' . $proceso->id) ?>"><?= $proceso->nombre ?></a> <span class="divider">/</span>
    </li>
    <li class="active"><?= $formulario->nombre ?></li>
</ul>
<h2><?=$proceso->nombre?></h2>
<ul class="nav nav-tabs">
    <li><a href="<?= site_url('backend/procesos/editar/' . $proceso->id) ?>">Diseñador</a></li>
    <li class="active"><a href="<?= site_url('backend/formularios/listar/' . $proceso->id) ?>">Formularios</a></li>
    <li><a href="<?= site_url('backend/documentos/listar/' . $proceso->id) ?>">Documentos</a></li>
    <li><a href="<?= site_url('backend/acciones/listar/' . $proceso->id) ?>">Acciones</a></li>
    <li><a href="<?= site_url('backend/trazabilidad/listar/' . $proceso->id) ?>">Trazabilidad</a></li>
</ul>

<div id="areaFormulario">
  <div class="titulo-form">
    <h3><?= $formulario->nombre ?><a href="#" onclick="return editarFormulario(<?= $formulario->id ?>)"><span class="icon-edit" style="vertical-align:middle;"></span></a></h3>
  </div>

    <div class="btn-toolbar toolbar-formulario">
        <div class="btn-group">
            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'title')">Título</button>
            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'subtitle')">Subtítulo</button>
            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'paragraph')">Parrafo</button>
            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'documento')">Documento</button>
        </div>
        <div class="btn-group">
            <button class="btn btn-inverse campo_no_requerido" onclick="return agregarCampo(<?= $formulario->id ?>,'error')">Mensaje de error</button>
            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'fieldset')">Fieldset</button>
            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'text')">Textbox</button>
            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'textarea')">Textarea</button>
            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'select')">Select</button>
            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'radio')">Radio</button>
            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'checkbox')">Checkbox</button>
            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'file')">File</button>
            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'date')">Date</button>
            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'grid')">Grilla</button>
        </div>
        <div class="btn-group">
            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'bloque')">Bloques</button>
            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'paises')">Paises</button>
            <button class="btn btn-inverse hidden" onclick="return agregarCampo(<?= $formulario->id ?>,'comunas')">Comunas</button>
            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'moneda')">Moneda</button>
            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'agenda')">Agenda</button>
            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'pagos')">Pagos</button>
            <button class="btn btn-inverse" onclick="return agregarCampo(<?= $formulario->id ?>,'javascript')">Javascript</button>
        </div>
    </div>

    <form id="formEditarFormulario" class="form-horizontal dynaForm debugForm" onsubmit="return false">
      <fieldset>
        <legend><?= $formulario->nombre ?></legend>
        <div class="edicionFormulario">
            <?php foreach ($formulario->Campos as $c): ?>
                <div class="arrastrable">
                  <div class="campo" data-id="<?= $c->id ?>" <?= $c->dependiente_campo ? 'data-dependiente-campo="' . $c->dependiente_campo.'" data-dependiente-valor="' . $c->dependiente_valor .'" data-dependiente-tipo="' . $c->dependiente_tipo.'" data-dependiente-relacion="'.$c->dependiente_relacion.'"' : '' ?> >
                      <div class="grupo-dato">
                        <div class="botones-edit">
                            <div class="buttons">
                                <a href="#" class="btn btn-primary" onclick="return editarCampo(<?= $c->id ?>)"><span class="icon-edit icon-white"></span><span class="hide-text">Editar <?=$c->id?></span></a>
                                <a href="<?= site_url('backend/formularios/eliminar_campo/' . $c->id) ?>" class="btn btn-danger" onclick="return confirm('¿Esta seguro que desea eliminar?')"><span class="icon-trash icon-white"></span><span class="hide-text">Eliminar <?=$c->id?></span></a>
                            </div>
                        </div>
                        <div class="campo-form">
                            <div><?= $c->displaySinDato() ?></div>
                        </div>
                      </div>
                  </div>
              </div>
          <?php endforeach; ?>
        </div>
      </fieldset>
      <button type="submit" class="hidden-accessible" value="enviar"/>
    </form>

</div>

<div class="modal hide fade" id="modal">

</div>
