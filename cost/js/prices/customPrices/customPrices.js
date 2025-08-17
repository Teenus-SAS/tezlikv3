$(document).ready(function () {
  $('#idProduct').change(async function (e) {
    e.preventDefault();
    let id = this.value;

    let data = await searchData(`/api/products/productCost/${id}`);

    data.sale == '0' ? price = parseFloat(data.price) : price = parseFloat(data.sale_price);

    $('#priceProduct').val(
      price.toLocaleString('es-CO', {
        maximumFractionDigits: 2,
      })
    );
  });

  /* Ocultar panel Nuevo Servicio */
  $('.cardCreateCustomPrices').hide();

  /* Abrir panel crear servicio  */
  $('#btnNewCustomPrice').click(async function (e) {
    e.preventDefault();

    op_price_list = false;

    $('#btnCreateCustomPrice').html('Adicionar');

    sessionStorage.removeItem('id_custom_price');

    $('#formCreateCustomPrices').trigger('reset');

    $(`#idProduct`).prop('disabled', false);

    let visible = $('.cardCreateCustomPrices').is(':visible');

    if (visible == false) await loadPriceList(1);

    $('.cardCreateCustomPrices').toggle(800);
    $('.cardImportCustom').hide(800);
    $('.cardCreateCustomPercentages').hide(800);
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
    e.preventDefault();
    $('.cardCreateCustomPercentages').hide(800);
    $('.cardImportCustom').hide(800);

    let data = combinedData[this.id];
    sessionStorage.setItem('dataCustomPrice', JSON.stringify(data));

    $(`#idProduct option[value=${data.id_product}]`).prop('selected', true);
    $(`#idProduct`).prop('disabled', true);

    $('#priceProduct').val(parseFloat(data.price_cost).toLocaleString('es-CO'));

    $('#pricesList').empty();

    $('#pricesList').append('<option disabled selected>Seleccionar</option>');

    for (let i = 0; i < data.id_price_list.length; i++) {
      $('#pricesList').append(
        `<option value = ${data.id_price_list[i]}> ${data.price_names[i]} </option>`
      );
    }

    op_price_list = true;

    $('.cardCreateCustomPrices').show(800);

    $('#btnCreateCustomPrice').html('Actualizar');

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  $('#pricesList').change(function (e) {
    e.preventDefault();

    if (op_price_list == true) {
      let data = JSON.parse(sessionStorage.getItem('dataCustomPrice'));
      $('#customPricesValue2').val('');
      let id_price_list = this.value;
      let price = 0;

      for (let i = 0; i < data.id_price_list.length; i++) {
        if (id_price_list == data.id_price_list[i]) {
          sessionStorage.setItem('id_custom_price', data.id_custom_price[i]);

          price = parseFloat(data.prices[i]);

          $('#customPricesValue2').val(price.toLocaleString('es-CO'));
          break;
        }
      }
    }
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

  /* Eliminar servicio */

  $(document).on('click', '.deleteFunction', function (e) {
    e.preventDefault();

    let data = combinedData[this.id];

    var options = ``;

    for (var i = 0; i < data.id_price_list.length; i++) {
      options += `<option value="${data.id_custom_price[i]}"> ${data.price_names[i]} </option>`;
    }

    bootbox.confirm({
      title: 'Eliminar',
      message:
        `<p>Está seguro de eliminar este precio? Esta acción no se puede reversar.</p><br>
       <label>Tipo Precio</label>
        <select class="form-control" id="selectDeleteCustomPrice">
          <option value='0' disabled selected>Seleccionar</option>
          ${options}
        </select>`,
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
          let id_custom_price = $('#selectDeleteCustomPrice').val();

          if (id_custom_price == '0' || !id_custom_price) {
            toastr.error('Seleccione precio a eliminar');
            return false;
          }

          $.get(
            `../api/deleteCustomPrice/${id_custom_price}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  });

  /* Mensaje de exito */

  message = (data) => {
    if (data.reload) {
      location.reload();
    }

    $('#fileCustom').val('');
    $('.cardLoading').remove();
    $('.cardBottons').show(400);

    if (data.success == true) {
      $('.cardCreateCustomPrices').hide(800);
      $('.cardImportCustom').hide(800);
      $('.cardCreateCustomPercentages').hide(800);
      $('#formCreateCustomPrices').trigger('reset');
      $('#formCreateCustomPercentage').trigger('reset');
      toastr.success(data.message);
      loadAllData();
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
