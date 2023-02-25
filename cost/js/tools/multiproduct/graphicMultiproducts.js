$(document).ready(function () {
  $('#btnShowGraphic').click(function (e) {
    e.preventDefault();

    $('.cardTblMultiproducts').hide(800);
    $('.cardGraphicMultiproducts').show(800);
  });
});
