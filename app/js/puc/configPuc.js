$(document).ready(function () {
  // $('.cardCreateExpenses').hide();

  // $('#btnNewExpense').click(function (e) {
  //   e.preventDefault();
  //   $('.cardCreateExpenses').toggle(800);
  // });

  $.ajax({
    type: 'GET',
    url: '../../api/puc',
    success: function (r) {
      let $select1 = $(`#idPuc`);
      $select1.empty();

      $select1.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select1.append(
          `<option value = ${value.id_puc}>${value.number_count} - ${value.count} </option>`
        );
      });
    },
  });
});
