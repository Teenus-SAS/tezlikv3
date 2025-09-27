$(document).ready(function () {
  const fetchData = async (url, options = {}) => {
    try {
      const result = await $.ajax({ url, ...options });

      return result;
    } catch (error) {
      console.error(`Error fetching data from ${url}:`, error);
      if (error.status === 401)
        window.location.href = '../';
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

    if (typeof resp === 'object') return resp;
  };
});
