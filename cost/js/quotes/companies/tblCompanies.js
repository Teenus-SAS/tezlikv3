$(document).ready(function () {
  /* Cargue tabla de Contactos*/

  tblCompanies = $('#tblCompanies').dataTable({
    pageLength: 50,
    ajax: {
      url: '/api/quotesCompanies',
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
        title: 'NIT',
        data: 'nit',
        className: 'uniqueClassName',
      },
      {
        title: 'Compañia',
        data: 'company_name',
        className: 'uniqueClassName',
      },
      {
        title: 'Dirección',
        data: 'address',
        className: 'uniqueClassName',
      },
      {
        title: 'Telefono',
        data: 'phone',
        className: 'uniqueClassName',
      },
      {
        title: 'Ciudad',
        data: 'city',
        className: 'uniqueClassName',
      },
      {
        title: 'Acciones',
        data: 'id_quote_company',
        className: 'uniqueClassName',
        render: function (data) {
          return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateCompany" data-toggle='tooltip' title='Actualizar Compañia' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Compañia' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
  });
});
