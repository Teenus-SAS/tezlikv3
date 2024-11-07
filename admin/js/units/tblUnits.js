$(document).ready(function () {
  /* Cargar unidades */

  tblUnits = $('#tblUnits').dataTable({
    destroy: true,
    pageLength: 50,
    // ajax: {
    //   url: `/api/units`,
    //   dataSrc: '',
    // },
    ajax: function (data, callback, settings) {
      fetch(`/api/units`)
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
    language: {
      url: '/assets/plugins/i18n/Spanish.json',
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
        title: 'Magnitud',
        data: 'magnitude',
        visible: false,
      },
      {
        title: 'Unidad',
        data: 'unit',
        className: 'uniqueClassName',
      },
      {
        title: 'Abreviación',
        data: 'abbreviation',
        className: 'uniqueClassName',
      },
      {
        title: 'Acciones',
        data: 'id_unit',
        className: 'uniqueClassName',
        render: function (data) {
          return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateUnit" data-toggle='tooltip' title='Actualizar Unidad' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Unidad' style="font-size: 30px; color:red" onclick="deleteFunction()"></i></a>         
                `;
        },
      },
    ],
    rowGroup: {
      dataSrc: function (row) {
        return `<th class="text-center" colspan="4" style="font-weight: bold;"> ${row.magnitude} </th>`;
      },
      startRender: function (rows, group) {
        return $('<tr/>').append(group);
      },
      className: 'odd',
    },
  });
});
