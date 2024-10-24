$(document).ready(function () {
  let userAgentNum = window.navigator.userAgent;

  $(document).on('keyup', '.number', function () {
    if (!/[0-9]/.test(this.value)) {
      event.preventDefault();
      number = '';
    } else {
      userAgentNum
      number = this.value;

      if (userAgentNum.indexOf("Mac") !== -1) {
        while (number.includes(',')) {
          number = number.replace(',', '');
        }
      } else {
        while (number.includes('.')) {
          number = number.replace('.', '');
        }
      }

      number = decimalNumber(number);
    }

    $(`#${this.id}`).val(number);
  });

  $('.numberCalc').on({
    focus: function (event) {
      $(event.target).select();
    },
    keyup: function (event) {
      $(event.target).val(function (index, value) {
        if (!/[0-9]/.test(value)) {
          event.preventDefault();
          return '';
        }

        if (userAgentNum.indexOf("Mac") !== -1) {
          number = value
            .replace(/\./g, '')
            .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ',');
        } else
          number = value
            .replace(/\./g, '')
            .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, '.');

        return number;
      });
    },
  });

  $('.money').on({
    focus: function (event) {
      $(event.target).select();
    },
    keyup: function (event) {
      $(event.target).val(function (index, value) {
        return (
          value
            .replace(/\D/g, '')
            //.replace(/([0-9])([0-9]{2})$/, '$1,$2')
            .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ',')
        );
      });
    },
  });

  /* Devolver numero formato 0.000.000,0 */
  formatNumber = (floatValue = 0, decimals = 0, multiplier = 1) => {
    let floatMultiplied = floatValue * multiplier;
    let stringFloat = floatMultiplied + '';

    if (userAgentNum.indexOf("Mac") !== -1)
      arraySplitFloat = stringFloat.split(',');
    else
      arraySplitFloat = stringFloat.split('.');

    let decimalsValue = '0';

    if (arraySplitFloat.length > 1) {
      decimalsValue = arraySplitFloat[1].slice(0, decimals);
    }

    let integerValue = arraySplitFloat[0];
    let arrayFullStringValue = [integerValue, decimalsValue];

    if (userAgentNum.indexOf("Mac") !== -1)
      FullStringValue = arrayFullStringValue.join(',');
    else
      FullStringValue = arrayFullStringValue.join('.');

    let floatFullValue = parseFloat(FullStringValue) + '';

    if (userAgentNum.indexOf("Mac") !== -1) {
      formatFloatFullValue = new Intl.NumberFormat('ja-JP', {
        minimumFractionDigits: decimals,
      }).format(floatFullValue);
    } else {
      formatFloatFullValue = new Intl.NumberFormat(undefined, {
        minimumFractionDigits: decimals,
      }).format(floatFullValue);
    }
    return formatFloatFullValue;
  };

  /* Quitar miles y decimales del numero */
  strReplaceNumber = (num) => {
    if (userAgentNum.indexOf("Mac") !== -1) {
      while (num.includes(',')) {
        num = num.replace(',', '');
      }
      // num = num.replace('.', ',');
    } else {
      while (num.includes('.')) {
        num = num.replace('.', '');
      }
    }
    num = num.replace(',', '.');
    return num;
  };

  /* Validar si es entero o decimal */
  validateNumber = (number) => {
    if (userAgentNum.indexOf("Mac") !== -1) {
      if (number.isInteger) number = number.toLocaleString('ja-JP');
      else
        number = number.toLocaleString('ja-JP', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        });
    } else {

      if (number.isInteger) number = number.toLocaleString('es-CO');
      else
        number = number.toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        });
    }
    return number;
  };

  /* Devolver cantidad de decimales */
  decimalNumber = (number) => {
    let decimals = 0;
    dnumber = number;

    if (userAgentNum.indexOf("Mac") !== -1) {
      while (dnumber.includes('.')) {
        dnumber = dnumber.slice(1, dnumber.length);
        if (dnumber == '') {
          decimals = '';
          break;
        }
        decimals = dnumber.length;
      }
    }
    else {
      while (dnumber.includes(',')) {
        dnumber = dnumber.slice(1, dnumber.length);
        if (dnumber == '') {
          decimals = '';
          break;
        }
        decimals = dnumber.length;
      }
    }

    if (decimals == '' && typeof decimals === 'string') return number;

    number = number.replace(',', '.');
    number = formatNumber(parseFloat(number), decimals);
    return number;
  };

  contarDecimales = (numero) => {
    var cadena = numero.toString();

    if (userAgentNum.indexOf("Mac") !== -1)
      decimalIndex = cadena.indexOf(',');
    else
      decimalIndex = cadena.indexOf('.');
    
    if (decimalIndex === -1) {
      return 0;
    }
    return cadena.length - decimalIndex - 1;
  };
});

