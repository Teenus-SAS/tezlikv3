$(document).ready(function () {
  /* Ocultar panel Nuevo usuario */
  $('.cardCreateUser').hide();

  /* Abrir panel Nuevo usuario */
  $('#btnNewUser').click(function (e) {
    e.preventDefault();
    $('.cardCreateUser').toggle(800);
    $('#btnCreateUser').html('Crear Usuario');

    sessionStorage.removeItem('id_user');
    $('#email').prop('disabled', false);
    $('#company').prop('disabled', false);

    $('#formCreateUser').trigger('reset');
  });

  /* Agregar nuevo usuario */
  $('#btnCreateUser').click(function (e) {
    e.preventDefault();
    let idUser = sessionStorage.getItem('id_user');

    if (idUser == '' || idUser == null) {
      company = $('#company').val();
      firstname = $('#firstname').val();
      lastname = $('#lastname').val();
      email = $('#email').val();

      if (
        firstname == '' ||
        firstname == null ||
        lastname == '' ||
        lastname == null ||
        email == '' ||
        email == null ||
        company == '' ||
        company == null
      ) {
        toastr.error('Ingrese nombre, apellido y/o email');
        return false;
      }

      let dataUser = {};
      dataUser['nameUser'] = firstname;
      dataUser['lastnameUser'] = lastname;
      dataUser['emailUser'] = email;
      dataUser['company'] = company;

      dataUser = setDataUserAccess(dataUser);

      $.post('/api/addUser', dataUser, function (data, textStatus, jqXHR) {
        message(data);
      });
    } else {
      updateUser();
    }
  });

  /* Actualizar User */
  $(document).on('click', '.updateUser', function (e) {
    $('.cardCreateUser').show(800);
    $('#btnCreateUser').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblUsers.fnGetData(row);

    let idUser = this.id;
    sessionStorage.setItem('id_user', idUser);

    $('#firstname').val(data.firstname);
    $('#lastname').val(data.lastname);
    $('#email').val(data.email);
    $('#email').prop('disabled', true);
    $(`#company option[value=${data.id_company}]`).prop('selected', true);
    $('#company').prop('disabled', true);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateUser = () => {
    idUser = sessionStorage.getItem('id_user');

    dataUser = $('#formCreateUser').serialize();

    dataUser = `${dataUser}&idUser=${idUser}`;

    $.post('/api/updateUser', dataUser, function (data, textStatus, jqXHR) {
      $('#email').prop('disabled', false);
      $('#company').prop('disabled', false);

      message(data);
    });
  };

  /* Eliminar usuario */
  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblUsers.fnGetData(row);

    dataUser = {};
    dataUser['idUser'] = data.id_user;
    dataUser['email'] = data.email;

    dataUser = setDataUserAccess(dataUser);

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este Usuario? Esta acción no se puede reversar.',
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
          $.post(
            '/api/deleteUser',
            dataUser,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  setDataUserAccess = (dataUser) => {
    dataUser['costCreateProducts'] = 1;
    dataUser['costCreateMaterials'] = 1;
    dataUser['costCreateMachines'] = 1;
    dataUser['costCreateProcess'] = 1;
    dataUser['costProductMaterials'] = 1;
    dataUser['costProductProcess'] = 1;
    dataUser['factoryLoad'] = 1;
    dataUser['externalService'] = 1;
    dataUser['payrollLoad'] = 1;
    dataUser['expense'] = 1;
    dataUser['expenseDistribution'] = 1;
    dataUser['costUser'] = 1;
    dataUser['costBackup'] = 1;
    dataUser['quotePaymentMethod'] = 1;
    dataUser['quoteCompany'] = 1;
    dataUser['quoteContact'] = 1;
    dataUser['price'] = 1;
    dataUser['priceUSD'] = 1;
    dataUser['customPrices'][0] = -1;
    dataUser['analysisMaterial'] = 1;
    dataUser['simulator'] = 1;
    dataUser['economyScale'] = 1;
    dataUser['multiproduct'] = 1;
    dataUser['quote'] = 1;
    dataUser['support'] = 1;

    return dataUser;
  };

  /* Mensaje de exito */
  message = (data) => {
    if (data.success == true) {
      $('.cardCreateUser').hide(800);
      $('#formCreateUser').trigger('reset');
      updateTable();
      toastr.success(data.message);
      // console.log(data.pass);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */
  function updateTable() {
    $('#tblUsers').DataTable().clear();
    $('#tblUsers').DataTable().ajax.reload();
  }
});
