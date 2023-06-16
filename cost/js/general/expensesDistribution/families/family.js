$(document).ready(function () {
  $('.cardAddNewFamily').hide();

  $(document).on('click', '#btnAddNewFamily', async function () {
    await loadTableFamilies();
    $('#formFamily').trigger('reset');
    $('.cardTblExpensesDistribution').hide(800);
    $('.cardExpensesDistribution').hide(800);
    $('.cardAddProductFamily').hide(800);
    $('.cardTblFamilies').show(800);
    $('.cardAddNewFamily').toggle(800);
    sessionStorage.removeItem('id_family');
    let tables = document.getElementById('tblFamilies');

    let attr = tables;
    attr.style.width = '100%';
    attr = tables.firstElementChild;
    attr.style.width = '100%';
  });

  $('#btnSaveFamily').click(function (e) {
    e.preventDefault();

    let idFamily = sessionStorage.getItem('id_family');

    if (idFamily == '' || idFamily == null)
      checkDataFamily('/api/addFamily', idFamily);
    else checkDataFamily('/api/updateFamily', idFamily);
  });

  $(document).on('click', '.updateFamily', function () {
    $('#btnSaveFamily').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblFamilies.fnGetData(row);

    sessionStorage.setItem('id_family', data.id_family);

    $('#family').val(data.family);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  checkDataFamily = async (url, idFamily) => {
    let family = $('#family').val();

    if (family == '') {
      toastr.error('Ingrese nuevo campo');
      return false;
    }

    let dataFamily = new FormData(formFamily);

    if (idFamily != '' || idFamily != null)
      dataFamily.append('idFamily', idFamily);

    let resp = await sendDataPOST(url, dataFamily);
    message(resp, 3);
  };

  /* Eliminar Familia */
  deleteFamily = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblFamilies.fnGetData(row);

    let id_family = data.id_family;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar esta familia? Esta acción no se puede reversar.',
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
          $.get(
            `/api/deleteFamily/${id_family}`,
            function (data, textStatus, jqXHR) {
              message(data, 3);
            }
          );
        }
      },
    });
  };

  /* Actualizar gasto de distribucion x familia */
  $(document).on('click', '.updateExpenseDistributionFamilies', function () {
    $('.cardImportExpenses').hide(800);
    $('.cardExpenseRecover').hide(800);
    $('.cardExpensesDistribution').show(800);
    $('#btnAssignExpenses').html('Actualizar');

    sessionStorage.setItem('id_expenses_distribution', 1);

    let row = $(this).parent().parent()[0];
    let data = tblExpensesDistribution.fnGetData(row);

    $(`#familiesDistribute option[value=${data.id_family}]`).prop(
      'selected',
      true
    );

    $('#undVendidas').val(data.units_sold.toLocaleString('es-CO'));
    $('#volVendidas').val(data.turnover.toLocaleString('es-CO'));

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Eliminar distribucion de gasto x familia */
  deleteExpenseDistributionFamilies = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblExpensesDistribution.fnGetData(row);

    let id_family = data.id_family;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar esta familia? Esta acción no se puede reversar.',
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
          $.get(
            `/api/deleteExpensesDistributionFamily/${id_family}`,
            function (data, textStatus, jqXHR) {
              message(data, 3);
            }
          );
        }
      },
    });
  };

  /* Guardar Familia */
  $(document).on('keyup', '.inputsFamily', function () {
    let value = parseFloat(strReplaceNumber(this.value));
    let key = getLastText(this.className);

    value = value * 1;

    if (isNaN(value)) {
      toastr.error('Ingrese un campo valido');
      return false;
    }

    dataExpenseDistributionFamily[key][this.id] = this.value;
  });

  /* Asignar productos */
  $(document).on('click', '.btnAddProductsFamilies', async function () {
    await loadTableProductsFamilies();
    await loadExpensesDFamiliesProducts();
    $('#btnAddProductFamily').html('Asignar');

    $('#formProductFamily').trigger('reset');
    $('.cardTblExpensesDistribution').hide(800);
    $('.cardExpensesDistribution').hide(800);
    $('.cardTblFamilies').show(800);
    $('.cardAddNewFamily').hide(800);
    $('.cardAddProductFamily').toggle(800);
    sessionStorage.removeItem('id_product');
    let tables = document.getElementById('tblFamilies');

    let attr = tables;
    attr.style.width = '100%';
    attr = tables.firstElementChild;
    attr.style.width = '100%';
  });

  $('#btnAddProductFamily').click(function (e) {
    e.preventDefault();

    let id_product = parseInt($('#familyRefProduct').val());
    let id_family = parseInt($('#families').val());

    let data = id_product * id_family;

    if (isNaN(data)) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    data = $('#formProductFamily').serialize();

    $.post('/api/saveProductFamily', data, function (data, textStatus, jqXHR) {
      message(data, 4);
    });
  });

  $(document).on('click', '.updateProductFamily', function () {
    $('#btnAddProductFamily').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblFamilies.fnGetData(row);

    $('#familyRefProduct').empty();
    $('#familyNameProduct').empty();

    $('#familyRefProduct').append(
      `<option value ='${data.id_product}'> ${data.reference} </option>`
    );
    $('#familyNameProduct').append(
      `<option value ='${data.id_product}'> ${data.product} </option>`
    );
    $(`#families option[value=${data.id_family}]`).prop('selected', true);

    $('.cardAddProductFamily').show(800);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  deleteProductFamily = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblFamilies.fnGetData(row);

    let id_product = data.id_product;
    dataFamily = {};

    dataFamily['selectNameProduct'] = id_product;
    dataFamily['idFamily'] = 0;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este producto de la familia? Esta acción no se puede reversar.',
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
            '/api/saveProductFamily',
            dataFamily,
            function (data, textStatus, jqXHR) {
              message(data, 4);
            }
          );
        }
      },
    });
  };
});
