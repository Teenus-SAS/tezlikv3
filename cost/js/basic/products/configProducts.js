$(document).ready(function () {
  $('#refCompositeProduct').change(async function (e) {
    e.preventDefault();
    let id = this.value;

    $('#compositeProduct option').removeAttr('selected');
    $(`#compositeProduct option[value=${id}]`).prop('selected', true);
  });

  $('#compositeProduct').change(async function (e) {
    e.preventDefault();
    let id = this.value;

    $('#refCompositeProduct option').removeAttr('selected');
    $(`#refCompositeProduct option[value=${id}]`).prop('selected', true);
  });

  $.ajax({
    url: '/api/products',
    success: function (r) {
      sessionStorage.setItem('dataProducts', JSON.stringify(r));
      
      let $select = $(`.refProduct`);
      $select.empty();

      let ref = r.sort(sortReference);

      $select.append(
        `<option value='0' disabled selected>Seleccionar</option>`
      );
      $.each(ref, function (i, value) {
        $select.append(
          `<option value ='${value.id_product}' class='${value.composite}'> ${value.reference} </option>`
        );
      });

      let $select1 = $(`.selectNameProduct`);
      $select1.empty();

      let prod = r.sort(sortNameProduct);

      $select1.append(
        `<option value='0' disabled selected>Seleccionar</option>`
      );
      $.each(prod, function (i, value) {
        $select1.append(
          `<option value ='${value.id_product}' class='${value.composite}'> ${value.product} </option>`
        );
      });

      let compositeProduct = prod.filter(item => item.composite == 1);
      let $select2 = $(`#refCompositeProduct`);
      $select2.empty();

      $select2.append(
        `<option value='0' disabled selected>Seleccionar</option>`
      );
      $.each(compositeProduct, function (i, value) {
        $select2.append(
          `<option value ="${value.id_product}"> ${value.reference} </option>`
        );
      });

      let $select3 = $(`#compositeProduct`);
      $select3.empty();

      $select3.append(
        `<option value='0' disabled selected>Seleccionar</option>`
      );
      $.each(compositeProduct, function (i, value) {
        $select3.append(
          `<option value ="${value.id_product}"> ${value.product} </option>`
        );
      });
    },
  });
});
