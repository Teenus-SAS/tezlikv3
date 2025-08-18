$(document).ready(function () {
  /* Cargue tabla de Gastos distribuidos */
  loadAllDataExpensesAnual = async () => {
    try {
      const dataExpenses = await searchData('/api/expensesAnual');

      loadTblAssExpensesAnual(dataExpenses);
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  const loadTblAssExpensesAnual = (data, op) => {

    tblAssExpensesAnual = $('#tblAssExpensesAnual').dataTable({
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
            var id = data.id_expense_anual;

            return `<a href="javascript:;" <i id="${id}" class="bx bx-edit-alt updateExpensesAnual" data-toggle='tooltip' title='Actualizar Gasto' style="font-size: 30px;"></i></a>    
                       <a href="javascript:;" <i id="${id}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Gasto' style="font-size: 30px;color:red" onclick="deleteExpenseDA(${op})"></i></a>`;
          },
        },
      ],
      headerCallback: function (thead, data, start, end, display) {
        $(thead).find("th").css({
          "background-color": "#386297",
          color: "white",
          "text-align": "center",
          "font-weight": "bold",
          padding: "10px",
          border: "1px solid #ddd",
        });
      },
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

  loadAllDataExpensesAnual();
});
