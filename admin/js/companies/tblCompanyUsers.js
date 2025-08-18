$(document).ready(function () {
  /* Cargue tabla Usuarios Empresa */

  let idCompany = sessionStorage.getItem('id_company');

  tblCompanyUsers = $('#tblCompanyUsers').dataTable({
    pageLength: 50,
    ajax: function (data, callback, settings) {
      fetch(`/api/companyUsers/${idCompany}`)
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
        title: 'Empresa',
        data: 'company',
      },
      {
        title: 'Nombres',
        data: 'firstname',
      },
      {
        title: 'Apellidos',
        data: 'lastname',
      },
      {
        title: 'E-mail',
        data: 'email',
      },
      {
        title: 'Estado',
        data: 'active',
        render: function (data) {
          if (data === 1) {
            return 'Activo';
          } else {
            return 'Inactivo';
          }
        },
      },
      {
        title: 'Acciones',
        data: 'id_user',
        className: 'uniqueClassName',
        render: function (data) {
          return `
                    <a href="javascript:;" <i id="${data}" class="bx bx-x-circle userStatus" data-toggle='tooltip' title='Estado Usuario' style="font-size: 30px;"></i></a>
                    `;
        },
      },
    ],
  });
});
