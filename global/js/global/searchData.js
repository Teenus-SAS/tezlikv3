$(document).ready(function () {
  searchData = async (urlApi) => {
    try {
      result = await $.ajax({ url: urlApi });
      return result;
    } catch (error) {
      console.error(error);
    }
  };

  sendDataPOST = async (urlApi, params) => {
    try {
      result = await $.ajax({
        url: urlApi,
        type: 'POST',
        data: params,
        contentType: false,
        cache: false,
        processData: false,
      });
      return result;
    } catch (error) {
      console.error(error);
    }
  };

  // Función para cargar datos desde la API y almacenar en sessionStorage
  // loadData = async (url, storageKey) => {
  //   let data = await searchData(url);
  //   sessionStorage.setItem(storageKey, JSON.stringify(data));
  //   return data;
  // };
});
