$(document).ready(function () {
  $('.number').on({
    focus: function (event) {
      $(event.target).select();
    },
    keyup: function (event) {
      $(event.target).val(function (index, value) {
        if (!/[0-9]/.test(value)) {
          event.preventDefault();
          return '';
        }

        if (!value.includes(','))
          number = value
            .replace(/\./g, '')
            .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, '.');
        else {
          number = value;
          // while (number.includes('.')) {
          //   number = number.replace('.', '');
          // }
          // number = number.replace(',', '.');

          // number = parseFloat(number).toLocaleString();
        }

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
            .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, '.')
        );
      });
    },
  });
});
