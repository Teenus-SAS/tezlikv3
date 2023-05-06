$(document).ready(function () {
  /* Cargue tabla de Materias Primas */

  tblRawMaterials = $('#tblRawMaterials').dataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/materials',
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
        title: 'Materia Prima',
        data: 'material',
        className: 'uniqueClassName',
      },
      {
        title: 'Unidad',
        data: 'abbreviation',
        className: 'classCenter',
      },
      {
        width: '80px',
        title: 'Precio',
        data: 'cost',
        className: 'classRight',
        render: $.fn.dataTable.render.number('.', ',', 2, '$ '),
      },
      {
        title: 'Acciones',
        data: null,
        className: 'uniqueClassName',
        render: function (data) {
          if (data.status == 0) icon = 'fa-solid fa-trash-xmark';
          else icon = 'fa-solid fa-trash-check';

          return `
                <a href="javascript:;" <i id="${data.id_material}" class="bx bx-edit-alt updateRawMaterials" data-toggle='tooltip' title='Actualizar Materia Prima' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data.id_material}" class="${icon}" data-toggle='tooltip' title='Eliminar Materia Prima' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
  });
});
