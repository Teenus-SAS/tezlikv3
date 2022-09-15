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

  //General
  $('.month').on('blur', function (e) {
    id = this.id;
    day = this.value;

    // if (day == 0) {
    //   $(`#${id}`).val('');
    //   return false;
    // }
    // Enero
    if (id == 'january') {
      january = new Date(date.getFullYear(), 1, 0);
      lastDay = january.getDate();
      m = january.getMonth();
    }
    // Febrero
    else if (id == 'february') {
      february = new Date(date.getFullYear(), 2, 0);
      lastDay = february.getDate();
      m = february.getMonth();
    }
    // Marzo
    else if (id == 'march') {
      march = new Date(date.getFullYear(), 3, 0);
      lastDay = march.getDate();
      m = march.getMonth();
    }
    // Abril
    else if (id == 'april') {
      april = new Date(date.getFullYear(), 4, 0);
      lastDay = april.getDate();
      m = april.getMonth();
    }
    // Mayo
    else if (id == 'may') {
      may = new Date(date.getFullYear(), 5, 0);
      lastDay = may.getDate();
      m = may.getMonth();
    }
    // Junio
    else if (id == 'june') {
      june = new Date(date.getFullYear(), 6, 0);
      lastDay = june.getDate();
      m = june.getMonth();
    }
    // Julio
    else if (id == 'july') {
      july = new Date(date.getFullYear(), 7, 0);
      lastDay = july.getDate();
      m = july.getMonth();
    }
    // Agosto
    else if (id == 'august') {
      august = new Date(date.getFullYear(), 8, 0);
      lastDay = august.getDate();
      m = august.getMonth();
    }
    // Septiembre
    else if (id == 'september') {
      september = new Date(date.getFullYear(), 9, 0);
      lastDay = september.getDate();
      m = september.getMonth();
    }
    // Octubre
    else if (id == 'october') {
      october = new Date(date.getFullYear(), 10, 0);
      lastDay = october.getDate();
      m = october.getMonth();
    }
    // Noviembre
    else if (id == 'november') {
      november = new Date(date.getFullYear(), 11, 0);
      lastDay = november.getDate();
      m = november.getMonth();
    }
    // Diciembre
    else if (id == 'december') {
      december = new Date(date.getFullYear(), 12, 0);
      lastDay = december.getDate();
      m = december.getMonth();
    }

    businessDays = getBusinessDays(lastDay, m);
    $(`#${id}`).val(businessDays);

    if (day > businessDays) {
      message = 'El valor es mayor al ultimo dia';
      showError(message, id);
      return false;
    }
  });

  getBusinessDays = (days, month) => {
    businessDays = days;

    for (i = 1; i < days; i++) {
      businessDate = new Date(date.getFullYear(), month, i);
      nameDay = businessDate.toLocaleDateString('es-CO', { weekday: 'long' });

      if (nameDay == 'sábado' || nameDay == 'domingo')
        businessDays = businessDays - 1;

      for (let d in festivos[month]) {
        if (i == festivos[month][d]) {
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
