$(document).ready(function () {
  $('.cardTableSimulator').hide();

  /* Modal Simulador */
  $('#btnSimulate').click(function (e) {
    e.preventDefault();

    let product = $('#product').val();

    if (!product) {
      toastr.error('Seleccione un producto antes de simular');
      return false;
    }

    $('#modalSimulator').modal('show');
  });

  $('.closeModalSimulator').click(function (e) {
    e.preventDefault();

    let navbarToggleExternalContent = document.getElementById(
      'navbarToggleExternalContent'
    );

    $('#modalSimulator').modal('hide');
    navbarToggleExternalContent.classList.remove('show');
  });

  $(document).on('click', '.btn-outline-secondary', async function () {
    $('.cardTableSimulator').show(800);
  });
});
