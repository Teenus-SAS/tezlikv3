$(document).ready(function () {
  loadDataProducts = async () => {
    let r = await searchData('/api/expenseRecoverProducts');

    let $select = $(`.refProduct`);
    $select.empty();

    let ref = r.sort(sortReference);

    $select.append(`<option value='0' disabled selected>Seleccionar</option>`);
    $.each(ref, function (i, value) {
      $select.append(
        `<option value =${value.id_product}> ${value.reference} </option>`
      );
    });

    let $select1 = $(`.selectNameProduct`);
    $select1.empty();

    let prod = r.sort(sortNameProduct);

    $select1.append(`<option value='0' disabled selected>Seleccionar</option>`);
    $.each(prod, function (i, value) {
      $select1.append(
        `<option value = ${value.id_product}> ${value.product} </option>`
      );
    });
  };

  loadDataProducts();
});
