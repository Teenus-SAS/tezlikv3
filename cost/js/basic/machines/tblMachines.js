$(document).ready(function () {
  /* Cargue tabla de Máquinas */

  tblMachines = $('#tblMachines').dataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/machines',
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
        title: 'Máquina',
        data: 'machine',
        className: 'uniqueClassName',
      },
      {
        title: 'Costo',
        data: 'cost',
        className: 'classRight',
        render: function (data) {
          let decimals = contarDecimales(data);
          let cost = formatNumber(data, decimals);

          return `$ ${cost}`;
        },
      },
      {
        title: 'Años de Depreciación',
        data: 'years_depreciation',
        className: 'classCenter',
      },
      {
        title: 'Depreciación X Minuto',
        data: 'minute_depreciation',
        className: 'classCenter',
        render: $.fn.dataTable.render.number('.', ',', 5),
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
                <a href="javascript:;" <i id="${data.id_machine}" class="bx bx-edit-alt updateMachines" data-toggle='tooltip' title='Actualizar Maquina' style="font-size: 30px;"></i></a>
                <a href="javascript:;"><img src="${icon}" alt="Eliminar Maquina" id="${data.id_machine}" style="width:30px;height:30px;margin-top:-20px" onclick="deleteFunction()"></a>`;
        },
      },
    ],
  });
});
