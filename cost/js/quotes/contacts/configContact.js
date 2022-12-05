$(document).ready(function () {
  $('#company').change(function (e) {
    e.preventDefault();
    id_company = this.value;

    configData(id_company);
  });

  configData = async (id) => {
    r = await searchData(`/api/contactsByCompany/${id}`);

    let $select = $(`#contacts`);
    $select.empty();

    $select.append(`<option disabled selected>Seleccionar</option>`);

    r.forEach(function (value) {
      $select.append(
        `<option value = ${value.id_contact}> ${value.firstname} - ${value.lastname} </option>`
      );
    });
  };
});
