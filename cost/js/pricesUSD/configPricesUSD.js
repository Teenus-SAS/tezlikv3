$(document).ready(function () {
  $(document).on('click', '.seeDetail', function (e) {
    sessionStorage.removeItem('idProduct');
    let id_product = this.id;
    sessionStorage.setItem('idProduct', id_product);
  });

  $.ajax({
    url: '/api/prices',
    success: function (r) {
      let $select = $(`#product`);
      $select.empty();

      let prod = r.sort(sortNameProduct);

      $select.append(
        `<option value='0' disabled selected>Seleccionar</option>`
      );
      $.each(prod, function (i, value) {
        $select.append(
          `<option value = ${value.id_product}> ${value.product} </option>`
        );
      });
    },
  });
});
