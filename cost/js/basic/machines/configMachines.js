$(document).ready(function () {
  sessionStorage.removeItem('machinesData');

  $('.cardCreateMachines').hide();

  $('#btnNewMachine').click(function (e) {
    e.preventDefault();
    $('.cardCreateMachines').toggle(800);
  });

  $.ajax({
    type: 'GET',
    url: '/api/machines',
    success: function (r) {
      let dataMachines = JSON.stringify(r);
      sessionStorage.setItem('dataMachines', dataMachines);

      let $select = $(`#idMachine`);
      $select.empty();
      $select.append(`<option disabled>Seleccionar</option>`);
      $select.append(`<option value = "0" selected>PROCESO MANUAL</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = '${value.id_machine}'> ${value.machine} </option>`
        );
      });

      let $select1 = $('#machine');
      $select1.empty();
      $select1.append('<option>Seleccionar</option>');

      $.each(r, function (i, value) {
        $select1.append(
          `<option value=${value.id_machine}>${value.machine}</option>`
        );
      });
    },
  });

  $('#idMachine').change(function (e) { 
    e.preventDefault();
    
    let data = JSON.parse(sessionStorage.getItem('dataMachines'));

    data = data.filter(item => item.id_machine == this.value);

    $('#enlistmentTime').val(data[0].unity_time);
  });

});
