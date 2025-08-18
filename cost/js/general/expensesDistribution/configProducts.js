const appendOptions = ($select, data, valueKey, textKey) => {
  $select.empty();
  $select.append(`<option value='0' disabled selected>Seleccionar</option>`);
  $.each(data, (i, value) => {
    $select.append(`<option value=${value[valueKey]}>${value[textKey]}</option>`);
  });
};

loadExpensesDProducts = () => {
  $.ajax({
    url: '/api/distribution/expensesDistributionProducts',
    success: function (r) {
      if (r.reload) {
        location.reload();
      }

      // Filtrar gastos no asignados
      let unassignedData = r.filter(item => parseInt(item.status) === 0);

      // Opciones de producto referenciado
      appendOptions($(`.refProduct`), sortFunction(unassignedData, 'reference'), 'id_product', 'reference');
      appendOptions($(`.selectNameProduct`), sortFunction(unassignedData, 'product'), 'id_product', 'product');

      // Productos creados
      let createdData = r.filter(item => parseInt(item.status) !== 0 && parseInt(item.units_sold) > 0 && parseInt(item.turnover) > 0);
      sessionStorage.setItem('dataProducts', JSON.stringify(createdData));
      appendOptions($(`#refOldProduct`), sortFunction(createdData, 'product'), 'id_product', 'reference');
      appendOptions($(`#nameOldProduct`), sortFunction(createdData, 'product'), 'id_product', 'product');

      // Productos nuevos
      let newProductData = r.filter(item => parseInt(item.status) === 0 && parseInt(item.new_product) === 1 && parseInt(item.units_sold) === 0 && parseInt(item.turnover) === 0);
      appendOptions($(`#newRefProduct`), sortFunction(newProductData, 'reference'), 'id_product', 'reference');
      appendOptions($(`#newNameProduct`), sortFunction(newProductData, 'product'), 'id_product', 'product');
    },
  });
};

$(document).ready(function () {
  loadExpensesDProducts();
});
