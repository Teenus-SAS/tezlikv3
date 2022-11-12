$(document).ready(function () {
  /* Ocultar panel Nueva carga fabril */
  $('.cardCreateCompany').hide();

  /* Abrir panel crear carga nomina */

  $('#btnNewCompany').click(function (e) {
    e.preventDefault();

    $('.cardCreateCompany').toggle(800);
    $('#btnCreateCompany').html('Crear');

    sessionStorage.removeItem('id_company');

    $('#formCreateCompany').trigger('reset');
  });

  /* Agregar nueva carga nomina */

  $('#btnCreateCompany').click(function (e) {
    e.preventDefault();
    let idCompany = sessionStorage.getItem('id_company');

    if (idCompany == '' || idCompany == null) {
      nit = $('#nit').val();
      companyName = $('#companyName').val();
      address = $('#address').val();
      phone = $('#phone').val();
      city = $('#city').val();

      if (
        nit == '' ||
        companyName == '' ||
        address == '' ||
        phone == '' ||
        city == ''
      ) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      company = $('#formCreateCompany').serialize();

      $.post('/api/addQCompany', company, function (data, textStatus, jqXHR) {
        message(data);
      });
    } else {
      updateCompany();
    }
  });

  /* Actualizar nomina */

  $(document).on('click', '.updateCompany', function (e) {
    $('.cardCreateCompany').show(800);
    $('#btnCreateCompany').html('Actualizar');

    idCompany = this.id;
    idCompany = sessionStorage.setItem('id_company', idCompany);

    let row = $(this).parent().parent()[0];
    let data = tblCompanies.fnGetData(row);

    $('#nit').val(data.nit);
    $('#companyName').val(data.company_name);
    $('#address').val(data.address);
    $('#phone').val(data.phone);
    $('#city').val(data.city);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateCompany = () => {
    let data = $('#formCreateCompany').serialize();
    idCompany = sessionStorage.getItem('id_company');
    data = `${data}&idCompany=${idCompany}`;

    $.post('/api/updateQCompany', data, function (data, textStatus, jqXHR) {
      message(data);
    });
  };

  /* Eliminar carga nomina */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblCompanies.fnGetData(row);

    let idCompany = data.id_company;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar esta nómina? Esta acción no se puede reversar.',
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
            `/api/deleteQCompany/${idCompany}`,
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
      $('.cardCreateCompany').hide(800);
      $('#formCreateCompany').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblCompanies').DataTable().clear();
    $('#tblCompanies').DataTable().ajax.reload();
  }
});
