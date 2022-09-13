$(document).ready(function () {
  // Ocultar card productos en proceso
  $('.cardAddProductInProccess').hide();

  // Mostrar lista de productos en proceso
  $.ajax({
    type: 'GET',
    url: '/api/productsInProcess',
    success: function (r) {
      let $select = $(`#product`);
      $select.empty();
      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_product}> ${value.product} </option>`
        );
      });
    },
  });

  // Mostrar card productos en proceso
  $('#btnCreateProductInProcess').click(function (e) {
    e.preventDefault();

    $('.cardCreateRawMaterials').hide(800);
    $('.cardImportProductsMaterials').hide(800);
    $('.cardAddMaterials').hide(800);
    $('.cardAddProductInProccess').toggle(800);
    $('#btnAddProductInProccess').html('Asignar');

    sessionStorage.removeItem('id_product_in_process');

    $('#formAddProductInProccess').trigger('reset');
  });

  // Guardar Productos en proceso
  $('#btnAddProductInProccess').click(function (e) {
    e.preventDefault();

    idProduct = $('#product').val();

    if (!idProduct || idProduct == 0) {
      toastr.error('Seleccione producto');
      return false;
    }

    productInProcess = $('#formAddProductInProccess').serialize();

    $.post(
      '/api/addPlanProductInProcess',
      productInProcess,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  });

  /* Mensaje de exito */
  message = (data) => {
    if (data.success == true) {
      $('.cardAddProductInProccess').hide(800);
      $('#formAddProductInProccess').trigger('reset');
      // updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
