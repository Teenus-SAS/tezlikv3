$(document).ready(function () {
  $('#btnShowTbl').click(function (e) {
    e.preventDefault();

    $('.cardGraphicMultiproducts').hide(800);
    $('.cardTblBreakeven').show(800);
    $('.cardTblMultiproducts').show(800);
  });

  loadTblMultiproducts = async () => {
    data = await searchData('/api/multiproducts');

    let tblMultiproductsBody = document.getElementById('tblMultiproductsBody');

    expenseAsignation = data[0].expense;

    if (expenseAsignation == 0) $('.cardExpenseAssignation').show(800);
    else
      $('#expenses').html(
        `$ ${expenseAsignation.toLocaleString('es-CO', {
          maximumFractionDigits: 0,
        })}`
      );
    for (let i = 0; i < data.length; i++) {
      data[i]['soldUnit'] = 0;
      data[i]['unitsToSold'] = 0;

      let marginContribution = data[i].price - data[i].variable_cost;

      tblMultiproductsBody.insertAdjacentHTML(
        'beforeend',
        `<tr>
          <td>${data[i].product}</td>
          <td>
            <input type="number" class="form-control text-center general soldUnits" id="soldUnit-${i}">
          </td>
          <td id="price-${i}">$ ${data[i].price.toLocaleString('es-CO', {
          maximumFractionDigits: 0,
        })}</td>
          <td id="variable-${i}">$ ${data[i].variable_cost.toLocaleString(
          'es-CO',
          {
            maximumFractionDigits: 0,
          }
        )}</td>
          <td id="part-${i}" class="row-${i} general"></td>
          <td id="cont-${i}">$ ${marginContribution.toLocaleString('es-CO', {
          maximumFractionDigits: 0,
        })}</td>
          <td id="aver-${i}" class="row-${i} general"></td>
          <td id="unitTo-${i}" class="row-${i} general"></td>
        </tr>`
      );
    }

    $('#tblMultiproducts').dataTable({
      pageLength: 50,
      autoWidth: true,
    });
  };

  loadTblMultiproducts();
});
