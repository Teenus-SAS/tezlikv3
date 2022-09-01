$(document).ready(function () {
  //   fetch(`/api/profile`)
  //     .then((response) => response.text())
  //     .then((data) => {
  //       loadProfile(data);
  //     });

  /* Cargar Perfil de usuario */
  loadProfile = (data) => {
    $('#firstname').val(data.firstname);
    $('#lastname').val(data.lastname);
    $('#email').val(data.email);
  };

  /* Guardar perfil */
  $('#btnSaveProfile').click(function (e) {
    e.preventDefault();

    firstname = $('#firstname').val();
    lastname = $('#lastname').val();
    // position = $('#position').val();
    email = $('#email').val();
    pass = $('#pass').val();
    cel = $('#cel').val();

    if (
      !firstname ||
      firstname == '' ||
      !lastname ||
      lastname == '' ||
      !email ||
      email == '' ||
      !pass ||
      pass == '' ||
      !cel ||
      cel == ''
    ) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    data = $('#formSaveProfile').serialize();

    $.post('url', data, function (data, textStatus, jqXHR) {
      message(data);
    });
  });

  /* Cargar notificación */
  message = (data) => {
    if (data.success == true) {
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
