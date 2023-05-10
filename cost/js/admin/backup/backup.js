$(document).ready(function () {
  $(document).on('click', '.backup', async function () {
    let wb = XLSX.utils.book_new();

    let dataProducts = await searchData('/api/products');
    if (dataProducts.length > 0) {
      let ws = XLSX.utils.json_to_sheet(dataProducts);
      XLSX.utils.book_append_sheet(wb, ws, 'Productos');
    }

    let dataMaterials = await searchData('/api/materials');
    if (dataMaterials.length > 0) {
      ws = XLSX.utils.json_to_sheet(dataMaterials);
      XLSX.utils.book_append_sheet(wb, ws, 'Materias Prima');
    }

    let dataMachines = await searchData('/api/machines');
    if (dataMachines.length > 0) {
      ws = XLSX.utils.json_to_sheet(dataMachines);
      XLSX.utils.book_append_sheet(wb, ws, 'Maquinas');
    }

    let dataProcess = await searchData('/api/process');
    if (dataProcess.length > 0) {
      ws = XLSX.utils.json_to_sheet(dataProcess);
      XLSX.utils.book_append_sheet(wb, ws, 'Procesos');
    }

    let dataProductsMateriass = await searchData('/api/allProductsMaterials');
    if (dataProductsMateriass.length > 0) {
      ws = XLSX.utils.json_to_sheet(dataProductsMateriass);
      XLSX.utils.book_append_sheet(wb, ws, 'F. Tecnica Materias Prima');
    }

    let dataProductsProcess = await searchData('/api/allProductsProcess');
    if (dataProductsProcess.length > 0) {
      ws = XLSX.utils.json_to_sheet(dataProductsProcess);
      XLSX.utils.book_append_sheet(wb, ws, 'F. Tecnica Procesos');
    }

    let dataFactoryLoad = await searchData('/api/factoryLoad');
    if (dataFactoryLoad.length > 0) {
      ws = XLSX.utils.json_to_sheet(dataFactoryLoad);
      XLSX.utils.book_append_sheet(wb, ws, 'Carga Fabril');
    }

    let dataServices = await searchData('/api/allExternalservices');
    if (dataServices.length > 0) {
      ws = XLSX.utils.json_to_sheet(dataServices);
      XLSX.utils.book_append_sheet(wb, ws, 'Servicios');
    }

    let dataPayroll = await searchData('/api/payroll');
    if (dataPayroll.length > 0) {
      ws = XLSX.utils.json_to_sheet(dataPayroll);
      XLSX.utils.book_append_sheet(wb, ws, 'Nomina');
    }

    let dataExpenses = await searchData('/api/expenses');
    if (dataExpenses.length > 0) {
      ws = XLSX.utils.json_to_sheet(dataExpenses);
      XLSX.utils.book_append_sheet(wb, ws, 'Asignacion de Gastos');
    }

    let dataExpensesDistribution = await searchData(
      '/api/expensesDistribution'
    );
    if (dataExpensesDistribution.length > 0) {
      ws = XLSX.utils.json_to_sheet(dataExpensesDistribution);
      XLSX.utils.book_append_sheet(wb, ws, 'Distribucion de Gastos');
    }

    let dataExpenseRecover = await searchData('/api/expensesRecover');
    if (dataExpenseRecover.length > 0) {
      ws = XLSX.utils.json_to_sheet(dataExpenseRecover);
      XLSX.utils.book_append_sheet(wb, ws, 'Recuperacion de Gastos');
    }

    XLSX.writeFile(wb, 'backup.xlsx');
  });
});
