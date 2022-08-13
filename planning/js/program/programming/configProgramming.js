$(document).ready(function () {
  $('#idMachine').change(function (e) {
    e.preventDefault();
    id_machine = this.value;

    loadProductsAndOrders(id_machine);
  });

  /* Cargar Maquinas */
  $.ajax({
    type: 'GET',
    url: '/api/machines',
    success: function (r) {
      let $select = $(`#idMachine`);
      $select.empty();
      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_machine}> ${value.machine} </option>`
        );
      });
    },
  });

  /* Cargar Pedidos y Productos */
  loadProductsAndOrders = (id_machine) => {
    $.ajax({
      url: `/api/productsByMachine/${id_machine}`,
      success: function (r) {
        let $select = $(`#selectNameProduct`);
        $select.empty();

        $select.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r, function (i, value) {
          $select.append(
            `<option value = ${value.id_product}> ${value.product} </option>`
          );
        });

        let $select1 = $(`#order`);
        $select1.empty();

        $select1.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r, function (i, value) {
          $select1.append(
            `<option value = ${value.id_order}> ${value.num_order} </option>`
          );
        });
      },
    });
  };
});
