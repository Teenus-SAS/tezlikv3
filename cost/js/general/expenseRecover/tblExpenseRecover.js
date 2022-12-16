$(document).ready(function () {
  /* Cargue tabla de gastos recuperados */
  loadTableExpenseRecover = () => {
    tblExpenseRecover = $('#tblExpenses').dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: '../../api/expensesRecover',
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
          title: 'Referencia',
          data: 'reference',
        },
        {
          title: 'Producto',
          data: 'product',
        },
        {
          title: 'Porcentaje recuperado',
          data: 'expense_recover',
          className: 'classRight',
          render: function (data) {
            return `${data} %`;
          },
        },
        {
          title: 'Acciones',
          data: 'id_expense_recover',
          className: 'uniqueClassName',
          render: function (data) {
            return `
            <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateExpenseRecover" data-toggle='tooltip' title='Actualizar Gasto' style="font-size: 30px;"></i></a>    
            <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Gasto' style="font-size: 30px;color:red" onclick="deleteExpenseRecover()"></i></a>`;
          },
        },
      ],
    });
  };
});
