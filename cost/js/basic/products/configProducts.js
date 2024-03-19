$(document).ready(function () {
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

  $.ajax({
    url: '/api/products',
    success: function (r) {
      sessionStorage.setItem('dataProducts', JSON.stringify(r));
      
      let $select = $(`.refProduct`);
      $select.empty();

      // let ref = r.sort(sortReference);
      let ref = sortFunction(r, 'reference');

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

      // let prod = r.sort(sortNameProduct);
      let prod = sortFunction(r, 'product');

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
