
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

/* Actualizar recuperación gasto */
$(document).on('click', '.updateExpenseRecover', function () {

  //Mostrar formulario
  $('.cardExpenseRecover').show(800);

  // Obtener data
  const tblExpenses = $('#tblExpenses').DataTable();
  const row = $(this).closest("tr");
  const data = tblExpenses.row(row).data();

  handleUpdateExpenseRecovery(data);
});

$('#btnExpenseRecover').click(function (e) {
  e.preventDefault();

  const id_expense_recover = $(this).data('expense-id');

  if (id_expense_recover == '' || !id_expense_recover) {
    handleCheckDataExpenseRecover('/api/addExpenseRecover', id_expense_recover);
  } else handleCheckDataExpenseRecover('/api/updateExpenseRecover', id_expense_recover);
});

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

