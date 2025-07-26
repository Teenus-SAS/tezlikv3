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
  // Filtrar cuentas que no empiezan con "41"
  //const filteredData = data.filter(expense => !expense.puc.startsWith('41'));
  const filteredData = data
  // Agrupar y sumar los valores
  var sumExpenses = filteredData.reduce(function (acc, expense) {
    var id = expense.id_expense;
    if (!acc[id]) {
      acc[id] = Object.assign({}, expense);
      acc[id].expense_value = 0;
      acc[id].participation = 0;
    }
    acc[id].expense_value += expense.expense_value;
    acc[id].participation += expense.participation;
    return acc;
  }, {});

  return Object.values(sumExpenses);
};


loadTblAssExpenses = (data, op) => {
  tblAssExpenses = $('#tblAssExpenses').dataTable({
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
          var id;
          production_center == '1' && flag_production_center == '1' && data.id_expense_product_center != '0' ? id = data.id_expense_product_center
            : id = data.id_expense;

          return `<a href="javascript:;" <i id="${id}" class="bx bx-edit-alt updateExpenses" data-toggle='tooltip' title='Actualizar Gasto' style="font-size: 30px;"></i></a>    
                       <a href="javascript:;" <i id="${id}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Gasto' style="font-size: 30px;color:red" onclick="deleteFunction(${op})"></i></a>`;
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

      for (let i = 0; i < display.length; i++) {
        const item = data[display[i]];
        if (!item.puc.startsWith('41')) {
          expense_value += parseFloat(item.expense_value);
        }
      }

      $(this.api().column(4).footer()).html(
        `$ ${expense_value.toLocaleString('es-CO', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0,
        })}`
      );
    }
  });
}

$(document).ready(function () {
  loadAllDataExpenses();
});
