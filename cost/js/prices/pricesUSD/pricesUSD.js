$(document).ready(function () {
  getCurrentDollar = async () => {
    let dollar = await searchData('/api/currentDollar');

    $('#valueDollar').val(
      `$ ${dollar.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })}`
    );
  };

  /* Calcular valor de cobertura ingresando numero de desviación */
  $(document).on('blur', '#deviation', function (e) {
    let num = this.value;

    getUSDData(num);
  });

  getUSDData = async (num) => {
    let data = await searchData(`/api/priceUSD/${num}`);

    if (data.success) {
      $('#exchangeCoverage').val(
        `$ ${data.exchangeCoverage.toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        })}`
      );
      $('#valueCoverage').val(
        `$ ${data.coverage.toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        })}`
      );
      $('#deviation').val(data.deviation);

      $('#tblPricesUSD').DataTable().clear();
      $('#tblPricesUSD').DataTable().ajax.reload();
    }
  };

  getUSDData(0);
  getCurrentDollar();
});
