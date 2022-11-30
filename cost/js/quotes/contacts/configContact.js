$(document).ready(function () {
  $('#company').change(function (e) {
    e.preventDefault();
    id_company = this.value;

    $.ajax({
      url: `/contactsByCompany/${id_company}`,
      success: function (r) {
        let $select = $(`#contacts`);
        $select.empty();

        $select.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r, function (i, value) {
          $select.append(
            `<option value = ${value.id_contact}> ${value.firstname} - ${value.lastname} </option>`
          );
        });
      },
    });
  });
});
