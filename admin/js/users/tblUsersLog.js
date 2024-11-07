$(document).ready(function () {  
    /* Cargue tabla Usuarios */
  
    // const loadtableCompanies = (stat) => {
    tblCompanies = $("#tblUsersLog").DataTable({
      destroy: true,
      pageLength: 50,
      // ajax: {
      //   url: `/api/lastLoginUsers`,
      //   dataSrc: "",
      // },
      ajax: function (data, callback, settings) {
        fetch(`/api/lastLoginUsers`)
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
        url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json",
      },
      columns: [
        {
          title: "No.",
          data: null,
          className: "uniqueClassName",
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
        },
        {
          title: "Empresa",
          data: "company",
        },
        {
          title: "Nombre",
          data: "firstname",
        },
        {
          title: "Apellido",
          data: "lastname",
        },
        {
          title: "Última Sesión",
          data: "last_login",
        },
        {
          title: "Acciones",
          data: "id_user",
          className: "uniqueClassName",
          render: function (data) {
            return `<a href="javascript:;" <i id="${data}" class="bx bx-user-x text-danger closeSession" data-toggle='tooltip' title='Cerrar Sesión' style="font-size: 30px;"></i></a>`;
          },
        },
      ],
    });
    // }
  });
  