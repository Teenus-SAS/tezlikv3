loadExpensesDAProducts = () => {
  $.ajax({
    url: '/api/annualDistribution/expensesDistributionAnualProducts',
    success: function (r) {
      if (r.reload) {
        location.reload();
      }

      // distribuir gastos que no esten asignados
      let data = r.filter(item => parseInt(item.status) == 0);

      let $select = $(`#EDARefProduct`);
      $select.empty();

      // let ref = r.sort(sortReference);
      let ref = sortFunction(data, 'reference');

      $select.append(
        `<option value='0' disabled selected>Seleccionar</option>`
      );
      $.each(ref, function (i, value) {
        $select.append(
          `<option value =${value.id_product}> ${value.reference} </option>`
        );
      });

      let $select1 = $(`#EDANameProduct`);
      $select1.empty();

      let prod = sortFunction(data, 'product');

      $select1.append(
        `<option value='0' disabled selected>Seleccionar</option>`
      );
      $.each(prod, function (i, value) {
        $select1.append(
          `<option value = ${value.id_product}> ${value.product} </option>`
        );
      });

    },
  });
};

$(document).ready(function () {
  loadExpensesDAProducts();
});
