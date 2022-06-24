$(document).ready(function () {
  /* Horas */
  new tempusDominus.TempusDominus(document.getElementById('hourEnd'), {
    display: {
      viewMode: 'clock',
      components: {
        decades: false,
        year: false,
        month: false,
        date: false,
        hours: true,
        minutes: true,
        seconds: false,
      },
    },
  });

  /* Meses 
  let date = new Date();

  $('#january').datepicker({
    format: 'dd/mm/yyyy',
    maxDate: '31/01/2022',
  });
  $('#february').datepicker();
  $('#march').datepicker();
  $('#april').datepicker();
  $('#may').datepicker();
  $('#june').datepicker();
  $('#july').datepicker();
  $('#august').datepicker();
  $('#september').datepicker();
  $('#october').datepicker();
  $('#november').datepicker();
  $('#december').datepicker(); */
});
