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
        render: function (data) {
          return `$ ${data.toLocaleString('es-CO', {
            maximumFractionDigits: 2,
          })}`;
        },
      },
      {
        title: 'Acciones',
        data: null,
        className: 'uniqueClassName',
        render: function (data) {
          if (data.status == 0) icon = '/global/assets/images/trash_v.png';
          else icon = '/global/assets/images/trash_x.png';

          return `
                <a href="javascript:;" <i id="${data.id_material}" class="bx bx-edit-alt updateRawMaterials" data-toggle='tooltip' title='Actualizar Materia Prima' style="font-size: 30px;"></i></a>
                <a href="javascript:;"><img src="${icon}" alt="Eliminar Materia Prima" id="${data.id_material}" style="width:30px;height:30px;margin-top:-20px" onclick="deleteFunction()"></a>`;
        },
      },
    ],
  });
});
