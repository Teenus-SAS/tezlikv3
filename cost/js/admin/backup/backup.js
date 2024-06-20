$(document).ready(function () {
  let execute = true;

  $(document).on('click', '.aBackup', async function () {
    try {
      $('.loading').show(800);
      $('.close-btn').show();
      document.body.style.overflow = 'hidden';

      let wb = XLSX.utils.book_new();
      let data = [];

      if (execute == false) return false;
      /* Productos */
      let dataProducts = await searchData('/api/products');
      if (dataProducts.length > 0) {
        for (i = 0; i < dataProducts.length; i++) {
          data.push({
            referencia: dataProducts[i].reference,
            producto: dataProducts[i].product,
            precio_venta: parseFloat(dataProducts[i].sale_price),
            rentabilidad: parseFloat(dataProducts[i].profitability),
            comision_ventas: parseFloat(dataProducts[i].commission_sale),
            sub_producto: `${dataProducts[i].composite == 1 ? 'SI' : 'NO'}`,
            activo: `${dataProducts[i].active == 1 ? 'SI' : 'NO'}`,
          });
        }

        let ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Productos');
      }

      if (execute == false) return false;
      /* Materia Prima */
      let dataMaterials = await searchData('/api/materials');
      if (dataMaterials.length > 0) {
        data = []; 

        dataMaterials.forEach(item => {
          let baseData = {
            referencia: item.reference,
            material: item.material,
            categoria: item.category,
            magnitud: item.magnitude,
            unidad: item.unit,
            costo: flag_currency_usd === '1' && parseFloat(item.flag_usd) == 1 ? parseFloat(item.cost_usd) : parseFloat(item.cost)
          };

          if (flag_currency_usd === '0') {
            if (export_import === '1' && flag_export_import === '1') {
              data.push({
                ...baseData,
                costo_importacion: parseFloat(item.cost_import),
                costo_nacionalizacion: parseFloat(item.cost_export)
              });
            } else {
              data.push(baseData);
            }
          } else {
            if (export_import === '1' && flag_export_import === '1') {
              data.push({
                ...baseData,
                costo_importacion: parseFloat(item.flag_usd) == 0 ? parseFloat(item.cost_import) : parseFloat(item.cost_import_usd),
                costo_nacionalizacion: parseFloat(item.flag_usd) == 0 ? parseFloat(item.cost_export) : parseFloat(item.cost_export_usd),
                tipo_moneda: parseFloat(item.flag_usd) == 0 ? 'COP' : 'USD'
              });
            } else {
              data.push({
                ...baseData,
                tipo_moneda: parseFloat(item.flag_usd) == 0 ? 'COP' : 'USD'
              });
            }
          }
        });

        ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Materias Prima');
      }

      if (execute == false) return false;
      /* Categorias */
      let dataCategory = await searchData('/api/categories');
      if (dataCategory.length > 0) {
        data = [];

        for (i = 0; i < dataCategory.length; i++) {
          data.push({
            categoria: dataCategory[i].category,
          });
        }

        ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Categorias');
      }

      if (execute == false) return false;
      /* Maquinas */
      let dataMachines = await searchData('/api/machines');
      if (dataMachines.length > 0) {
        data = [];

        for (i = 0; i < dataMachines.length; i++) {
          data.push({
            maquina: dataMachines[i].machine,
            costo: dataMachines[i].cost,
            años_depreciacion: parseFloat(dataMachines[i].years_depreciation),
            valor_residual: parseFloat(dataMachines[i].residual_value),
            horas_maquina: parseFloat(dataMachines[i].hours_machine),
            dias_maquina: parseFloat(dataMachines[i].days_machine),
          });
        }

        ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Maquinas');
      }

      if (execute == false) return false;
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

      if (execute == false) return false;
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
            unidad: dataProductsMaterials[i].unit,
            cantidad: parseFloat(dataProductsMaterials[i].quantity),
            desperdicio: parseFloat(dataProductsMaterials[i].waste),
            precio_unitario: parseFloat(dataProductsMaterials[i].cost_product_material),
            tipo: 'MATERIAL',
          });
        }

        /* Productos Compuestos */
        if (flag_composite_product == '1') {
          let dataCompositeProduct = await searchData('/api/allCompositeProducts');
          if (dataCompositeProduct.length > 0) {
            let data1 = [];

            for (i = 0; i < dataCompositeProduct.length; i++) {
              let dataProducts1 = dataProducts.filter((item) => item.id_product == dataCompositeProduct[i].id_product);

              data1.push({
                referencia_producto: dataProducts1[0].reference,
                producto: dataProducts1[0].product,
                referencia_material: dataCompositeProduct[i].reference,
                material: dataCompositeProduct[i].material,
                magnitud: dataCompositeProduct[i].magnitude,
                unidad: dataCompositeProduct[i].unit,
                cantidad: parseFloat(dataCompositeProduct[i].quantity),
                desperdicio: parseFloat(dataProductsMaterials[i].waste),
                precio_unitario: parseFloat(dataCompositeProduct[i].cost_product_material),
                tipo: 'PRODUCTO',
              });
            }
 
            data = [...data, ...data1];
          }
        }

        ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'F. Tecnica Materias Prima');
      }

      if (execute == false) return false;
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
            tiempo_enlistamiento: parseFloat(dataProductsProcess[i].enlistment_time),
            tiempo_operacion: parseFloat(dataProductsProcess[i].operation_time),
            eficiencia: parseFloat(dataProductsProcess[i].efficiency),
            mano_de_obra: parseFloat(dataProductsProcess[i].workforce_cost),
            costo_indirecto: parseFloat(dataProductsProcess[i].indirect_cost),
            maquina_autonoma: dataProductsProcess[i].auto_machine
          });
        }

        ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'F. Tecnica Procesos');
      }

      if (execute == false) return false;
      /* Carga Fabril */
      let dataFactoryLoad = await searchData('/api/factoryLoad');
      if (dataFactoryLoad.length > 0) {
        data = [];

        for (i = 0; i < dataFactoryLoad.length; i++) {
          data.push({
            maquina: dataFactoryLoad[i].machine,
            descripcion: dataFactoryLoad[i].input,
            costo: parseFloat(dataFactoryLoad[i].cost),
          });
        }

        ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Carga Fabril');
      }

      if (execute == false) return false;
      /* Servicios */
      let dataServices = await searchData('/api/allExternalservices');
      if (dataServices.length > 0) {
        data = [];

        for (i = 0; i < dataServices.length; i++) {
          data.push({
            referencia_producto: dataServices[i].reference,
            producto: dataServices[i].product,
            servicio: dataServices[i].name_service,
            costo: parseFloat(dataServices[i].cost),
          });
        }

        ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Servicios');
      }

      if (execute == false) return false;
      /* Nomina */
      let dataPayroll = await searchData('/api/payroll');
      if (dataPayroll.length > 0) {
        data = [];

        for (i = 0; i < dataPayroll.length; i++) {
          data.push({
            nombres_y_apellidos: dataPayroll[i].employee,
            proceso: dataPayroll[i].process,
            salario_basico: parseFloat(dataPayroll[i].salary),
            transporte: parseFloat(dataPayroll[i].transport),
            dotaciones: parseFloat(dataPayroll[i].endowment),
            horas_extras: parseFloat(dataPayroll[i].extra_time),
            otros_ingresos: parseFloat(dataPayroll[i].bonification),
            prestacional: 'NO',
            horas_trabajo_x_dia: parseFloat(dataPayroll[i].hours_day),
            dias_trabajo_x_mes: parseFloat(dataPayroll[i].working_days_month),
            tipo_riesgo: dataPayroll[i].risk_level,
            tipo_nomina: dataPayroll[i].type_contract,
            factor: dataPayroll[i].factor_benefit,
          });
        }

        ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Nomina');
      }

      if (execute == false) return false;
      /* Gastos */
      let dataExpenses = await searchData('/api/expenses');
      if (dataExpenses.length > 0) {
        data = [];

        for (i = 0; i < dataExpenses.length; i++) {
          data.push({
            numero_cuenta: dataExpenses[i].number_count,
            cuenta: dataExpenses[i].count,
            valor: parseFloat(dataExpenses[i].expense_value),
          });
        }

        ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Asignacion de Gastos');
      }

      if (execute == false) return false;
       
      data = [];
      let url, op;

      if (flag_expense == '1') {
        if (flag_expense_distribution == '1') {
          url = '/api/expensesDistribution';
          op = 1;
        } else {
          url = '/api/expensesDistributionFamilies';
          op = 2;
        }
      } else {
        url = '/api/expensesRecover';
        op = 3;
      }

      let dataTypeExpense = await searchData(url);

      if (execute == false) return false;

      const addToSheet = (sheetName, jsonData) => {
        if (jsonData.length > 0) {
          let ws = XLSX.utils.json_to_sheet(jsonData);
          XLSX.utils.book_append_sheet(wb, ws, sheetName);
        }
      };

      const processProductData = (dataTypeExpense) => {
        // return dataTypeExpense.filter(item => flag_composite_product == '1' && item.composite == 0 || flag_composite_product == '0')
        return dataTypeExpense
          .map(item => ({
            referencia_producto: item.reference,
            producto: item.product,
            unidades_vendidas: parseFloat(item.units_sold),
            volumen_ventas: parseFloat(item.turnover),
          }));
      };

      const processFamilyData = (dataTypeExpense) => {
        return dataTypeExpense.map(item => ({
          familia: item.family,
          unidades_vendidas: parseFloat(item.units_sold),
          volumen_ventas: parseFloat(item.turnover),
        }));
      };

      const processRecoveryData = (dataTypeExpense) => {
        return dataTypeExpense.map(item => ({
          reference_producto: item.reference,
          producto: item.product,
          porcentaje_recuperado: parseFloat(item.expense_recover),
        }));
      };

      switch (op) {
        case 1:
          data = processProductData(dataTypeExpense);
          addToSheet('Distribucion Producto', data);
          break;
        case 2:
          data = processFamilyData(dataTypeExpense);
          addToSheet('Distribucion Familia', data);
          break;
        case 3:
          data = processRecoveryData(dataTypeExpense);
          addToSheet('Recuperacion Gasto', data);
          break;
      }

      if (anual_expense == '1') {
        // Gastos anuales
        let dataExpenses = await searchData('/api/expensesAnual');
        if (dataExpenses.length > 0) {
          data = [];

          for (i = 0; i < dataExpenses.length; i++) {
            data.push({
              numero_cuenta: dataExpenses[i].number_count,
              cuenta: dataExpenses[i].count,
              valor: parseFloat(dataExpenses[i].expense_value),
            });
          }

          ws = XLSX.utils.json_to_sheet(data);
          XLSX.utils.book_append_sheet(wb, ws, 'Asignacion de Gastos Anuales');
        }

        if (execute == false) return false;
        // Distribucion Anuales
        let dataExpensesDistributionA = await searchData('/api/expensesDistributionAnual');
        if (dataExpensesDistributionA.length > 0) {
          data = [];

          for (i = 0; i < dataExpensesDistributionA.length; i++) {
            data.push({
              reference_producto: dataExpensesDistributionA[i].reference,
              producto: dataExpensesDistributionA[i].product,
              unidades_vendidas: parseFloat(dataExpensesDistributionA[i].units_sold),
              volumen_ventas: parseFloat(dataExpensesDistributionA[i].turnover),
              volumen_ventas: parseFloat(dataExpensesDistributionA[i].turnover),
            });
          }

          ws = XLSX.utils.json_to_sheet(data);
          XLSX.utils.book_append_sheet(wb, ws, 'Distribucion de Gastos Anuales');
        }
      }
 
      $('.loading').hide(800);
      document.body.style.overflow = '';
      execute = true;

      XLSX.writeFile(wb, 'backup.xlsx');
    } catch (error) {
      console.log(error);
    }
  });

  $('.close-btn').click(function (e) {
    e.preventDefault();
    
    $('.loading').hide(800);
    document.body.style.overflow = '';
    execute = false;
  });
});
