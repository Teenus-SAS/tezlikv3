$(document).ready(function () {
  $.ajax({
    url: '/api/contacts',
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
