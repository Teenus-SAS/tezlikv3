$(document).ready(function () {
  var chartExpensesByPuc;

  loadModalExpenses = (label) => {
    let expenses = [];
    let expense = [];
    let expense_value = [];
    let puc;

    switch (label) {
      case 'Operacionales de administración':
        puc = '51';
        break;

      case 'Gastos de Ventas':
        puc = '52';
        break;
      case 'No operacionales':
        puc = '53';
        break;
    }

    /* Capturar y ordenar de mayor a menor  */
    for (i = 0; i < dataPucExpenes.length; i++) {
      let number_count = dataPucExpenes[i].number_count.toString();

      if (number_count.startsWith(puc))
        expenses.push({
          number_count: `N° - ${dataPucExpenes[i].number_count}`,
          expense_value: dataPucExpenes[i].expense_value,
        });
    }

    expenses.sort(function (a, b) {
      return b['expense_value'] - a['expense_value'];
    });

    /* Guardar datos para grafica */

    expenses.length > length ? (count = length) : (count = expenses.length);

    for (i = 0; i < count; i++) {
      expense.push(expenses[i].number_count);
      expense_value.push(expenses[i].expense_value);
    }

    const cmc = document.getElementById('chartExpensesByPuc');

    chartExpensesByPuc ? chartExpensesByPuc.destroy() : chartExpensesByPuc;

    chartExpensesByPuc = new Chart(cmc, {
      plugins: [ChartDataLabels],
      type: 'bar',
      data: {
        labels: expense,
        formatter: function (value, context) {
          return context.chart.data.labels[context.dataIndex];
        },
        datasets: [
          {
            data: expense_value,
            backgroundColor: getRandomColor(count),
            borderWidth: 1,
          },
        ],
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
          },
          x: {
            display: false,
          },
        },
        plugins: {
          legend: {
            display: false,
          },
          datalabels: {
            anchor: 'end',
            formatter: (expense_value) =>
              expense_value.toLocaleString('es-CO', {
                maximumFractionDigits: 0,
              }),
            color: 'black',
            font: {
              size: '12',
              weight: 'normal',
            },
          },
        },
      },
    });

    $('#pucName').html(`${puc} - ${label}`);
    $('#modalExpensesByPuc').modal('show');
  };
});
