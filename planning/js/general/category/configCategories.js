$(document).ready(function () {
  $.ajax({
    type: 'GET',
    url: '/api/productsCategories',
    success: function (r) {
      let $select = $(`#category`);
      $select.empty();

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value=${value.id_category}> ${value.category} </option>`
        );
      });
    },
  });
});
