$(document).ready(function () {
  $.ajax({
    url: '/api/paymentMethods',
    success: function (r) {
      let $select = $(`#idPayment`);
      $select.empty();

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_method}> ${value.method} </option>`
        );
      });
    },
  });
});
