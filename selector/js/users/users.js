$(document).ready(function () {
  /* Ocultar Accesos usuario */

  $('.cardAccessCost').hide();
  $('.separator').hide();
  $('.cardAccessPlanning').hide();

  // Ocultar Modal Nuevo usuario
  $('#btnCloseUser').click(function (e) {
    e.preventDefault();
    $('#createUserAccess').modal('hide');
  });

  /* Abrir panel Nuevo usuario */

  $('#btnNewUser').click(function (e) {
    e.preventDefault();
    $('.cardAccessCost').hide();
    $('.separator').hide();
    $('.cardAccessPlanning').hide();
    $('#createUserAccess').modal('show');
    $('#btnCreateUserAndAccess').html('Crear Usuario y Accesos');

    sessionStorage.removeItem('id_user');

    $('#nameUser').prop('disabled', false);
    $('#lastnameUser').prop('disabled', false);
    $('#emailUser').prop('disabled', false);

    $('#formCreateUser').trigger('reset');
  });

  /* Accesos de usuario*/
  $('.switch').change(function (e) {
    e.preventDefault();
    if (
      $('#switchCost').is(':checked') &&
      $('#switchPlanning').is(':checked')
    ) {
      $('.cardAccessCost').show(800);
      $('.separator').show(800);
      $('.cardAccessPlanning').show(800);
    } else if ($('#switchCost').is(':checked')) {
      $('.cardAccessCost').show(800);
    } else if ($('#switchPlanning').is(':checked')) {
      $('.cardAccessPlanning').show(800);
    }

    if (!$('#switchCost').is(':checked')) {
      $('.separator').hide();
      $('.cardAccessCost').hide(800);
    }

    if (!$('#switchPlanning').is(':checked')) {
      $('.separator').hide();
      $('.cardAccessPlanning').hide(800);
    }
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
    let idUser = this.id;
    sessionStorage.setItem('id_user', idUser);

    $.get(`/api/generalUserAccess/${idUser}`, function (data) {
      getUserAccess(data);
      $('#createUserAccess').modal('show');
      $('#btnCreateUserAndAccess').html('Actualizar Accesos');

      $('#nameUser').prop('disabled', true);
      $('#lastnameUser').prop('disabled', true);
      $('#emailUser').prop('disabled', true);
    });
  });

  // Obtener datos accesos de usuario
  getUserAccess = (data) => {
    // Datos usuario
    $('#nameUser').val(data.firstname);
    $('#lastnameUser').val(data.lastname);
    $('#emailUser').val(data.email);

    // Tabla costos
    if (data.cost_product == 1) $('#checkbox-1').prop('checked', true);
    else $('#checkbox-1').prop('checked', false);
    if (data.cost_material == 1) $('#checkbox-2').prop('checked', true);
    else $('#checkbox-2').prop('checked', false);
    if (data.cost_machine == 1) $('#checkbox-3').prop('checked', true);
    else $('#checkbox-3').prop('checked', false);
    if (data.cost_process == 1) $('#checkbox-4').prop('checked', true);
    else $('#checkbox-4').prop('checked', false);
    if (data.cost_products_material == 1)
      $('#checkbox-5').prop('checked', true);
    else $('#checkbox-5').prop('checked', false);
    if (data.cost_products_process == 1) $('#checkbox-6').prop('checked', true);
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
    if (data.cost_user == 1) $('#checkbox-12').prop('checked', true);
    else $('#checkbox-12').prop('checked', false);
    if (data.price == 1) $('#checkbox-13').prop('checked', true);
    else $('#checkbox-13').prop('checked', false);
    if (data.analysis_material == 1) $('#checkbox-14').prop('checked', true);
    else $('#checkbox-14').prop('checked', false);
    if (data.tool == 1) $('#checkbox-15').prop('checked', true);
    else $('#checkbox-15').prop('checked', false);

    // Tabla accesos
    if (data.create_mold == 1) $('#checkbox-16').prop('checked', true);
    else $('#checkbox-16').prop('checked', false);
    if (data.planning_product == 1) $('#checkbox-17').prop('checked', true);
    else $('#checkbox-17').prop('checked', false);
    if (data.planning_material == 1) $('#checkbox-18').prop('checked', true);
    else $('#checkbox-18').prop('checked', false);
    if (data.planning_machine == 1) $('#checkbox-19').prop('checked', true);
    else $('#checkbox-19').prop('checked', false);
    if (data.planning_process == 1) $('#checkbox-20').prop('checked', true);
    else $('#checkbox-20').prop('checked', false);
    if (data.planning_products_material == 1)
      $('#checkbox-21').prop('checked', true);
    else $('#checkbox-21').prop('checked', false);
    if (data.planning_products_process == 1)
      $('#checkbox-22').prop('checked', true);
    else $('#checkbox-22').prop('checked', false);
    if (data.programs_machine == 1) $('#checkbox-23').prop('checked', true);
    else $('#checkbox-23').prop('checked', false);
    if (data.cicles_machine == 1) $('#checkbox-24').prop('checked', true);
    else $('#checkbox-24').prop('checked', false);
    if (data.inv_category == 1) $('#checkbox-25').prop('checked', true);
    else $('#checkbox-25').prop('checked', false);
    if (data.sale == 1) $('#checkbox-26').prop('checked', true);
    else $('#checkbox-26').prop('checked', false);
    if (data.planning_user == 1) $('#checkbox-27').prop('checked', true);
    else $('#checkbox-27').prop('checked', false);
    if (data.inventory == 1) $('#checkbox-28').prop('checked', true);
    else $('#checkbox-28').prop('checked', false);
    if (data.plan_order == 1) $('#checkbox-29').prop('checked', true);
    else $('#checkbox-29').prop('checked', false);
    if (data.programming == 1) $('#checkbox-30').prop('checked', true);
    else $('#checkbox-30').prop('checked', false);
    if (data.plan_load == 1) $('#checkbox-31').prop('checked', true);
    else $('#checkbox-31').prop('checked', false);
    if (data.explosion_of_material == 1)
      $('#checkbox-32').prop('checked', true);
    else $('#checkbox-32').prop('checked', false);
    if (data.office == 1) $('#checkbox-33').prop('checked', true);
    else $('#checkbox-33').prop('checked', false);
  };

  updateUserAccess = () => {
    idUser = sessionStorage.getItem('id_user');

    dataUser = {};
    dataUser['idUser'] = idUser;
    dataUser['nameUser'] = $('#nameUser').val();
    dataUser['lastnameUser'] = $('#lastnameUser').val();
    dataUser['emailUser'] = $('#emailUser').val();

    dataUser = setCheckBoxes(dataUser);

    $.post(
      '/api/updatePlanningUserAccess',
      dataUser,
      function (data, textStatus, jqXHR) {
        message(data);
        updateTable();
      }
    );
  };

  /* Metodo para definir checkboxes */
  setCheckBoxes = (dataUser) => {
    for (let i = 1; i <= 33; i++) {
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
        if (i == 16) dataUser['createMold'] = '1';
        if (i == 17) dataUser['planningCreateProduct'] = '1';
        if (i == 18) dataUser['planningCreateMaterial'] = '1';
        if (i == 19) dataUser['planningCreateMachine'] = '1';
        if (i == 20) dataUser['planningCreateProcess'] = '1';
        if (i == 21) dataUser['planningProductsMaterial'] = '1';
        if (i == 22) dataUser['planningProductsProcess'] = '1';
        if (i == 23) dataUser['programsMachine'] = '1';
        if (i == 24) dataUser['ciclesMachine'] = '1';
        if (i == 25) dataUser['invCategory'] = '1';
        if (i == 26) dataUser['sale'] = '1';
        if (i == 27) dataUser['plannigUser'] = '1';
        if (i == 28) dataUser['inventory'] = '1';
        if (i == 29) dataUser['order'] = '1';
        if (i == 30) dataUser['programming'] = '1';
        if (i == 31) dataUser['load'] = '1';
        if (i == 32) dataUser['explosionOfMaterial'] = '1';
        if (i == 33) dataUser['office'] = '1';
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
        if (i == 16) dataUser['createMold'] = '0';
        if (i == 17) dataUser['planningCreateProduct'] = '0';
        if (i == 18) dataUser['planningCreateMaterial'] = '0';
        if (i == 19) dataUser['planningCreateMachine'] = '0';
        if (i == 20) dataUser['planningCreateProcess'] = '0';
        if (i == 21) dataUser['planningProductsMaterial'] = '0';
        if (i == 22) dataUser['planningProductsProcess'] = '0';
        if (i == 23) dataUser['programsMachine'] = '0';
        if (i == 24) dataUser['ciclesMachine'] = '0';
        if (i == 25) dataUser['invCategory'] = '0';
        if (i == 26) dataUser['sale'] = '0';
        if (i == 27) dataUser['plannigUser'] = '0';
        if (i == 28) dataUser['inventory'] = '0';
        if (i == 29) dataUser['order'] = '0';
        if (i == 30) dataUser['programming'] = '0';
        if (i == 31) dataUser['load'] = '0';
        if (i == 32) dataUser['explosionOfMaterial'] = '0';
        if (i == 33) dataUser['office'] = '0';
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
    let programsMachine = data.programs_machine;
    dataUser = {};
    // dataUser['idUser'] = idUser;
    dataUser['idUser'] = idUser;
    dataUser['programsMachine'] = programsMachine;

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
      $('.cardAccessCost').hide();
      $('.separator').hide();
      $('.cardAccessPlanning').hide();
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
