$(document).ready(function () {
  //   fetch(`/api/profile`)
  //     .then((response) => response.text())
  //     .then((data) => {
  //       loadProfile(data);
  //     });

  /* Cargar Perfil de usuario */
  loadProfile = (data) => {
    $('#name').html(data.firstname);
    $('#mail').html(data.email);

    // Configuración Usuario
    $('#firstname').val(data.firstname);
    $('#lastname').val(data.lastname);
    $('#email').val(data.email);
  };

  /* Guardar perfil */
  $('#btnSaveProfile').click(function (e) {
    e.preventDefault();
  });
});
