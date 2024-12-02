$(document).ready(function () {
  /* Ocultar panel Nuevo usuario */
  $('.cardCreateUser').hide();

  /* Abrir panel Nuevo usuario */
  $('#btnNewUser').click(function (e) {
    e.preventDefault();
    $('#btnCreateUser').html('Crear Usuario');
    
    sessionStorage.removeItem('id_user');
    $('#email').prop('disabled', false);
    $('#company').prop('disabled', false);
    
    $('#formCreateUser').trigger('reset');
    
    let display = $('.cardCreateUser').css('display');
    
    if (display == 'block')
      loadTblUsers(allUsers);
    
    $('.cardCreateUser').toggle(800);
  });

  /* Agregar nuevo usuario */
  $('#btnCreateUser').click(function (e) {
    e.preventDefault();
    let id_user = sessionStorage.getItem('id_user');

    if (id_user == '' || id_user == null) {
      company = $('#company').val();
      firstname = $('#firstname').val();
      lastname = $('#lastname').val();
      email = $('#email').val(); 
      $(`#principalUser`).val() == '1' ? check = 1 : check = 0;

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
      dataUser['check'] = check;

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

    let id_user = this.id;
    sessionStorage.setItem('id_user', id_user);

    $('#firstname').val(data.firstname);
    $('#lastname').val(data.lastname);
    $('#email').val(data.email);
    $('#email').prop('disabled', true);
    $(`#company option[value=${data.id_company}]`).prop('selected', true);
    $('#company').prop('disabled', true);
    
    data.contract == '1' ? op = 1 : op = 2;

    $(`#principalUser option[value=${op}]`).prop('selected', true);

    data = allUsers.filter(item => item.id_company == data.id_company);

    loadTblUsers(data);  

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateUser = () => {
    if ($(`#principalUser`).val() == '2') { 
      toastr.error('Debe haber por lo menos un usuario principal por empresa');
      return false;
    }

    id_user = sessionStorage.getItem('id_user');
    $(`#principalUser`).val() == '1' ? check = 1 : check = 0;

    $('#company').prop('disabled', false);
    dataUser = $('#formCreateUser').serialize();
    
    dataUser = `${dataUser}&id_user=${id_user}&check=${check}`;
    
    $.post('/api/updateUser', dataUser, function (data, textStatus, jqXHR) {
      $('#email').prop('disabled', false);

      message(data);
    });
  };

  /* Eliminar usuario */
  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblUsers.fnGetData(row);

    if (data.contract == '1') { 
      toastr.error('Debe haber por lo menos un usuario principal por empresa');
      return false;
    }

    dataUser = {};
    dataUser['id_user'] = data.id_user;
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
    dataUser['exportImport'] = 1;
    dataUser['costCreateMachines'] = 1;
    dataUser['costCreateProcess'] = 1;
    dataUser['costProductMaterials'] = 1;
    dataUser['factoryLoad'] = 1;
    dataUser['externalService'] = 1;
    dataUser['payrollLoad'] = 1;
    dataUser['costUser'] = 1;
    dataUser['costBackup'] = 1;
    dataUser['quotePaymentMethod'] = 1;
    dataUser['quoteCompany'] = 1;
    dataUser['quoteContact'] = 1;
    dataUser['price'] = 1; 
    dataUser['customPrices'] = 1;
    dataUser['analysisMaterial'] = 1;
    dataUser['economyScale'] = 1;
    dataUser['saleObjectives'] = 1;
    dataUser['multiproduct'] = 1;
    dataUser['simulator'] = 1;
    dataUser['historical'] = 1;
    dataUser['generalCostReport'] = 1;
    dataUser['quote'] = 1;
    dataUser['support'] = 1;
    dataUser['expense'] = 1;
    dataUser['expenseDistribution'] = 1;
    dataUser['production'] = 1;
    dataUser['anualExpense'] = 1;
    dataUser['typeCustomPrices'] = [];
    dataUser['typeCustomPrices'].push(-1);
    dataUser['typePayroll'] = 1;
    dataUser['typeExpenses'] = 1;
    dataUser['priceObjectives'] = 1;

    return dataUser;
  };

  $(document).on('click', '.checkUser', function () { 

    let row = $(this).parent().parent()[0];
    let data = tblUsers.fnGetData(row);

    dataUser = {};
    dataUser['id_user'] = data.id_user;
    dataUser['company'] = data.id_company;

    bootbox.confirm({
      title: 'Usuario principal',
      message:
        'Está seguro de cambiar el usuario principal? Esta acción no se puede reversar.',
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
          $.post(`/api/changePrincipalUser`,dataUser,
            function (data, textStatus, jqXHR) {
              message(data);
            },
          );
        }
      },
    });
  });

  /* Mensaje de exito */
  message = (data) => {
    if (data.reload) {
      location.reload();
    }
    
    if (data.success == true) {
      $('.cardCreateUser').hide(800);
      $('#formCreateUser').trigger('reset');
      loadAllData();
      toastr.success(data.message);
      // console.log(data.pass);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  }; 
});
