$(document).ready(function () {
  $.ajax({
    url: '/api/puc',
    success: function (r) {
      if (r.reload) {
      location.reload();
    }

      let $select1 = $(`.idPuc`);
      $select1.empty();

      $select1.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select1.append(
          `<option value = ${value.id_puc}>${value.number_count} - ${value.count} </option>`
        );
      });
    },
  });
});
