$(document).ready(function () {
  $.ajax({
    url: '/api/processPayroll',
    success: function (r) { 
      if (r.reload) {
        location.reload();
      }

      let $select = $(`#idProcess`);
      $select.empty();

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_process}> ${value.process} </option>`
        );
      });
    },
  });
});
