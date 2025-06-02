$(document).ready(function () {
  /* Cargue tabla de Contactos*/

  tblQuotes = $('#tblQuotes').dataTable({
    pageLength: 50,
    // ajax: {
    //   url: '/api/quotes',
    //   dataSrc: '',
    // },
    ajax: function (data, callback, settings) {
      fetch(`/api/quotes`)
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
        title: 'Cliente',
        data: 'contact',
        className: 'uniqueClassName',
      },
      {
        title: 'Compañia',
        data: 'company_name',
        className: 'uniqueClassName',
      },
      {
        title: 'Precio',
        data: 'price',
        className: 'uniqueClassName',
        render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
      },
      {
        title: 'Metodo de Pago',
        data: 'method',
        className: 'uniqueClassName',
      },
      {
        title: 'Fecha de entrega',
        data: 'delivery_date',
        className: 'uniqueClassName',
      },
      {
        title: 'Acciones',
        data: 'id_quote',
        className: 'uniqueClassName',
        render: function (data) {
          return `
                <a href="/cost/details-quote" <i id="${data}" class="mdi mdi-playlist-check" data-toggle='tooltip' title='Ver Cotización' style="font-size: 30px;color:black" onclick="seeQuote()"></i></a>
                <a href="javascript:;" <i id="${data}" class="bx bx-copy" data-toggle='tooltip' title='Copiar Cotización' style="font-size: 35px;" onclick="copyQuote()"></i></a>
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateQuote" data-toggle='tooltip' title='Actualizar Cotización' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Cotización' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a> 
              `;
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
    rowCallback: function (row, data, index) {
      if (data['flag_quote'] == 1) $(row).css('color', 'blue');
    },
  });
});
