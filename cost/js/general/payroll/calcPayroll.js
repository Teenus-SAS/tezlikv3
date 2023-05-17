$(document).ready(function () {
  /* Mostrar factor prestacional */
  sessionStorage.removeItem('percentage');
  sessionStorage.removeItem('salary');

  $(document).on('change', '#typeFactor', function (e) {
    let risk = $('#risk').val();

    if (risk == '' || !risk) {
      $('#typeFactor option').removeAttr('selected');
      $(`#typeFactor option[value='0']`).prop('selected', true);

      toastr.error('Seleccione primero tipo de riesgo');
      return false;
    }

    $('#factor').prop('disabled', true);

    if (this.value == 0) return false;
    else if (this.value == 1) {
      let dataBenefits = sessionStorage.getItem('dataBenefits');
      dataBenefits = JSON.parse(dataBenefits);
      let percentage = parseFloat(sessionStorage.getItem('percentage'));

      for (i = 0; i < dataBenefits.length; i++) {
        if (dataBenefits[i].id_benefit == 2) {
          let salary = sessionStorage.getItem('salary');
          salary = parseFloat(strReplaceNumber(salary));

          if (salary > 1160000 * 10) percentage += dataBenefits[i].percentage;
        } else percentage += dataBenefits[i].percentage;
      }

      value = percentage.toFixed(2);
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

    sessionStorage.setItem('percentage', percentage);

    $('#valueRisk').val(
      percentage.toLocaleString('es-CO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
    );

    $('#typeFactor').change();
  });

  $(document).on('keyup', '#basicSalary', function () {
    if (this.value == '' || !this.value) return false;

    let percentage = parseFloat(sessionStorage.getItem('percentage'));

    if (percentage) $('#typeFactor').change();
  });

  // $(document).on('keyup', '#bonification', function () {
  //   let bonification = this.value;
  //   let salary = $('#basicSalary').val();

  //   salary = parseFloat(strReplaceNumber(salary));
  //   bonification = parseFloat(strReplaceNumber(bonification));

  //   let data = salary * bonification;

  //   if (data <= 0 || isNaN(data)) {
  //     toastr.error('Ingrese un valor mayor a cero');
  //     return false;
  //   }

  //   $('#bonification').prop('readondly', true);

  //   let sessionSalary = sessionStorage.getItem('salary');

  //   if (!sessionSalary || sessionSalary == '')
  //     bootbox.confirm({
  //       title: 'Eliminar',
  //       message: 'El valor a ingresar es salarial?',
  //       buttons: {
  //         confirm: {
  //           label: 'Si',
  //           className: 'btn-success',
  //         },
  //         cancel: {
  //           label: 'No',
  //           className: 'btn-danger',
  //         },
  //       },
  //       callback: function (result) {
  //         sessionStorage.removeItem('salary');

  //         $('#bonification').prop('readondly', false);

  //         if (result == true) {
  //           sessionStorage.setItem('salary', salary + bonification);
  //         } else sessionStorage.setItem('salary', salary);
  //       },
  //     });
  //   else $('#bonification').prop('readondly', false);
  // });
});
