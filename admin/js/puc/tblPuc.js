$(document).ready(function () {
    /* Cargue tabla PUC */
  
    tblPUC = $('#tblPUC').dataTable({
      destroy: true,
      pageLength: 50,
      // ajax: {
      //   url: `/api/findPUC`,
      //   dataSrc: '',
      // },
      ajax: function (data, callback, settings) {
        fetch(`/api/findPUC`)
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
          title: 'Número de Cuenta',
          data: 'number_count',
        },
        {
          title: 'Cuenta',
          data: 'count',
        },
        {
          title: 'Acciones',
          data: 'id_puc',
          className: 'uniqueClassName',
          render: function (data) {
            return `<a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updatePuc" data-toggle='tooltip' title='Actualizar Cuenta' style="font-size: 30px;"></i></a>`;
          },
        },
      ],
    });
  });
  
