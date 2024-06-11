$(document).ready(function () {
  /* Cargue tabla de Máquinas */

  tblPaymentMethods = $('#tblPaymentMethods').dataTable({
    pageLength: 50,
    ajax: {
      url: '/api/paymentMethods',
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
        title: 'Metodo',
        data: 'method',
        className: 'uniqueClassName',
      },
      {
        title: 'Acciones',
        data: 'id_method',
        className: 'uniqueClassName',
        render: function (data) {
          return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updatePaymentMethod" data-toggle='tooltip' title='Actualizar Metodo' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Metodo' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
  });
});
