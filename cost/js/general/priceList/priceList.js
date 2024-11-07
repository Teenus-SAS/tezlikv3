$(document).ready(function () {
  /* Ocultar panel crear producto */
  $('.cardCreatePricesList').hide();

  /* Abrir panel crear producto */
  $('#btnNewPricesList').click(function (e) {
    e.preventDefault();

    $('.cardCreatePricesList').toggle(800);
    $('#btnCreatePricesList').html('Crear');

    sessionStorage.removeItem('id_price_list');

    $('#priceName').val('');
  });

  /* Crear nuevo lista de precio */

  $('#btnCreatePricesList').click(function (e) {
    e.preventDefault();

    let idPriceList = sessionStorage.getItem('id_price_list');

    if (idPriceList == '' || idPriceList == null) {
      checkDataPricesList('/api/addPriceList', idPriceList);
    } else {
      checkDataPricesList('/api/updatePriceList', idPriceList);
    }
  });

  /* Actualizar lista de precios */

  $(document).on('click', '.updatePriceList', function (e) {
    $('.cardCreatePricesList').show(800);
    $('#btnCreatePricesList').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblPricesList.fnGetData(row);

    sessionStorage.setItem('id_price_list', data.id_price_list);
    $('#priceName').val(data.price_name);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision data lista de precios */
  checkDataPricesList = async (url, idPriceList) => {
    let priceName = $('#priceName').val();

    if (priceName.trim() == '' || !priceName) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    let dataPricesList = new FormData(formCreatePricesList);

    if (idPriceList != '' || idPriceList != null)
      dataPricesList.append('idPriceList', idPriceList);

    let resp = await sendDataPOST(url, dataPricesList);

    message(resp);
  };

  /* Eliminar lista de precio */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblPricesList.fnGetData(row);

    let id_price_list = data.id_price_list;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar esta lista de precio? Esta acción no se puede reversar.',
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
            `../../api/deletePriceList/${id_price_list}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  message = (data) => {
    if (data.reload) {
      location.reload();
    }

    if (data.success == true) {
      $('.cardCreatePricesList').hide(800);
      $('#formCreatePricesList').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblPricesList').DataTable().clear();
    $('#tblPricesList').DataTable().ajax.reload();
  }
});
