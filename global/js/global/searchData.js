$(document).ready(function () {
  // searchData = async (urlApi) => {
  //   try {
  //     result = await $.ajax({ url: urlApi });
  //     return result;
  //   } catch (error) {
  //     console.error(error);
  //   }
  // };

  // sendDataPOST = async (urlApi, params) => {
  //   try {
  //     result = await $.ajax({
  //       url: urlApi,
  //       type: 'POST',
  //       data: params,
  //       contentType: false,
  //       cache: false,
  //       processData: false,
  //     });
  //     return result;
  //   } catch (error) {
  //     console.error(error);
  //   }
  // };
  const fetchData = async (url, options = {}) => {
    try {
      const result = await $.ajax({ url, ...options });
      return result;
    } catch (error) {
      console.error(`Error fetching data from ${url}:`, error);
    }
  };

  searchData = async (urlApi) => fetchData(urlApi);

  sendDataPOST = async (urlApi, params) => fetchData(urlApi, {
    type: 'POST',
    data: params,
    contentType: false,
    cache: false,
    processData: false,
  });

  // Función para cargar datos desde la API y almacenar en sessionStorage
  // loadData = async (url, storageKey) => {
  //   let data = await searchData(url);
  //   sessionStorage.setItem(storageKey, JSON.stringify(data));
  //   return data;
  // };
});
