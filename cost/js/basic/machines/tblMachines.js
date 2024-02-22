$(document).ready(function () {
  /* Cargue tabla de Máquinas */
  inyection == 1 ? visible = true : visible = false;

  tblMachines = $('#tblMachines').dataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/machines',
      dataSrc: '',
    },
    dom: '<"datatable-error-console">frtip',
    language: {
      url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
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
        title: 'Máquina',
        data: 'machine',
        className: 'uniqueClassName',
      },
      {
        title: 'Costo',
        data: 'cost',
        className: 'classRight',
        render: function (data) {
          data = parseFloat(data);

          if (Math.abs(data) < 0.001) { 
              let decimals = contarDecimales(data);
              data = formatNumber(data, decimals);
            } else
              data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
            return `$ ${data}`;
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
        render: function (data) {
          data = parseFloat(data);

          if (Math.abs(data) < 0.001) { 
              let decimals = contarDecimales(data);
              data = formatNumber(data, decimals);
            } else
              data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
            return `${data}`;
        },
      },
      {
        title: 'Ciclos Maquina',
        data: 'cicles_machine',
        className: 'classCenter',
        visible: visible,
      },
      {
        title: 'No Cavidades',
        data: 'cavities',
        className: 'classCenter',
        visible: visible,
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
