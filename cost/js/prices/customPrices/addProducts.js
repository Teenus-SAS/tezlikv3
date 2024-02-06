$(document).ready(function () {
    let products = [];

    /* Modificar porcentaje masivo */
    $(document).on('click', '.check', function () {
        let id = this.id;

        // if (id.includes('all')) {
        //   expensesRecover = [];
        //   if ($(`#${id}`).is(':checked')) {
        //     let data = sessionStorage.getItem('dataExpensesRecover');
        //     data = JSON.parse(data);

        //     for (let i = 0; i < data.length; i++) {
        //       expensesRecover.push({
        //         idExpenseRecover: data[i].id_expense_recover,
        //         idProduct: data[i].id_product,
        //       });
        //     }
        //     $('.checkExpense').prop('checked', true);
        //   } else {
        //     $('.checkExpense').prop('checked', false);
        //     $('.cardBtnUpdateExpenses').hide(800);
        //   }
        // } else {
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
        // }
    });

    $('#btnSaveProducts').click(async function (e) {
        e.preventDefault();

        if (products.length == 0) {
            toastr.error('Seleccione un producto');
            return false;
        }

        // let data = $('#formCreateCustomPercentage').serialize();
        // let data = new FormData(formCreateCustomPercentage);
        typePrice == '0' ? namePrice = 'sale_price' : namePrice = 'price';

        // data.append('name', namePrice);
        // data.append('typePrice', typePrice);
        // data.append('typeProducts', 2);
        // data.append('products', products);

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
                $('#modalNotProducts').modal('show');
                $('#nameNotProducts').html('Productos No Agregados');
                $('#btnSaveProducts').hide();
                loadPriceList(1);
                loadTblNotProducts(resp.dataNotData, 1);
            }
        });
    });
});