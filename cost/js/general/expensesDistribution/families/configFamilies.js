$(document).ready(function () {
  loadFamilies = async () => {
    let data = await searchData('/api/families');

    let $select = $('.families');

    $select.empty();

    $select.append(`<option disabled selected>Seleccionar</option>`);
    $.each(data, function (i, value) {
      $select.append(
        `<option value = '${value.id_family}'>${value.family} </option>`
      );
    });
  };
});
