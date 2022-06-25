$(document).ready(function () {
  // Mostrar Tabla planeacion maquinas
  tblPlanMachines = $('#tblPlanMachines').dataTable({
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
        title: 'Trabajadores',
        data: 'number_workers',
        className: 'text-center',
      },
      {
        title: 'Hora x Dia',
        data: 'hours_day',
        className: 'text-center',
      },
      {
        title: 'Hora x Inicio',
        data: null,
        className: 'text-center',
        render: function (data) {
          hourStart = moment(data.hour_start, ['HH:mm']).format('h:mm A');
          return `<p>${hourStart}</p>`;
        },
      },
      {
        title: 'Hora x Fin',
        data: null,
        className: 'text-center',
        render: function (data) {
          hourEnd = moment(data.hour_end, ['HH:mm']).format('h:mm A');
          return `<p>${hourEnd}</p>`;
        },
      },
      {
        title: 'Enero',
        data: null,
        className: 'text-center',
        render: function (data) {
          january = moment(data.january, ['YYYY-MM-DD']).format('DD/MM/YYYY');
          return `<p>${january}</p>`;
        },
      },
      {
        title: 'Febrero',
        data: null,
        className: 'text-center',
        render: function (data) {
          february = moment(data.february, ['YYYY-MM-DD']).format('DD/MM/YYYY');
          return `<p>${february}</p>`;
        },
      },
      {
        title: 'Marzo',
        data: null,
        className: 'text-center',
        render: function (data) {
          march = moment(data.march, ['YYYY-MM-DD']).format('DD/MM/YYYY');
          return `<p>${march}</p>`;
        },
      },
      {
        title: 'Abril',
        data: null,
        className: 'text-center',
        render: function (data) {
          april = moment(data.april, ['YYYY-MM-DD']).format('DD/MM/YYYY');
          return `<p>${april}</p>`;
        },
      },
      {
        title: 'Mayo',
        data: null,
        className: 'text-center',
        render: function (data) {
          may = moment(data.may, ['YYYY-MM-DD']).format('DD/MM/YYYY');
          return `<p>${may}</p>`;
        },
      },
      {
        title: 'Junio',
        data: null,
        className: 'text-center',
        render: function (data) {
          june = moment(data.june, ['YYYY-MM-DD']).format('DD/MM/YYYY');
          return `<p>${june}</p>`;
        },
      },
      {
        title: 'Julio',
        data: null,
        className: 'text-center',
        render: function (data) {
          july = moment(data.july, ['YYYY-MM-DD']).format('DD/MM/YYYY');
          return `<p>${july}</p>`;
        },
      },
      {
        title: 'Agosto',
        data: null,
        className: 'text-center',
        render: function (data) {
          august = moment(data.august, ['YYYY-MM-DD']).format('DD/MM/YYYY');
          return `<p>${august}</p>`;
        },
      },
      {
        title: 'Septiembre',
        data: null,
        className: 'text-center',
        render: function (data) {
          september = moment(data.september, ['YYYY-MM-DD']).format(
            'DD/MM/YYYY'
          );
          return `<p>${september}</p>`;
        },
      },
      {
        title: 'Octubre',
        data: null,
        className: 'text-center',
        render: function (data) {
          october = moment(data.october, ['YYYY-MM-DD']).format('DD/MM/YYYY');
          return `<p>${october}</p>`;
        },
      },
      {
        title: 'Noviembre',
        data: null,
        className: 'text-center',
        render: function (data) {
          november = moment(data.november, ['YYYY-MM-DD']).format('DD/MM/YYYY');
          return `<p>${november}</p>`;
        },
      },
      {
        title: 'Diciembre',
        data: null,
        className: 'text-center',
        render: function (data) {
          december = moment(data.december, ['YYYY-MM-DD']).format('DD/MM/YYYY');
          return `<p>${december}</p>`;
        },
      },
      {
        title: 'Acciones',
        data: 'id_program_machine',
        className: 'uniqueClassName',
        render: function (data) {
          return `
                    <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updatePMachines" data-toggle='tooltip' title='Actualizar Plan Maquina' style="font-size: 30px;"></i></a>
                    <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever deletePMachines" data-toggle='tooltip' title='Eliminar Plan Maquina' style="font-size: 30px;color:red"></i></a>`;
        },
      },
    ],
  });
});
