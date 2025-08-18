$(document).ready(function () {
  $.ajax({
    url: '/api/benefitsPayroll',
    success: function (r) {
      if (r.reload) {
        location.reload();
      }

      sessionStorage.removeItem('dataBenefits');

      let dataBenefits = JSON.stringify(r);
      sessionStorage.setItem('dataBenefits', dataBenefits);

      let $select = $('#benefit');

      $select.append(
        `<option value='0' disabled selected>Seleccionar</option>`
      );
      $.each(r, function (i, value) {
        $select.append(
          `<option value ='${value.id_benefit}'> ${value.benefit} </option>`
        );
      });
    },
  });
});
