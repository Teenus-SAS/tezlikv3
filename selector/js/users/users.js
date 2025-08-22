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

      $.post('/api/users/addUser', dataUser, function (data, textStatus, jqXHR) {
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

    $.get(`/api/generalUser/generalUserAccess/${idUser}`, function (data) {
      if (data.reload) {
        location.reload();
      }

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
      costProductMaterials: data.cost_products_material,
      costProductProcess: data.cost_products_process,
      factoryLoad: data.factory_load,
      externalService: data.external_service,
      payrollLoad: data.payroll_load,
      expense: data.expense,
      expenseDistribution: data.expense_distribution,
      costUser: data.cost_user,
      quotePaymentMethod: data.quote_payment_method,
      quoteCompany: data.quote_company,
      quoteContact: data.quote_contact,
      price: data.price,
      priceUSD: data.price_usd,
      analysisMaterial: data.analysis_material,
      economyScale: data.cost_economy_scale,
      multiproduct: data.multiproduct,
      quote: data.quote,
      support: data.support,

      //Planeacion
      createMold: data.create_mold,
      planningCreateProduct: data.planning_product,
      planningCreateMaterial: data.planning_material,
      planningCreateMachine: data.planning_machine,
      planningCreateProcess: data.planning_process,
      planningProductsMaterial: data.planning_products_material,
      planningProductsProcess: data.planning_products_process,
      programsMachine: data.programs_machine,
      ciclesMachine: data.cicles_machine,
      invCategory: data.inv_category,
      sale: data.sale,
      plannigUser: data.planning_user,
      client: data.client,
      ordersType: data.orders_type,
      inventory: data.inventory,
      order: data.plan_order,
      program: data.program,
      load: data.plan_load,
      explosionOfMaterial: data.explosion_of_material,
      office: data.office,
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
      '/api/generalUser/updateUserAccess',
      dataUser,
      function (data, textStatus, jqXHR) {
        message(data);
        updateTable();
      }
    );
  };

  /* Metodo para definir checkboxes */
  setCheckBoxes = (dataUser) => {
    let i = 1;

    let access = {
      //costos
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
      priceUSD: 0,
      economyScale: 0,
      multiproduct: 0,
      analysisMaterial: 0,
      quote: 0,
      support: 0,

      //Planeacion
      createMold: 0,
      planningCreateProduct: 0,
      planningCreateMaterial: 0,
      planningCreateMachine: 0,
      planningCreateProcess: 0,
      planningProductsMaterial: 0,
      planningProductsProcess: 0,
      programsMachine: 0,
      ciclesMachine: 0,
      invCategory: 0,
      sale: 0,
      plannigUser: 0,
      client: 0,
      ordersType: 0,
      inventory: 0,
      order: 0,
      program: 0,
      load: 0,
      explosionOfMaterial: 0,
      office: 0,
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
    let row = $(this).closest('tr')[0];
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
          $.post('/api/users/deleteUser', dataUser, function (data, textStatus, jqXHR) {
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
