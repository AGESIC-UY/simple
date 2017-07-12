  <h2>Trámites de Ciudadano</h2>

<form class="ajaxForm" method="post" action="<?= site_url('etapas/busqueda_ciudadano_form/') ?>">
    <fieldset>
        <div class="validacion validacion-error"></div>

          <legend>Buscar ciudadano</legend>

          <table width="100%">
            <tr>
              <td>
                <label for="nombre" class="control-label">Documento*</label>
                <input type="text" id="documento" class="filter" name="documento"/>

                <label for="nombre" class="control-label">País*  </label>
                <select class="filter" id="pais"  name="pais">
                  <option value="uy" selected>Uruguay</option>
                  <option value="ar">Argetntina</option>
                  <option value="br">Brasil</option>
                  <option value="py">Paraguay</option>
                  <option value="bo">Bolivia</option>
                  <option value="cl">Chile</option>
                  <option value="ec">Ecuador</option>
                  <option value="pe">Perú</option>
                  <option value="ve">Venezuela</option>
                </select>
            </td>
            <td>
              <label for="nombre" class="control-label">Tipo de documento*</label>
              <select class="filter"id="tipo_documento"  name="tipo_documento">
                <option value="ci" selected>CI</option>
              </select>
              <br /><br />
              <button class="btn btn-primary" type="submit">Buscar</button>
            </td>
          </tr>
        </table>

    </fieldset>
</form>


<script>
  $('#pais').change(function() {

    switch($(this).val()) {
      case 'uy':
            $('#tipo_documento').html($('<option>', {
              value: 'ci',
              text : 'CI'
            }));

          break;
      case 'ar':
          $('#tipo_documento').html($('<option>', {
            value: 'dni',
            text : 'DNI'
          }));
          break;
        case 'br':
              $('#tipo_documento').html($('<option>', {
                value: 'ric',
                text : 'RIC'
              }));
              $('#tipo_documento').append($('<option>', {
                value: 'ci',
                text : 'CI'
              }));
              $('#tipo_documento').append($('<option>', {
                value: 'cie',
                text : 'CIE'
              }));
              break;
          case 'py':
                $('#tipo_documento').html($('<option>', {
                  value: 'ci',
                  text : 'CI'
                }));

              break;
          case 'bo':
                $('#tipo_documento').html($('<option>', {
                  value: 'cin',
                  text : 'CIN'
                }));
                $('#tipo_documento').append($('<option>', {
                  value: 'cie',
                  text : 'CIE'
                }));

              break;

          case 'cl':
                $('#tipo_documento').html($('<option>', {
                  value: 'ci',
                  text : 'CI'
                }));

              break;

          case 'co':
                $('#tipo_documento').html($('<option>', {
                  value: 'cc',
                  text : 'CC'
                }));

                break;

                $('#tipo_documento').append($('<option>', {
                  value: 'ti',
                  text : 'TI'
                }));

                break;

                $('#tipo_documento').append($('<option>', {
                  value: 'ce',
                  text : 'CE'
                }));

                break;

            case 'ec':
                  $('#tipo_documento').html($('<option>', {
                    value: 'cc',
                    text : 'CC'
                  }));

                  break;

                  $('#tipo_documento').append($('<option>', {
                    value: 'cie',
                    text : 'I'
                  }));

                  break;

              case 'pe':
                    $('#tipo_documento').html($('<option>', {
                      value: 'dni',
                      text : 'DNI '
                    }));

                    break;

                    $('#tipo_documento').append($('<option>', {
                      value: 'ce',
                      text : 'ce'
                    }));

                    break;
            case 've':
                  $('#tipo_documento').html($('<option>', {
                    value: 'ci',
                    text : 'CI '
                  }));

                  break;
          }
  });
</script>
