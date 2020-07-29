<h1>Trámites disponibles a iniciar  para el ciudadano: <b class="blue"> - <?php echo $usuario_nombres;?></b></h1>
<script>
function searchByName() {
  // Declare variables
  var input, filter, table, tr, td, i;
  input = document.getElementById("searchInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("mainTable");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}
</script>
<?php if (count($procesos) > 0): ?>
<input title="Búsqueda por nombre" type="text" id="searchInput" onkeyup="searchByName()" placeholder="Búsqueda por nombre...">
<table id="mainTable" class="table" title="Trámites disponibles">
  <caption class="hide-text">Trámites disponibles</caption>
    <thead>
        <tr>
            <th><a href="<?=current_url().'?funcionario_ciudadano=1&orderby=nombre&amp;direction='.($direction=='asc'?'desc':'asc')?>">Nombre</a></th>
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
                    <?php if($p->canUsuarioIniciarlo($ciudadano->id)):?>
                    <a href="<?= site_url('tramites/iniciar_f/'.$p->id) ?>" class="btn btn-primary preventDoubleRequest"><span class="icon-play icon-white"></span> Iniciar <span class="hide-text"><?= $p->nombre ?></span></a>
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
