$(document).ready(function () {
  $('.cardTypePayroll').hide();
  $('.cardTypePrices').hide();

  // Ocultar Modal Nuevo usuario
  $('#btnCloseUser').click(function (e) {
    e.preventDefault();
    $('#createUserAccess').modal('hide');
  });

  /* Abrir panel Nuevo usuario */

  $('#btnNewUser').click(function (e) {
    e.preventDefault();
    $('#createUserAccess').modal('show');
    $('#btnCreateUserAndAccess').html('Crear Usuario y Accesos');

    sessionStorage.removeItem('id_user');

    $('#nameUser').prop('disabled', false);
    $('#lastnameUser').prop('disabled', false);
    $('#emailUser').prop('disabled', false);

    $('#formCreateUser').trigger('reset');
  });

  $(document).on('click', '.typeCheckbox', function () {
    if (this.id == 'checkbox-9')
      $('.cardTypePayroll').toggle(800);
    if (this.id == 'checkbox-19')
      $('.cardTypePrices').toggle(800);
  });

  /* Agregar nuevo usuario */

  $('#btnCreateUserAndAccess').click(function (e) {
    e.preventDefault();
    let idUser = sessionStorage.getItem('id_user');

    if (idUser == '' || idUser == null) {
      let nameUser = $('#nameUser').val();
      let lastnameUser = $('#lastnameUser').val();
      let emailUser = $('#emailUser').val();

      if (
        nameUser == '' ||
        nameUser == null ||
        lastnameUser == '' ||
        lastnameUser == null ||
        emailUser == '' ||
        emailUser == null
      ) {
        toastr.error('Ingrese nombre, apellido y/o email');
        return false;
      }

      /* Validar que al menos un acceso sea otorgado */
      if ($('input[type=checkbox]:checked').length === 0) {
        toastr.error('Debe seleccionar al menos un acceso');
        return false;
      }

      
      /* Obtener los checkbox seleccionados */
      
      let typeCustomPrices = 0;
      if ($(`#checkbox-19`).is(':checked')) {
        typeCustomPrices = $('#pricesList').val();

        if (typeCustomPrices == 0 || !typeCustomPrices) {
          toastr.error('Debe seleccionar tipo de precio');
          return false;
        } 
      }

      let typePayroll = 0;

      if ($(`#checkbox-9`).is(':checked')) {
        typePayroll = $('#typePayroll').val();

        if (typePayroll == 0 || !typePayroll) {
          toastr.error('Debe seleccionar tipo de nomina');
          return false;
        }  
      }

      let dataUser = {};
      dataUser['nameUser'] = nameUser;
      dataUser['lastnameUser'] = lastnameUser;
      dataUser['emailUser'] = emailUser;
      dataUser['typePayroll'] = typePayroll;
      dataUser['typeCustomPrices'] = typeCustomPrices;

      dataUser = setCheckBoxes(dataUser);

      $.post('/api/addUser', dataUser, function (data, textStatus, jqXHR) {
        message(data, null);
      });
    } else {
      updateUserAccess();
    }
  });

  /* Actualizar User */

  $(document).on('click', '.updateUser', function (e) {
    $('#createUserAccess').modal('show');
    $('#btnCreateUserAndAccess').html('Actualizar Accesos');

    $('#nameUser').prop('disabled', true);
    $('#lastnameUser').prop('disabled', true);
    $('#emailUser').prop('disabled', true);

    let row = $(this).parent().parent()[0];
    let data = tblUsers.fnGetData(row);

    let idUser = this.id;
    sessionStorage.setItem('id_user', idUser);

    $('#nameUser').val(data.firstname);
    $('#lastnameUser').val(data.lastname);
    $('#emailUser').val(data.email);

    let access = {
      costCreateProducts: data.create_product,
      costCreateMaterials: data.create_materials,
      costCreateMachines: data.create_machines,
      costCreateProcess: data.create_process,
      productsMaterials: data.product_materials,
      productsProcess: data.product_process,
      factoryLoad: data.factory_load,
      servicesExternal: data.external_service,
      payroll: data.payroll_load,
      generalExpenses: data.expense,
      distributionExpenses: data.expense_distribution,
      users: data.user,
      backup: data.backup,
      quotePaymentMethod: data.quote_payment_method,
      quoteCompany: data.quote_company,
      quoteContact: data.quote_contact,
      prices: data.price,
      pricesUSD: data.price_usd,
      customPrices: data.custom_price,
      analysisMaterials: data.analysis_material,
      economyScale: data.economy_scale,
      multiproduct: data.multiproduct,
      simulator: data.simulator,
      quotes: data.quote,
      support: data.support,
    };

    let i = 1;

    $.each(access, (index, value) => {
      if (value === 1) {
        $(`#checkbox-${i}`).prop('checked', true);
      } else $(`#checkbox-${i}`).prop('checked', false);
      i++;
    });

    if ($(`#checkbox-9`).is(':checked')) $('.cardTypePayroll').show();
    if ($(`#checkbox-19`).is(':checked')) $('.cardTypePrices').show();

    
    $(`#typePayroll option[value=${data.type_payroll}]`).prop('selected', true);
    $(`#pricesList option[value=${data.type_custom_price}]`).prop('selected', true);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateUserAccess = () => {
    let idUser = sessionStorage.getItem('id_user');

    let typePayroll = 0;
    
    if ($(`#checkbox-9`).is(':checked')) {
      typePayroll = $('#typePayroll').val();
      
      if (typePayroll == 0 || !typePayroll) {
        toastr.error('Debe seleccionar tipo de nomina');
        return false;
      }

    }
    
    let typeCustomPrices = 0;
    
    if ($(`#checkbox-19`).is(':checked')) {
      typeCustomPrices = $('#pricesList').val();
      
      if (typeCustomPrices == 0 || !typeCustomPrices) {
        toastr.error('Debe seleccionar tipo de precio');
        return false;
      } 
    }

    let dataUser = {};
    dataUser['idUser'] = idUser;
    dataUser['nameUser'] = $('#nameUser').val();
    dataUser['lastnameUser'] = $('#lastnameUser').val();
    dataUser['emailUser'] = $('#emailUser').val();
    dataUser['typeCustomPrices'] = typeCustomPrices;
    dataUser['typePayroll'] = typePayroll;

    dataUser = setCheckBoxes(dataUser);

    $.post(
      '/api/updateCostUserAccess',
      dataUser,
      function (data, textStatus, jqXHR) {
        message(data, idUser);
        updateTable();
      }
    );
  };

  /* Seleccionar checkboxes */
  setCheckBoxes = (dataUser) => {
    let i = 1;

    let access = {
      costCreateProducts: 0,
      costCreateMaterials: 0,
      costCreateMachines: 0,
      costCreateProcess: 0,
      costProductMaterials: 0,
      costProductProcess: 0,
      factoryLoad: 0,
      externalService: 0,
      payrollLoad: 0,
      expense: 0,
      expenseDistribution: 0,
      costUser: 0,
      costBackup: 0,
      quotePaymentMethod: 0,
      quoteCompany: 0,
      quoteContact: 0,
      price: 0,
      priceUSD: 0,
      customPrices: 0,
      analysisMaterial: 0,
      economyScale: 0,
      multiproduct: 0,
      simulator: 0,
      quote: 0,
      support: 0,
    };

    $.each(access, (index, value) => {
      if ($(`#checkbox-${i}`).is(':checked')) dataUser[`${index}`] = 1;
      else dataUser[`${index}`] = 0;
      i++;
    });
    return dataUser;
  };

  /* Eliminar usuario */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblUsers.fnGetData(row);

    let idUser = data.id_user;
    let factoryLoad = data.factory_load;
    let dataUser = {};
    dataUser['idUser'] = idUser;
    dataUser['factoryLoad'] = factoryLoad;

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
              message(data, idUser);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  message = async (data, id_user) => {
    if (data.success == true) {
      $('#createUserAccess').modal('hide');
      $('.cardTypePayroll').hide();
      $('.cardTypePrices').hide();
      $('#formCreateUser').trigger('reset');
      updateTable();
      if (id_user == idUser)
        await loadUserAccess();
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
