$(document).ready(function () {
    $('.cardAddNewProduct').hide();

    $('#btnAddNewProduct').click(async function (e) {
        e.preventDefault();

        $('.cardImportProductsMaterials').hide(800);
        $('.cardAddMaterials').hide(800);
        $('.cardAddNewProduct').toggle(800);
        $('#btnAddProduct').html('Asignar');
        $('#units2').empty();
        
        sessionStorage.removeItem('id_composite_product');
        
        $('#formAddNewProduct').trigger('reset');
        let idProduct = $('#selectNameProduct').val();

        if (!idProduct)
            return false;

        await loadTblCompositeProducts(idProduct);
    });

    $('#compositeProduct').change(function (e) {
        e.preventDefault();
        
        let $select = $(`#unit2`);
        $select.empty();
        $select.append(`<option disabled>Seleccionar</option>`);
        $select.append(`<option value ="11" selected>UNIDAD</option>`);
    });

    $('#btnAddProduct').click(function (e) {
        e.preventDefault();

        let idCompositeProduct = sessionStorage.getItem('id_composite_product');

        if (idCompositeProduct == '' || idCompositeProduct == null) {
            checkDataProducts(
                '/api/addCompositeProduct',
                idCompositeProduct
            );
        } else {
            checkDataProducts(
                '/api/updateCompositeProduct',
                idCompositeProduct
            );
        }
    });

    /* Actualizar productos materials */

    $(document).on('click', '.updateComposite', async function (e) {
        $('.cardImportProductsMaterials').hide(800);
        $('.cardAddNewProduct').show(800);
        $('#btnAddProduct').html('Actualizar');

        let row = $(this).parent().parent()[0];
        let data = tblConfigMaterials.fnGetData(row);

        sessionStorage.setItem('id_composite_product', data.id_composite_product);
        $(`#compositeProduct option[value=${data.id_child_product}]`).prop('selected', true);

        let $select = $(`#unit2`);
        $select.empty();
        $select.append(`<option disabled>Seleccionar</option>`);
        $select.append(`<option value ="11" selected>UNIDAD</option>`);

        let quantity = `${data.quantity}`;

        $('#quantity2').val(quantity.replace('.', ','));

        $('html, body').animate(
            {
                scrollTop: 0,
            },
            1000
        );
    });

    /* Revision data Productos materiales */
    checkDataProducts = async (url, idCompositeProduct) => {
        let ref = parseInt($('#compositeProduct').val());
        let quan = $('#quantity2').val();
        let idProduct = parseInt($('#selectNameProduct').val());

        let data = ref * idProduct;

        if (!data || quan == '') {
            toastr.error('Ingrese todos los campos');
            return false;
        }
      
        if (ref == idProduct) {
            toastr.error('Seleccione un producto compuesto diferente');
            return false;
        }

        quan = parseFloat(strReplaceNumber(quan));

        quant = 1 * quan;

        if (quan <= 0 || isNaN(quan)) {
            toastr.error('La cantidad debe ser mayor a cero (0)');
            return false;
        }

        let dataProduct = new FormData(formAddNewProduct);
        dataProduct.append('idProduct', idProduct);

        if (idCompositeProduct != '' || idCompositeProduct != null)
            dataProduct.append('idCompositeProduct', idCompositeProduct);

        let resp = await sendDataPOST(url, dataProduct);

        message(resp);
    };
});