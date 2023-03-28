$(document).ready(function () {
  let unitsmanufacturated;

  loadTableRawMaterialsAnalisys = async (data) => {
    $('#unitsmanufacturated').val('');
    $('#unitsmanufacturated').click();

    $('.colMaterials').empty();

    if (data.length == 0) {
      $('.colMaterials').append(`
                    <tr class="col">
                        <th class="text-center" colspan="9">Ningún dato disponible en esta tabla =(</th>
                    </tr>
                  `);
      return false;
    } else {
      $('.empty').hide();
      $('.colMaterials').empty();

      for (i = 0; i < data.length; i++) {
        //<th id="reference-${i + 1}">${r[i].reference}</th>
        $('.colMaterials').append(
          `<tr class="col${i + 1} text-center" id="col${i + 1}">
                          <th scope="row">${i + 1}</th>
                          <th scope="row">${data[i].participation.toFixed(
                            1
                          )}%</th>
                          <th id="rawMaterial-${i + 1}">${data[i].material}</th>
                          <th id="quantity-${i + 1}">${data[i].quantity}</th>
                          <th id="currentPrice-${i + 1}">$ ${data[
            i
          ].cost_product_material.toLocaleString('es-CO', {
            maximumFractionDigits: 0,
          })}</th>
                          <th><input class="form-control numberCalc negotiatePrice text-center" type="text" id="${
                            i + 1
                          }"></th>
                          <th id="percentage-${i + 1}"></th>
                          <th id="unityCost-${i + 1}">$ ${data[
            i
          ].unityCost.toLocaleString('es-CO', {
            maximumFractionDigits: 0,
          })}</th>
                          <th id="totalCost-${i + 1}"></th>
                          <th id="projectedCost-${i + 1}"></th>
                        </tr>`
        );
      }
    }
    count = i;

    for (let i = 1; i < count + 1; i++) $(`#${i}`).prop('readonly', 'readonly');
  };

  $(document).on('click', '.negotiatePrice', function (e) {
    e.preventDefault();

    unitsmanufacturated = $('#unitsmanufacturated').val();
    // Eliminar decimales
    unitsmanufacturated = strReplaceNumber(unitsmanufacturated);
    unitsmanufacturated = parseFloat(unitsmanufacturated);

    if (!unitsmanufacturated) {
      toastr.error('Ingrese las unidades a fabricar');
      return false;
    } else $(`.negotiatePrice`).removeAttr('readonly');
  });
});
