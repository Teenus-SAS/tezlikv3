$(document).ready(function() {
    /* $('.cardTableRawMaterials').hide();
    $('.cardRawMaterialsAnalysis').hide(); */

    // Mostrar tabla composicion materias prima
    $('#btnComposition').click(function(e) {
        e.preventDefault();
        /* $('#btnComposition').prop('disabled', true);
        $('#btnRawMaterialsAnalysis').prop('disabled', false); */

        $('.cardTableRawMaterials').show(800);
        $('.cardRawMaterialsAnalysis').hide(800);
    });

    // Mostrar tabla analisis de materia prima
    $('#btnRawMaterialsAnalysis').click(function(e) {
        e.preventDefault();
        /* $('#btnRawMaterialsAnalysis').prop('disabled', true);
        $('#btnComposition').prop('disabled', false); */

        id = $('#selectNameProduct').val();

        if (id == null) {
            toastr.error('Seleccione un producto');
            return false;
        } else {
            $('.cardTableRawMaterials').hide(800);
            $('.cardRawMaterialsAnalysis').show(800);

        }
    });

});