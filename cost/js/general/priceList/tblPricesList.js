$(document).ready(function () {
  /* Cargue tabla de Lista de precios */
  tblPricesList = $('#tblPricesList').dataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/priceList',
      dataSrc: '',
    },
    dom: '<"datatable-error-console">frtip',
    language: {
      url: '/assets/plugins/i18n/Spanish.json',
    },
    fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
      if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
        console.error(oSettings.json.error);
      }
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
