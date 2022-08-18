$(document).ready(function () {
  tblOrderTypes = $('#tblOrderTypes').dataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/orderTypes',
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
        title: 'Tipo de pedido',
        data: 'order_type',
        className: 'uniqueClassName',
      },
      {
        title: 'Acciones',
        data: 'id_order_type',
        className: 'uniqueClassName',
        render: function (data) {
          return `
                  <a href="javascript:;" <i class="bx bx-edit-alt updateOrderType" id="${data}" data-toggle='tooltip' title='Actualizar Tipo Pedido' style="font-size: 30px;"></i></a>
                  <a href="javascript:;" <i class="mdi mdi-delete-forever deleteOrderType" id="${data}" data-toggle='tooltip' title='Eliminar Tipo Pedido' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
  });
});
