$(document).ready(function () {
  /* Mostrar factor prestacional */

  $(document).on('change', '#typeFactor', function (e) {
    $('#factor').prop('disabled', true);

    if (this.value == 0) 'Seleccione una opción';
    else if (this.value == 1) value = 38.35;
    else if (this.value == 2) value = 0;
    else if (this.value == 3) {
      $('#factor').prop('disabled', false);
      value = $('#factor').val();
    }

    $('#factor').val(value);
  });

  $(document).on('keyup', '#workingHoursDay', function (e) {
    let value = this.value;
    if (value > 16) {
      toastr.error('El número de horas por dia no puede ser mayor a 16');
      $('#workingHoursDay').val('');
      return false;
    }
  });

  $(document).on('keyup', '#workingDaysMonth', function (e) {
    let value = this.value;
    if (value > 31) {
      toastr.error('El número de días por mes no puede ser mayor a 31');
      $('#workingDaysMonth').val('');
      return false;
    }
  });
});
