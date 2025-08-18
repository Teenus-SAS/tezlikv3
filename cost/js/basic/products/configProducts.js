
$('#refCompositeProduct').change(async function (e) {
  e.preventDefault();
  let id = this.value;

  $('#compositeProduct option').prop('selected', function () {
    return $(this).val() == id;
  });
});

$('#compositeProduct').change(async function (e) {
  e.preventDefault();
  let id = this.value;

  $('#refCompositeProduct option').prop('selected', function () {
    return $(this).val() == id;
  });
});


function economyScaleOtions(selector, data, property) {
  let $select = $(selector);
  $select.empty();

  data = data.filter(item => item.composite == 0);

  $select.append(`<option value='0' disabled selected>Seleccionar</option>`);

  $.each(data, function (i, value) {
    $select.append(`<option value ="${value.id_product}"> ${value[property]} </option>`);
  });
};

