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

    let acces = {
      //costos
      costCreateProducts: data.cost_product,
      costCreateMaterials: data.cost_material,
      costCreateMachines: data.cost_machine,
      costCreateProcess: data.cost_process,
      costProductsMaterials: data.cost_products_material,
      costProductsProcess: data.cost_products_process,
      factoryLoad: data.factory_load,
      servicesExternal: data.external_service,
      payroll: data.payroll_load,
      generalExpenses: data.expense,
      distributionExpenses: data.expense_distribution,
      costUsers: data.cost_user,
      analysisMaterials: data.analysis_material,
      prices: data.price,
      tools: data.tool,

      //Planeacion
      invMolds: data.create_mold,
      planProducts: data.planning_product,
      planMaterials: data.planning_material,
      planMachines: data.planning_machine,
      planProcess: data.planning_process,
      planProductsMaterials: data.planning_products_material,
      planProductProcess: data.planning_products_process,
      planningMachines: data.programs_machine,
      planCiclesMachine: data.cicles_machine,
      categories: data.inv_category,
      sales: data.sale,
      planUsers: data.planning_user,
      clients: data.client,
      typeOrder: data.orders_type,
      inventories: data.inventory,
      orders: data.plan_order,
      programs: data.program,
      loads: data.plan_load,
      explosionMaterials: data.explosion_of_material,
      offices: data.office,
    };

    let i = 1;

    $.each(acces, (index, value) => {
      if (value === 1) {
        $(`#checkbox-${i}`).prop('checked', true);
      } else $(`#checkbox-${i}`).prop('checked', false);
      i++;
    });
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
    for (let i = 1; i <= 35; i++) {
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
        if (i == 28) dataUser['client'] = '1';
        if (i == 29) dataUser['ordersType'] = '1';
        if (i == 30) dataUser['inventory'] = '1';
        if (i == 31) dataUser['order'] = '1';
        if (i == 32) dataUser['program'] = '1';
        if (i == 33) dataUser['load'] = '1';
        if (i == 34) dataUser['explosionOfMaterial'] = '1';
        if (i == 35) dataUser['office'] = '1';
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
        if (i == 28) dataUser['client'] = '0';
        if (i == 29) dataUser['ordersType'] = '0';
        if (i == 30) dataUser['inventory'] = '0';
        if (i == 31) dataUser['order'] = '0';
        if (i == 32) dataUser['program'] = '0';
        if (i == 33) dataUser['load'] = '0';
        if (i == 34) dataUser['explosionOfMaterial'] = '0';
        if (i == 35) dataUser['office'] = '0';
      }
    }

    return dataUser;
  };

  /* Eliminar usuario */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblUsers.fnGetData(row);

    let idUser = data.id_user;
    let programsMachine = data.programs_machine;
    dataUser = {};
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
      // $('#formCreateAccessUser').trigger('reset')
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
