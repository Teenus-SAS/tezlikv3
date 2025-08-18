/* Cargue tabla de Proyectos */

tblFactoryLoad = $('#tblFactoryLoad').dataTable({
  destroy: true,
  pageLength: 50,
  ajax: function (data, callback, settings) {
    fetch(`/api/factoryLoad`)
      .then(response => response.json())
      .then(data => {
        // Si el servidor indica recargar la página
        if (data.reload) {
          location.reload();
        } else if (Array.isArray(data) && data.length > 0) {
          // Si `data` es un array, se envía en un objeto para que DataTables lo interprete correctamente
          callback({ data: data });
        } else if (data && data.data && Array.isArray(data.data) && data.data.length > 0) {
          // Verificar estructura `{ data: [...] }`
          callback(data);
        } else {
          console.error("Formato de datos inesperado o datos vacíos:", data);
          callback({ data: [] }); // Envía un array vacío para evitar errores en la tabla
        }
      })
      .catch(error => {
        console.error("Error en la carga de datos:", error);
        callback({ data: [] }); // Enviar un array vacío en caso de error
      });
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
  headerCallback: function (thead, data, start, end, display) {
    $(thead).find("th").css({
      "background-color": "#386297",
      color: "white",
      "text-align": "center",
      "font-weight": "bold",
      padding: "10px",
      border: "1px solid #ddd",
    });
  },
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

