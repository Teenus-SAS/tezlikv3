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

    /* Cargar data compañia */
    data = await searchData('/api/company');

    $('#idCompany').val(data[0].id_company);
    $('#state').val(data[0].state);
    $('#company').val(data[0].company);
    $('#nit').val(data[0].nit);
    $('#city').val(data[0].city);
    $('#country').val(data[0].country);
    $('#phone').val(data[0].telephone);
    $('#address').val(data[0].address);
    if (data[0].logo) $('#logo').prop('src', data[0].logo);
  };

  loadProfile();

  /* Guardar perfil */
  $('#btnSaveProfile').click(function (e) {
    e.preventDefault();

    let firstname = $('#firstname').val();
    let lastname = $('#lastname').val();

    let company = $('#company').val();
    let nit = $('#nit').val();
    let city = $('#city').val();
    let country = $('#country').val();
    let phone = $('#phone').val();
    let address = $('#address').val();
    let position = $('#position').val();

    let password = $('#password').val();
    let conPassword = $('#conPassword').val();

    if (
      !firstname.trim() ||
      firstname.trim() == '' ||
      !lastname.trim() ||
      lastname.trim() == '' ||
      company.trim() == '' ||
      nit.trim() == '' ||
      city.trim() == '' ||
      country.trim() == '' ||
      phone.trim() == '' ||
      address.trim() == ''||
      position.trim() == ''
    ) {
      let generalInputs = document.getElementsByClassName('general');

      for (let i = 0; i < generalInputs.length; i++) {
        if (generalInputs[i].value == '')
          generalInputs[i].style.border = '2px solid red';
      }

      toastr.error('No puede dejar espacios vacios');
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

    sessionStorage.setItem('name', firstname);
    sessionStorage.setItem('lastname', lastname);
    
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

  $('#btnShowModalContract').click(async function (e) { 
    e.preventDefault();

    let data = await searchData('/api/contracts');

    let divContract = document.getElementById('contractContent');

    divContract.insertAdjacentHTML('beforeend', data.content);

    $('#modalContract').modal('show');
  }); 

  $('#btnCloseContract').click(function (e) { 
    e.preventDefault();
    $('#modalContract').modal('hide'); 
  });

  /* Cargar notificación */
  message = (data) => {
    if (data.success == true) {
      $('.general').css('border', '');
      toastr.success(data.message);
      // avatar = sessionStorage.getItem('avatar');
      firstname = sessionStorage.getItem('name');
      lastname = sessionStorage.getItem('lastname');

      // sessionStorage.removeItem('avatar');
      sessionStorage.removeItem('name');
      sessionStorage.removeItem('lastname');

      // if (avatar) hAvatar.src = avatar;
      $('.userName').html(`${firstname} ${lastname}`);
      $('#email').prop('disabled', true);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
