$(document).ready(function () {
  $('.cardAddNewFamily').hide();

  $(document).on('click', '#btnAddNewFamily', function () {
    $('#formFamily').trigger('reset');
    $('.cardTblExpensesDistribution').hide(800);
    $('.cardExpensesDistribution').hide(800);
    $('.cardTblFamilies').show(800);
    $('.cardAddNewFamily').toggle(800);

    let tables = document.getElementById('tblFamilies');

    let attr = tables;
    attr.style.width = '100%';
    attr = tables.firstElementChild;
    attr.style.width = '100%';
  });

  $('#btnSaveFamily').click(function (e) {
    e.preventDefault();

    let family = $('#family').val();

    if (family == '') {
      toastr.error('Ingrese nuevo campo');
      return false;
    }

    let data = $('#formFamily').serialize();

    $.post('/api/addFamily', data, function (data, textStatus, jqXHR) {
      message(data, 3);
    });
  });

  $(document).on('click', '.seeDetail', async function () {
    let id_family = this.id;

    let data = await searchData(
      `/api/expensesDistributionFamilies/${id_family}`
    );
    dataExpenseDistributionFamily = data;

    $('#inputsDistribution').empty();

    let form = document.getElementById('inputsDistribution');

    for (let i = 0; i < data.length; i++) {
      dataExpenseDistributionFamily[i].idExpensesDistribution =
        data[i].id_expenses_distribution;
      dataExpenseDistributionFamily[i].selectNameProduct = data[i].id_product;
      dataExpenseDistributionFamily[i].unitsSold = data[i].units_sold;

      form.insertAdjacentHTML(
        'beforeend',
        `<div class="col-12 col-lg-12 titlePayroll">
            <label for=""><b>${data[i].reference} - ${
          data[i].product
        }</b></label>
        </div>
        <div class="col-12 col-lg-4">
            <div class="form-group floating-label enable-floating-label show-label">
                <input type="text" class="form-control number inputsFamily ${[
                  i,
                ]}" name="unitsSold" id="unitsSold" value="${data[
          i
        ].units_sold.toLocaleString()}">
                <label for="unitsSold">Unidades Vendidas</label>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="form-group floating-label enable-floating-label show-label">
                <input type="text" class="form-control number inputsFamily ${[
                  i,
                ]}" name="turnover" id="turnover" value="${data[
          i
        ].turnover.toLocaleString()}">
                <label for="turnover">Volumen Ventas</label>
            </div>
        </div> 
        <div class="col-12 col-lg-4">
          <a href="javascript:;" <i id="${
            data[i].id_expense_distribution
          }" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Gasto' style="font-size: 30px;color:red" onclick="deleteExpenseDistribution(${i})"></i></a>
        </div> 
        `
      );
    }

    $('#modalExpenseDistributionByFamily').modal('show');
  });

  /* Guardar Familia */
  $(document).on('keyup', '.inputsFamily', function () {
    let value = parseFloat(strReplaceNumber(this.value));
    let key = getLastText(this.className);

    value = value * 1;

    if (isNaN(value)) {
      toastr.error('Ingrese un campo valido');
      return false;
    }

    dataExpenseDistributionFamily[key][this.id] = this.value;
  });

  $('#btnModifyDistribution').click(function (e) {
    e.preventDefault();

    $.ajax({
      type: 'POST',
      url: '/api/updateExpensesDistribution',
      data: { data: dataExpenseDistributionFamily },
      success: function (resp) {
        message(resp, 3);
      },
    });
  });

  /* Cerrar modal */

  $('#btnCloseDistribution').click(function (e) {
    e.preventDefault();

    $('#modalExpenseDistributionByFamily').modal('hide');
  });
});
