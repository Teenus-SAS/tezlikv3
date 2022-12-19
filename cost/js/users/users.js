$(document).ready(function () {
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
      }

      /* Obtener los checkbox seleccionados */

      let dataUser = {};
      dataUser['nameUser'] = nameUser;
      dataUser['lastnameUser'] = lastnameUser;
      dataUser['emailUser'] = emailUser;

      dataUser = setCheckBoxes(dataUser);

      $.post('/api/addUser', dataUser, function (data, textStatus, jqXHR) {
        message(data);
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
      quotePaymentMethod: data.quote_payment_method,
      quoteCompany: data.quote_company,
      quoteContact: data.quote_contact,
      prices: data.price,
      analysisMaterials: data.analysis_material,
      tools: data.tool,
      quotes: data.quote,
      economyScale: data.economy_scale,
    };

    let i = 1;

    $.each(access, (index, value) => {
      if (value === 1) {
        $(`#checkbox-${i}`).prop('checked', true);
      } else $(`#checkbox-${i}`).prop('checked', false);
      i++;
    });

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateUserAccess = () => {
    idUser = sessionStorage.getItem('id_user');

    let dataUser = {};
    dataUser['idUser'] = idUser;
    dataUser['nameUser'] = $('#nameUser').val();
    dataUser['lastnameUser'] = $('#lastnameUser').val();
    dataUser['emailUser'] = $('#emailUser').val();

    dataUser = setCheckBoxes(dataUser);

    $.post(
      '/api/updateCostUserAccess',
      dataUser,
      function (data, textStatus, jqXHR) {
        message(data);
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
      quotePaymentMethod: 0,
      quoteCompany: 0,
      quoteContact: 0,
      price: 0,
      analysisMaterial: 0,
      tool: 0,
      quote: 0,
      economyScale: 0,
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
      $('#createUserAccess').modal('hide');
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
