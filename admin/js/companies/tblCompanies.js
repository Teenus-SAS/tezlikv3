$(document).ready(function () {
  $('.statusCompanies').on('click', function () {
    if ($(this).is(':checked')) loadtableCompanies(1);
    else loadtableCompanies(0);
  });

  const loadtableCompanies = (stat) => {
    tblCompanies = $('#tblCompanies').dataTable({
      destroy: true,
      pageLength: 50,

      ajax: function (data, callback, settings) {
        fetch(`/api/customers/${stat}`)
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
          title: 'Departamento',
          data: 'state',
        },
        {
          title: 'Ciudad',
          data: 'city',
        },
        {
          title: 'País',
          data: 'country',
        },
        {
          title: 'Dirección',
          data: 'address',
        },
        {
          title: 'Teléfono',
          data: 'telephone',
        },
        {
          title: 'NIT',
          data: 'nit',
        },
        {
          title: 'Logo',
          data: 'logo',
          render: function (data) {
            return `<img src="${data}" width="100px">`;
          },
        },
        {
          title: 'Fecha de creación',
          data: 'created_at',
        },
        {
          title: 'Acciones',
          data: 'id_company',
          className: 'uniqueClassName',
          render: function (data) {
            return `<a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateCompany" data-toggle='tooltip' title='Actualizar Empresa' style="font-size: 30px;"></i></a>`;
          },
        },
      ],
    });
  };

  loadtableCompanies(1);
});
