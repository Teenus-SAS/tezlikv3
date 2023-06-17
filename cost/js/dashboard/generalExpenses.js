$(document).ready(function () {
  var chartExpensesByPuc;

  loadModalExpenses = (label, value) => {
    $('#totalExpenseByCount').html(`$ ${value.toLocaleString('es-ES')}`);

    let expenses = [];
    let expense = [];
    let expense_value = [];
    let puc;

    for (let i = 0; i < dataExpenses.length; i++) {
      if (dataExpenses[i].count == label) {
        puc = dataExpenses[i].number_count;
        break;
      }
    }
    /*switch (label) {
      case 'Operacionales de administración':
        puc = '51';
        break;

      case 'Gastos de Ventas':
        puc = '52';
        break;
      case 'No operacionales':
        puc = '53';
        break;
    } */

    /* Capturar y ordenar de mayor a menor  */
    for (i = 0; i < dataPucExpenes.length; i++) {
      let number_count = dataPucExpenes[i].number_count.toString();

      if (number_count.startsWith(puc))
        expenses.push({
          number_count: `N° - ${dataPucExpenes[i].number_count} (${dataPucExpenes[i].count})`,
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
      type: 'doughnut',
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
        plugins: {
          legend: {
            display: false,
          },
          datalabels: {
            formatter: (value, ctx) => {
              let sum = 0;
              let dataArr = ctx.chart.data.datasets[0].data;
              dataArr.map((data) => {
                sum += data;
              });
              let percentage =
                ((value * 100) / sum).toLocaleString('es-CO', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2,
                }) + '%';
              return percentage;
            },
            color: 'white',
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
