$(document).ready(function () {
  getCurrentDollar = async () => {
    let actualTrm = await searchData('/api/currentDollar');

    $('#valueDollar').val(
      `$ ${actualTrm[0]['valor'].toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })}`
    );
  };

  /* Calcular valor de cobertura ingresando numero de desviación */
  $(document).on('keyup', '#deviation', function (e) {
    let num = this.value;

    if (num == '' || !num) {
      toast.error('Ingrese numero de desviacion');
      return false;
    }

    getUSDData(num);
  });

  getUSDData = async (num) => {
    $('.USDInputs').hide(400);

    let USDHeader = document.getElementById('USDHeader');
    USDHeader.insertAdjacentHTML(
      'beforeend',
      `<div class="spinner-border text-secondary" role="status">
        <span class="sr-only">Loading...</span>
      </div>`
    );

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

      $('.spinner-border').remove();
      $('.USDInputs').show(400);

      $('#tblPricesUSD').DataTable().clear();
      $('#tblPricesUSD').DataTable().ajax.reload();
    }
  };

  getUSDData(0);
  getCurrentDollar();
});
