$(document).ready(function () {
  $('#btnShowTbl').click(function (e) {
    e.preventDefault();

    $('.cardGraphicMultiproducts').hide(800);
    $('.cardTblBreakeven').show(800);
    $('.cardTblMultiproducts').show(800);
  });

  loadTblMultiproducts = async () => {
    data = await searchData('/api/multiproducts');

    multiproducts = data.multiproducts;

    let tblMultiproductsBody = document.getElementById('tblMultiproductsBody');

    sumTotalCostFixed = 0;

    for (let i = 0; i < multiproducts.length; i++) {
      sumTotalCostFixed += multiproducts[i].cost_fixed;

      multiproducts[i]['soldUnit'] = 0;
      multiproducts[i]['unitsToSold'] = 0;

      let marginContribution =
        multiproducts[i].price - multiproducts[i].variable_cost;

      tblMultiproductsBody.insertAdjacentHTML(
        'beforeend',
        `<tr>
          <td>${multiproducts[i].product}</td>
          <td>
            <input type="number" class="form-control text-center general soldUnits" id="soldUnit-${i}">
          </td>
          <td id="price-${i}">$ ${multiproducts[i].price.toLocaleString(
          'es-CO',
          {
            maximumFractionDigits: 0,
          }
        )}</td>
          <td id="variable-${i}">$ ${multiproducts[
          i
        ].variable_cost.toLocaleString('es-CO', {
          maximumFractionDigits: 0,
        })}</td>
          <td id="part-${i}" class="row-${i} general"></td>
          <td id="cont-${i}">$ ${marginContribution.toLocaleString('es-CO', {
          maximumFractionDigits: 0,
        })}</td>
          <td id="aver-${i}" class="row-${i} general"></td>
          <td id="unitTo-${i}" class="row-${i} general"></td>
        </tr>`
      );
    }

    existingMultiproducts = data.existingMultiproducts;
    expenseAsignation = multiproducts[0].expense;

    repeat = false;
    $.each(existingMultiproducts, function (i, value) {
      expenseAsignation = value.expense;

      for (i = 0; i < multiproducts.length; i++) {
        if (multiproducts[i].id_product == value.id_product) {
          repeat = true;

          if (value.units_sold > 0) $(`#soldUnit-${i}`).val(value.units_sold);
        }
      }
    });

    if (repeat == true) $(`#soldUnit-${i}`).click();

    if (expenseAsignation == 0 || repeat == true) {
      $('.cardExpenseAssignation').show(800);
      $('#expenses').html(
        `$ ${(expenseAsignation + sumTotalCostFixed).toLocaleString('es-CO', {
          maximumFractionDigits: 0,
        })}`
      );
    } else
      $('#expenses').html(
        `$ ${(expenseAsignation + sumTotalCostFixed).toLocaleString('es-CO', {
          maximumFractionDigits: 0,
        })}`
      );

    $('#tblMultiproducts').dataTable({
      pageLength: 50,
      autoWidth: true,
    });
  };

  loadTblMultiproducts();
});
