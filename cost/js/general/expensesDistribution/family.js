$(document).ready(function () {
  $('.cardAddNewFamily').hide();

  $('#btnAddNewFamily').click(function (e) {
    e.preventDefault();

    $('#formFamily').trigger('reset');
    $('.cardTblExpensesDistribution').hide(800);
    $('.cardExpensesDistribution').hide(800);
    $('.cardTblFamilies').show(800);
    $('.cardAddNewFamily').toggle(800);

    let tables = document.getElementById('tblFamilies');

    let attr = tables;
    attr.style.width = '100%';
    attr = tables.firstElementChild;
    attr.style.width = '100%';
  });

  $('#btnSaveFamily').click(function (e) {
    e.preventDefault();

    let family = $('#family').val();

    if (family == '') {
      toastr.error('Ingrese nuevo campo');
      return false;
    }

    let data = $('#formFamily').serialize();

    $.post('/api/addFamily', data, function (data, textStatus, jqXHR) {
      message(data, 3);
    });
  });
});
