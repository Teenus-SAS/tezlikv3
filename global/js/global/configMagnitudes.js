$(document).ready(function () {
  /* Cargar data magnitudes */
  loadDataMagnitudes = async () => {
    let data = await searchData('/api/magnitudes');

    let $select = $('#magnitudes');
    $select.empty();
    $select.append(`<option disabled selected>Seleccionar</option>`);
    $.each(data, function (i, value) {
      $select.append(
        `<option value = ${value.id_magnitude}> ${value.magnitude} </option>`
      );
    });
  };
  loadDataMagnitudes();
});
