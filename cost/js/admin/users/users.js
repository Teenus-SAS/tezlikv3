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
    if (this.id == 'checkbox-8')
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

      let selectExpenses = $('#selectExpenses').val();

      if (!selectExpenses) {
        toastr.error('Seleccione tipo de gasto');
        return false;
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

      if ($(`#checkbox-8`).is(':checked')) {
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

      if (selectExpenses == '0'){
        dataUser['expense'] = 1;
        dataUser['expenseDistribution'] = 1;
      } else if (selectExpenses == '1') {
        dataUser['expense'] = 1;
        dataUser['expenseDistribution'] = 0;
      } else {
        dataUser['expense'] = 0;
        dataUser['expenseDistribution'] = 1;
      }
      
      dataUser['nameUser'] = nameUser;
      dataUser['lastnameUser'] = lastnameUser;
      dataUser['emailUser'] = emailUser;
      dataUser['typePayroll'] = typePayroll;
      dataUser['typeExpenses'] = typeExpenses;
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

    let id_user = this.id;
    sessionStorage.setItem('id_user', id_user);

    $('#nameUser').val(data.firstname);
    $('#lastnameUser').val(data.lastname);
    $('#emailUser').val(data.email);

    let access = {
      costCreateProducts: data.create_product,
      costCreateMaterials: data.create_materials,
      costCreateMachines: data.create_machines,
      costCreateProcess: data.create_process,
      productsMaterials: data.product_materials,
      //productsProcess: data.product_process,
      factoryLoad: data.factory_load,
      servicesExternal: data.external_service,
      payroll: data.payroll_load,
      // generalExpenses: data.expense,
      //distributionExpenses: data.expense_distribution,
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
      historical: data.historical,
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

    let selectExpenses = 0;
    if (data.expense_distribution == 1 && data.expense == 1) 
      $(`#chckExpenses`).prop('checked', true); 

    if (data.expense_distribution == 0 && data.expense == 1) 
      selectExpenses = 1;
    
    else if (data.expense_distribution == 1 && data.expense == 0)
      selectExpenses = 2;
      
    if ($(`#checkbox-8`).is(':checked')) $('.cardTypePayroll').show();
    if ((selectExpenses == 0 || selectExpenses == 2) && (flag_expense == '1' || flag_expense == '0')) {
      $('.cardChkExpenses').show();
      $('.cardTypeExpenses').show();
    }
    if ($(`#checkbox-16`).is(':checked')) $('.cardTypePrices').show();
    
    $(`#selectExpenses option[value=${selectExpenses}]`).prop('selected', true);

    if(data.type_expense == 1)
      $(`#typeExpenses`).prop('checked', true);
    else
      $(`#typeExpenses`).prop('checked', false);

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

  updateUserAccess = () => {
    let id_user = sessionStorage.getItem('id_user');

    let typePayroll = 0;
    
    if ($(`#checkbox-8`).is(':checked')) {
      typePayroll = $('#typePayroll').val();
      
      if (typePayroll == 0 || !typePayroll) {
        toastr.error('Debe seleccionar tipo de nomina');
        return false;
      }

    }

    let selectExpenses = $('#selectExpenses').val();

    if (!selectExpenses) {
      toastr.error('Seleccione tipo de gasto');
      return false;
    }

    let typeExpenses = flag_expense_distribution;

    if ((selectExpenses == '0' || selectExpenses == '2') && (flag_expense == '1' || flag_expense == '0')) {
      if ($(`#typeExpenses`).is(':checked')) typeExpenses = 1;
      else typeExpenses = 0;
    }
    
    if ($(`#checkbox-16`).is(':checked')) {
      if (typeCustomPrices.length == 0) {
        toastr.error('Debe seleccionar tipo de precio');
        return false;
      }
    } else {
      typeCustomPrices.push(0);
    }

    let dataUser = {};

    if (selectExpenses == '0') {
      dataUser['expense'] = 1;
      dataUser['expenseDistribution'] = 1;
    } else if (selectExpenses == '1') {
      dataUser['expense'] = 1;
      dataUser['expenseDistribution'] = 0;
    } else {
      dataUser['expense'] = 0;
      dataUser['expenseDistribution'] = 1;
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
      '/api/updateCostUserAccess',
      dataUser,
      function (data, textStatus, jqXHR) {
        message(data, id_user);
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
      //costProductProcess: 0,
      factoryLoad: 0,
      externalService: 0,
      payrollLoad: 0,
      // expense: 0,
      // expenseDistribution: 0,
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
      historical: 0,
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
          $.post(
            '/api/deleteUser',
            dataUser,
            function (data, textStatus, jqXHR) {
              message(data, id_user);
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
