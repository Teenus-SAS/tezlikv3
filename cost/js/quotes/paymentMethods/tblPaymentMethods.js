$(document).ready(function () {
  /* Cargue tabla de Máquinas */

  tblPaymentMethods = $('#tblPaymentMethods').dataTable({
    pageLength: 50,
    ajax: {
      url: '/api/paymentMethods',
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
