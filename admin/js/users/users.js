$(document).ready(function () {
  /* Ocultar panel Nuevo usuario 

  $('.cardCreateUsers').hide();
  $('.cardCreateAccessUser').hide(); */

  // Ocultar Modal Nuevo usuario
  $('#btnCloseUser').click(function (e) {
    e.preventDefault();
    $('#createUserAccess').modal('hide');
  });

  /* Abrir panel Nuevo usuario */

  $('#btnNewUser').click(function (e) {
    e.preventDefault();
    // $('.cardCreateUsers').toggle(800);
    // $('.cardCreateAccessUser').toggle(800);
    $('#createUserAccess').modal('show');
    $('#btnCreateUserAndAccess').html('Crear Usuario y Accesos');

    sessionStorage.removeItem('id_user');

    $('#nameUser').prop('disabled', false);
    $('#lastnameUser').prop('disabled', false);
    $('#emailUser').prop('disabled', false);

    $('#formCreateUser').trigger('reset');
    // $('#formCreateAccessUser').trigger('reset');
  });

  /* Agregar nuevo usuario */

  $('#btnCreateUserAndAccess').click(function (e) {
    e.preventDefault();
    let idUser = sessionStorage.getItem('id_user');

    if (idUser == '' || idUser == null) {
      nameUser = $('#nameUser').val();
      lastnameUser = $('#lastnameUser').val();
      emailUser = $('#emailUser').val();

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

      dataUser = {};
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

    let acces = {
      costCreateProducts: resp.create_product,
      costCreateMaterials: resp.create_materials,
      costCreateMachines: resp.create_machines,
      costCreateProcess: resp.create_process,
      productsMaterials: resp.product_materials,
      productsProcess: resp.product_process,
      factoryLoad: resp.factory_load,
      servicesExternal: resp.external_service,
      payroll: resp.payroll_load,
      generalExpenses: resp.expense,
      distributionExpenses: resp.expense_distribution,
      users: resp.user,
      analysisMaterials: resp.analysis_material,
      prices: resp.price,
      tools: resp.tool,
    };

    let i = 1;

    $.each(access, (index, value) => {
      if (value === 1) {
        $(`#checkbox-${i}`).prop('checked', true);
      } else $(`#checkbox-${i}`).prop('checked', false);
      i++;
    });

    /*
    if (data.create_product == 1) $('#checkbox-1').prop('checked', true);
    else $('#checkbox-1').prop('checked', false);
    if (data.create_materials == 1) $('#checkbox-2').prop('checked', true);
    else $('#checkbox-2').prop('checked', false);
    if (data.create_machines == 1) $('#checkbox-3').prop('checked', true);
    else $('#checkbox-3').prop('checked', false);
    if (data.create_process == 1) $('#checkbox-4').prop('checked', true);
    else $('#checkbox-4').prop('checked', false);
    if (data.product_materials == 1) $('#checkbox-5').prop('checked', true);
    else $('#checkbox-5').prop('checked', false);
    if (data.product_process == 1) $('#checkbox-6').prop('checked', true);
    else $('#checkbox-6').prop('checked', false);
    if (data.factory_load == 1) $('#checkbox-7').prop('checked', true);
    else $('#checkbox-7').prop('checked', false);
    if (data.external_service == 1) $('#checkbox-8').prop('checked', true);
    else $('#checkbox-8').prop('checked', false);
    if (data.payroll_load == 1) $('#checkbox-9').prop('checked', true);
    else $('#checkbox-9').prop('checked', false);
    if (data.expense == 1) $('#checkbox-10').prop('checked', true);
    else $('#checkbox-10').prop('checked', false);
    if (data.expense_distribution == 1) $('#checkbox-11').prop('checked', true);
    else $('#checkbox-11').prop('checked', false);
    if (data.user == 1) $('#checkbox-12').prop('checked', true);
    else $('#checkbox-12').prop('checked', false);
    if (data.price == 1) $('#checkbox-13').prop('checked', true);
    else $('#checkbox-13').prop('checked', false);
    if (data.analysis_material == 1) $('#checkbox-14').prop('checked', true);
    else $('#checkbox-14').prop('checked', false);
    if (data.tool == 1) $('#checkbox-15').prop('checked', true);
    else $('#checkbox-15').prop('checked', false);*/

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateUserAccess = () => {
    idUser = sessionStorage.getItem('id_user');

    dataUser = {};
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
    for (let i = 1; i <= 16; i++) {
      if ($(`#checkbox-${i}`).is(':checked')) {
        if (i == 1) dataUser['costCreateProducts'] = '1';
        if (i == 2) dataUser['costCreateMaterials'] = '1';
        if (i == 3) dataUser['costCreateMachines'] = '1';
        if (i == 4) dataUser['costCreateProcess'] = '1';
        if (i == 5) dataUser['costProductMaterials'] = '1';
        if (i == 6) dataUser['costProductProcess'] = '1';
        if (i == 7) dataUser['factoryLoad'] = '1';
        if (i == 8) dataUser['externalService'] = '1';
        if (i == 9) dataUser['payrollLoad'] = '1';
        if (i == 10) dataUser['expense'] = '1';
        if (i == 11) dataUser['expenseDistribution'] = '1';
        if (i == 12) dataUser['costUser'] = '1';
        if (i == 13) dataUser['price'] = '1';
        if (i == 14) dataUser['analysisMaterial'] = '1';
        if (i == 15) dataUser['tool'] = '1';
      } else {
        if (i == 1) dataUser['costCreateProducts'] = '0';
        if (i == 2) dataUser['costCreateMaterials'] = '0';
        if (i == 3) dataUser['costCreateMachines'] = '0';
        if (i == 4) dataUser['costCreateProcess'] = '0';
        if (i == 5) dataUser['costProductMaterials'] = '0';
        if (i == 6) dataUser['costProductProcess'] = '0';
        if (i == 7) dataUser['factoryLoad'] = '0';
        if (i == 8) dataUser['externalService'] = '0';
        if (i == 9) dataUser['payrollLoad'] = '0';
        if (i == 10) dataUser['expense'] = '0';
        if (i == 11) dataUser['expenseDistribution'] = '0';
        if (i == 12) dataUser['costUser'] = '0';
        if (i == 13) dataUser['price'] = '0';
        if (i == 14) dataUser['analysisMaterial'] = '0';
        if (i == 15) dataUser['tool'] = '0';
      }
    }
    return dataUser;
  };

  /* Eliminar usuario */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblUsers.fnGetData(row);

    // let idUser = data.id_user;
    let idUser = data.id_user;
    let factoryLoad = data.factory_load;
    dataUser = {};
    // dataUser['idUser'] = idUser;
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
      // $('#formCreateAccessUser')[0].reset();
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
