
$('#refMaterial').change(async function (e) {
  e.preventDefault();
  let id = this.value;

  $('#nameMaterial option').prop('selected', function () {
    return $(this).val() == id;
  });
});

$('#nameMaterial').change(async function (e) {
  e.preventDefault();
  let id = this.value;

  $('#refMaterial option').prop('selected', function () {
    return $(this).val() == id;
  });
});

/* Cargar data materia prima */
loadDataMaterial = async (op, url) => {
  sessionStorage.removeItem('dataMaterials');
  let data = await searchData(url);

  let dataMaterials = JSON.stringify(data);
  sessionStorage.setItem('dataMaterials', dataMaterials);

  if (op === 1) {
    let $select = $(`#refMaterial`);
    $select.empty();
    $select.append(`<option disabled selected value='0'>Seleccionar</option>`);
    $.each(data, function (i, value) {
      $select.append(
        `<option value = ${value.id_material}> ${value.reference} </option>`
      );
    });

    let $select1 = $(`#nameMaterial`);
    $select1.empty();
    $select1.append(`<option disabled selected value='0'>Seleccionar</option>`);
    $.each(data, function (i, value) {
      $select1.append(
        `<option value = ${value.id_material}> ${value.material} </option>`
      );
    });
  } else {

    let indirectMaterial = data.filter((item) => item.flag_indirect == 1);
    // let indirect = 1;

    if (indirectMaterial.length == 0 || flag_indirect == '0') {
      $('.indirectMaterial').hide();
      indirect = 0;
    } else {
      let $select1 = $(`#refMaterial`);
      $select1.empty();
      $select1.append(`<option disabled selected value='0'>Seleccionar</option>`);
      $.each(indirectMaterial, function (i, value) {
        $select1.append(
          `<option value = ${value.id_material}> ${value.reference} </option>`
        );
      });

      let $select2 = $(`#nameMaterial`);
      $select2.empty();
      $select2.append(`<option disabled selected value='0'>Seleccionar</option>`);
      $.each(indirectMaterial, function (i, value) {
        $select2.append(
          `<option value = ${value.id_material}> ${value.material} </option>`
        );
      });
    }
  }
};

