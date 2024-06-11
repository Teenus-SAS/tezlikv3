$(document).ready(function () {
  /* Cargar bitacora */

  tblBinnacle = $('#tblBinnacle').dataTable({
    destroy: true,
    pageLength: 50,
    ajax: {
      url: `/api/binnacle`,
      dataSrc: '',
    },
    language: {
      url: '/assets/plugins/i18n/Spanish.json',
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
        data: 'firstname',
        className: 'uniqueClassName',
      },
      {
        title: 'Apellido',
        data: 'lastname',
        className: 'uniqueClassName',
      },
      {
        title: 'Fecha Creación',
        data: 'date_binnacle',
        className: 'uniqueClassName',
      },
      {
        title: 'Actividad Realizada',
        data: 'activity_performed',
        className: 'uniqueClassName',
      },
      {
        title: 'Información Actual',
        data: 'actual_information',
        className: 'uniqueClassName',
      },
      {
        title: 'Información Anterior',
        data: 'previous_information',
        className: 'uniqueClassName',
      },
    ],
  });
});
