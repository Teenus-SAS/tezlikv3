// $(document).ready(function () {
//   /* Cargar unidades por magnitud */ 
//   loadDataMagnitudes = async () => {
//     let data = await searchData('/api/units');

//     sessionStorage.setItem('dataUnits', JSON.stringify(data));
//   };
//   loadDataMagnitudes();
  
//   loadUnitsByMagnitude = async (data, op) => {
//     Object.prototype.toString.call(data) === '[object Object]'
//       ? (id_magnitude = data.id_magnitude)
//       : (id_magnitude = data);
    
//     let dataUnits = JSON.parse(sessionStorage.getItem('dataUnits'));
    
//     let dataPMaterials = dataUnits.filter(item => item.id_magnitude == id_magnitude);

//     // let dataPMaterials = await searchData(`/api/units/${id_magnitude}`);

//     let $select = $(`#units`);
//     $select.empty();

//     $select.append(`<option disabled selected>Seleccionar</option>`);
//     $.each(dataPMaterials, function (i, value) {
//       if (id_magnitude == '4' && op == 2) {
//         if (value.id_unit == data.id_unit) {
//           $select.empty();
//           $select.append(
//             `<option value ='${value.id_unit}' selected> ${value.unit} </option>`
//           );
//           return false;
//         }
//       } else $select.append(`<option value = ${value.id_unit}> ${value.unit} </option>`);
//     });
//   };
// });
$(document).ready(function () {
  // Función para cargar datos desde la API y almacenar en sessionStorage
  const loadData = async (url, storageKey) => {
    let data = await searchData(url);
    sessionStorage.setItem(storageKey, JSON.stringify(data));
    return data;
  };

  // Función para cargar unidades por magnitud
  const loadUnitsByMagnitude = (data, op) => {
    let id_magnitude = typeof data === 'object' ? data.id_magnitude : data;
    let dataUnits = JSON.parse(sessionStorage.getItem('dataUnits')) || [];

    let dataPMaterials = dataUnits.filter(item => item.id_magnitude == id_magnitude);
    let $select = $('#units');
    $select.empty();
    $select.append(`<option disabled selected>Seleccionar</option>`);

    dataPMaterials.forEach(value => {
      if (id_magnitude == '4' && op == 2 && value.id_unit == data.id_unit) {
        $select.empty();
        $select.append(`<option value="${value.id_unit}" selected>${value.unit}</option>`);
        return false;
      } else {
        $select.append(`<option value="${value.id_unit}">${value.unit}</option>`);
      }
    });
  };

  // Inicializar datos
  const init = async () => {
    await loadData('/api/units', 'dataUnits'); // Cargar y almacenar unidades
  };

  // Llamar a la función de inicialización
  init();

  // Exponer la función para que sea accesible desde configMagnitudes.js
  window.loadUnitsByMagnitude = loadUnitsByMagnitude;
});
