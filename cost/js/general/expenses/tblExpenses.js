$(document).ready(function () {
  /* Cargue tabla de Gastos distribuidos */
  loadAllDataExpenses = async () => {
    try {
      const dataExpenses = await searchData('/api/expenses');
 
      sessionStorage.setItem('dataExpenses', JSON.stringify(dataExpenses));

      if (production_center == '1' && flag_production_center == '1') {
        var summarizedExpenses = sumAndGroupExpenses(dataExpenses);

        summarizedExpenses.sort((a, b) => a.puc.localeCompare(b.puc));
      } else
        var summarizedExpenses = dataExpenses;

      loadTblAssExpenses(summarizedExpenses, 1);
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  sumAndGroupExpenses = (data) => {
    // Objeto para almacenar las sumas
    var sumExpenses = data.reduce(function (acc, expense) {
      var id = expense.id_expense;
      if (!acc[id]) {
        // Si es la primera vez que encontramos este id_expense, creamos un nuevo objeto con todas las claves originales
        acc[id] = Object.assign({}, expense);
        // Inicializamos las sumas en 0
        acc[id].expense_value = 0;
        acc[id].participation = 0;
      }
      // Sumar los valores de expense_value y participation
      acc[id].expense_value += expense.expense_value;
      acc[id].participation += expense.participation;
      return acc;
    }, {});

    // Convertir el objeto de sumas de nuevo en un array
    var summarizedExpenses = Object.values(sumExpenses);
    
    return summarizedExpenses;
  };

  loadTblAssExpenses = (data, op) => {
    // if ($.fn.DataTable.isDataTable('#tblAssExpenses')) {
    //   tblAssExpenses.DataTable().clear().rows.add(data).draw(); 
 
    //   // Renderizar las acciones para cada fila de datos
    //   $('#tblAssExpenses .uniqueClassName').each(function (index) {
    //     var rowData = tblAssExpenses.DataTable().row(index).data();
    //     if (op == 2 || rowData.id_expense_product_center == '0') { 
    //       var id = rowData.id_expense_product_center !== '0' ? rowData.id_expense_product_center : rowData.id_expense;
    //       var actionsHTML = `<a href="javascript:;" <i id="${id}" class="bx bx-edit-alt updateExpenses" data-toggle='tooltip' title='Actualizar Gasto' style="font-size: 30px;"></i></a>    
    //         <a href="javascript:;" <i id="${id}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Gasto' style="font-size: 30px;color:red" onclick="deleteFunction(${id})"></i></a>`;
    //     } else {
    //       var actionsHTML = '';
    //     }

    //     $(this).find('td:eq(3)').html(actionsHTML); // 3 es el índice de la cuarta columna
    //   });
    // } else { 
    tblAssExpenses = $('#tblAssExpenses').dataTable({
      destroy: true,
      pageLength: 50,
      data: data,
      dom: '<"datatable-error-console">frtip',
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      },
      fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
        if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
          console.error(oSettings.json.error);
        }
      },
      columns: [
        {
          title: 'No.',
          data: null,
          className: 'uniqueClassName',
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
        },
        {
          title: 'Puc',
          data: 'puc',
          visible: false,
        },
        {
          title: 'No. Cuenta',
          data: 'number_count',
        },
        {
          title: 'Cuenta',
          data: 'count',
        },
        {
          title: 'Valor',
          data: 'expense_value',
          className: 'classRight',
          render: function (data) {
            data = parseFloat(data);
            if (Math.abs(data) < 0.01) {
              // let decimals = contarDecimales(data);
              // data = formatNumber(data, decimals);
              data = data.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            } else
              data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
            return data;
          },
        },
        {
          title: 'Porcentaje',
          data: 'participation',
          className: 'classRight',
          render: function (data) {
            return `${data.toLocaleString('es-CO', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            })} %`;
          },
        },
        {
          title: 'Acciones',
          data: null,
          className: 'uniqueClassName',
          render: function (data) {
            // if (op == 2 || data.id_expense_product_center == '0') {
            //   var id;
            //   production_center == '1' && flag_production_center == '1' && data.id_expense_product_center != '0' ? id = data.id_expense_product_center
            //     : id = data.id_expense;
              
            //   return `<a href="javascript:;" <i id="${id}" class="bx bx-edit-alt updateExpenses" data-toggle='tooltip' title='Actualizar Gasto' style="font-size: 30px;"></i></a>    
            //          <a href="javascript:;" <i id="${id}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Gasto' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
            // } else {
            //   return '';
            // } 
            var id;
            production_center == '1' && flag_production_center == '1' && data.id_expense_product_center != '0' ? id = data.id_expense_product_center
              : id = data.id_expense;
            
            return `<a href="javascript:;" <i id="${id}" class="bx bx-edit-alt updateExpenses" data-toggle='tooltip' title='Actualizar Gasto' style="font-size: 30px;"></i></a>    
                       <a href="javascript:;" <i id="${id}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Gasto' style="font-size: 30px;color:red" onclick="deleteFunction(${op})"></i></a>`;
          },
        },
      ],
      rowGroup: {
        dataSrc: function (row) {
          return `<th class="text-center" colspan="6" style="font-weight: bold;"> ${row.puc} </th>`;
        },
        startRender: function (rows, group) {
          return $('<tr/>').append(group);
        },
        className: 'odd',
      },
      footerCallback: function (row, data, start, end, display) {
        let expense_value = 0;

        for (i = 0; i < display.length; i++) {
          expense_value += parseFloat(data[display[i]].expense_value);
        }

        $(this.api().column(4).footer()).html(
          `$ ${expense_value.toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })}`
        );
      },
    });
    // }
  }

  loadAllDataExpenses();
});
