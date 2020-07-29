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

<h1>Firma por lotes</h1>

<?php if (count($procesos) > 0): ?>

<input title="Búsqueda por nombre" type="text" id="searchInput" onkeyup="searchByName()" placeholder="Búsqueda por nombre...">

<table id="mainTable" class="table">
  <caption class="hide-text">Trámites disponibles</caption>
    <thead>
        <tr>
            <th><a href="<?=current_url().'?orderby=nombre&amp;direction='.($direction=='asc'?'desc':'asc')?>">Nombre</a></th>
            <th><a href="<?=current_url().'?orderby=version&amp;direction='.($direction=='asc'?'desc':'asc')?>">Versión</a></th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($procesos as $p): ?>
            <tr>
                <td class="name" data-title="Nombre">
                    <?= $p->nombre ?>
                </td>
                <td class="name" data-title="Versión">
                    <?= $p->version ?>
                </td>
                <td class="actions" data-title="Acciones">
                    <a href="<?= site_url('tramites/firma_por_lotes/'.$p->id) ?>" class="btn btn-primary"><i class="icon-white icon-th-list"></i> Ver etapas</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>
<p>No hay trámites disponibles.</p>
<?php endif; ?>
