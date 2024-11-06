$(document).ready(function () {
  /* Cargar bitacora */

  tblBinnacle = $('#tblBinnacle').dataTable({
    destroy: true,
    pageLength: 50,
    // ajax: {
    //   url: `/api/binnacle`,
    //   dataSrc: '',
    // },
    ajax: function (data, callback, settings) {
      fetch(`/api/binnacle`)
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
        title: 'Nombre',
        data: 'firstname',
        className: 'uniqueClassName',
      },
      {
        title: 'Apellido',
        data: 'lastname',
        className: 'uniqueClassName',
      },
      {
        title: 'Fecha Creación',
        data: 'date_binnacle',
        className: 'uniqueClassName',
      },
      {
        title: 'Actividad Realizada',
        data: 'activity_performed',
        className: 'uniqueClassName',
      },
      {
        title: 'Información Actual',
        data: 'actual_information',
        className: 'uniqueClassName',
      },
      {
        title: 'Información Anterior',
        data: 'previous_information',
        className: 'uniqueClassName',
      },
    ],
  });
});
