$(document).ready(function () {
  /* Cargue tabla de Contactos*/

  tblQuotes = $('#tblQuotes').dataTable({
    pageLength: 50,
    ajax: {
      url: '/api/quotes',
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
        title: 'Cliente',
        data: 'contact',
        className: 'uniqueClassName',
      },
      {
        title: 'Compañia',
        data: 'company_name',
        className: 'uniqueClassName',
      },
      {
        title: 'Precio',
        data: 'price',
        className: 'uniqueClassName',
        render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
      },
      {
        title: 'Metodo de Pago',
        data: 'method',
        className: 'uniqueClassName',
      },
      {
        title: 'Fecha de entrega',
        data: 'delivery_date',
        className: 'uniqueClassName',
      },
      {
        title: 'Acciones',
        data: 'id_quote',
        className: 'uniqueClassName',
        render: function (data) {
          return `
                <a href="/cost/details-quote" <i id="${data}" class="mdi mdi-playlist-check" data-toggle='tooltip' title='Ver Cotización' style="font-size: 30px;color:black" onclick="seeQuote()"></i></a>
                <a href="javascript:;" <i id="${data}" class="bx bx-copy" data-toggle='tooltip' title='Copiar Cotización' style="font-size: 35px;" onclick="copyQuote()"></i></a>
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateQuote" data-toggle='tooltip' title='Actualizar Cotización' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Cotización' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a> 
              `;
        },
      },
    ],
    rowCallback: function (row, data, index) {
      if (data['flag_quote'] == 1) $(row).css('color', 'blue');
    },
  });
});
