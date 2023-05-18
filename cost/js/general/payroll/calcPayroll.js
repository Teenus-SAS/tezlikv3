$(document).ready(function () {
  /* Mostrar factor prestacional */
  sessionStorage.removeItem('percentage');
  sessionStorage.removeItem('salary');
  sessionStorage.removeItem('type_salary');

  $(document).on('change', '#typeFactor', function (e) {
    let risk = $('#risk').val();

    if (risk == '' || !risk) {
      $('#typeFactor option').removeAttr('selected');
      $(`#typeFactor option[value='0']`).prop('selected', true);

      toastr.error('Seleccione primero tipo de riesgo');
      return false;
    }

    $('#factor').prop('disabled', true);

    let percentage = parseFloat(
      strReplaceNumber(sessionStorage.getItem('percentage'))
    );

    if (this.value == 0) return false;
    else if (this.value == 1) {
      let dataBenefits = sessionStorage.getItem('dataBenefits');
      dataBenefits = JSON.parse(dataBenefits);

      for (i = 0; i < dataBenefits.length; i++) {
        if (dataBenefits[i].id_benefit == 2) {
          let salary = sessionStorage.getItem('salary');
          !salary ? (salary = '0') : salary;
          salary = parseFloat(strReplaceNumber(salary));

          if (salary > 1160000 * 10) percentage += dataBenefits[i].percentage;
        } else percentage += dataBenefits[i].percentage;
      }

      value = percentage.toFixed(2);
    } else if (this.value == 2) value = percentage;
    else if (this.value == 3) {
      $('#factor').prop('disabled', false);
      value = $('#factor').val();
    }

    $('#factor').val(value);
  });

  $(document).on('blur', '#factor', function () {
    let percentage = parseFloat(
      strReplaceNumber(sessionStorage.getItem('percentage'))
    );

    let factor = parseFloat(this.value);

    isNaN(factor) ? (factor = 0) : factor;

    $('#factor').val(
      (percentage + factor).toLocaleString('es-CO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
    );
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

    if (id_risk == 0) percentage = 0;
    else {
      let dataRisks = sessionStorage.getItem('dataRisks');
      dataRisks = JSON.parse(dataRisks);

      for (let i = 0; i < dataRisks.length; i++) {
        if (dataRisks[i].id_risk == id_risk) {
          percentage = dataRisks[i].percentage;
          break;
        }
      }

      percentage = percentage.toLocaleString('es-CO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }

    sessionStorage.setItem('percentage', percentage);

    $('#valueRisk').val(percentage);

    $('#typeFactor').change();
  });

  $(document).on('keyup', '#basicSalary', function () {
    if (this.value == '' || !this.value) return false;

    let percentage = parseFloat(sessionStorage.getItem('percentage'));

    if (percentage) $('#typeFactor').change();

    let type_salary = sessionStorage.getItem('type_salary');
    if (type_salary) calcTypeSalary(type_salary);
    else {
      let basicSalary = parseFloat(strReplaceNumber(this.value));
      isNaN(basicSalary) ? (basicSalary = 0) : basicSalary;
      sessionStorage.setItem('salary', basicSalary);
    }
  });

  $(document).on('blur', '#bonification', function () {
    let type_salary = sessionStorage.getItem('type_salary');

    if (!type_salary || type_salary == '')
      bootbox.confirm({
        title: 'Otros Ingresos',
        message: 'El valor a ingresar es salarial?',
        buttons: {
          confirm: {
            label: 'Si',
            className: 'btn-success',
          },
          cancel: {
            label: 'No',
            className: 'btn-danger',
          },
        },
        callback: function (result) {
          sessionStorage.removeItem('type_salary');

          if (result == true) {
            sessionStorage.setItem('type_salary', 1);
            calcTypeSalary(1);
          } else {
            sessionStorage.setItem('type_salary', 0);
            calcTypeSalary(0);
          }

          setTimeout(() => {
            let horizontal_navbar =
              document.getElementsByClassName('horizontal-navbar');

            horizontal_navbar[0].className = 'horizontal-navbar modal-open';
            horizontal_navbar[0].style.paddingRight = '17px';
          }, 500);
        },
      });
    else {
      calcTypeSalary(type_salary);
    }
  });

  calcTypeSalary = (type_salary) => {
    sessionStorage.removeItem('salary');

    let basicSalary = $('#basicSalary').val();
    basicSalary = parseFloat(strReplaceNumber(basicSalary));
    isNaN(basicSalary) ? (basicSalary = 0) : basicSalary;

    if (type_salary == 1) {
      let bonification = $('#bonification').val();
      bonification = parseFloat(strReplaceNumber(bonification));
      isNaN(bonification) ? (bonification = 0) : bonification;

      sessionStorage.setItem('salary', basicSalary + bonification);
    } else sessionStorage.setItem('salary', basicSalary);
  };
});
