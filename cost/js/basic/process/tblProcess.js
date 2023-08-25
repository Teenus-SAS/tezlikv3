$(document).ready(function () {
  /* Cargue tabla de Máquinas */

  tblProcess = $('#tblProcess').dataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/process',
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
        title: 'Proceso',
        data: 'process',
        className: 'uniqueClassName',
      },
      {
        title: 'Acciones',
        data: null,
        className: 'uniqueClassName',
        render: function (data) {
          if (data.status == 0)
            icon = '/global/assets/images/trash_v.png';
          else 
            icon = '/global/assets/images/trash_x.png';
          
          return `
                <a href="javascript:;" <i id="${data.id_process}" class="bx bx-edit-alt updateProcess" data-toggle='tooltip' title='Actualizar Proceso' style="font-size: 30px;"></i></a>
                <a href="javascript:;"><img src="${icon}" alt="Eliminar Proceso" id="${data.id_process}" style="width:30px;height:30px;margin-top:-20px" onclick="deleteFunction()"></a>`;
        },
      },
    ],
  });
});
