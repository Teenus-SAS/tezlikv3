$(document).ready(function () {
  /*Ocualtar panel de actualización*/

  $('.cardCreateLicense').hide();

  $('#newCompanyLicense').click(function (e) {
    e.preventDefault();
    $('#company').prop('disabled', false);
    sessionStorage.removeItem('id_company');
    $('#formAddLicense').trigger('reset');
    $('.cardCreateLicense').toggle(800);
    $('#btnAddLicense').html('Crear');
    $('.economyScale').show();
  });


  // Revisar tipo de plan tenga economia de escala
  $('#plan').change(function (e) {
    e.preventDefault();
    let id_plan = this.value;

    let dataPlans = JSON.parse(sessionStorage.getItem('dataPlans'));

    let data = dataPlans.find(item => item.id_plan == id_plan);

    if (data.cost_economy_scale == 1 && data.cost_sale_objectives == 1 && data.cost_price_objectives) {
      $('.economyScale').hide(800);
    } else {
      $('.economyScale').show(800);
    }
  });

  /* Agregar licencia */
  $('#btnAddLicense').click(function (e) {
    e.preventDefault();

    idCompany = sessionStorage.getItem('id_company');
    if (!idCompany || idCompany == null) {
      checkLicences('/api/licenses/addLicense', idCompany);
    } else {
      $('#company').prop('disabled', false);
      checkLicences('/api/licenses/updateLicense', idCompany);
    }
  });

  /*Actualizar licencia*/
  $(document).on('click', '.updateLicenses', function (e) {
    e.preventDefault();
    $('.cardCreateLicense').show(800);
    $('#formAddLicense').trigger('reset');
    let row = $(this).parent().parent()[0];
    $('#btnAddLicense').html('Actualizar');
    let data = tblCompaniesLic.fnGetData(row);

    sessionStorage.setItem('id_company', data.id_company);

    $(`#company option[value=${data.id_company}]`).prop('selected', true);
    $('#license_start').val(data.license_start);
    $('#license_end').val(data.license_end);
    $('#quantityUsers').val(data.quantity_user);
    $(`#plan option[value=${data.plan}]`).prop('selected', true);

    data.flag_currency_usd == '1' ? (currencyUSD = '1') : (currencyUSD = '2');
    data.flag_currency_eur == '1' ? (currencyEUR = '1') : (currencyEUR = '2');
    data.flag_employee == '1' ? (payrollEmployee = '1') : (payrollEmployee = '2');
    data.flag_composite_product == '1' ? (compositeProducts = '1') : (compositeProducts = '2');
    data.flag_economy_scale == '1' ? (economyScale = '1') : (economyScale = '2');
    data.flag_sales_objective == '1' ? (salesObjective = '1') : (salesObjective = '2');
    data.flag_price_objective == '1' ? (priceObjective = '1') : (priceObjective = '2');
    data.flag_production_center == '1' ? (production = '1') : (production = '2');
    data.flag_expense_anual == '1' ? (anualExpenses = '1') : (anualExpenses = '2');
    data.cost_historical == '1' ? (historical = '1') : (historical = '2');
    data.flag_indirect == '1' ? (indirect = '1') : (indirect = '2');
    data.flag_export_import == '1' ? (exportImport = '1') : (exportImport = '2');
    data.inyection == '1' ? (inyection = '1') : (inyection = '2');

    $(`#currencyUSD option[value=${currencyUSD}]`).prop('selected', true);
    $(`#currencyEUR option[value=${currencyEUR}]`).prop('selected', true);
    $(`#payrollEmployee option[value=${payrollEmployee}]`).prop('selected', true);
    $(`#compositeProducts option[value=${compositeProducts}]`).prop('selected', true);
    $(`#economyScale option[value=${economyScale}]`).prop('selected', true);
    $(`#salesObjective option[value=${salesObjective}]`).prop('selected', true);
    $(`#priceObjective option[value=${priceObjective}]`).prop('selected', true);
    $(`#production option[value=${production}]`).prop('selected', true);
    $(`#anualExpenses option[value=${anualExpenses}]`).prop('selected', true);
    $(`#historical option[value=${historical}]`).prop('selected', true);
    $(`#indirect option[value=${indirect}]`).prop('selected', true);
    $(`#exportImport option[value=${exportImport}]`).prop('selected', true);
    $(`#inyection option[value=${inyection}]`).prop('selected', true);

    if (data.cost_economy_scale == 1 && data.cost_sale_objectives == 1) {
      $('.economyScale').hide(800);
    } else {
      $('.economyScale').show(800);
    }

    $('#company').prop('disabled', true);
    $('html, body').animate({ scrollTop: 0 }, 1000);
  });

  const checkLicences = async (url, idCompany) => {
    let company = parseFloat($('#company').val());
    let license_start = $('#license_start').val();
    let license_end = $('#license_end').val();
    let quantityUsers = parseFloat($('#quantityUsers').val());
    let plan = parseFloat($('#plan').val());
    let currencyUSD = parseFloat($('#currencyUSD').val());
    let currencyEUR = parseFloat($('#currencyEUR').val());
    let payrollEmployee = parseFloat($('#payrollEmployee').val());
    let compositeProducts = parseFloat($('#compositeProducts').val());
    let economyScale = parseFloat($('#economyScale').val());
    let salesObjective = parseFloat($('#salesObjective').val());
    let priceObjective = parseFloat($('#priceObjective').val());
    let historical = parseFloat($('#historical').val());
    let exportImport = parseFloat($('#exportImport').val());
    let indirect = parseFloat($('#indirect').val());
    let inyection = parseFloat($('#inyection').val());
    let production = parseFloat($('#production').val());
    let anualExpenses = parseFloat($('#anualExpenses').val());

    data = company * quantityUsers * plan * currencyUSD * currencyEUR * payrollEmployee * compositeProducts * economyScale
      * salesObjective * priceObjective * historical * inyection * indirect * exportImport * production * anualExpenses;

    if (license_start == '' || license_end == '' || isNaN(data) || data <= 0) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    if (license_start > license_end) {
      toastr.error('La fecha inicial no debe ser mayor a la final');
      return false;
    }

    currencyUSD == 1 ? (currencyUSD = 1) : (currencyUSD = 0);
    currencyEUR == 1 ? (currencyEUR = 1) : (currencyEUR = 0);
    payrollEmployee == 1 ? (payrollEmployee = 1) : (payrollEmployee = 0);
    compositeProducts == 1 ? (compositeProducts = 1) : (compositeProducts = 0);
    economyScale == 1 ? (economyScale = 1) : (economyScale = 0);
    salesObjective == 1 ? (salesObjective = 1) : (salesObjective = 0);
    priceObjective == 1 ? (priceObjective = 1) : (priceObjective = 0);
    production == 1 ? (production = 1) : (production = 0);
    anualExpenses == 1 ? (anualExpenses = 1) : (anualExpenses = 0);
    historical == 1 ? (historical = 1) : (historical = 0);
    indirect == 1 ? (indirect = 1) : (indirect = 0);
    exportImport == 1 ? (exportImport = 1) : (exportImport = 0);
    inyection == 1 ? (inyection = 1) : (inyection = 0);

    let dataCompany = new FormData(formAddLicense);
    dataCompany.append('currencyUSD', currencyUSD);
    dataCompany.append('currencyEUR', currencyEUR);
    dataCompany.append('payrollEmployee', payrollEmployee);
    dataCompany.append('compositeProducts', compositeProducts);
    dataCompany.append('economyScale', economyScale);
    dataCompany.append('salesObjective', salesObjective);
    dataCompany.append('priceObjective', priceObjective);
    dataCompany.append('production', production);
    dataCompany.append('anualExpenses', anualExpenses);
    dataCompany.append('historical', historical);
    dataCompany.append('indirect', indirect);
    dataCompany.append('exportImport', exportImport);
    dataCompany.append('inyection', inyection);

    if (idCompany != '' || idCompany != null)
      dataCompany.append('idCompany', idCompany);

    let resp = await sendDataPOST(url, dataCompany);

    message(resp);
  }

  /* Cambiar Estado Licencia */
  $(document).on('click', '.licenseStatus', function (e) {
    e.preventDefault();
    // Obtener el ID del elemento
    let id = $(this).attr('id');
    // Obtener la parte después del guion '-'
    let id_company = id.split('-')[1];

    $.ajax({
      type: 'POST',
      url: `/api/licenses/changeStatusCompany/${id_company}`,
      success: function (resp) {
        if (resp.reload) {
          location.reload();
        }

        if (resp.success == true) {
          updateTable();
          toastr.success(resp.message);
          return false;
        } else if (resp.error == true) toastr.error(resp.message);
        else if (resp.info == true) toastr.info(resp.message);
        // message(resp);
      },
    });
  });

  /* Mensaje de exito */

  const message = (data) => {
    if (data.success == true) {
      $('.cardCreateLicense').hide(800);
      $('#formAddLicense').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblCompaniesLicense').DataTable().clear();
    $('#tblCompaniesLicense').DataTable().ajax.reload();
  }
});
