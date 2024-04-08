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
  $(document).on('blur', '.calcInputs', function (e) {
    let num = parseFloat($('#deviation').val());
    let coverage = parseFloat($('#valueCoverage1').val());

    let data = num * coverage;

    if (isNaN(data) || data <= 0) {
      toastr.error('Ingrese un valor de covertura valido');
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
 
    let coverage = parseFloat($('#valueCoverage1').val());

    let data = await searchData(`/api/priceUSD/${num}/${coverage}`);

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

      $('#valueCoverage1').val(data.coverage1.toFixed(2));

      $('#deviation').val(data.deviation);

      $('.spinner-border').remove();
      $('.USDInputs').show(400);

      loadAllData();
      // $('#tblPricesUSD').DataTable().clear();
      // $('#tblPricesUSD').DataTable().ajax.reload();
    }
  };

  getUSDData(0);
  getCurrentDollar();
});
