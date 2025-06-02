$(document).ready(function () {
    // Cargar datos
    window.getDataProducts = async (url) => {
        await $.ajax({
            url: url,
            success: function (r) {
                if (r.reload) {
                    location.reload();
                }

                r = r.filter(item => item.active == 1);
                sessionStorage.setItem('dataProducts', JSON.stringify(r));

                const compositeProduct = r.filter(item => item.composite == 1);

                populateSelect('.refProduct', r, 'reference');
                populateSelect('.selectNameProduct', r, 'product');
                populateSelect('#refCompositeProduct', compositeProduct, 'reference');
                populateSelect('#compositeProduct', compositeProduct, 'product');

                initializeSelect2();
                synchronizeSelects('.refProduct', '.selectNameProduct');
                synchronizeSelects('#refCompositeProduct', '#compositeProduct');
            }
        });
    };

    // Función para poblar selects
    function populateSelect(selector, data, property) {
        const $select = $(selector);
        $select.empty().append(`<option value="0" disabled selected>Seleccionar</option>`);
        const sortedData = sortFunction(data, property);

        $.each(sortedData, function (i, item) {
            $select.append(`<option value="${item.id_product}" class="${item.composite}">${item[property]}</option>`);
        });
    }

    // Inicializa Select2
    function initializeSelect2() {
        $('.refProduct, .selectNameProduct, #refCompositeProduct, #compositeProduct').select2({
            width: '100%',
            placeholder: 'Seleccionar',
            allowClear: true,
        });
    }

    // Sincroniza selects por ID del producto
    function synchronizeSelects(selector1, selector2) {
        $(selector1).on('change', function () {
            let value = $(this).val();
            $(selector2).val(value).trigger('change.select2');
        });

        $(selector2).on('change', function () {
            let value = $(this).val();
            $(selector1).val(value).trigger('change.select2');
        });
    }

    //getDataProducts('/api/selectProducts');
});
