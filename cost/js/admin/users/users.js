$(document).ready(function () {
  typeCustomPrices = [];

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

    $('.cardTypePayroll').hide();
    $('.cardTypeExpenses').hide();
    $('.cardTypePrices').hide();
    $('.cardChkExpenses').hide();
    typeCustomPrices = [];
    $('#nameUser').prop('disabled', false);
    $('#lastnameUser').prop('disabled', false);
    $('#emailUser').prop('disabled', false);

    $('#formCreateUser').trigger('reset');
  });

  $(document).on('click', '.typeCheckbox', function () {
    if (this.id == 'checkbox-9')
      $('.cardTypePayroll').toggle(800);
    if (this.id == 'chckExpenses')
      $('.cardChkExpenses').toggle(800);
    if (this.id == 'checkbox-16')
      $('.cardTypePrices').toggle(800);
  });

  $('#selectExpenses').change(function (e) {
    e.preventDefault();

    if (this.value == '0' || this.value == '2' && (flag_expense == '1' || flag_expense == '0'))
      $('.cardTypeExpenses').show(800);
    else
      $('.cardTypeExpenses').hide(800);
  });

  $(document).on('click', '.typePriceList', function () {
    $(`#${this.id}`).is(':checked') ? op = true : op = false;
    $(`#${this.id}`).prop('checked', op);

    if (this.id == '-1')
      $(`.typePriceList`).prop('checked', op);

    if (!$(`#${this.id}`).is(':checked')) {
      if (this.id == '-1') {
        for (i = 0; i < typeCustomPrices.length; i++) {
          typeCustomPrices.splice(i, 1);
        }
      } else
        if (this.id == '-1') {
          typeCustomPrices = [];
          $(`#-1`).prop('checked', op);
        }
        else
          for (let i = 0; i < typeCustomPrices.length; i++) {
            if (typeCustomPrices[i] == this.id) typeCustomPrices.splice(i, 1);
          }
    } else {
      if (this.id == '-1')
        typeCustomPrices = [];
      typeCustomPrices.push(this.id);
    }
  });

  /* Agregar nuevo usuario */

  $('#btnCreateUserAndAccess').click(function (e) {
    e.preventDefault();
    let id_user = sessionStorage.getItem('id_user');

    if (id_user == '' || id_user == null) {
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

      if ($(`#chckExpenses`).is(':checked')) {
        let selectExpenses = $('#selectExpenses').val();

        if (!selectExpenses) {
          toastr.error('Seleccione tipo de gasto');
          return false;
        }
      }

      /* Validar que al menos un acceso sea otorgado */
      if ($('input[type=checkbox]:checked').length === 0) {
        toastr.error('Debe seleccionar al menos un acceso');
        return false;
      }

      /* Obtener los checkbox seleccionados */

      if ($(`#checkbox-16`).is(':checked')) {
        if (typeCustomPrices.length == 0) {
          toastr.error('Debe seleccionar tipo de precio');
          return false;
        }
      } else {
        typeCustomPrices.push(0);
      }

      let typePayroll = 0;

      if ($(`#checkbox-9`).is(':checked')) {
        typePayroll = $('#typePayroll').val();

        if (typePayroll == 0 || !typePayroll) {
          toastr.error('Debe seleccionar tipo de nomina');
          return false;
        }
      }
      let typeExpenses = flag_expense_distribution;

      if ((selectExpenses == '0' || selectExpenses == '2') && (flag_expense == '1' || flag_expense == '0')) {
        if ($(`#typeExpenses`).is(':checked')) typeExpenses = 1;
        else typeExpenses = 0;
      }

      let dataUser = {};

      if ((selectExpenses == '0' || selectExpenses == '2') && (flag_expense == '1' || flag_expense == '0')) {
        if ($(`#typeExpenses`).is(':checked')) typeExpenses = 1;
        else typeExpenses = 0;
      }

      dataUser['expense'] = 0;
      dataUser['expenseDistribution'] = 0;
      dataUser['production'] = 0;
      dataUser['anualExpense'] = 0;

      switch (selectExpenses) {
        case '0':// Todos
          dataUser['expense'] = 1;
          dataUser['expenseDistribution'] = 1;
          dataUser['production'] = 1;
          dataUser['anualExpense'] = 1;
          break;
        case '1':// Asignacion
          dataUser['expense'] = 1;
          break;
        case '2': // Distribucion o Recuperacion
          dataUser['expenseDistribution'] = 1;
          break;
        case '3': // Unidad Produccion
          dataUser['production'] = 1;
          break;
        case '4': // Gastos anuales
          dataUser['anualExpense'] = 1;
          break;
      }

      dataUser['nameUser'] = nameUser;
      dataUser['lastnameUser'] = lastnameUser;
      dataUser['emailUser'] = emailUser;
      dataUser['typePayroll'] = typePayroll;
      dataUser['typeExpenses'] = typeExpenses;
      dataUser['typeCustomPrices'] = typeCustomPrices;

      dataUser = setCheckBoxes(dataUser);

      $.post('/api/users/addUser', dataUser, function (data, textStatus, jqXHR) {
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
    $('.cardTypePayroll').hide();
    $('.cardTypeExpenses').hide();
    $('.cardTypePrices').hide();
    $('.cardChkExpenses').hide();

    $('#nameUser').prop('disabled', true);
    $('#lastnameUser').prop('disabled', true);
    $('#emailUser').prop('disabled', true);

    let row = $(this).closest('tr')[0];
    let data = tblUsers.fnGetData(row);

    let id_user = this.id;
    sessionStorage.setItem('id_user', id_user);

    $('#nameUser').val(data.firstname);
    $('#lastnameUser').val(data.lastname);
    $('#emailUser').val(data.email);

    let access = {
      costCreateProducts: data.create_product,
      costCreateMaterials: data.create_materials,
      exportImport: data.export_import,
      costCreateMachines: data.create_machines,
      costCreateProcess: data.create_process,
      productsMaterials: data.product_materials,
      factoryLoad: data.factory_load,
      servicesExternal: data.external_service,
      payroll: data.payroll_load,
      users: data.user,
      backup: data.backup,
      quotePaymentMethod: data.quote_payment_method,
      quoteCompany: data.quote_company,
      quoteContact: data.quote_contact,
      prices: data.price,
      customPrices: data.custom_price,
      analysisMaterials: data.analysis_material,
      economyScale: data.economy_scale,
      saleObjectives: data.sale_objectives,
      priceObjectives: data.price_objectives,
      multiproduct: data.multiproduct,
      simulator: data.simulator,
      historical: data.historical,
      generalCostReport: data.general_cost_report,
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

    if (data.expense == '1') {
      let selectExpenses;
      $(`#chckExpenses`).prop('checked', true);
      $('.cardChkExpenses').show();

      // Combinación de condiciones utilizando un objeto para mapeo
      const conditions = {
        '1-1-1-1': 0,
        '0-1-0-0': 1,
        '1-0-0-0': 2,
        '0-0-1-0': 3,
        '0-0-0-1': 4
      };

      const key = `${data.expense_distribution}-${data.expense}-${data.production_center}-${data.anual_expense}`;

      selectExpenses = conditions[key] ?? selectExpenses;

      if (selectExpenses === 1) {
        $('.cardTypeExpenses').hide();
      } else if ((selectExpenses === 0 || selectExpenses === 2) && (flag_expense === '1' || flag_expense === '0')) {
        $('.cardTypeExpenses').show();
      }

      $(`#selectExpenses option[value=${selectExpenses}]`).prop('selected', true);

      if (data.type_expense == 1)
        $(`#typeExpenses`).prop('checked', true);
      else
        $(`#typeExpenses`).prop('checked', false);
    }

    if ($(`#checkbox-9`).is(':checked')) $('.cardTypePayroll').show();

    if ($(`#checkbox-16`).is(':checked')) $('.cardTypePrices').show();

    $(`#typePayroll option[value=${data.type_payroll}]`).prop('selected', true);

    typeCustomPrices = [];
    let typePriceList = document.getElementsByClassName('typePriceList');
    let type_custom_price = data.type_custom_price.toString().split(",");

    $(`.typePriceList`).prop('checked', false);

    if (type_custom_price[0] == '-1') {
      $(`.typePriceList`).prop('checked', true);
      typeCustomPrices.push('-1');
    } else {
      for (let i = 0; i < type_custom_price.length; i++) {
        for (let j = 1; j < typePriceList.length; j++) {
          if (type_custom_price[i] == typePriceList[j].id) {
            $(`#${type_custom_price[i]}`).prop('checked', true);
            typeCustomPrices.push(type_custom_price[i]);
            break;
          }
        }
      }
    }

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  const updateUserAccess = () => {
    let id_user = sessionStorage.getItem('id_user');

    let typePayroll = 0;

    if ($(`#checkbox-9`).is(':checked')) {
      typePayroll = $('#typePayroll').val();

      if (typePayroll == 0 || !typePayroll) {
        toastr.error('Debe seleccionar tipo de nomina');
        return false;
      }
    }

    let typeExpenses = flag_expense_distribution;
    let dataUser = {};
    let selectExpenses = $('#selectExpenses').val();

    if ($(`#chckExpenses`).is(':checked')) {
      if (!selectExpenses) {
        toastr.error('Seleccione tipo de gasto');
        return false;
      }
    }

    if ((selectExpenses == '0' || selectExpenses == '2') && (flag_expense == '1' || flag_expense == '0')) {
      if ($(`#typeExpenses`).is(':checked')) typeExpenses = 1;
      else typeExpenses = 0;
    }
    dataUser['expense'] = 0;
    dataUser['expenseDistribution'] = 0;
    dataUser['production'] = 0;
    dataUser['anualExpense'] = 0;

    switch (selectExpenses) {
      case '0':// Todos
        dataUser['expense'] = 1;
        dataUser['expenseDistribution'] = 1;
        dataUser['production'] = 1;
        dataUser['anualExpense'] = 1;
        break;
      case '1':// Asignacion
        dataUser['expense'] = 1;
        break;
      case '2': // Distribucion o Recuperacion
        dataUser['expenseDistribution'] = 1;
        break;
      case '3': // Unidad Produccion
        dataUser['production'] = 1;
        break;
      case '4': // Gastos anuales
        dataUser['anualExpense'] = 1;
        break;
    }

    if ($(`#checkbox-16`).is(':checked')) {
      if (typeCustomPrices.length == 0) {
        toastr.error('Debe seleccionar tipo de precio');
        return false;
      }
    } else {
      typeCustomPrices.push(0);
    }

    dataUser['id_user'] = id_user;
    dataUser['nameUser'] = $('#nameUser').val();
    dataUser['lastnameUser'] = $('#lastnameUser').val();
    dataUser['emailUser'] = $('#emailUser').val();
    dataUser['typeCustomPrices'] = typeCustomPrices;
    dataUser['typePayroll'] = typePayroll;
    dataUser['typeExpenses'] = typeExpenses;

    dataUser = setCheckBoxes(dataUser);

    $.post(
      '/api/accessUsers/updateCostUserAccess',
      dataUser,
      function (data, textStatus, jqXHR) {
        message(data, id_user);
        updateTable();
      }
    );
  };

  /* Seleccionar checkboxes */
  const setCheckBoxes = (dataUser) => {
    let i = 1;

    let access = {
      costCreateProducts: 0,
      costCreateMaterials: 0,
      exportImport: 0,
      costCreateMachines: 0,
      costCreateProcess: 0,
      costProductMaterials: 0,
      factoryLoad: 0,
      externalService: 0,
      payrollLoad: 0,
      costUser: 0,
      costBackup: 0,
      quotePaymentMethod: 0,
      quoteCompany: 0,
      quoteContact: 0,
      price: 0,
      customPrices: 0,
      analysisMaterial: 0,
      economyScale: 0,
      saleObjectives: 0,
      priceObjectives: 0,
      multiproduct: 0,
      simulator: 0,
      historical: 0,
      generalCostReport: 0,
      quote: 0,
      support: 0,
    };

    $.each(access, (index, value) => {
      if ($(`#checkbox-${i}`).is(':checked')) dataUser[`${index}`] = 1;
      else dataUser[`${index}`] = 0;
      i++;
    });

    if (!$(`#chckExpenses`).is(':checked')) {
      dataUser[`expense`] = 0;
      dataUser[`expenseDistribution`] = 0;
      dataUser[`production`] = 0;
      dataUser[`anualExpense`] = 0;
    }

    return dataUser;
  };

  /* Eliminar usuario */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblUsers.fnGetData(row);

    let id_user = data.id_user;
    let factoryLoad = data.factory_load;
    let dataUser = {};
    dataUser['id_user'] = id_user;
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
          $.post('/api/users/deleteUser', dataUser, function (data, textStatus, jqXHR) {
            message(data, id_user);
          }
          );
        }
      },
    });
  };

  /* Inactivar / Activar Usuario */
  $(document).on('click', '.checkUser', function () {
    let id_user = this.id;

    if (id_user == idUser) {
      toastr.error('No puede cambiar de estado al usuario actualmente logueado');
      return false;
    }

    let className = this.className.toString().split(" ");

    let badge = className[1];
    badge == 'badge-warning' ? op = 0 : op = 1;

    bootbox.dialog({
      title: op == 0 ? 'Inactivar' : 'Activar',
      message: `¿Esta Seguro que desea ${op == 0 ? 'inactivar' : 'activar'} este usuario?.`,
      backdrop: 'static', // Evita que el modal se cierre haciendo clic fuera de él
      closeButton: false, // Oculta el botón de cierre del modal
      size: 'small',
      buttons: {
        si: {
          label: 'Si',
          className: 'btn-success',
          callback: function () {
            $.get(`/api/users/changeActiveUser/${id_user}/${op}`, function (data, textStatus, jqXHR) {
              message(data);
            },
            );
          }
        },
        no: {
          label: 'No',
          className: 'btn-danger',
          callback: function () {
          }
        }
      }
    });
  });

  /* Mensaje de exito */
  const message = async (data, id_user) => {
    if (data.reload) {
      location.reload();
    }

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
