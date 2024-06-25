$(document).ready(function () { 
  const fetchData = async (url, options = {}) => {
    try {
      const result = await $.ajax({ url, ...options });
      return result;
    } catch (error) {
      console.error(`Error fetching data from ${url}:`, error);
    }
  };

  searchData = async (urlApi) => fetchData(urlApi);

  sendDataPOST = async (urlApi, params) => {
    let resp = await fetchData(urlApi, {
      type: 'POST',
      data: params,
      contentType: false,
      cache: false,
      processData: false,
    });

    if(typeof resp === 'object') return resp;

    if (resp.includes('<!DOCTYPE html>')) window.location.reload();
  };

  // Función para cargar datos desde la API y almacenar en sessionStorage
  // loadData = async (url, storageKey) => {
  //   let data = await searchData(url);
  //   sessionStorage.setItem(storageKey, JSON.stringify(data));
  //   return data;
  // };
});
