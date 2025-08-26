$(document).ready(function () {
  // Función para cargar datos desde la API y almacenar en sessionStorage
  const loadData = async (url, storageKey) => {
    let data = await searchData(url);
    sessionStorage.setItem(storageKey, JSON.stringify(data));
    return data;
  };

  // Función para cargar magnitudes en el select
  const loadMagnitudes = async () => {
    try {
      let data = await loadData('/api/measurements/magnitudes', 'dataMagnitudes');
      let $select = $('#magnitudes');
      $select.empty();
      $select.append(`<option disabled selected>Seleccionar</option>`);
      data.forEach(value => {
        $select.append(`<option value="${value.id_magnitude}">${value.magnitude}</option>`);
      });
    } catch (error) {
      if (error.error && error.reload) {
        // Session expired, redirect to login
        alert(error.message || 'Sesión expirada');
        window.location.href = '/';
      } else {
        console.error('Error loading magnitudes:', error);
      }
    }
  };

  // Inicializar datos
  const init = async () => {
    await loadMagnitudes(); // Cargar magnitudes en el select
  };

  // Evento para cargar unidades cuando se selecciona una magnitud
  $(document).on('change', '#magnitudes', function () {
    let value = this.value;
    loadUnitsByMagnitude(value, 1); // Función definida en configUnits.js
  });

  // Llamar a la función de inicialización
  init();
});
