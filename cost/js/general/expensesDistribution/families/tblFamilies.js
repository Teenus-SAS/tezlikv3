
loadTableFamilies = () => {
  if ($.fn.dataTable.isDataTable('#tblFamilies')) {
    $('#tblFamilies').DataTable().destroy();
    $('#tblFamilies').empty();
  }

  tblFamilies = $('#tblFamilies').dataTable({
    destroy: true,
    pageLength: 50,
    ajax: function (data, callback, settings) {
      fetch(`/api/distributionByFamilies/families`)
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
        title: 'Nombre',
        data: 'family',
      },
      {
        title: 'Acciones',
        data: 'id_family',
        className: 'uniqueClassName',
        render: function (data) {
          return `
          <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateFamily" data-toggle='tooltip' title='Actualizar Familia' style="font-size: 30px;"></i></a>    
          <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Familia' style="font-size: 30px;color:red" onclick="deleteFamily()"></i></a>`;
        },
      },
    ],
  });
};

loadTableProductsFamilies = () => {
  if ($.fn.dataTable.isDataTable('#tblFamilies')) {
    $('#tblFamilies').DataTable().destroy();
    $('#tblFamilies').empty();
  }

  tblFamilies = $('#tblFamilies').dataTable({
    destroy: true,
    pageLength: 50,

    ajax: function (data, callback, settings) {
      fetch(`/api/distributionByFamilies/productsFamilies`)
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
        title: 'Familia',
        data: 'family',
        visible: false,
      },
      {
        title: 'Referencia',
        data: 'reference',
      },
      {
        title: 'Producto',
        data: 'product',
      },
      {
        title: 'Acciones',
        data: null,
        className: 'uniqueClassName',
        render: function () {
          return `
            <a href="javascript:;" <i class="bx bx-edit-alt updateProductFamily" data-toggle='tooltip' title='Actualizar Producto' style="font-size: 30px;"></i></a>    
            <a href="javascript:;" <i class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 30px;color:red" onclick="deleteProductFamily()"></i></a>`;
        },
      },
    ],
    rowGroup: {
      dataSrc: function (row) {
        return `<th class="text-center" colspan="4" style="font-weight: bold;"> ${row.family} </th>`;
      },
      startRender: function (rows, group) {
        return $('<tr/>').append(group);
      },
      className: 'odd',
    },
  });
};

