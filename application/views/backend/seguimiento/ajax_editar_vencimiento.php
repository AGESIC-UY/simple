<script>
    $(document).ready(function() {
        $(".datepicker")
                .datepicker({
            format: "dd-mm-yyyy",
            weekStart: 1,
            autoclose: true,
            language: "es"
        })
    });

</script>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h3 id="myModalLabel">Editar Fecha de Vencimiento</h3>
</div>
<form id="formEditarVencimiento" method='POST' class='ajaxForm' action="<?= site_url('backend/seguimiento/editar_vencimiento_form/' . $etapa->id) ?>">
<div class="modal-body">
        <div class='validacion'></div>
        <?php if(!$etapa->Tarea->vencimiento_a_partir_de_variable){?>
        <label>Fecha de Vencimiento</label>
        <input class='datepicker' name='vencimiento_at' type='text' value='<?= $etapa->vencimiento_at?date('d-m-Y',  strtotime($etapa->vencimiento_at)):'' ?>' placeholder='DD-MM-YYYY' />
      <?php } else{ ?>
        <h4>La configuración de la tarea no permite editar la fecha de vencimiento</h4>
        <label>
          La tarea de esta etapa fue configurada para recalcular la fecha de vencimiento mediante una fecha fija o una variable @@
        </label>
        <br>
        <br>
        <a href="<?= site_url('backend/procesos/editar/'.$etapa->Tarea->Proceso->id) ?>">Para cambiar la fecha de vencimiento por favor haga click para editar la tarea en el modelador</a>
      <?php }?>
</div>
<div class="modal-footer">
  <button type="submit" class="btn btn-primary">Guardar</button>
    <!--a href="#" onclick="javascript:$('#formEditarVencimiento').submit();return false;" class="btn btn-primary">Guardar</a-->
    <button class="btn btn-link" data-dismiss="modal">Cerrar</button>
</div>
</form>
