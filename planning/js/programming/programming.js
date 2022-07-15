$(document).ready(function () {
  /* Ocultar panel crear producto */

  $('.cardCreateProgramming').hide();

  /* Abrir panel crear producto */

  $('#btnNewProgramming').click(function (e) {
    e.preventDefault();

    $('.cardImportProgramming').hide(800);
    $('.cardCreateProgramming').toggle(800);
    $('#btnCreateProgramming').html('Crear');

    // sessionStorage.removeItem('id_order');

    $('#formCreateProgramming').trigger('reset');
  });

  /* Crear nuevo proceso */

  $('#btnCreateProgramming').click(function (e) {
    e.preventDefault();
    idMachine = parseInt($('#idMachine').val());
    idOrder = parseInt($('#order').val());
    idProduct = parseInt($('#selectNameProduct').val());
    quantity = $('#quantity').val();

    data = idMachine * idOrder * idProduct;

    if (!data || data == 0 || quantity == '') {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    bootbox.prompt({
      title: 'Producción',
      message: '<p>Ingrese fecha de inicio:</p>',
      inputType: 'date',
      callback: function (result) {
        if (!result || result == '') {
          toastr.error('Ingrese fecha de inicio');
          return false;
        }
        console.log(result);
      },
    });

    programming = $('#formCreateProgramming').serialize();
  });

  /* Actualizar procesos */

  // $(document).on('click', '.updateProgramming', function (e) {
  //   $('.cardImportProgramming').hide(800);
  //   $('.cardCreateProgramming').show(800);
  //   $('#btnCreateProgramming').html('Actualizar');

  //   let row = $(this).parent().parent()[0];
  //   let data = tblProgramming.fnGetData(row);

  //   sessionStorage.setItem('id_order', data.id_order);
  //   $(`#idMachine option[value=${data.id_machine}]`).prop('selected', true);
  //   $(`#order option[value=${data.id_order}]`).prop('selected', true);
  //   $(`#selectNameProduct option[value=${data.id_product}]`).prop(
  //     'selected',
  //     true
  //   );
  //   $('#quantity').val(data.quantity);

  //   $('html, body').animate(
  //     {
  //       scrollTop: 0,
  //     },
  //     1000
  //   );
  // });

  // updateProgramming = () => {
  //   let data = $('#formCreateProgramming').serialize();
  //   idOrder = sessionStorage.getItem('id_order');
  //   data = data + '&idOrder=' + idOrder;

  //   $.post(
  //     '../../api/updatePlanProgramming',
  //     data,
  //     function (data, textStatus, jqXHR) {
  //       message(data);
  //     }
  //   );
  // };

  /* Eliminar proceso */

  // deleteFunction = () => {
  //   let row = $(this.activeElement).parent().parent()[0];
  //   let data = tblProgramming.fnGetData(row);

  //   let id_order = data.id_order;

  //   bootbox.confirm({
  //     title: 'Eliminar',
  //     message:
  //       'Está seguro de eliminar esta producción? Esta acción no se puede reversar.',
  //     buttons: {
  //       confirm: {
  //         label: 'Si',
  //         className: 'btn-success',
  //       },
  //       cancel: {
  //         label: 'No',
  //         className: 'btn-danger',
  //       },
  //     },
  //     callback: function (result) {
  //       if (result == true) {
  //         $.get(
  //           `../../api/deletePlanProgramming/${id_order}`,
  //           function (data, textStatus, jqXHR) {
  //             message(data);
  //           }
  //         );
  //       }
  //     },
  //   });
  // };

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('.cardCreateProgramming').hide(800);
      $('#formCreateProgramming').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblProgramming').DataTable().clear();
    $('#tblProgramming').DataTable().ajax.reload();
  }
});
