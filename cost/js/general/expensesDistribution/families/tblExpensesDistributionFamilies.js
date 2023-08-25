$(document).ready(function () {
  /* Cargue tabla de Gastos distribuidos */
  loadTableExpensesDistributionFamilies = () => {
    tblExpensesDistribution = $('#tblExpenses').dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: '../../api/expensesDistributionFamilies',
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
          data: 'id_family',
          visible: false,
        },
        {
          title: 'Familia',
          data: 'family',
        },
        {
          title: 'Unidades Vendidas (mes)',
          data: 'units_sold',
          className: 'classRight',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
          title: 'Total Ventas (mes)',
          data: 'turnover',
          className: 'classRight',
          render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
        },
        {
          title: 'Gasto Asignable Familia',
          data: 'assignable_expense',
          className: 'classRight',
          render: $.fn.dataTable.render.number('.', ',', 2, '$ '),
        },
        {
          title: 'Acciones',
          data: null,
          className: 'uniqueClassName',
          render: function () {
            return `
            <a href="javascript:;" <i class="bx bx-edit-alt updateExpenseDistributionFamilies" data-toggle='tooltip' title='Ver Detalle' style="font-size: 30px;"></i></a>
            <a href="javascript:;" <i class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 30px;color:red" onclick="deleteExpenseDistributionFamilies()"></i></a>`;
          },
        },
      ],
      footerCallback: function (row, data, start, end, display) {
        let units_sold = 0;
        let turnover = 0;

        for (let i = 0; i < data.length; i++) {
          units_sold += parseFloat(data[i].units_sold);
          turnover += parseFloat(data[i].turnover);
        }

        $(this.api().column(3).footer()).html(
          units_sold.toLocaleString('es-CO')
        );

        $(this.api().column(4).footer()).html(
          `$ ${turnover.toLocaleString('es-CO')}`
        );
      },
    });
  };
});
