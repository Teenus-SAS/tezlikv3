$(document).ready(function () {
  /* Cargue tabla de Lista de precios */
  tblPricesList = $('#tblPricesList').dataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/priceList',
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
        data: 'price_name',
        className: 'uniqueClassName',
      },
      {
        title: 'Acciones',
        data: 'id_price_list',
        className: 'uniqueClassName',
        render: function (data) {
          return `
                  <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updatePriceList" data-toggle='tooltip' title='Actualizar Lista de Precio' style="font-size: 30px;"></i></a>
                  <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Lista de Precio' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
  });
});