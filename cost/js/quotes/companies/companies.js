$(document).ready(function () {
  /* Ocultar modal crear compañia */
  $('#btnCloseQCompany').click(function (e) {
    e.preventDefault();
    $('#createQCompany').modal('hide');
  });

  /* Abrir modal crear compañia */

  $('#btnNewCompany').click(function (e) {
    e.preventDefault();

    $('#createQCompany').modal('show');
    $('#btnCreateQCompany').html('Crear');

    sessionStorage.removeItem('id_company');

    $('#formCreateQCompany').trigger('reset');
  });

  /* Agregar nueva compañia */

  $('#btnCreateQCompany').click(function (e) {
    e.preventDefault();
    let idCompany = sessionStorage.getItem('id_company');

    if (idCompany == '' || idCompany == null) {
      let nit = $('#nit').val();
      let companyName = $('#companyName').val();
      let address = $('#address').val();
      let phone = $('#phone').val();
      let city = $('#city').val();

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

      let imgCompany = $('#formFile')[0].files[0];

      let company = new FormData(formCreateQCompany);
      company.append('img', imgCompany);

      $.ajax({
        type: 'POST',
        url: '/api/companies/addQCompany',
        data: company,
        contentType: false,
        cache: false,
        processData: false,

        success: function (resp) {
          $('#createQCompany').modal('hide');
          $('#formFile').val('');
          message(resp);
          updateTable();
        },
      });
    } else {
      updateCompany();
    }
  });

  /* Actualizar compañia */

  $(document).on('click', '.updateCompany', function (e) {
    let idCompany = this.id;
    sessionStorage.setItem('id_company', idCompany);

    let row = $(this).parent().parent()[0];
    let data = tblCompanies.fnGetData(row);

    $('#nit').val(data.nit);
    $('#companyName').val(data.company_name);
    $('#address').val(data.address);
    $('#phone').val(data.phone);
    $('#city').val(data.city);
    if (data.img) avatar.src = data.img;

    $('#createQCompany').modal('show');
    $('#btnCreateQCompany').html('Actualizar');

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateCompany = () => {
    let idCompany = sessionStorage.getItem('id_company');
    let imgCompany = $('#formFile')[0].files[0];

    let company = new FormData(formCreateQCompany);
    company.append('idCompany', idCompany);
    company.append('img', imgCompany);

    $.ajax({
      type: 'POST',
      url: '/api/companies/updateQCompany',
      data: company,
      contentType: false,
      cache: false,
      processData: false,

      success: function (resp) {
        $('#createQCompany').modal('hide');
        $('#formFile').val('');
        message(resp);
        updateTable();
      },
    });
  };

  /* Eliminar carga compañia */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblCompanies.fnGetData(row);

    let idCompany = data.id_quote_company;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar esta compañia? Esta acción no se puede reversar.',
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
          $.get(`/api/companies/deleteQCompany/${idCompany}`, function (data, textStatus, jqXHR) {
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
      $('#createQCompany').modal('hide');
      $('#formCreateQCompany').trigger('reset');
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
