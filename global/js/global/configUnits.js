$(document).ready(function () {
  /* Cargar unidades por magnitud */
  loadUnitsByMagnitude = async (id_magnitude, op) => {
    let data = await searchData(`/api/units/${id_magnitude}`);

    let $select = $(`#units`);
    $select.empty();

    $select.append(`<option disabled selected>Seleccionar</option>`);
    $.each(data, function (i, value) {
      if (value.id_magnitude == '5' && op == 2) {
        $select.empty();
        $select.append(
          `<option value ='${value.id_unit}' selected disabled> ${value.unit} </option>`
        );
        return false;
      } else $select.append(`<option value = ${value.id_unit}> ${value.unit} </option>`);
    });
  };
});
