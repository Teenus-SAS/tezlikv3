$(document).ready(function () {
  loadExpensesDFamiliesProducts = () => {
    $.ajax({
      url: '/api/expensesDistributionFamiliesProducts',
      success: function (r) {
        // Si el acceso de producto compuesto esta activo filtrar y no mostrar los productos compuestos
        if (flag_composite_product === '1')
          r = r.filter(item => parseInt(item.composite) == 0);
        
        let $select = $(`.refProduct`);
        $select.empty();

        // let ref = r.sort(sortReference);
        let ref = sortFunction(r, 'reference');

        $select.append(
          `<option value='0' disabled selected>Seleccionar</option>`
        );
        $.each(ref, function (i, value) {
          $select.append(
            `<option value =${value.id_product}> ${value.reference} </option>`
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
            `<option value = ${value.id_product}> ${value.product} </option>`
          );
        });
      },
    });
  };
});
