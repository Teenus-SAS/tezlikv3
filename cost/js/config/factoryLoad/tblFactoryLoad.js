$(document).ready(function () {
  /* Cargue tabla de Proyectos */

  tblFactoryLoad = $('#tblFactoryLoad').dataTable({
    destroy: true,
    pageLength: 50,
    ajax: {
      url: `../../api/factoryLoad`,
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
        title: 'Máquina',
        data: 'machine',
        className: 'uniqueClassName',
      },
      {
        title: 'Descripción',
        data: 'input',
        className: 'uniqueClassName',
      },
      {
        title: 'Precio',
        data: 'cost',
        className: 'classCenter',
        render: function (data) {
          data = parseFloat(data);

          if (Math.abs(data) < 0.01) {
            data = data.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
          } else
            data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
          return `$ ${data}`;
        },
      },
      {
        title: 'Valor Minuto',
        data: 'cost_minute',
        className: 'classRight',
        render: function (data) {
          data = parseFloat(data);

          if (Math.abs(data) < 0.01) {
            data = data.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
          } else
            data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
          return `$ ${data}`;
        },
      },
      {
        title: 'Acciones',
        data: 'id_manufacturing_load',
        className: 'uniqueClassName',
        render: function (data) {
          return `<a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateFactoryLoad" data-toggle='tooltip' title='Actualizar Carga Fabril' style="font-size: 30px;"></i></a>
          <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Carga Fabril' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
    footerCallback: function (row, data, start, end, display) {
      let cost = 0;
      let cost_minute = 0; 

      for (let i = 0; i < display.length; i++) {
        cost += parseFloat(data[display[i]].cost);
        cost_minute += parseFloat(data[display[i]].cost_minute); 
      }

      $(this.api().column(3).footer()).html(
        `$ ${cost.toLocaleString('es-CO')}`
      );

      $(this.api().column(4).footer()).html(
        `$ ${cost_minute.toLocaleString('es-CO')}`
      );
    },
  });
});
