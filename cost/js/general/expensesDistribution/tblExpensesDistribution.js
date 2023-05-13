$(document).ready(function () {
  /* Sincronizar selects referencia y nombre producto */
  $('.refProduct').change(function (e) {
    e.preventDefault();
    $('.selectNameProduct option').removeAttr('selected');
    let id = this.value;
    $(`.selectNameProduct option[value=${id}]`).attr('selected', true);
  });

  $('.selectNameProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    $('.refProduct option').removeAttr('selected');
    $(`.refProduct option[value=${id}]`).attr('selected', true);
  });

  /* Cargue tabla de Gastos distribuidos */
  loadTableExpensesDistribution = () => {
    tblExpensesDistribution = $('#tblExpenses').dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: '../../api/expensesDistribution',
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
          title: 'Unidades Vendidas',
          data: 'units_sold',
          className: 'classRight',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
          title: 'Volumen de Ventas',
          data: 'turnover',
          className: 'classRight',
          render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
        },
        {
          title: 'Gasto Asignable al Producto',
          data: 'assignable_expense',
          className: 'classRight',
          render: $.fn.dataTable.render.number('.', ',', 2, '$ '),
        },
        {
          title: 'Acciones',
          data: 'id_expenses_distribution',
          className: 'uniqueClassName',
          render: function (data) {
            return `
          <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateExpenseDistribution" data-toggle='tooltip' title='Actualizar Gasto' style="font-size: 30px;"></i></a>    
          <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Gasto' style="font-size: 30px;color:red" onclick="deleteExpenseDistribution()"></i></a>`;
          },
        },
      ],
      footerCallback: function (row, data, start, end, display) {
        let units_sold = 0;
        let turnover = 0;

        for (let i = 0; i < data.length; i++) {
          units_sold += data[i].units_sold;
          turnover += data[i].turnover;
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
