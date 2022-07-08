$(document).ready(function () {
  /* Ocultar modal crear venta */
  $('#btnCloseSale').click(function (e) {
    e.preventDefault();

    $('.month').css('border-color', '');
    $('#createSale').modal('hide');
  });
  /* Abrir modal crear venta */

  $('#btnNewSale').click(function (e) {
    e.preventDefault();

    $('.cardImportSales').hide(800);
    $('#createSale').modal('show');
    $('#btnCreateSale').html('Crear');

    sessionStorage.removeItem('id_unit_sales');

    $('#formCreateSale').trigger('reset');
  });

  /* Crear nueva venta */

  $('#btnCreateSale').click(function (e) {
    e.preventDefault();

    let idSales = sessionStorage.getItem('id_unit_sales');

    if (idSales == '' || idSales == null) {
      idProduct = $('#selectNameProduct').val();
      january = $('#january').val();
      february = $('#february').val();
      march = $('#march').val();
      april = $('#april').val();
      may = $('#may').val();
      june = $('#june').val();
      july = $('#july').val();
      august = $('#august').val();
      september = $('#september').val();
      october = $('#october').val();
      november = $('#november').val();
      december = $('#december').val();

      data =
        january +
        february +
        march +
        april +
        may +
        june +
        july +
        august +
        september +
        october +
        november +
        december;

      if (!idProduct || !data || data == 0 || isNaN(data)) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      sales = $('#formCreateSale').serialize();

      $.post(
        '../../api/addUnitSales',
        sales,
        function (data, textStatus, jqXHR) {
          message(data);
        }
      );
    } else {
      updateSale();
    }
  });

  /* Actualizar venta */

  $(document).on('click', '.updateSale', function (e) {
    $('.cardImportSales').hide(800);
    $('#createSale').modal('show');
    $('#btnCreateSale').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblSales.fnGetData(row);

    sessionStorage.setItem('id_unit_sales', data.id_unit_sales);

    $(`#refProduct option[value=${data.id_product}]`).prop('selected', true);
    $(`#selectNameProduct option[value=${data.id_product}]`).prop(
      'selected',
      true
    );
    $('#january').val(data.jan);
    $('#february').val(data.feb);
    $('#march').val(data.mar);
    $('#april').val(data.apr);
    $('#may').val(data.may);
    $('#june').val(data.jun);
    $('#july').val(data.jul);
    $('#august').val(data.aug);
    $('#september').val(data.sept);
    $('#october').val(data.oct);
    $('#november').val(data.nov);
    $('#december').val(data.dece);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateSale = () => {
    let data = $('#formCreateSale').serialize();
    idSale = sessionStorage.getItem('id_unit_sales');
    data = data + '&idSale=' + idSale;

    $.post(
      '../../api/updateUnitSale',
      data,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  };

  /* Eliminar venta */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblSales.fnGetData(row);

    let id_unit_sales = data.id_unit_sales;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar esta venta? Esta acción no se puede reversar.',
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
            `../../api/deleteUnitSale/${id_unit_sales}`,
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
    if (data.success == true) {
      $('.month').css('border-color', '');
      $('#createSale').modal('hide');
      $('#formCreateSale').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblSales').DataTable().clear();
    $('#tblSales').DataTable().ajax.reload();
  }
});
