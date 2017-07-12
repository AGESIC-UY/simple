<ul class="breadcrumb">
    <li>
        <a href="<?= site_url('backend/ws_catalogos') ?>">Catálogo de Servicios</a> <span class="divider">/</span>
    </li>
    <li>
        <a href="<?= site_url('backend/ws_catalogos/editar/'.$catalogo->id.'')?>"><?= $catalogo->nombre ?></a> <span class="divider">/</span>
    </li>
    <li>
        <a href="<?= site_url('backend/ws_catalogos/'.$catalogo->id.'/operaciones')?>">Operaciones</a> <span class="divider">/</span>
    </li>
    <li class="active"><?= $operacion->nombre ?></li>
</ul>

<form class="ajaxForm" action="<?=site_url('backend/ws_catalogos/operaciones_editar_form/'.$operacion->id)?>" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
  <a href="#" class="btn btn-ayuda btn-secundary" id="ayuda_contextual_operacion_editar"><span class="icon-white icon-question-sign"></span> Ayuda</a>
  <div class="validacion validacion-error"></div>
  <div class="titulo-form">
    <h2><?= $operacion->nombre ?></h2>
  </div>
  <fieldset>
    <legend>Datos generales</legend>
	  <input type="hidden" value="<?= $catalogo->id ?>" name="catalogo_id" />
    <input type="hidden" value="<?= $operacion->id ?>" id="operacion_id" />
    <div class="form-horizontal">
      <div class="control-group">
        <label for="codigo" class="control-label">Código*</label>
        <div class="controls">
          <input id="codigo" class="input-xlarge" type="text" value="<?= $operacion->codigo ?>" name="codigo" maxlength="12" />
        </div>
      </div>
      <div class="control-group">
        <label for="nombre" class="control-label">Nombre*</label>
        <div class="controls">
          <input id="nombre"  class="input-xlarge" type="text" value="<?= $operacion->nombre ?>" name="nombre" />
        </div>
      </div>
      <div class="control-group">
        <label for="operacion" class="control-label">Nombre real de operación (SoapAction)*</label>
        <div class="controls">
          <input id="operacion" class="input-xlarge" type="text" value="<?= $operacion->operacion ?>" name="operacion" />
        </div>
      </div>
      <div class="control-group">
        <label for="soap" class="control-label">Cuerpo SOAP*</label>
        <div class="controls">
          <textarea id="soap" name="soap" class="large-textarea" spellcheck="false"><?= $operacion->soap ?></textarea>
        </div>
      </div>
      <div class="control-group">
        <label for="ayuda" class="control-label">Ayuda*</label>
        <div class="controls">
          <textarea id="ayuda" name="ayuda" class="large-textarea"><?= $operacion->ayuda ?></textarea>
        </div>
      </div>
    </div>
  </fieldset>
  <fieldset>
    <legend>Respuestas</legend>
        <div id="respuestas_visual"><?php (count($operacion->respuestas) == 0) ? 'No hay respuestas creadas' : '' ?></div>
        <div class="btn btn-success" id="agregar_respuestas"><span class="icon-plus"></span> Agregar respuesta</div>
        <label class="hidden-accessible" for="respuestas">respuesta</label>
        <textarea id="respuestas" name="respuestas" class="hidden"><?= $operacion->respuestas ?></textarea>
        <?php if(isset($operacion_respuestas)): ?>
            <?php if(count($operacion_respuestas) > 0): ?>
                <div id="respuestas_creadas" class="hidden">
                    <?php foreach($operacion_respuestas as $respuesta) { ?>
                        <div class="margen">
                          <label for="xsl<?=$respuesta->operacion_id?>">XSL</label>
                          <textarea data-operacion-id="xsl<?=$respuesta->operacion_id?>" name="xslt[<?=$respuesta->operacion_id?>][<?=$respuesta->respuesta_id?>]" data-respuesta-id="<?=$respuesta->respuesta_id?>" spellcheck="false" class="large-textarea respuestas_campos_xslt" placeholder="Ingrese el XSL..."><?=$respuesta->xslt?></textarea>
                        </div>
                    <?php } ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
  </fieldset>
  <ul class="form-action-buttons">
      <li class="action-buttons-primary">
          <ul>
              <li>
                <input class="btn btn-primary btn-lg" type="submit" id="guardar_operacion" value="Guardar" />
              </li>
          </ul>
      </li>
      <li class="action-buttons-second">
          <ul>
              <li class="float-left">
                <a class="btn btn-link btn-lg" href="<?=site_url('backend/ws_catalogos/'.$catalogo->id.'/operaciones')?>">Cancelar</a>
              </li>
          </ul>
      </li>
  </ul>
</form>

<label class="hidden-accessible" for="xsl_example">XSL ejemplo</label>
<textarea class="hidden" id="xsl_example">
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="/">
        <elementos>
            <xsl:for-each select="Lista/Persona">
                <elemento>
                    <item>
                        <xsl:value-of select="Nombre"/>
                    </item>
                </elemento>
            </xsl:for-each>
        </elementos>
    </xsl:template>
</xsl:stylesheet>
</textarea>
