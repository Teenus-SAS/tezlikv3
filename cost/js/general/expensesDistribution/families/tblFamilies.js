$(document).ready(function () {
  loadTableFamilies = () => {
    $('#tblFamilies').dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: '../../api/families',
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
          title: 'Nombre',
          data: 'family',
        },
        //   {
        //     title: 'Acciones',
        //     data: 'id_expenses_distribution',
        //     className: 'uniqueClassName',
        //     render: function (data) {
        //       return `
        //         <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateExpenseDistribution" data-toggle='tooltip' title='Actualizar Gasto' style="font-size: 30px;"></i></a>
        //         <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Gasto' style="font-size: 30px;color:red" onclick="deleteExpenseDistribution()"></i></a>`;
        //     },
        //   },
      ],
    });
  };

  loadTableFamilies();
});
