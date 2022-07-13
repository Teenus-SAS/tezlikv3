$(document).ready(function () {
  /* Cargue tabla de Proyectos */

  tblFactoryLoad = $('#tblFactoryLoad').dataTable({
    destroy: true,
    pageLength: 50,
    ajax: {
      url: `../../api/factoryLoad`,
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
        title: 'Máquina',
        data: 'machine',
        className: 'uniqueClassName',
      },
      {
        title: 'Descripción',
        data: 'input',
        className: 'uniqueClassName',
      },
      {
        title: 'Precio',
        data: 'cost',
        className: 'classCenter',
        render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
      },
      {
        title: 'Valor Minuto',
        data: 'cost_minute',
        className: 'classRight',
        render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
      },
      {
        title: 'Acciones',
        data: 'id_manufacturing_load',
        className: 'uniqueClassName',
        render: function (data) {
          return `
                        <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateFactoryLoad" data-toggle='tooltip' title='Actualizar Carga Fabril' style="font-size: 30px;"></i></a>
                        <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Carga Fabril' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
  });
});
