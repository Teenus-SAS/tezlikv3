$(document).ready(function () {
  /* Cargue tabla de Máquinas */

  tblMachines = $('#tblMachines').dataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/machines',
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
        title: 'Costo',
        data: 'cost',
        className: 'classRight',
        render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
      },
      {
        title: 'Años de Depreciación',
        data: 'years_depreciation',
        className: 'classCenter',
      },
      {
        title: 'Depreciación X Minuto',
        data: 'minute_depreciation',
        className: 'classCenter',
        render: $.fn.dataTable.render.number('.', ',', 5),
      },
      {
        title: 'Acciones',
        data: 'id_machine',
        className: 'uniqueClassName',
        render: function (data) {
          return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateMachines" data-toggle='tooltip' title='Actualizar Maquina' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever deleteMachines" data-toggle='tooltip' title='Eliminar Maquina' style="font-size: 30px;color:red"></i></a>`;
        },
      },
    ],
  });
});
