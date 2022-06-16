$(document).ready(function () {
  // Mostrar Tabla planeacion maquinas
  tblMachines = $('#tblMachines').dataTable({
    pageLength: 50,
    ajax: {
      url: '/api/planningMachines',
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
        title: 'Trabajadas',
        data: 'number_workers',
        className: 'text-center',
      },
      {
        title: 'Hora Dia',
        data: 'hours_day',
        className: 'text-center',
      },
      {
        title: 'Hora Inicio',
        data: 'hour_start',
        className: 'text-center',
      },
      {
        title: 'Hora Fin',
        data: 'hour_end',
        className: 'text-center',
      },
      {
        title: 'Año',
        data: 'year',
        className: 'text-center',
      },
      {
        title: 'Acciones',
        data: 'id_program_machines',
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
