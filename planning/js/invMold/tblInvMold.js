$(document).ready(function () {
  /* Cargue tabla de Máquinas */

  tblInvMold = $('#tblInvMold').dataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/invMolds',
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
        title: 'Referencia',
        data: 'reference',
        className: 'uniqueClassName',
      },
      {
        title: 'Molde',
        data: 'mold',
        className: 'uniqueClassName',
      },
      {
        title: 'Tiempo Ensamblado',
        data: 'assembly_time',
        className: 'uniqueClassName',
      },
      {
        title: 'Acciones',
        data: 'id_mold',
        className: 'uniqueClassName',
        render: function (data) {
          return `
                <a href="javascript:;" <i class="bx bx-edit-alt updateMold" id="${data}" data-toggle='tooltip' title='Actualizar Molde' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i class="mdi mdi-delete-forever deleteMold" id="${data}" data-toggle='tooltip' title='Eliminar Molde' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
  });
});
