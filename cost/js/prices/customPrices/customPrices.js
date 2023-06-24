$(document).ready(function () {
  $('#idProduct').change(async function (e) {
    e.preventDefault();
    let id = this.value;

    let data = await searchData(`/api/productCost/${id}`);

    $('#priceProduct').val(
      data.price.toLocaleString('es-CO', {
        maximumFractionDigits: 2,
      })
    );
  });

  /* Ocultar panel Nuevo Servicio */
  $('.cardCreateCustomPrices').hide();

  /* Abrir panel crear servicio  */
  $('#btnNewCustomPrice').click(async function (e) {
    e.preventDefault();

    $('#btnCreateCustomPrice').html('Adicionar');

    sessionStorage.removeItem('id_custom_price');

    $('#formCreateCustomPrices').trigger('reset');
    $(`#idProduct`).prop('disabled', false);

    $('.cardCreateCustomPrices').toggle(800);
  });

  /* Adicionar nuevo precio */
  $('#btnCreateCustomPrice').click(function (e) {
    e.preventDefault();

    let idCustomPrice = sessionStorage.getItem('id_custom_price');

    if (idCustomPrice == '' || idCustomPrice == null) {
      checkDataServices('/api/addCustomPrice', idCustomPrice);
    } else {
      checkDataServices('/api/updateCustomPrice', idCustomPrice);
    }
  });

  /* Actualizar servicio */

  $(document).on('click', '.updateCustomPrice', function (e) {
    $('.cardCreateCustomPrices').show(800);
    $('#btnCreateCustomPrice').html('Actualizar');

    let data = combinedData[this.id];

    $(`#idProduct option[value=${data.id_product}]`).prop('selected', true);
    $(`#idProduct`).prop('disabled', true);

    $('#priceProduct').val(data.price_cost.toLocaleString('es-CO'));

    $('#pricesList').empty();

    $('#pricesList').append('<option disabled selected>Seleccionar</option>');

    for (let i = 0; i < data.id_price_list.length; i++) {
      $('#pricesList').append(
        `<option value = ${data.id_price_list[i]}> ${data.price_names[i]} </option>`
      );
    }

    $('#pricesList').change(function (e) {
      e.preventDefault();

      $('#customPricesValue').val('');
      let id_price_list = this.value;
      let price = 0;

      for (let i = 0; i < data.id_price_list.length; i++) {
        if (id_price_list == data.id_price_list[i]) {
          sessionStorage.setItem('id_custom_price', data.id_custom_price[i]);

          price = data.prices[i];

          $('#customPricesValue').val(price.toLocaleString('es-CO'));
          break;
        }
      }
    });

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision data servicio */
  checkDataServices = async (url, idCustomPrice) => {
    let idProduct = parseInt($('#idProduct').val());
    let pricesList = $('#pricesList').val();
    let cost = $('#customPricesValue').val();

    cost = parseFloat(strReplaceNumber(cost));

    let data = idProduct * cost * pricesList;

    if (isNaN(data) || data <= 0) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    let dataExternalService = new FormData(formCreateCustomPrices);
    dataExternalService.append('idProduct', idProduct);

    if (idCustomPrice != '' || idCustomPrice != null)
      dataExternalService.append('idCustomPrice', idCustomPrice);

    let resp = await sendDataPOST(url, dataExternalService);

    message(resp);
  };

  /* Eliminar servicio 

  deleteFunction = (idCustomPrice) => {
    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este precio? Esta acción no se puede reversar.',
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
          $.get(
            `../api/deleteCustomPrice/${idCustomPrice}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  }; */

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('.cardCreateCustomPrices').hide(800);
      $('#formCreateCustomPrices').trigger('reset');
      toastr.success(data.message);
      loadTblCustomPrices();
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
