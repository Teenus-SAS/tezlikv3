$(document).ready(function () {
  /* Cargue tabla de Gastos distribuidos */

  tblExpenses = $('#tblExpenses').dataTable({
    destroy: true,
    pageLength: 50,
    ajax: {
      url: `/api/expenses`,
      dataSrc: '',
    },
    language: {
      url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
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
        render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
      },
      {
        title: 'Acciones',
        data: 'id_expense',
        className: 'uniqueClassName',
        render: function (data) {
          return `
          <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateExpenses" data-toggle='tooltip' title='Actualizar Gasto' style="font-size: 30px;"></i></a>    
          <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Gasto' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],

    // rowGroup: {
    //   dataSrc: function (row) {
    //     return `<th class="text-center" colspan="4" style="font-weight: bold;"> ${row.magnitude} </th>`;
    //   },
    //   startRender: function (rows, group) {
    //     return $('<tr/>').append(group);
    //   },
    //   className: 'odd',
    // },

    footerCallback: function (row, data, start, end, display) {
      total = this.api()
        .column(3)
        .data()
        .reduce(function (a, b) {
          return parseInt(a) + parseInt(b);
        }, 0);

      $(this.api().column(3).footer()).html(
        new Intl.NumberFormat('en-US', {
          style: 'currency',
          currency: 'USD',
        }).format(total)
      );
    },
  });
});
