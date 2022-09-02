$(document).ready(function () {
  fetch(`/api/user`)
    .then((response) => response.text())
    .then((data) => {
      data = JSON.parse(data);
      loadProfile(data);
    });

  /* Cargar Perfil de usuario */
  loadProfile = (data) => {
    $('#idUser').val(data.id_user);
    $('#firstname').val(data.firstname);
    $('#lastname').val(data.lastname);
    $('#position').val(data.position);
    $('#email').val(data.email);
    if (data.avatar) avatar.src = data.avatar;
  };

  /* Cargar foto de perfil */
  $('#formFile').change(function (e) {
    e.preventDefault();
    avatar.src = URL.createObjectURL(event.target.files[0]);
  });

  /* Limpiar imagen */
  $('#clearImg').click(function (e) {
    e.preventDefault();
    document.getElementById('formFile').value = null;
    avatar.src = '';
  });

  /* Guardar perfil */
  $('#btnSaveProfile').click(function (e) {
    e.preventDefault();

    firstname = $('#firstname').val();
    lastname = $('#lastname').val();
    // position = $('#position').val();
    // email = $('#email').val();

    if (!firstname || firstname == '' || !lastname || lastname == '') {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    let imageProd = $('#formFile')[0].files[0];
    dataProfile = new FormData(formSaveProfile);
    dataProfile.append('avatar', imageProd);

    $.ajax({
      type: 'POST',
      url: '/api/updateProfile',
      data: dataProfile,
      contentType: false,
      cache: false,
      processData: false,
      success: function (resp) {
        message(resp);
      },
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
