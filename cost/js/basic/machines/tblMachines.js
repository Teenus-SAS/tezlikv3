
/* Cargue tabla de Máquinas */
inyection == 1 ? visible = true : visible = false;

// Función para formatear los numeros
const renderNum = (data) => {
  let num = parseFloat(data);
  const options = Math.abs(num) < 0.01
    ? { minimumFractionDigits: 2, maximumFractionDigits: 9 }
    : { maximumFractionDigits: 2 };

  num = num.toLocaleString('es-CO', options);
  return num;
};

tblMachines = $('#tblMachines').dataTable({
  pageLength: 50,

  ajax: function (data, callback, settings) {
    fetch(`/api/machines`)
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
      title: 'Costo',
      data: 'cost',
      className: 'classRight',
      render: (data, type, row) => { return `$ ${renderNum(data)}` },
    },
    {
      title: 'Años de Depreciación',
      data: 'years_depreciation',
      className: 'classCenter',
    },
    {
      title: 'Depreciación X Minuto',
      data: 'minute_depreciation',
      className: 'classCenter',
      render: (data, type, row) => renderNum(data),
    },
    {
      title: 'Ciclos Maquina',
      data: 'cicles_machine',
      className: 'classCenter',
      visible: visible,
    },
    {
      title: 'No Cavidades',
      data: 'cavities',
      className: 'classCenter',
      visible: visible,
    },
    {
      title: 'Acciones',
      data: null,
      className: 'uniqueClassName',
      render: function (data) {
        if (data.status == 0)
          icon = '/public/assets/images/trash_v.png';
        else
          icon = '/public/assets/images/trash_x.png';

        return `
                <a href="javascript:;" <i id="${data.id_machine}" class="bx bx-edit-alt updateMachines" data-toggle='tooltip' title='Actualizar Maquina' style="font-size: 30px;"></i></a>
                <a href="javascript:;"><img src="${icon}" alt="Eliminar Maquina" id="${data.id_machine}" style="width:30px;height:30px;margin-top:-20px" onclick="deleteFunction()"></a>`;
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
});

