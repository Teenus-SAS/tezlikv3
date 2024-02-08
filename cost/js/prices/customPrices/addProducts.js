$(document).ready(function () {
    let products = [];

    /* Modificar porcentaje masivo */
    $(document).on('click', '.check', function () {
        let id = this.id;

        if (id.includes('all')) {
            products = [];
            
            if ($(`#${id}`).is(':checked')) {
                let data = JSON.parse(sessionStorage.getItem('dataProducts'));

                for (let i = 0; i < data.length; i++) {
                    products.push({
                        id_product: data[i].id_product,
                        reference: data[i].reference,
                        product: data[i].product,
                        price: data[i].price,
                        sale_price: data[i].sale_price,
                    });
                }
                $('.check').prop('checked', true);
            } else {
                $('.check').prop('checked', false);
            }
        } else {
            let id_product = id.slice(6, id.length);
            if ($(`#${id}`).is(':checked')) {
                let className = $(this).attr('class');
                text = className.split("¨¨");
                let [reference, name, price, sale_price] = text.slice(-4);

                let product = {
                    id_product: id_product,
                    reference: reference,
                    product: name,
                    price: price,
                    sale_price: sale_price,
                };

                products.push(product);
            } else {
                for (i = 0; i < products.length; i++)
                    if (products[i].id_product == id_product)
                        products.splice(i, 1);
            }
        }
    });

    $('#btnSaveProducts').click(async function (e) {
        e.preventDefault();

        if (products.length == 0) {
            toastr.error('Seleccione un producto');
            return false;
        }
 
        typePrice == '0' ? namePrice = 'sale_price' : namePrice = 'price';
 
        $.ajax({
            type: "POST",
            url: "/api/addCustomPercentage",
            data: {
                idPriceList: $('#pricesList2').val(),
                percentage: $('#percentage').val(),
                name: namePrice,
                typePrice: typePrice,
                typeProducts: 2,
                products: products,
            },
            success: function (resp) {
                message(resp);
                // $('#modalNotProducts').modal('show');
                products = [];
                $('#nameNotProducts').html('Productos No Agregados');
                $('#btnSaveProducts').hide();
                loadPriceList(1);

                if (resp.dataNotData.length > 1)
                    loadTblNotProducts(resp.dataNotData, 1);
                else {
                    $('#modalNotProducts').modal('hide');
                }
            }
        });
    });
});