$(document).ready(function () {
  /* Horas */
  $('#hourStartPicker').datetimepicker({ format: 'LT' });
  $('#hourEndPicker').datetimepicker({ format: 'LT' });

  /* Meses */
  let date = new Date();
  let options = {
    weekday: 'long',
  };
  const festivos = [
    [1, 10], // Enero
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

    if (day == 0) {
      $(`#${id}`).val('');
      return false;
    }
    // Enero
    if (id == 'january') {
      january = new Date(date.getFullYear(), 1, 0);
      lastDay = january.getDate();

      januaryDay = new Date(date.getFullYear(), january.getMonth(), day);
      nameDay = januaryDay.toLocaleDateString('es-CO', options);
      nameMonth = 'enero';
      m = january.getMonth();
    }
    // Febrero
    if (id == 'february') {
      february = new Date(date.getFullYear(), 2, 0);
      lastDay = february.getDate();

      februaryDay = new Date(date.getFullYear(), february.getMonth(), day);
      nameDay = februaryDay.toLocaleDateString('es-CO', options);
      nameMonth = 'febrero';
      m = february.getMonth();
    }
    // Marzo
    if (id == 'march') {
      march = new Date(date.getFullYear(), 3, 0);
      lastDay = march.getDate();

      marchDay = new Date(date.getFullYear(), march.getMonth(), day);
      nameDay = marchDay.toLocaleDateString('es-CO', options);
      nameMonth = 'marzo';
      m = march.getMonth();
    }
    // Abril
    if (id == 'april') {
      april = new Date(date.getFullYear(), 4, 0);
      lastDay = april.getDate();

      aprilDay = new Date(date.getFullYear(), april.getMonth(), day);
      nameDay = aprilDay.toLocaleDateString('es-CO', options);
      nameMonth = 'abril';
      m = april.getMonth();
    }
    // Mayo
    if (id == 'may') {
      may = new Date(date.getFullYear(), 5, 0);
      lastDay = may.getDate();

      mayDay = new Date(date.getFullYear(), may.getMonth(), day);
      nameDay = mayDay.toLocaleDateString('es-CO', options);
      nameMonth = 'mayo';
      m = may.getMonth();
    }
    // Junio
    if (id == 'june') {
      june = new Date(date.getFullYear(), 6, 0);
      lastDay = june.getDate();

      juneDay = new Date(date.getFullYear(), june.getMonth(), day);
      nameDay = juneDay.toLocaleDateString('es-CO', options);
      nameMonth = 'junio';
      m = june.getMonth();
    }
    // Julio
    if (id == 'july') {
      july = new Date(date.getFullYear(), 7, 0);
      lastDay = july.getDate();

      julyDay = new Date(date.getFullYear(), july.getMonth(), day);
      nameDay = julyDay.toLocaleDateString('es-CO', options);
      nameMonth = 'julio';
      m = july.getMonth();
    }
    // Agosto
    if (id == 'august') {
      august = new Date(date.getFullYear(), 8, 0);
      lastDay = august.getDate();

      augustDay = new Date(date.getFullYear(), august.getMonth(), day);
      nameDay = augustDay.toLocaleDateString('es-CO', options);
      nameMonth = 'agosto';
      m = august.getMonth();
    }
    // Septiembre
    if (id == 'september') {
      september = new Date(date.getFullYear(), 9, 0);
      lastDay = september.getDate();

      septemberDay = new Date(date.getFullYear(), september.getMonth(), day);
      nameDay = septemberDay.toLocaleDateString('es-CO', options);
      nameMonth = 'septiembre';
      m = september.getMonth();
    }
    // Octubre
    if (id == 'october') {
      october = new Date(date.getFullYear(), 10, 0);
      lastDay = october.getDate();

      octoberDay = new Date(date.getFullYear(), october.getMonth(), day);
      nameDay = octoberDay.toLocaleDateString('es-CO', options);
      nameMonth = 'octubre';
      m = october.getMonth();
    }
    // Noviembre
    if (id == 'november') {
      november = new Date(date.getFullYear(), 11, 0);
      lastDay = november.getDate();

      novemberDay = new Date(date.getFullYear(), november.getMonth(), day);
      nameDay = novemberDay.toLocaleDateString('es-CO', options);
      nameMonth = 'noviembre';
      m = november.getMonth();
    }
    // Diciembre
    if (id == 'december') {
      december = new Date(date.getFullYear(), 12, 0);
      lastDay = december.getDate();

      decemberDay = new Date(date.getFullYear(), december.getMonth(), day);
      nameDay = decemberDay.toLocaleDateString('es-CO', options);
      nameMonth = 'diciembre';
      m = december.getMonth();
    }

    if (day > lastDay) {
      message = 'El valor es mayor al ultimo dia';
      showError(message, id);
      return false;
    }

    if (nameDay == 'sábado' || nameDay == 'domingo') {
      message = `${day} de ${nameMonth} no es un dia habil (${nameDay})`;
      showError(message, id);
      return false;
    }

    for (let d in festivos[m]) {
      if (day == festivos[m][d]) {
        message = `${
          nameDay.charAt(0).toUpperCase() + nameDay.slice(1)
        }(${day}) de ${nameMonth} no es un dia habil (Festivo)`;

        showError(message, id);
        break;
      }
    }
  });

  showError = (message, id) => {
    toastr.error(message);
    $(`#${id}`).css('border-color', 'red');
    $(`#${id}`).val('');
  };
});
