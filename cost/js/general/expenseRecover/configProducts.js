$(document).ready(function () {
  loadDataProducts = async () => {
    let r = await searchData('/api/expenseRecoverProducts');

    let $select = $(`.refProduct`);
    $select.empty();

    $select.append(`<option value='0' disabled selected>Seleccionar</option>`);
    $.each(r, function (i, value) {
      $select.append(
        `<option value =${value.id_product}> ${value.reference} </option>`
      );
    });

    let $select1 = $(`.selectNameProduct`);
    $select1.empty();

    $select1.append(`<option value='0' disabled selected>Seleccionar</option>`);
    $.each(r, function (i, value) {
      $select1.append(
        `<option value = ${value.id_product}> ${value.product} </option>`
      );
    });
  };

  loadDataProducts();
});