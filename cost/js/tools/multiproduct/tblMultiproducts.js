$(document).ready(function () {
  $('#btnShowTbl').click(function (e) {
    e.preventDefault();

    $('.cardImportMultiproducts').hide(800);
    $('.cardGraphicMultiproducts').hide(800);
    $('.cardTblBreakeven').show(800);
    $('.cardTblMultiproducts').show(800);
  });

  loadTblMultiproducts = async () => {
    if ($.fn.dataTable.isDataTable('#tblMultiproducts')) {
      $('#tblMultiproducts').DataTable().destroy();
    }

    $('#tblMultiproductsBody').empty();
    data = await searchData('/api/multiproducts');

    multiproducts = data.multiproducts;

    let tblMultiproductsBody = document.getElementById('tblMultiproductsBody');

    sumTotalCostFixed = 0;

    for (let i = 0; i < multiproducts.length; i++) {
      sumTotalCostFixed = multiproducts[i].cost_fixed;

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
          <td id="part-${i}" class="row-${i} general"></td>
          <td id="cont-${i}">$ ${marginContribution.toLocaleString('es-CO', {
          maximumFractionDigits: 0,
        })}</td>
          <td id="unitTo-${i}" class="row-${i} general"></td>
        </tr>`
      );
    }

    expenseAsignation = multiproducts[0].expense;

    if (expenseAsignation == 0) $('.cardExpenseAssignation').show(800);

    existingMultiproducts = data.existingMultiproducts;
    repeat = false;
    $.each(existingMultiproducts, function (e, value) {
      if (expenseAsignation == 0) expenseAsignation = value.expense;

      for (i = 0; i < multiproducts.length; i++) {
        if (multiproducts[i].id_product == value.id_product) {
          repeat = true;

          // if (value.units_sold > 0) {
          $(`#soldUnit-${i}`).val(value.units_sold);
          row = i;
          multiproducts[i].soldUnit = value.units_sold;
          // }
        }
      }
    });

    if (repeat == true) $(`#soldUnit-${row}`).click();

    if (expenseAsignation > 0)
      $('#expenseAssignation').val(
        expenseAsignation.toLocaleString('es-CO', {
          maximumFractionDigits: 0,
        })
      );

    $('#expenses').html(
      `$ ${(expenseAsignation + sumTotalCostFixed).toLocaleString('es-CO', {
        maximumFractionDigits: 0,
      })}`
    );

    $('#tblMultiproducts').dataTable({
      destroy: true,
      pageLength: 50,
      autoWidth: true,
      // language: {
      //   url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      // },
      dom: '<"datatable-error-console">frtip', 
      fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
        if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
          console.error(oSettings.json.error);
        }
      },
    });
  };

  loadTblMultiproducts();
});
