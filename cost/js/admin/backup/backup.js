$(document).ready(function () {
  $(document).on('click', '.backup', async function () {
    try {
      $('.loading').show(800);

      let wb = XLSX.utils.book_new();
      let data = [];

      /* Productos */
      let dataProducts = await searchData('/api/products');
      if (dataProducts.length > 0) {
        for (i = 0; i < dataProducts.length; i++) {
          data.push({
            referencia: dataProducts[i].reference,
            producto: dataProducts[i].product,
            rentabilidad: dataProducts[i].profitability,
            comision_ventas: dataProducts[i].commission_sale,
          });
        }

        let ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Productos');
      }

      /* Materia Prima */
      let dataMaterials = await searchData('/api/materials');
      if (dataMaterials.length > 0) {
        data = [];

        for (i = 0; i < dataMaterials.length; i++) {
          data.push({
            referencia: dataMaterials[i].reference,
            material: dataMaterials[i].material,
            magnitud: dataMaterials[i].magnitude,
            unidad: dataMaterials[i].abbreviation,
            costo: dataMaterials[i].cost,
          });
        }

        ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Materias Prima');
      }

      /* Maquinas */
      let dataMachines = await searchData('/api/machines');
      if (dataMachines.length > 0) {
        data = [];

        for (i = 0; i < dataMachines.length; i++) {
          data.push({
            maquina: dataMachines[i].machine,
            costo: dataMachines[i].cost,
            años_depreciacion: dataMachines[i].years_depreciation,
            valor_residual: dataMachines[i].residual_value,
            horas_maquina: dataMachines[i].hours_machine,
            dias_maquina: dataMachines[i].days_machine,
          });
        }

        ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Maquinas');
      }

      /* Procesos */
      let dataProcess = await searchData('/api/process');
      if (dataProcess.length > 0) {
        data = [];

        for (i = 0; i < dataProcess.length; i++) {
          data.push({
            proceso: dataProcess[i].process,
          });
        }

        ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Procesos');
      }

      /* Productos Materiales */
      let dataProductsMaterials = await searchData('/api/allProductsMaterials');
      if (dataProductsMaterials.length > 0) {
        data = [];

        for (i = 0; i < dataProductsMaterials.length; i++) {
          data.push({
            referencia_producto: dataProductsMaterials[i].reference_product,
            producto: dataProductsMaterials[i].product,
            referencia_material: dataProductsMaterials[i].reference_material,
            material: dataProductsMaterials[i].material,
            magnitud: dataProductsMaterials[i].magnitude,
            unidad: dataProductsMaterials[i].abbreviation,
            cantidad: dataProductsMaterials[i].quantity,
          });
        }

        ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'F. Tecnica Materias Prima');
      }

      /* Productos Procesos */
      let dataProductsProcess = await searchData('/api/allProductsProcess');
      if (dataProductsProcess.length > 0) {
        data = [];

        for (i = 0; i < dataProductsProcess.length; i++) {
          data.push({
            referencia_producto: dataProductsProcess[i].reference,
            producto: dataProductsProcess[i].product,
            proceso: dataProductsProcess[i].process,
            maquina: dataProductsProcess[i].machine,
            tiempo_enlistamiento: dataProductsProcess[i].enlistment_time,
            tiempo_operacion: dataProductsProcess[i].operation_time,
          });
        }

        ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'F. Tecnica Procesos');
      }

      /* Carga Fabril */
      let dataFactoryLoad = await searchData('/api/factoryLoad');
      if (dataFactoryLoad.length > 0) {
        data = [];

        for (i = 0; i < dataFactoryLoad.length; i++) {
          data.push({
            maquina: dataFactoryLoad[i].machine,
            descripcion: dataFactoryLoad[i].input,
            costo: dataFactoryLoad[i].cost,
          });
        }

        ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Carga Fabril');
      }

      /* Servicios */
      let dataServices = await searchData('/api/allExternalservices');
      if (dataServices.length > 0) {
        data = [];

        for (i = 0; i < dataServices.length; i++) {
          data.push({
            referencia_producto: dataServices[i].reference,
            producto: dataServices[i].product,
            servicio: dataServices[i].name_service,
            costo: dataServices[i].cost,
          });
        }

        ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Servicios');
      }

      /* Nomina */
      let dataPayroll = await searchData('/api/payroll');
      if (dataPayroll.length > 0) {
        data = [];

        for (i = 0; i < dataPayroll.length; i++) {
          data.push({
            nombres_y_apellidos: dataPayroll[i].employee,
            proceso: dataPayroll[i].process,
            salario_basico: dataPayroll[i].salary,
            transporte: dataPayroll[i].transport,
            dotaciones: dataPayroll[i].endowment,
            horas_extras: dataPayroll[i].extra_time,
            otros_ingresos: dataPayroll[i].bonification,
            horas_trabajo_x_dia: dataPayroll[i].working_days_month,
            dias_trabajo_x_mes: dataPayroll[i].hours_day,
            tipo_nomina: dataPayroll[i].factor_benefit,
            factor: dataPayroll[i].type_contract,
          });
        }

        ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Nomina');
      }

      /* Gastos */
      let dataExpenses = await searchData('/api/expenses');
      if (dataExpenses.length > 0) {
        data = [];

        for (i = 0; i < dataExpenses.length; i++) {
          data.push({
            numero_cuenta: dataExpenses[i].number_count,
            cuenta: dataExpenses[i].count,
            valor: dataExpenses[i].expense_value,
          });
        }

        ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Asignacion de Gastos');
      }

      $('.loading').hide(800);

      XLSX.writeFile(wb, 'backup.xlsx');
    } catch (error) {
      console.log(error);
    }
  });
});
