$(document).ready(function () {
  let idProduct;
  let dataProductProcess = [];

  /* Ocultar panel crear producto */

  $('.cardAddProcess').hide();

  /* Abrir panel crear producto */

  $('#btnCreateProcess').click(function (e) {
    e.preventDefault();

    $('.cardImportProductsProcess').hide(800);
    $('.cardAddProcess').toggle(800);
    $('#btnAddProcess').html('Asignar');

    sessionStorage.removeItem('id_product_process');

    $('#formAddProcess').trigger('reset');
  });

  /* Seleccionar producto */

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    idProduct = $('#selectNameProduct').val();
  });

  /* calcular el tiempo total proceso */

  $(document).on('click keyup', '.time', function (e) {
    let tOperation = $('#operationTime').val();
    let tEnlistment = $('#enlistmentTime').val();

    tOperation == '' ? (tOperation = '0') : tOperation;
    tOperation = replaceNumber(tOperation);

    tEnlistment == '' ? (tEnlistment = '0') : tEnlistment;
    tEnlistment = replaceNumber(tEnlistment);

    let val = parseFloat(tEnlistment) + parseFloat(tOperation);
    val = validateNumber(val);
    $('#totalTime').val(val);
  });

  replaceNumber = (number) => {
    if (number.includes('.')) number = number.replace('.', '');
    if (number.includes(',')) number = number.replace(',', '.');
    return number;
  };

  /* Adicionar nuevo proceso */

  $('#btnAddProcess').click(function (e) {
    e.preventDefault();
    let idProductProcess = sessionStorage.getItem('id_product_process');

    if (idProductProcess == '' || idProductProcess == null) {
      idProduct = parseInt($('#selectNameProduct').val());
      let refP = parseInt($('#idProcess').val());
      let refM = parseInt($('#idMachine').val());

      let totalTime = parseInt($('#totalTime').val());

      let data = idProduct * refP;

      if (!data || isNaN(refM) || totalTime == 0 || totalTime == '') {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      let productProcess = $('#formAddProcess').serialize();

      productProcess = productProcess + '&idProduct=' + idProduct;
      $.post(
        '/api/addProductsProcess',
        productProcess,
        function (data, textStatus, jqXHR) {
          message(data);
        }
      );
    } else {
      updateProcess();
    }
  });

  /* Actualizar productos Procesos */

  $(document).on('click', '.updateProcess', function (e) {
    $('.cardImportProductsProcess').hide(800);
    $('.cardAddProcess').show(800);
    $('#btnAddProcess').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblConfigProcess.fnGetData(row);

    sessionStorage.setItem('id_product_process', data.id_product_process);

    $(`#idProcess option[value=${data.id_process}]`).prop('selected', true);

    data.id_machine == null ? (data.id_machine = 0) : data.id_machine;
    $(`#idMachine option[value=${data.id_machine}]`).prop('selected', true);

    let enlistment_time = validateNumber(data.enlistment_time);
    $('#enlistmentTime').val(enlistment_time);

    let operation_time = validateNumber(data.operation_time);
    $('#operationTime').val(operation_time);

    $('#enlistmentTime').click();

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  validateNumber = (number) => {
    if (number.isInteger) number = number.toLocaleString();
    else
      number = number.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });

    return number;
  };

  updateProcess = () => {
    let data = $('#formAddProcess').serialize();
    idProduct = $('#selectNameProduct').val();
    let idProductProcess = sessionStorage.getItem('id_product_process');

    data =
      data +
      '&idProductProcess=' +
      idProductProcess +
      '&idProduct=' +
      idProduct;

    $.post(
      '../../api/updateProductsProcess',
      data,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  };

  /* Eliminar proceso */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblConfigProcess.fnGetData(row);

    let idProductProcess = data.id_product_process;
    idProduct = $('#selectNameProduct').val();
    dataProductProcess['idProductProcess'] = idProductProcess;
    dataProductProcess['idProduct'] = idProduct;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este proceso? Esta acción no se puede reversar.',
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
            '/api/deleteProductProcess',
            dataProductProcess,
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
      $('.cardAddProcess').hide(800);
      $('#formAddProcess').trigger('reset');
      updateTable();
      toastr.success(data.message);
      //return false
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblConfigProcess').DataTable().clear();
    $('#tblConfigProcess').DataTable().ajax.reload();
  }
});
