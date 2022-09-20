$(document).ready(function () {
  // Abrir modal crear pedidos
  $('#btnNewOrder').click(function (e) {
    e.preventDefault();

    $('#createOrder').modal('show');
    $('#btnCreatePlanMachine').html('Crear');

    sessionStorage.removeItem('id_order');

    $('#formCreateOrder').trigger('reset');
  });

  // Ocultar modal Pedidos
  $('#btnCloseOrder').click(function (e) {
    e.preventDefault();

    $('#createOrder').modal('hide');
  });
});
