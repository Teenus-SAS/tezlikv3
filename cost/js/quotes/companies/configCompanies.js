$(document).ready(function () {
  $.ajax({
    url: '/api/quotesCompanies',
    success: function (r) {
      let $select = $(`#company`);
      $select.empty();

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_quote_company}> ${value.company_name} </option>`
        );
      });
    },
  });
});
