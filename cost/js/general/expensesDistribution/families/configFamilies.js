loadFamilies = async (op) => {
  let data = await searchData('/api/distributionByFamilies/families');

  let $select = $('.families');

  $select.empty();

  $select.append(`<option disabled selected>Seleccionar</option>`);
  $.each(data, function (i, value) {
    if (op == 1)
      $select.append(
        `<option value = '${value.id_family}'>${value.family} </option>`
      );
    else if (op == 2 && value.assignable_expense == 0)
      $select.append(
        `<option value = '${value.id_family}'>${value.family} </option>`
      );
  });
};
