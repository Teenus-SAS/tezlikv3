$(document).ready(function () {
  $('#email').prop('disabled', true);

  /* Cargar Perfil de usuario */
  loadProfile = async () => {
    let data = await searchData('/api/user');

    $('#profileName').html(data.firstname);
    $('#idUser').val(data.id_user);
    $('#firstname').val(data.firstname);
    $('#lastname').val(data.lastname);
    $('#position').val(data.position);
    $('#email').val(data.email);
    if (data.avatar) avatar.src = data.avatar;
  };

  loadProfile();

  /* Guardar perfil */
  $('#btnSaveProfile').click(function (e) {
    e.preventDefault();

    let firstname = $('#firstname').val();
    let lastname = $('#lastname').val();
    sessionStorage.setItem('name', firstname);
    sessionStorage.setItem('lastname', lastname);

    let password = $('#password').val();
    let conPassword = $('#conPassword').val();

    if (!firstname || firstname == '' || !lastname || lastname == '') {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    if (password != conPassword) {
      toastr.error('Las contraseñas no coinciden');
      return false;
    }

    $('#email').prop('disabled', false);
    let imageProd = $('#formFile')[0].files[0];
    let imageCompany = $('#formFileC')[0].files[0];
    let dataProfile = new FormData(formSaveProfile);
    dataProfile.append('avatar', imageProd);
    dataProfile.append('logo', imageCompany);
    dataProfile.append('admin', 0);

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
      let firstname = sessionStorage.getItem('name');
      let lastname = sessionStorage.getItem('lastname');

      sessionStorage.removeItem('name');
      sessionStorage.removeItem('lastname');

      if (data.avatar) hAvatar.src = data.avatar;
      $('.userName').html(`${firstname} ${lastname}`);
      $('#email').prop('disabled', true);

      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
