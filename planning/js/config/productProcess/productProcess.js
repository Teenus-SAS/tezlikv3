$(document).ready(function () {
  let idProduct;
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

    // $('#idProcess option:contains(Seleccionar)').prop('selected', true);
    // $('#idMachine option:contains(Seleccionar)').prop('selected', true);

    // $('#enlistmentTime').val('');
    // $('#operationTime').val('');
    // $('#totalTime').val('');
  });

  /* Seleccionar producto */

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    idProduct = $('#selectNameProduct').val();
  });

  /* calcular el tiempo total proceso */

  $(document).on('click keyup', '#enlistmentTime', function (e) {
    tOperation = $('#operationTime').val();

    tOperation == '' ? (tOperation = 0) : tOperation;
    this.value == '' ? (this.value = 0) : this.value;

    let val = parseFloat(this.value) + parseFloat(tOperation);
    val = val.toString();
    $('#totalTime').val(val);
  });

  $(document).on('click keyup', '#operationTime', function (e) {
    tEnlistment = $('#enlistmentTime').val();

    tEnlistment == '' ? (tEnlistment = 0) : tEnlistment;
    this.value == '' ? (this.value = 0) : this.value;

    let val = parseFloat(this.value) + parseFloat(tEnlistment);
    $('#totalTime').val(val);
  });

  /* Adicionar nuevo proceso */

  $('#btnAddProcess').click(function (e) {
    e.preventDefault();
    let idProductProcess = sessionStorage.getItem('id_product_process');

    if (idProductProcess == '' || idProductProcess == null) {
      idProduct = parseInt($('#selectNameProduct').val());
      refP = parseInt($('#idProcess').val());
      refM = parseInt($('#idMachine').val());

      enlisT = parseInt($('#enlistmentTime').val());
      operT = parseInt($('#operationTime').val());
      totalTime = parseInt($('#totalTime').val());

      data = idProduct * refP;
      //data = idProduct * refP * refM;

      if (!data || totalTime == 0 || !totalTime) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      productProcess = $('#formAddProcess').serialize();

      productProcess = productProcess + '&idProduct=' + idProduct;
      $.post(
        '/api/addPlanProductsProcess',
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

    enlistment_time = validateNumber(data.enlistment_time);
    $('#enlistmentTime').val(enlistment_time);

    operation_time = validateNumber(data.operation_time);
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
      number = number.toLocaleString('es-ES', {
        minimumFractionDigits: 4,
        maximumFractionDigits: 4,
      });

    return number;
  };

  updateProcess = () => {
    let data = $('#formAddProcess').serialize();
    idProduct = $('#selectNameProduct').val();
    idProductProcess = sessionStorage.getItem('id_product_process');

    data =
      data +
      '&idProductProcess=' +
      idProductProcess +
      '&idProduct=' +
      idProduct;

    $.post(
      '../../api/updatePlanProductsProcess',
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
          $.get(
            `/api/deletePlanProductProcess/${idProductProcess}`,
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
      // $('.cardCreateRawProcesss').toggle(800);
      $('.cardAddProcess').hide(800);
      $('#formAddProcess')[0].reset();
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
