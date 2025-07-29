/* Carga los gastos y calcula automáticamente la participación (%) */
loadAllDataExpenses = async () => {
  try {
    const dataExpenses = await searchData('/api/expenses');
    sessionStorage.setItem('dataExpenses', JSON.stringify(dataExpenses));

    let summarizedExpenses;
    if (production_center == '1' && flag_production_center == '1') {
      summarizedExpenses = sumAndGroupExpenses(dataExpenses);
      summarizedExpenses.sort((a, b) => a.puc.localeCompare(b.puc));
    } else {
      summarizedExpenses = calculateParticipation(dataExpenses);
    }

    loadTblAssExpenses(summarizedExpenses, 1);
  } catch (error) {
    console.error('Error loading data:', error);
  }
};

/* Suma y agrupa gastos por ID y calcula participación total */
sumAndGroupExpenses = (data) => {
  const filteredData = data;

  const sumExpenses = filteredData.reduce((acc, expense) => {
    const id = expense.id_expense;
    if (!acc[id]) {
      acc[id] = { ...expense, expense_value: 0 };
    }
    acc[id].expense_value += parseFloat(expense.expense_value || 0);
    return acc;
  }, {});

  const grouped = Object.values(sumExpenses);
  return calculateParticipation(grouped);
};

/* Calcula el % de participación para cada gasto */
calculateParticipation = (data) => {
  // Agrupar por PUC
  const groups = {};
  data.forEach(e => {
    const key = e.puc;
    if (!groups[key]) {
      groups[key] = [];
    }
    groups[key].push(e);
  });

  // Calcular participación por grupo
  const result = [];
  for (const puc in groups) {
    const group = groups[puc];
    const totalGroup = group.reduce((sum, e) => sum + parseFloat(e.expense_value || 0), 0);

    group.forEach(e => {
      const participation = totalGroup > 0 ? (parseFloat(e.expense_value) / totalGroup) * 100 : 0;
      result.push({
        ...e,
        participation: parseFloat(participation.toFixed(2))
      });
    });
  }

  return result;
};


/* Carga tabla con gastos y su participación */
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
        render: (data, type, full, meta) => meta.row + 1,
      },
      { title: 'Puc', data: 'puc', visible: false },
      { title: 'No. Cuenta', data: 'number_count' },
      { title: 'Cuenta', data: 'count' },
      {
        title: 'Valor',
        data: 'expense_value',
        className: 'classRight',
        render: (data) => {
          const value = parseFloat(data);
          return value.toLocaleString('es-CO', {
            minimumFractionDigits: value < 0.01 ? 2 : 0,
            maximumFractionDigits: value < 0.01 ? 9 : 2
          });
        }
      },
      {
        title: 'Porcentaje',
        data: 'participation',
        className: 'classRight',
        render: (data) =>
          `${parseFloat(data).toLocaleString('es-CO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          })} %`,
      },
      {
        title: 'Acciones',
        data: null,
        className: 'uniqueClassName',
        render: (data) => {
          const id = (production_center == '1' && flag_production_center == '1' && data.id_expense_product_center != '0')
            ? data.id_expense_product_center
            : data.id_expense;

          return `<a href="javascript:;" <i id="${id}" class="bx bx-edit-alt updateExpenses" data-toggle='tooltip' title='Actualizar Gasto' style="font-size: 30px;"></i></a>    
                  <a href="javascript:;" <i id="${id}" class="mdi mdi-delete-forever deleteExpenses" data-toggle='tooltip' title='Eliminar Gasto' style="font-size: 30px;color:red" data-op="${op}"></i></a>`;
        },
      },
    ],
    headerCallback: function (thead) {
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
};

$(document).ready(function () {
  loadAllDataExpenses();
});
