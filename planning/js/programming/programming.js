$(document).ready(function () {
  // Obtener referencia producto
  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    id = this.value;
    $(`#refProduct option[value=${id}]`).prop('selected', true);
  });

  /* Ocultar panel crear programa de producción */
  $('.cardCreateProgramming').hide();

  /* Abrir panel crear programa de producción */

  $('#btnNewProgramming').click(function (e) {
    e.preventDefault();

    $('.cardImportProgramming').hide(800);
    $('.cardCreateProgramming').toggle(800);
    $('#btnCreateProgramming').html('Crear');

    // sessionStorage.removeItem('id_order');

    $('#formCreateProgramming').trigger('reset');
  });

  /* Crear nueva programa de producción */
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
    programming = $('#formCreateProgramming').serializeArray();

    // Validar si existe tabla de programacion maquinas
    $.post('/api/findDateMachine', programming, function (data) {
      if (data.success) {
        programming.push({ startDate: data.datesMachines.start_dat });
        saveProgramming(programming, data);
        return false;
      }
      if (data.error) setStartDate(programming, data);
    });
  });

  // Ingresar fecha de inicio
  setStartDate = (programming, data) => {
    bootbox.prompt({
      title: 'Programación',
      message: '<p>Ingrese fecha de inicio:</p>',
      inputType: 'date',
      callback: function (result) {
        if (result != null) {
          if (!result || result == '') {
            toastr.error('Ingrese fecha de inicio');
            return false;
          }
          programming.push({ startDate: result });
          // Calcular fecha final
          saveProgramming(programming, data);
        }
      },
    });
  };

  // Guardar programa de producción a la tabla
  saveProgramming = (programming, data) => {
    debugger;
    machine = $('#idMachine').find('option:selected').text();
    numOrder = $('#order').find('option:selected').text();
    refProduct = $('#refProduct').find('option:selected').text();
    product = $('#selectNameProduct').find('option:selected').text();

    $('.colMaterials').empty();
    $('.colProgramming').append(`
      <td>${numOrder}</td>
      <td>${refProduct}</td>
      <td>${product}</td>
      <td>${data.order.original_quantity}</td>
      <td>${data.order.quantity}</td>
      <td>${programming[3].value}</td>
      <td>${data.order.client}</td>
      <td></td>
      <td>${programming[4].startDate}</td>
      <td>${data.datesMachines.final_date}</td>`);

    message();
  };
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

  /* Mensaje de exito 

  message = (data) => {
    if (data.success == true) {
      $('.cardCreateProgramming').hide(800);
      $('#formCreateProgramming').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  }; */

  /* Mensaje de exito */

  message = () => {
    $('.cardCreateProgramming').hide(800);
    $('#formCreateProgramming').trigger('reset');
    //updateTable();
    toastr.success('Programación creada correctamente');
    return false;
  };
  /* Actualizar tabla

  function updateTable() {
    $('#tblProgramming').DataTable().clear();
    $('#tblProgramming').DataTable().ajax.reload();
  }*/
});
