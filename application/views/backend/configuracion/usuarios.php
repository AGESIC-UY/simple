<div class="row-fluid">

    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9">
        <ul class="breadcrumb">
            <li>
                <a href="<?=site_url('backend/configuracion')?>">Configuración</a> <span class="divider">/</span>
            </li>
            <li class="active">Usuarios</li>
        </ul>
        <h2>Accesos Frontend: Usuarios</h2>
        <?php $this->load->view('messages') ?>

        <div class="acciones-generales">
          <a class="btn" href="<?=site_url('backend/configuracion/usuario_editar')?>"><span class="icon-file"></span> Nuevo</a>
        </div>

        <table class="table">
          <caption class="hide-text">Usuarios</caption>
          <thead>
            <tr>
                <th>Usuario</th>
                <th>Nombres</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Pertenece a</th>
                <th>¿Fuera de oficina?</th>
                <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($usuarios as $u): ?>
            <tr>
                <td><?=$u->usuario?></td>
                <td><?=$u->nombres?></td>
                <td><?=$u->apellido_paterno?></td>
                <td><?=$u->apellido_materno?></td>
                <td>
                    <?php
                    $tmp=array();
                    foreach($u->GruposUsuarios as $g)
                        $tmp[]=$g->nombre;
                    echo implode(', ', $tmp);
                    ?>
                </td>
                <td><?=$u->vacaciones?'Si':'No'?></td>
                <td class="actions">
                    <a class="btn btn-primary" href="<?=site_url('backend/configuracion/usuario_editar/'.$u->id)?>"><span class="icon-edit icon-white"></span> Editar<span class="hidden-accessible"> <?=$u->usuario?></span></a>
                    <!--<a class="btn btn-danger delete_user" alt="<?=$u->id?>" href="<?=site_url('backend/configuracion/usuario_eliminar/'.$u->id)?>" onclick="return confirm('¿Está seguro que desea eliminar?')"><span class="icon-trash icon-white"></span> Eliminar<span class="hidden-accessible"> <?=$u->usuario?></span></a>--> 
                    <a class="btn btn-danger eliminar_usuario" alt="<?=$u->id?>" href="#" ><span class="icon-trash icon-white"></span> Eliminar<span class="hidden-accessible"> <?=$u->usuario?></span></a>
                </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
    </div>
</div>
<div id="alerta_eliminar" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Alerta</h3>
    </div>
    <div class="modal-body">
    </div>
    <div class="modal-footer">
        <a id="bt-modal-confirmar" class="btn btn-danger btn-ok">Eliminar</a>
        <a id="bt-modal-confirmar" class="btn btn-link"  data-dismiss="modal" aria-hidden="true">Cerrar</a>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $(".eliminar_usuario").live("click", function(){
           var decision = confirm('¿Está seguro que desea eliminar?');
           if (decision){
                var id = $(this).attr("alt"); 
                var url_eliminar = "<?=site_url('backend/configuracion/ajax_tramites_pendiente')?>";
                console.log(url_eliminar);
                $.ajax({
                     dataType: "json",
                     type: "POST",
                     url: url_eliminar,
                     data: "usuario_id="+id,
                     success: function(resultado) {
                       if (resultado.mensaje == "OK")
                         eliminar_usuario(resultado);
                       else 
                         alert(resultado.mensaje);

                     },
                     error: function(xhr, status, error) {
                       alert("Ocurri&otilde; un error " + status + "\nError: " + error);
                     }
               });
           }
           return false;
        });
    });
    function eliminar_usuario(resultado){
        var url = "<?=site_url('backend/configuracion/usuario_eliminar/')?>";
        url += "/" + resultado.usuario;
        if (resultado.tramites_pendiente > 0){
            
            var $modal = $("#alerta_eliminar");
            // Objects from alert modal
            var $cuerpo = $modal.find('div.modal-body');
            var $titulo = $modal.find('div.modal-header h3');
            var $btConfirmar = jQuery('#bt-modal-confirmar');
            var $btCancelar = jQuery('#bt-modal-cancelar');
            $cuerpo.html("Este usuario tiene " + resultado.tramites_pendiente + " trámite(s) asignado(s). Si desea continuar, se liberarán los trámites.");
            
            $modal.modal({
                show:true
            });
            $btConfirmar.click(function() {                
                window.location.href = url;
            });
            $btCancelar.click(function(){
                    $titulo.html('Warning');
                    $cuerpo.html('<p>Notice</p>');
                    $btConfirmar.attr('href', '#').attr('data-dismiss', 'modal');
            });
            
        } else {
            console.log(url);
            window.location.href = url;
        }
        return false;
    }
</script>
