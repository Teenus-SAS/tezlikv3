$(document).ready(function () {
  $.ajax({
    type: 'GET',
    url: '/api/priceList',
    success: function (r) {
      let $select = $(`#pricesList`);
      $select.empty();

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_price_list}> ${value.price_name} </option>`
        );
      });
    },
  });
});
