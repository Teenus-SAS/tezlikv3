$('.number').on({
  focus: function (event) {
    $(event.target).select();
  },
  keyup: function (event) {
    $(event.target).val(function (index, value) {
      //   num = value.replace(/\./g, '');
      //   num = num.replace(/\B(?=(\d{3})+(?!\d)\.?)/g, '.');
      //   return num;
      debugger;
      if (!value.includes(','))
        number = value
          .replace(/\./g, '')
          .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, '.');
      else number = value;
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
