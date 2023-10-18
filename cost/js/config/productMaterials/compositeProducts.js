$(document).ready(function () {
    $('.cardAddNewProduct').hide();

    $('#btnAddNewProduct').click(function (e) {
        e.preventDefault();

        $('.cardImportProductsMaterials').hide(800);
        $('.cardAddMaterials').hide(800);
        $('.cardAddNewProduct').toggle(800);
        $('#btnAddProduct').html('Asignar');
        $('#units2').empty();

        sessionStorage.removeItem('id_composite_product');

        $('#formAddNewProduct').trigger('reset');
    });

    $('#compositeProduct').change(function (e) {
        e.preventDefault();
        
        let $select = $(`#unit2`);
        $select.empty();
        $select.append(`<option disabled>Seleccionar</option>`);
        $select.append(`<option value = "1" selected>UNIDAD</option>`);
    });

    $('#btnAddProduct').click(function (e) { 
        e.preventDefault();
        
    }); 
});