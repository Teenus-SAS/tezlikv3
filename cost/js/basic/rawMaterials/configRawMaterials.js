$(document).ready(function () {
  /* Cargar data materia prima */
  loadDataMaterial = async () => {
    sessionStorage.removeItem('dataMaterials');
    let data = await searchData('/api/materials');

    let dataMaterials = JSON.stringify(data);
    sessionStorage.setItem('dataMaterials', dataMaterials);

    let $select = $(`#material`);
    $select.empty();
    $select.append(`<option disabled selected>Seleccionar</option>`);
    $.each(data, function (i, value) {
      $select.append(
        `<option value = ${value.id_material}> ${value.material} </option>`
      );
    });
  };

  /* Cargar unidades */
  loadUnits = async () => {
    let data = await searchData('/api/units');

    let $select = $(`#units`);
    $select.empty();
    $select.append(`<option disabled selected>Seleccionar</option>`);
    $.each(data, function (i, value) {
      $select.append(
        `<option value = ${value.id_unit}> ${value.unit} </option>`
      );
    });
  };

  /* Cargar unidades por magnitud */
  $(document).on('change', '#magnitudes', function () {
    let value = this.value;

    $('#units').empty();
    loadUnitsByMagnitude(value);
  });

  loadUnitsByMagnitude = async (id_magnitude) => {
    let data = await searchData(`/api/units/${id_magnitude}`);

    let $select = $(`#units`);

    $select.append(`<option disabled selected>Seleccionar</option>`);
    $.each(data, function (i, value) {
      $select.append(
        `<option value = ${value.id_unit}> ${value.unit} </option>`
      );
    });
  };

  loadDataMaterial();
  loadUnits();
});
