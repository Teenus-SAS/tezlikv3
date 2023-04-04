$(document).ready(function () {
  $('#tblHistoricalTrm').dataTable({
    order: [1, 'desc'],
    destroy: true,
    pageLength: 50,
    ajax: {
      url: `/api/historicalTrm`,
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
        title: 'Fecha',
        data: 'date_trm',
        className: 'uniqueClassName',
      },
      {
        title: 'Valor',
        data: 'value_trm',
        className: 'uniqueClassName',
        render: $.fn.dataTable.render.number('.', ',', 2, '$ '),
      },
    ],
  });
});
