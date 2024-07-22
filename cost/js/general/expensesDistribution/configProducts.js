$(document).ready(function () {
  // loadExpensesDProducts = () => {
  //   $.ajax({
  //     url: '/api/expensesDistributionProducts',
  //     success: function (r) {
  //       // Si el acceso de producto compuesto esta activo filtrar y no mostrar los productos compuestos
  //       // if (flag_composite_product === '1')
  //       //   r = r.filter(item => parseInt(item.composite) == 0);

  //       // distribuir gastos que no esten asignados
  //       let data = r.filter(item => parseInt(item.status) == 0);
        
  //       let $select = $(`.refProduct`);
  //       $select.empty();

  //       // let ref = r.sort(sortReference);
  //       let ref = sortFunction(data, 'reference');
        
  //       $select.append(
  //         `<option value='0' disabled selected>Seleccionar</option>`
  //       );
  //       $.each(ref, function (i, value) {
  //         $select.append(
  //           `<option value =${value.id_product}> ${value.reference} </option>`
  //         );
  //       });
            
  //       let $select1 = $(`.selectNameProduct`);
  //       $select1.empty();
             
  //       let prod = sortFunction(data, 'product');

  //       $select1.append(
  //         `<option value='0' disabled selected>Seleccionar</option>`
  //       );
  //       $.each(prod, function (i, value) {
  //         $select1.append(
  //           `<option value = ${value.id_product}> ${value.product} </option>`
  //         );
  //       });

  //       /* Productos creados */
  //       data = r.filter(item => parseInt(item.status) != 0 && parseInt(item.units_sold) > 0 && parseInt(item.turnover) > 0);
  //       sessionStorage.setItem('dataProducts', JSON.stringify(data));
        
  //       $select = $(`#oldNameProduct`);
  //       $select.empty();

  //       // let ref = r.sort(sortReference);
  //       prod = sortFunction(data, 'product');
        
  //       $select.append(
  //         `<option value='0' disabled selected>Seleccionar</option>`
  //       );
  //       $.each(prod, function (i, value) {
  //         $select.append(
  //           `<option value =${value.id_product}> ${value.product} </option>`
  //         );
  //       });

  //       // filtrar a productos nuevos
  //       let new_product = r.filter(item => parseInt(item.status) == 0 && parseInt(item.new_product) == 1
  //         && parseInt(item.units_sold) == 0 && parseInt(item.turnover) == 0);
 
  //       $select = $(`#newRefProduct`);
  //       $select.empty();
 
  //       ref = sortFunction(new_product, 'reference');
        
  //       $select.append(
  //         `<option value='0' disabled selected>Seleccionar</option>`
  //       );
  //       $.each(ref, function (i, value) {
  //         $select.append(
  //           `<option value =${value.id_product}> ${value.reference} </option>`
  //         );
  //       });
            
  //       $select1 = $(`#newNameProduct`);
  //       $select1.empty();
             
  //       prod = sortFunction(new_product, 'product');

  //       $select1.append(
  //         `<option value='0' disabled selected>Seleccionar</option>`
  //       );
  //       $.each(prod, function (i, value) {
  //         $select1.append(
  //           `<option value = ${value.id_product}> ${value.product} </option>`
  //         );
  //       });
  //     },
  //   });
  // };
  const appendOptions = ($select, data, valueKey, textKey) => {
    $select.empty();
    $select.append(`<option value='0' disabled selected>Seleccionar</option>`);
    $.each(data, (i, value) => {
      $select.append(`<option value=${value[valueKey]}>${value[textKey]}</option>`);
    });
  };

  loadExpensesDProducts = () => {
    $.ajax({
      url: '/api/expensesDistributionProducts',
      success: function (r) {
        // Filtrar gastos no asignados
        let unassignedData = r.filter(item => parseInt(item.status) === 0);

        // Opciones de producto referenciado
        appendOptions($(`.refProduct`), sortFunction(unassignedData, 'reference'), 'id_product', 'reference');
        appendOptions($(`.selectNameProduct`), sortFunction(unassignedData, 'product'), 'id_product', 'product');

        // Productos creados
        let createdData = r.filter(item => parseInt(item.status) !== 0 && parseInt(item.units_sold) > 0 && parseInt(item.turnover) > 0);
        sessionStorage.setItem('dataProducts', JSON.stringify(createdData));
        appendOptions($(`#refOldProduct`), sortFunction(createdData, 'product'), 'id_product', 'product');
        appendOptions($(`#oldNameProduct`), sortFunction(createdData, 'product'), 'id_product', 'product');

        // Productos nuevos
        let newProductData = r.filter(item => parseInt(item.status) === 0 && parseInt(item.new_product) === 1 && parseInt(item.units_sold) === 0 && parseInt(item.turnover) === 0);
        appendOptions($(`#newRefProduct`), sortFunction(newProductData, 'reference'), 'id_product', 'reference');
        appendOptions($(`#newNameProduct`), sortFunction(newProductData, 'product'), 'id_product', 'product');
      },
    });
  };

  loadExpensesDProducts();
});
