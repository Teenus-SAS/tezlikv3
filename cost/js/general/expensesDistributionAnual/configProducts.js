$(document).ready(function () {
  loadExpensesDAProducts = () => {
    $.ajax({
      url: '/api/expensesDistributionAnualProducts',
      success: function (r) {
        // Si el acceso de producto compuesto esta activo filtrar y no mostrar los productos compuestos
        // if (flag_composite_product === '1')
        //   r = r.filter(item => parseInt(item.composite) == 0);

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

        /* Productos creados */
        // data = r.filter(item => parseInt(item.status) != 0 && parseInt(item.units_sold) > 0 && parseInt(item.turnover) > 0);
        // sessionStorage.setItem('dataProducts', JSON.stringify(data));
        
        // $select = $(`#oldNameProduct`);
        // $select.empty();

        // // let ref = r.sort(sortReference);
        // prod = sortFunction(data, 'product');
        
        // $select.append(
        //   `<option value='0' disabled selected>Seleccionar</option>`
        // );
        // $.each(prod, function (i, value) {
        //   $select.append(
        //     `<option value =${value.id_product}> ${value.product} </option>`
        //   );
        // });

        // // filtrar a productos nuevos
        // let new_product = r.filter(item => parseInt(item.status) == 0 && parseInt(item.new_product) == 1
        //   && parseInt(item.units_sold) == 0 && parseInt(item.turnover) == 0);
 
        // $select = $(`#newRefProduct`);
        // $select.empty();
 
        // ref = sortFunction(new_product, 'reference');
        
        // $select.append(
        //   `<option value='0' disabled selected>Seleccionar</option>`
        // );
        // $.each(ref, function (i, value) {
        //   $select.append(
        //     `<option value =${value.id_product}> ${value.reference} </option>`
        //   );
        // });
            
        // $select1 = $(`#newNameProduct`);
        // $select1.empty();
             
        // prod = sortFunction(new_product, 'product');

        // $select1.append(
        //   `<option value='0' disabled selected>Seleccionar</option>`
        // );
        // $.each(prod, function (i, value) {
        //   $select1.append(
        //     `<option value = ${value.id_product}> ${value.product} </option>`
        //   );
        // });
      },
    });
  };
  
  loadExpensesDAProducts(); 
});
