$(document).ready(function () {
  /* Sincronizar selects referencia y nombre producto */
  $('.refProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;

    $('.selectNameProduct option').prop('selected', function () {
      return $(this).val() == id;
    });
  });
  
  $('.selectNameProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;

    $('.refProduct option').prop('selected', function () {
      return $(this).val() == id;
    });
  });

  loadAllDataDistributionA = async () => {
    try {
      const dataExpensesDistribution = await searchData('/api/expensesDistributionAnual');

      sessionStorage.setItem('dataExpensesDistributionA', JSON.stringify(dataExpensesDistribution)); 

      loadTableExpensesDistributionA(dataExpensesDistribution);
    } catch (error) {
      console.error('Error loading data:', error);
    }
  }

  /* Cargue tabla de Gastos distribuidos */
  const loadTableExpensesDistributionA = (data) => { 
    if ($.fn.DataTable.isDataTable('#tblExpensesDistributionAnual')) {
      tblExpensesDistributionAnual.DataTable().clear().rows.add(data).draw();
    } else {
      tblExpensesDistributionAnual = $('#tblExpensesDistributionAnual').dataTable({
        destroy: true,
        pageLength: 50,
        data: data,
        dom: '<"datatable-error-console">frtip',
        language: {
          url: '/assets/plugins/i18n/Spanish.json',
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
            title: 'Referencia',
            data: 'reference',
          },
          {
            title: 'Producto',
            data: 'product',
          },
          {
            title: 'Unidades Vendidas (Año)',
            data: 'units_sold',
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
            title: 'Total Ventas (Año)',
            data: 'turnover',
            className: 'classRight',
            render: function (data) {
              data = parseFloat(data);
              if (Math.abs(data) < 0.01) {
                // let decimals = contarDecimales(data);
                // data = formatNumber(data, decimals);
                data = data.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
              } else
                data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
              return `$ ${data}`;
            },
          },
          {
            title: 'Participacion',
            data: 'participation',
            className: 'classRight',
            render: function (data) {
              data = parseFloat(data);
              if (Math.abs(data) < 0.01) {
                // let decimals = contarDecimales(data);
                // data = formatNumber(data, decimals);
                data = data.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
              } else
                data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
              return `${data} %`;
            },
          },
          {
            title: 'Gasto Asignable al Producto',
            data: 'assignable_expense',
            className: 'classRight',
            render: function (data) {
              data = parseFloat(data);
              if (Math.abs(data) < 0.01) {
                // let decimals = contarDecimales(data);
                // data = formatNumber(data, decimals);
                data = data.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
              } else
                data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
              return `$ ${data}`;
            },
          },
          {
            title: 'Acciones',
            data: 'id_expense_distribution_anual',
            className: 'uniqueClassName',
            render: function (data) {
              return `
            <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateExpenseDistributionAnual" data-toggle='tooltip' title='Actualizar Gasto' style="font-size: 30px;"></i></a>    
            <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Gasto' style="font-size: 30px;color:red" onclick="deleteExpenseDistributionA(${data})"></i></a>`;
            },
          },
        ],
        footerCallback: function (row, data, start, end, display) {
          let units_sold = 0;
          let turnover = 0;
          let participation = 0;

          for (let i = 0; i < data.length; i++) {
            units_sold += parseFloat(data[i].units_sold);
            turnover += parseFloat(data[i].turnover);
            participation += parseFloat(data[i].participation);
          }

          $(this.api().column(3).footer()).html(
            units_sold.toLocaleString('es-CO')
          );

          $(this.api().column(4).footer()).html(
            `$ ${turnover.toLocaleString('es-CO')}`
          );

          $(this.api().column(5).footer()).html(
            `${participation.toLocaleString('es-CO', { maximumFractionDigits: 2 })} %`
          );
        },
      });
    }
  };

  loadAllDataDistributionA();
});
