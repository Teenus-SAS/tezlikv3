$(document).ready(function () {
    $('.addMaterial').hide();

    $('#btnNewMaterial').click(function (e) {
        e.preventDefault();
        
        if (products.length == 0) {
            toastr.error('Adicione un producto');
            return false;
        }

        // if (products.length == 2) {
        //     toastr.error('Maximo una materia prima');
        //     return false;
        // }

        sessionStorage.removeItem('actualizar');
        
        $('.addMaterial').toggle(800);
        $('.addProd').hide();

        $('#refMaterial option').removeAttr('selected');
        $(`#refMaterial option[value='0']`).prop('selected', true);
        $('#nameMaterial option').removeAttr('selected');
        $(`#nameMaterial option[value='0']`).prop('selected', true);
        $('#quantityMaterial').val('');
    });

    $('#btnAddMaterial').click(function (e) {
        e.preventDefault();
        
        let idMaterial = parseInt($('#refMaterial').val());
        let ref = $('#refMaterial :selected').text();
        let material = $('#nameMaterial :selected').text();
        let quantity = parseInt($('#quantityMaterial').val());

        let data = idMaterial * quantity;

        if (isNaN(data) || data <= 0) {
            toastr.error('Ingrese los datos');
            return false;
        }

        let dataMaterials = JSON.parse(sessionStorage.getItem('dataMaterials'));
        let indirectMaterial = dataMaterials.filter((item) => item.id_material == idMaterial);
        let totalPrice = quantity * indirectMaterial[0].cost;

        let op = sessionStorage.getItem('actualizar');

        if (!op || op == null) {
            data = {
                idProduct: products[0].idProduct,
                idMaterial: idMaterial,
                ref: ref.trim(),
                nameProduct: material.trim(),
                // price: products[0].price,
                price: `$ ${indirectMaterial[0].cost.toLocaleString('es-CO')}`,
                idPriceList: '',
                quantity: products[0].quantity, 
                quantityMaterial: quantity, 
                discount: '0',
                totalPrice: `$ ${totalPrice.toLocaleString('es-CO')}`,
                indirect: 1
            };
            products.push(data);
        } else {
            products[op].idMaterial = idMaterial;
            products[op].ref = ref.trim();
            products[op].nameProduct = material.trim();
            products[op].quantityMaterial = quantity;
            products[op].price = `$ ${indirectMaterial[0].cost.toLocaleString('es-CO')}`;
            products[op].discount = '0';
            products[op].totalPrice = `$ ${totalPrice}`;
            products[op].indirect = 1;
        }
        
        $('.addMaterial').hide(800);

        addProducts();
    });

     /* Modificar material */
    $(document).on('click', '.updateMaterial', async function (e) {
        e.preventDefault();

        let id = this.id;
        let data = products[id];
        $('.addprod').hide();

        $(`#refMaterial option:contains(${data.ref})`).prop('selected', true);
        $(`#nameMaterial option:contains(${data.nameProduct})`).prop(
            'selected',
            true
        );

        $('#quantityMaterial').val(data.quantity.toLocaleString());

        sessionStorage.setItem('actualizar', id);

        $('.addMaterial').show(1000);
    });
});