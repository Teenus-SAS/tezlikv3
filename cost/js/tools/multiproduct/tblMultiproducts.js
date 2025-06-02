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

    // sumTotalCostFixed = 0;

    for (let i = 0; i < multiproducts.length; i++) {
      // sumTotalCostFixed = multiproducts[i].cost_fixed;

      multiproducts[i]['soldUnit'] = 0;
      multiproducts[i]['unitsToSold'] = 0;

      let marginContribution =
        multiproducts[i].price - multiproducts[i].variable_cost;

      // marginContribution < 0 ? marginContribution = 0 : marginContribution;

      tblMultiproductsBody.insertAdjacentHTML(
        'beforeend',
        `<tr>
          <td>${multiproducts[i].reference}</td>
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

    !multiproducts[0] ? expense = 0 : expense = multiproducts[0].expense;
    !multiproducts[0] ? sum_payroll = 0 : sum_payroll = multiproducts[0].sum_payroll;

    expenseAsignation = expense;
    costPayroll = sum_payroll;
    sumTotalCostFixed = expenseAsignation + costPayroll;

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
      `$ ${parseFloat(expenseAsignation + costPayroll).toLocaleString('es-CO', {
        maximumFractionDigits: 0,
      })}`
    );

    if (DatatableTblMultiproducts == 1)
      $('#tblMultiproducts').dataTable({
        destroy: true,
        pageLength: 50,
        autoWidth: true,
        // language: {
        //   url: '/assets/plugins/i18n/Spanish.json',
        // },
        dom: '<"datatable-error-console">frtip',
        headerCallback: function (thead, data, start, end, display) {
          $(thead).find("th").css({
            "background-color": "#386297",
            color: "white",
            "text-align": "center",
            "font-weight": "bold",
            padding: "10px",
            border: "1px solid #ddd",
          });
        },
        fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
          if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
            console.error(oSettings.json.error);
          }
        },
      });
  };

  loadTblMultiproducts();
});
