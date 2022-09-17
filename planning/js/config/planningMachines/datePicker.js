$(document).ready(function () {
  /* Horas */
  $('#hourStartPicker').datetimepicker({ format: 'LT' });
  $('#hourEndPicker').datetimepicker({ format: 'LT' });

  /* Meses */
  let date = new Date();
  const festivos = [
    [10], // Enero (Sabado 1)
    [], // Febrero
    [21], // Marzo
    [14, 15], // Abril
    [1, 30], // Mayo
    [20, 27], // Junio
    [4, 20], // Julio
    [7, 15], // Agosto
    [], // Septiembre
    [17], // Octubre
    [7, 14], // Noviembre
    [8, 25], // Diciembre
  ];

  // General
  $('.month').on('blur', function (e) {
    id = this.id;
    day = this.value;
    month = parseInt(id.slice(6));

    if (day == 0) {
      $(`#${id}`).val('');
      return false;
    }

    businessDays = sessionStorage.getItem('businessDays');
    businessDays = JSON.parse(businessDays);

    if (day > businessDays[month]) {
      message = 'El valor es mayor al ultimo dia';
      showError(message, id);
      return false;
    }
  });

  getBusinessDays = (days, month) => {
    businessDays = days;

    for (j = 1; j < days; j++) {
      businessDate = new Date(date.getFullYear(), month, j);
      nameDay = businessDate.toLocaleDateString('es-CO', { weekday: 'long' });

      if (nameDay == 'sábado' || nameDay == 'domingo')
        businessDays = businessDays - 1;

      for (let d in festivos[month]) {
        if (j == festivos[month][d]) {
          businessDays = businessDays - 1;
        }
      }
    }

    return businessDays;
  };

  showError = (message, id) => {
    toastr.error(message);
    $(`#${id}`).css('border-color', 'red');
    $(`#${id}`).val('');
  };
});
