$(document).ready(function () {
  /* Horas */
  $('#hourStartPicker').datetimepicker({ format: 'LT' });
  $('#hourEndPicker').datetimepicker({ format: 'LT' });

  /* Meses */
  let date = new Date();

  //General
  $('.month').on('blur', function (e) {
    id = this.id;
    day = this.value;

    if (day == 0) {
      $(`#${id}`).val('');
    }
  });

  // Enero
  $('#january').on('keyup', function (e) {
    day = this.value;

    january = new Date(date.getFullYear(), 1, 0);

    lastDay = january.getDate();

    if (day > lastDay) {
      toastr.error('El valor es mayor al ultimo dia');
      $('#january').css('border-color', 'red');
      $('#january').val('');
      return false;
    }
  });

  // Febrero
  $('#february').on('keyup', function (e) {
    day = this.value;

    february = new Date(date.getFullYear(), 2, 0);

    lastDay = february.getDate();

    if (day > lastDay) {
      toastr.error('El valor es mayor al ultimo dia');
      $('#february').css('border-color', 'red');
      $('#february').val('');
      return false;
    }
  });

  // Marzo
  $('#march').on('keyup', function (e) {
    day = this.value;

    march = new Date(date.getFullYear(), 3, 0);

    lastDay = march.getDate();

    if (day > lastDay) {
      toastr.error('El valor es mayor al ultimo dia');
      $('#march').css('border-color', 'red');
      $('#march').val('');
      return false;
    }
  });

  // Abril
  $('#april').on('keyup', function (e) {
    day = this.value;

    april = new Date(date.getFullYear(), 4, 0);

    lastDay = april.getDate();

    if (day > lastDay) {
      toastr.error('El valor es mayor al ultimo dia');
      $('#april').css('border-color', 'red');
      $('#april').val('');
      return false;
    }
  });

  // Mayo
  $('#may').on('keyup', function (e) {
    day = this.value;

    may = new Date(date.getFullYear(), 5, 0);

    lastDay = may.getDate();

    if (day > lastDay) {
      toastr.error('El valor es mayor al ultimo dia');
      $('#may').css('border-color', 'red');
      $('#may').val('');
      return false;
    }
  });

  // Junio
  $('#june').on('keyup', function (e) {
    day = this.value;

    june = new Date(date.getFullYear(), 6, 0);

    lastDay = june.getDate();

    if (day > lastDay) {
      toastr.error('El valor es mayor al ultimo dia');
      $('#june').css('border-color', 'red');
      $('#june').val('');
      return false;
    }
  });

  // Julio
  $('#july').on('keyup', function (e) {
    day = this.value;

    july = new Date(date.getFullYear(), 7, 0);

    lastDay = july.getDate();

    if (day > lastDay) {
      toastr.error('El valor es mayor al ultimo dia');
      $('#july').css('border-color', 'red');
      $('#july').val('');
      return false;
    }
  });

  // Agosto
  $('#august').on('keyup', function (e) {
    day = this.value;

    august = new Date(date.getFullYear(), 8, 0);

    lastDay = august.getDate();

    if (day > lastDay) {
      toastr.error('El valor es mayor al ultimo dia');
      $('#august').css('border-color', 'red');
      $('#august').val('');
      return false;
    }
  });

  // Septiembre
  $('#september').on('keyup', function (e) {
    day = this.value;

    september = new Date(date.getFullYear(), 9, 0);

    lastDay = september.getDate();

    if (day > lastDay) {
      toastr.error('El valor es mayor al ultimo dia');
      $('#september').css('border-color', 'red');
      $('#september').val('');
      return false;
    }
  });

  // Octubre
  $('#october').on('keyup', function (e) {
    day = this.value;

    october = new Date(date.getFullYear(), 10, 0);

    lastDay = october.getDate();

    if (day > lastDay) {
      toastr.error('El valor es mayor al ultimo dia');
      $('#october').css('border-color', 'red');
      $('#october').val('');
      return false;
    }
  });

  // Noviembre
  $('#november').on('keyup', function (e) {
    day = this.value;

    november = new Date(date.getFullYear(), 11, 0);

    lastDay = november.getDate();

    if (day > lastDay) {
      toastr.error('El valor es mayor al ultimo dia');
      $('#november').css('border-color', 'red');
      $('#november').val('');
      return false;
    }
  });

  // Diciembre
  $('#december').on('keyup', function (e) {
    day = this.value;

    december = new Date(date.getFullYear(), 11, 0);

    lastDay = december.getDate();

    if (day > lastDay) {
      toastr.error('El valor es mayor al ultimo dia');
      $('#december').css('border-color', 'red');
      $('#december').val('');
      return false;
    }
  });
});
