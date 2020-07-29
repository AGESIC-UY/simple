<h2><?= $proceso->nombre .'&nbsp; <a class="icon-share" style="font-size: 16px;" title="Exportar '.$proceso->nombre .' '.$proceso->version.'" href="'.site_url('backend/procesos/exportar/' . $proceso->id) .'"></a>'?></h2>
<form id="procArchivadoForm" method="POST" action="<?=  site_url("backend/procesos/editar")?>" class="form-inline">
    <label for="proc_arch_id">Versiones anteriores</label>
    <select id="proc_arch_id" name="proc_arch_id" class="AlignText input-large">
        <?php foreach($procesos_arch as $proceso_arch):?>
            <option value="<?=$proceso_arch['id']?>" <?=$proceso_arch['id']==$proceso->id?'selected':''?>><?=$proceso_arch['nombre'].'-'.$proceso_arch['version']?></option>
        <?php endforeach ?>
    </select>
    <hr>
</form>
<h3><?= 'Versión del proceso: ' . $proceso->version ?> </h3>