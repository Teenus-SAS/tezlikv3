$(document).ready(function () {
  $.ajax({
    url: '/api/allCompanies',
    success: function (r) {
      let $select = $(`.company`);
      $select.empty();
      sessionStorage.setItem('op', r[0].id_company_user1);

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value ='${value.id_company}'> ${value.company} </option>`
        );
      });

      $select = $(`#sCompany`);
      $select.empty();
      // sessionStorage.setItem('op', r[0].id_company_user1);
      let data = r.filter(item => item.license_status == 1);

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(data, function (i, value) {
        $select.append(
          `<option value ='${value.id_company}'> ${value.company} </option>`
        );
      });
    },
  });
});
