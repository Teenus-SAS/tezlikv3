$(document).ready(function () {
  $('#cardCreateConsolidated').hide();

  $('#btnNewConsolidated').click(function (e) {
    e.preventDefault();

    sessionStorage.removeItem('id_order');
    $('#formCreateConsolidated').trigger('reset');
    $('#cardCreateConsolidated').toggle(800);
    $('#btnCreateConsolidated').html('Programar');
  });
});
