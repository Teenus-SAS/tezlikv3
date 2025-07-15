
$('.cardExpenseRecover').hide();

$('#btnNewExpenseRecover').click(async function (e) {
  e.preventDefault();
  $('.selectNameProduct option').removeAttr('selected');
  $('.refProduct option').removeAttr('selected');
  $(`.selectNameProduct option[value='0']`).prop('selected', true);
  $(`.refProduct option[value='0']`).prop('selected', true);
  $(`#ERNameProduct`).prop('disabled', false);
  $(`#ERRefProduct`).prop('disabled', false);

  $('.cardImportExpenses').hide(800);
  $('.cardExpensesDistribution').hide(800);
  $('.cardExpenseRecover').toggle(800);
  $('#btnExpenseRecover').html('Guardar');

  sessionStorage.removeItem('id_expense_recover');

  $('#percentage').val('');
  await loadExpensesRProducts();
});

$('#btnExpenseRecover').click(function (e) {
  e.preventDefault();

  let id_expense_recover = sessionStorage.getItem('id_expense_recover');

  if (id_expense_recover == '' || !id_expense_recover) {
    checkDataExpenseRecover('/api/addExpenseRecover', id_expense_recover);
  } else checkDataExpenseRecover('/api/updateExpenseRecover', id_expense_recover);
});

/* Actualizar recuperacion gasto */
$(document).on('click', '.updateExpenseRecover', function () {
  $('.cardImportExpenses').hide(800);
  $('.cardExpensesDistribution').hide(800);
  $('.cardExpenseRecover').show(800);
  $('#btnExpenseRecover').html('Actualizar');

  let data = sessionStorage.getItem('dataExpensesRecover');

  data = JSON.parse(data);

  data = setDataRowRecover(data, this.id);

  sessionStorage.setItem('id_expense_recover', data.id_expense_recover);

  $('#ERRefProduct').empty();
  $('#ERNameProduct').empty();

  $('#ERRefProduct').append(
    `<option value = '${data.id_product}'> ${data.reference} </option>`
  );

  $('#ERNameProduct').append(
    `<option value ='${data.id_product}'> ${data.product} </option>`
  );

  $(`#ERRefProduct`).prop('disabled', true);
  $(`#ERNameProduct`).prop('disabled', true);

  $('#percentage').val(data.expense_recover);

  $('html, body').animate(
    {
      scrollTop: 0,
    },
    1000
  );
});

/* Revision Data gasto */
checkDataExpenseRecover = async (url, idExpenseRecover) => {
  let idProduct = parseInt($('#ERNameProduct').val());
  let percentage = parseFloat($('#percentage').val());

  // percentage = parseFloat(percentage.replace(',', '.'));

  let data = idProduct * percentage;

  if (isNaN(data) || data <= 0) {
    toastr.error('Ingrese todos los campos');
    return false;
  }

  if (percentage > 100) {
    toastr.error('El porcentaje de recuperación debe ser menor al 100%');
    return false;
  }

  $(`#ERRefProduct`).prop('disabled', false);
  let dataExpenseRecover = new FormData(formExpenseRecover);

  if (idExpenseRecover != null)
    dataExpenseRecover.append('idExpenseRecover', idExpenseRecover);

  let resp = await sendDataPOST(url, dataExpenseRecover);

  messageDistribution(resp, 2);
};

/* Eliminar recuperacion de gasto */
$(document).on('click', '.deleteExpenseRecover', function () {
  let data = sessionStorage.getItem('dataExpensesRecover');

  data = JSON.parse(data);

  data = setDataRowRecover(data, this.id);

  let id_expense_recover = data.id_expense_recover;

  let idProduct = data.id_product;
  let dataExpenseRecover = {};

  dataExpenseRecover['idExpenseRecover'] = id_expense_recover;
  dataExpenseRecover['idProduct'] = idProduct;

  bootbox.confirm({
    title: 'Eliminar',
    message:
      'Está seguro de eliminar este gasto? Esta acción no se puede reversar.',
    buttons: {
      confirm: {
        label: 'Si',
        className: 'btn-success',
      },
      cancel: {
        label: 'No',
        className: 'btn-danger',
      },
    },
    callback: function (result) {
      if (result == true) {
        $.post(
          '/api/deleteExpenseRecover',
          dataExpenseRecover,
          function (data, textStatus, jqXHR) {
            messageDistribution(data, 2);
          }
        );
      }
    },
  });
});

setDataRowRecover = (data, id) => {
  for (let i = 0; i < data.length; i++) {
    if (data[i].id_expense_recover == id) {
      data = data[i];
      break;
    }
  }

  return data;
};

