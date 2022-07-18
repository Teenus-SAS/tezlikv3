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
    programming = $('#formCreateProgramming').serialize();

    // Validar si existe tabla de programacion maquinas
    $.get(`/api/dateMachine/${idMachine}`, function (data) {
      if (data.success) {
        // programming =
        // programming + `&startDate= ${data.datesMachines.start_dat}`;
        saveProgramming(programming);
        return false;
      }
      if (data.error) setStartDate(programming);
    });
  });

  // Ingresar fecha de inicio
  setStartDate = (programming) => {
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
          programming = programming + `&startDate= ${result}`;
          saveProgramming(programming);
        }
      },
    });
  };

  // Guardar programa de producción a la tabla
  saveProgramming = (programming) => {
    debugger;
    machine = $('#idMachine').find('option:selected').text();
    numOrder = $('#order').find('option:selected').text();
    refProduct = $('#refProduct').find('option:selected').text();
    product = $('#selectNameProduct').find('option:selected').text();

    // Obtener información
    $.post(
      '/api/getProgrammingInfo',
      programming,
      function (data, textStatus, jqXHR) {
        debugger;
        $('.colProgramming').append(`
        <tr draggable="true" ondragstart="dragit(event)" ondragover="dragover(event)">
          <td>${numOrder}</td>
          <td>${refProduct}</td>
          <td>${product}</td>
          <td>${data.order.original_quantity}</td>
          <td>${data.order.quantity}</td>
          <td>${quantity}</td>
          <td>${data.order.client}</td>
          <td>${data.economicLot.toFixed(2)}</td>
          <td>${data.datesMachines.start_dat}</td>
          <td>${data.datesMachines.final_date}</td>
        </tr>`);

        message();
      }
    );
  };

  /* Mensaje de exito */
  message = () => {
    $('.cardCreateProgramming').hide(800);
    $('#formCreateProgramming').trigger('reset');
    toastr.success('Programación creada correctamente');
    return false;
  };
});
