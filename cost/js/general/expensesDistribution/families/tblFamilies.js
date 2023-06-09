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
      ],
    });
  };

  loadTableFamilies();
});
