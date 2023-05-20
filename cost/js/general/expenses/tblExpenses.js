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

    rowGroup: {
      dataSrc: function (row) {
        return `<th class="text-center" colspan="5" style="font-weight: bold;"> ${row.puc} </th>`;
      },
      startRender: function (rows, group) {
        return $('<tr/>').append(group);
      },
      className: 'odd',
    },

    footerCallback: function (row, data, start, end, display) {
      let expense_value = 0;

      for (i = 0; i < display.length; i++) {
        expense_value += data[display[i]].expense_value;
      }

      $(this.api().column(4).footer()).html(
        `$ ${expense_value.toLocaleString('es-CO', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0,
        })}`
      );
    },
  });
});
