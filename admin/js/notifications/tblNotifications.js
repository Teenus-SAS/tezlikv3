$(document).ready(function () {
  tblNotifications = $('#tblNotifications').dataTable({
    destroy: true,
    pageLength: 50,
    ajax: {
      url: `/api/notifications`,
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
        title: 'Descripción',
        data: 'description',
      },
      {
        title: 'Empresa',
        data: 'company',
      },
      {
        title: 'Acciones',
        data: 'id_notification',
        className: 'uniqueClassName',
        render: function (data) {
          return `
          <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateNotification" data-toggle='tooltip' title='Actualizar notificación' style="font-size: 30px;"></i></a>
          <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Notificación' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>
          
          `;
        },
      },
    ],
  });
});
