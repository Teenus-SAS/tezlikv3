$(document).ready(function () {
  $('.cardImportOrder').hide();

  $('#btnImportNewOrder').click(function (e) {
    e.preventDefault();

    $('.cardImportOrder').toggle(800);
  });
});
