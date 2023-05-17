$(document).ready(function () {
  /* Mostrar factor prestacional */

  $(document).on('change', '#typeFactor', function (e) {
    $('#factor').prop('disabled', true);

    if (this.value == 0) 'Seleccione una opción';
    else if (this.value == 1) {
      let dataBenefits = sessionStorage.getItem('dataBenefits');
      dataBenefits = JSON.parse(dataBenefits);

      for (i = 0; i < dataBenefits.length; i++) {
        if (dataBenefits[i].id_benefit == 2) {
        } else percentage += dataBenefits[i].percentage;
      }

      value = percentage;
    } else if (this.value == 2) value = 0;
    else if (this.value == 3) {
      $('#factor').prop('disabled', false);
      value = $('#factor').val();
    }

    $('#factor').val(value);
  });

  $(document).on('keyup', '#workingHoursDay', function (e) {
    let value = this.value;
    if (value > 16) {
      toastr.error('El número de horas por dia no puede ser mayor a 16');
      $('#workingHoursDay').val('');
      return false;
    }
  });

  $(document).on('keyup', '#workingDaysMonth', function (e) {
    let value = this.value;
    if (value > 31) {
      toastr.error('El número de días por mes no puede ser mayor a 31');
      $('#workingDaysMonth').val('');
      return false;
    }
  });

  $('#risk').change(function (e) {
    e.preventDefault();

    let id_risk = this.value;

    let dataRisks = sessionStorage.getItem('dataRisks');
    dataRisks = JSON.parse(dataRisks);

    for (let i = 0; i < dataRisks.length; i++) {
      if (dataRisks[i].id_risk == id_risk) {
        percentage = dataRisks[i].percentage;
        break;
      }
    }

    $('#valueRisk').val(
      percentage.toLocaleString('es-CO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
    );
  });
});
