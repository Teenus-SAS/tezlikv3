$(document).ready(function () {
  $('#idMachine').change(function (e) {
    e.preventDefault();
    debugger;
    id_machine = this.value;

    loadProducts(id_machine);
  });

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

  /* Cargar Productos */
  loadProducts = (id_machine) => {
    debugger;

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
      },
    });
  };
});
