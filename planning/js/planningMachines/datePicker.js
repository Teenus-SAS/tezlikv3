$(document).ready(function () {
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
  $('#december').datepicker();
});
