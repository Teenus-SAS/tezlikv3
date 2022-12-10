$(document).ready(function () {
  // Mostrar tabla composicion materias prima
  $('#btnComposition').click(function (e) {
    e.preventDefault();

    $('.cardTableRawMaterials').show(800);
    $('.cardRawMaterialsAnalysis').hide(800);
  });

  // Mostrar tabla analisis de materia prima
  $('#btnRawMaterialsAnalysis').click(function (e) {
    e.preventDefault();

    let id = $('#selectNameProduct').val();

    if (id == null) {
      toastr.error('Seleccione un producto');
      return false;
    } else {
      $('.cardTableRawMaterials').hide(800);
      $('.cardRawMaterialsAnalysis').show(800);
    }
  });
});
