$(document).ready(function () {
  /* Horas */
  $('#hourStartPicker').datetimepicker({ format: 'LT' });
  $('#hourEndPicker').datetimepicker({ format: 'LT' });

  /* Meses */
  let date = new Date();

  // Enero
  january = new Date(date.getFullYear(), 1, 1);
  januaryFirst = `${january.getMonth()}/${january.getDate()}/${january.getFullYear()}`;
  january = new Date(date.getFullYear(), 1, 0);
  januaryLast = `${
    january.getMonth() + 1
  }/${january.getDate()}/${january.getFullYear()}`;
  $('#januaryPicker').datetimepicker({
    format: 'YYYY-MM-DD',
    minDate: januaryFirst,
    maxDate: januaryLast,
  });

  // Febrero
  february = new Date(date.getFullYear(), 2, 1);
  februaryFirst = `${february.getMonth()}/${february.getDate()}/${february.getFullYear()}`;
  february = new Date(date.getFullYear(), 2, 0);
  februaryLast = `${
    february.getMonth() + 1
  }/${february.getDate()}/${february.getFullYear()}`;
  $('#februaryPicker').datetimepicker({
    format: 'YYYY-MM-DD',
    minDate: februaryFirst,
    maxDate: februaryLast,
  });

  // Marzo
  march = new Date(date.getFullYear(), 3, 1);
  marchFirst = `${march.getMonth()}/${march.getDate()}/${march.getFullYear()}`;
  march = new Date(date.getFullYear(), 3, 0);
  marchLast = `${
    march.getMonth() + 1
  }/${march.getDate()}/${march.getFullYear()}`;
  $('#marchPicker').datetimepicker({
    format: 'YYYY-MM-DD',
    minDate: marchFirst,
    maxDate: marchLast,
  });

  // Abril
  april = new Date(date.getFullYear(), 4, 1);
  aprilFirst = `${april.getMonth()}/${april.getDate()}/${april.getFullYear()}`;
  april = new Date(date.getFullYear(), 4, 0);
  aprilLast = `${
    april.getMonth() + 1
  }/${april.getDate()}/${april.getFullYear()}`;
  $('#aprilPicker').datetimepicker({
    format: 'YYYY-MM-DD',
    minDate: aprilFirst,
    maxDate: aprilLast,
  });

  // Mayo
  may = new Date(date.getFullYear(), 5, 1);
  mayFirst = `${may.getMonth()}/${may.getDate()}/${may.getFullYear()}`;
  may = new Date(date.getFullYear(), 5, 0);
  mayLast = `${may.getMonth() + 1}/${may.getDate()}/${may.getFullYear()}`;
  $('#mayPicker').datetimepicker({
    format: 'YYYY-MM-DD',
    minDate: mayFirst,
    maxDate: mayLast,
  });

  // Junio
  june = new Date(date.getFullYear(), 6, 1);
  juneFirst = `${june.getMonth()}/${june.getDate()}/${june.getFullYear()}`;
  june = new Date(date.getFullYear(), 6, 0);
  juneLast = `${june.getMonth() + 1}/${june.getDate()}/${june.getFullYear()}`;
  $('#junePicker').datetimepicker({
    format: 'YYYY-MM-DD',
    minDate: juneFirst,
    maxDate: juneLast,
  });

  // Julio
  july = new Date(date.getFullYear(), 7, 1);
  julyFirst = `${july.getMonth()}/${july.getDate()}/${july.getFullYear()}`;
  july = new Date(date.getFullYear(), 7, 0);
  julyLast = `${july.getMonth() + 1}/${july.getDate()}/${july.getFullYear()}`;
  $('#julyPicker').datetimepicker({
    format: 'YYYY-MM-DD',
    minDate: julyFirst,
    maxDate: julyLast,
  });

  // Agosto
  august = new Date(date.getFullYear(), 8, 1);
  augustFirst = `${august.getMonth()}/${august.getDate()}/${august.getFullYear()}`;
  august = new Date(date.getFullYear(), 8, 0);
  augustLast = `${
    august.getMonth() + 1
  }/${august.getDate()}/${august.getFullYear()}`;
  $('#augustPicker').datetimepicker({
    format: 'YYYY-MM-DD',
    minDate: augustFirst,
    maxDate: augustLast,
  });

  // Septiembre
  september = new Date(date.getFullYear(), 9, 1);
  septemberFirst = `${september.getMonth()}/${september.getDate()}/${september.getFullYear()}`;
  september = new Date(date.getFullYear(), 9, 0);
  septemberLast = `${
    september.getMonth() + 1
  }/${september.getDate()}/${september.getFullYear()}`;
  $('#septemberPicker').datetimepicker({
    format: 'YYYY-MM-DD',
    minDate: septemberFirst,
    maxDate: septemberLast,
  });

  // Octubre
  october = new Date(date.getFullYear(), 10, 1);
  octoberFirst = `${october.getMonth()}/${october.getDate()}/${october.getFullYear()}`;
  october = new Date(date.getFullYear(), 10, 0);
  octoberLast = `${
    october.getMonth() + 1
  }/${october.getDate()}/${october.getFullYear()}`;
  $('#octoberPicker').datetimepicker({
    format: 'YYYY-MM-DD',
    minDate: octoberFirst,
    maxDate: octoberLast,
  });

  // Noviembre
  november = new Date(date.getFullYear(), 11, 1);
  novemberFirst = `${november.getMonth()}/${november.getDate()}/${november.getFullYear()}`;
  november = new Date(date.getFullYear(), 11, 0);
  novemberLast = `${
    november.getMonth() + 1
  }/${november.getDate()}/${november.getFullYear()}`;
  $('#novemberPicker').datetimepicker({
    format: 'YYYY-MM-DD',
    minDate: novemberFirst,
    maxDate: novemberLast,
  });

  // Diciembre
  december = new Date(date.getFullYear(), 11, 1);
  decemberFirst = `${
    december.getMonth() + 1
  }/${december.getDate()}/${december.getFullYear()}`;
  december = new Date(date.getFullYear(), 12, 0);
  decemberLast = `${
    december.getMonth() + 1
  }/${december.getDate()}/${december.getFullYear()}`;
  $('#decemberPicker').datetimepicker({
    format: 'YYYY-MM-DD',
    minDate: decemberFirst,
    maxDate: decemberLast,
  });
});
