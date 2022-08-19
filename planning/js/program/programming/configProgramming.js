$(document).ready(function () {
  let data = [];

  $(document).on('change', '.programmingSelect', function (e) {
    e.preventDefault();
    debugger;

    if (!data.idMachine && !data.idOrder && !data.idProduct) {
      id = this.id;

      if (id.includes('machines')) {
        id_machine = $('#idMachine').val();
        loadProductsAndOrders(id_machine);
      }
      if (id.includes('orders')) {
        id_order = $('#order').val();
        loadProducts(id_order);
      }
      if (id.includes('products')) {
        id_product = $('#selectNameProduct').val();
        loadMachinesAndOrders(id_product);
      }
    }
  });

  /* Cargar Pedidos y Productos */
  loadProductsAndOrders = (id_machine) => {
    debugger;
    data['idMachine'] = id_machine;
    // data.append('idMachine', id_machine);
    $.ajax({
      type: 'POST',
      url: '/api/programming',
      data: data,
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

  /* Cargar Maquinas y Pedidos */
  loadMachinesAndOrders = (id_product) => {
    data['idProduct'] = id_product;
    $.ajax({
      type: 'POST',
      url: '/api/programming',
      data: data,
      success: function (r) {
        let $select = $(`#idMachine`);
        $select.empty();

        $select.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r, function (i, value) {
          $select.append(
            `<option value = ${value.id_machine}> ${value.machine} </option>`
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

  /* Cargar Productos y Maquinas */
  loadProducts = (id_order) => {
    data['idOrder'] = id_order;
    $.ajax({
      type: 'POST',
      url: '/api/programming',
      data: data,
      success: function (r) {
        let $select = $(`#idMachine`);
        $select.empty();

        $select.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r, function (i, value) {
          $select.append(
            `<option value = ${value.id_machine}> ${value.machine} </option>`
          );
        });

        let $select1 = $(`#selectNameProduct`);
        $select1.empty();

        $select1.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r, function (i, value) {
          $select1.append(
            `<option value = ${value.id_product}> ${value.product} </option>`
          );
        });
      },
    });
  };
});
