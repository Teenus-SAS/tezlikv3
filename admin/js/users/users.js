$(document).ready(function () {
  /* Ocultar panel Nuevo usuario */
  $('.cardCreateUser').hide();

  /* Abrir panel Nuevo usuario */
  $('#btnNewUser').click(function (e) {
    e.preventDefault();
    $('.cardCreateUser').toggle(800);
    $('#btnCreateUser').html('Crear Usuario');

    sessionStorage.removeItem('id_admin');

    $('#formCreateUser').trigger('reset');
  });

  /* Agregar nuevo usuario */
  $('#btnCreateUser').click(function (e) {
    e.preventDefault();
    let idAdmin = sessionStorage.getItem('id_admin');

    if (idAdmin == '' || idAdmin == null) {
      firstname = $('#firstname').val();
      lastname = $('#lastname').val();
      email = $('#email').val();
      password = $('#password').val();

      if (
        firstname == '' ||
        firstname == null ||
        lastname == '' ||
        lastname == null ||
        email == '' ||
        email == null ||
        password == '' ||
        password == null
      ) {
        toastr.error('Ingrese nombre, apellido y/o email');
        return false;
      }

      dataUser = $('#formCreateUser').serialize();

      $.post('/api/addUserAdmin', dataUser, function (data, textStatus, jqXHR) {
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

    let idAdmin = this.id;
    sessionStorage.setItem('id_admin', idAdmin);

    $('#firstname').val(data.firstname);
    $('#lastname').val(data.lastname);
    $('#email').val(data.email);
    $('#password').val(data.password);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateUser = () => {
    idAdmin = sessionStorage.getItem('id_admin');

    dataUser = $('#formCreateUser').serialize();

    dataUser = `${dataUser}&idAdmin=${idAdmin}`;

    $.post(
      '/api/updateUserAdmin',
      dataUser,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  };

  /* Eliminar usuario */
  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblUsers.fnGetData(row);

    data = {};
    data['idAdmin'] = data.id_admin;
    data['email'] = data.email;

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
            '/api/deleteUserAdmin',
            data,
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
      $('.cardCreateUser').hide(800);
      $('#formCreateUser').trigger('reset');
      updateTable();
      toastr.success(data.message);
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
