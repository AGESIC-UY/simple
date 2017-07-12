<h2>Trámites disponibles a iniciar
  <?php if(isset($funcionario_actuando_como_ciudadano) && $funcionario_actuando_como_ciudadano):?>
    <b style="color:#337ab7"> - Ejecutando como ciudadano: <?php echo $usuario_nombres;?></b>
  <?php endif; ?>
</h2>

<?php if (count($procesos) > 0): ?>

<table id="mainTable" class="table" title="Trámites disponibles">
  <caption class="hide-text">Trámites disponibles</caption>
    <thead>
        <tr>
            <th><a href="<?=current_url().'?orderby=nombre&amp;direction='.($direction=='asc'?'desc':'asc')?>">Nombre</a></th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($procesos as $p): ?>
            <tr>
                <td class="name" data-title="Nombre">
                    <?= $p->nombre ?>
                </td>
                <td class="actions" data-title="Acciones">
                    <?php if($p->canUsuarioIniciarlo(UsuarioSesion::usuario()->id)):?>
                    <a href="<?=site_url('tramites/iniciar/'.$p->id)?>" class="btn btn-primary preventDoubleRequest"><span class="icon-play icon-white"></span> Iniciar <span class="hide-text"><?= $p->nombre ?></span></a>
                    <?php else: ?>
                        <?php if($p->getTareaInicial()->acceso_modo=='claveunica'):?>
                        <a href="<?=site_url('autenticacion/login_openid')?>?redirect=<?=site_url('tramites/iniciar/'.$p->id)?>"><img style="max-width: none;" src="<?=base_url('assets/img/claveunica-medium.png')?>" alt="ClaveUnica" /></a>
                        <?php else:?>
                        <a href="<?=site_url('autenticacion/login')?>?redirect=<?=site_url('tramites/iniciar/'.$p->id)?>" class="btn btn-primary"><i class="icon-white icon-off"></i> Autenticarse</a>
                        <?php endif ?>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>
<p>No hay trámites disponibles para ser iniciados.</p>
<?php endif; ?>
