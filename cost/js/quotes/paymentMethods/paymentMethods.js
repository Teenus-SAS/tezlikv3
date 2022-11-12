$(document).ready(function () {
  /* Ocultar panel crear producto */

  $('.cardCreateMethod').hide();

  /* Abrir panel crear producto */

  $('#btnNewMethod').click(function (e) {
    e.preventDefault();

    // $('.cardImportProcess').hide(800);
    $('.cardCreateMethod').toggle(800);
    $('#btnCreateMethod').html('Crear');

    sessionStorage.removeItem('id_method');

    $('#method').val('');
  });

  /* Crear nuevo metodo */

  $('#btnCreateMethod').click(function (e) {
    e.preventDefault();

    let idMethod = sessionStorage.getItem('id_method');

    if (idMethod == '' || idMethod == null) {
      method = $('#method').val();

      if (method == '' || method == 0) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      method = $('#formCreateMethod').serialize();

      $.post(
        '../../api/addPaymentMethod',
        method,
        function (data, textStatus, jqXHR) {
          message(data);
        }
      );
    } else {
      updatePaymentMethod();
    }
  });

  /* Actualizar metodos */

  $(document).on('click', '.updatePaymentMethod', function (e) {
    // $('.cardImportProcess').hide(800);
    $('.cardCreateMethod').show(800);
    $('#btnCreateMethod').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblPaymentMethods.fnGetData(row);

    sessionStorage.setItem('id_method', data.id_method);
    $('#method').val(data.method);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updatePaymentMethod = () => {
    let data = $('#formCreateMethod').serialize();
    idMethod = sessionStorage.getItem('id_method');
    data = data + '&idMethod=' + idMethod;

    $.post(
      '../../api/updatePaymentMethod',
      data,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  };

  /* Eliminar metodo */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblPaymentMethods.fnGetData(row);

    let id_method = data.id_method;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este metodo? Esta acción no se puede reversar.',
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
            `../../api/deletePaymentMethod/${id_method}`,
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
      $('.cardCreateMethod').hide(800);
      $('#formCreateMethod').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblPaymentMethods').DataTable().clear();
    $('#tblPaymentMethods').DataTable().ajax.reload();
  }
});
