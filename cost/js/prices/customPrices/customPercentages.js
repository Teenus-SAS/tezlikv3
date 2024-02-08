$(document).ready(function () {
    $('.cardCreateCustomPercentages').hide();

    $('#pricesList2').change(function (e) { 
        e.preventDefault();
        let data = JSON.parse(sessionStorage.getItem('dataPriceList'));

        for (let i = 0; i < data.length; i++) {
            if (data[i].id_price_list == this.value) {
                $('#percentage').val(parseFloat(data[i].percentage).toLocaleString('es-CO', { maximumFractionDigits: 2 }));
                break;
            }  
        }
    });

    $('#btnNewCustomPercentage').click(function (e) {
        e.preventDefault();

        let dataPriceList = JSON.parse(sessionStorage.getItem('dataPriceList'));

        if (dataPriceList.length == 0) {
            toastr.error('Ingrese lista de precios');
            return false;
        }
         
        if (flag_type_price == '') {
            let btxMessage = '';
            btxMessage = `<label>Seleccione Precio</label>
                       <select class="form-control" id="selectPricesCustom">
                            <option disabled selected>Seleccionar</option>
                            <option value="0">ACTUAL</option>
                            <option value="1">SUGERIDO</option>
                       </select>`; 

            bootbox.confirm({
                title: 'Tipo de Precio',
                message: btxMessage,
                buttons: {
                    confirm: {
                        label: 'Si',
                        className: 'btn-success',
                    },
                    cancel: {
                        label: 'No',
                        className: 'btn-danger',
                    },
                },
                callback: function (result) {
                    if (result == true) {
                        typePrice = parseFloat($('#selectPricesCustom').val());
                    
                        if ((!typePrice && typePrice != 0)) {
                            toastr.error('Seleccione tipo de precio');
                            return false;
                        }

                        newCustomPercentage();
                    }
                },
            });
        } else {
            typePrice = flag_type_price;
            newCustomPercentage();
        }
    });

    newCustomPercentage = async () => {
        op_price_list = false;

        $('#btnCreateCustomPercentage').html('Adicionar');

        sessionStorage.removeItem('id_custom_percentage');

        $('#formCreateCustomPercentage').trigger('reset');

        let visible = $('.cardCreateCustomPercentages').is(':visible');

        if (visible == false) await loadPriceList();

        $('.cardCreateCustomPercentages').toggle(800);
        $('.cardCreateCustomPrices').hide(800);
    }

    $('#btnCreateCustomPercentage').click(async function (e) {
        e.preventDefault();

        let priceList = parseFloat($('#pricesList2').val());
        let percentage = parseFloat($('#percentage').val());

        if (!priceList) {
            toastr.error('Ingrese todos los datos');
            return false;
        }

        if (percentage > 100) {
            toastr.error('Ingrese un porcentaje valido');
            return false;
        } 

        let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));

        $('#modalNotProducts').modal('show');
        $('#nameNotProducts').html('Seleccione Producto');
        $('#btnSaveProducts').show();
        loadTblNotProducts(dataProducts, 2); 
    });

    $('#btnCloseNotProducts').click(function (e) { 
        e.preventDefault();
        $('.check').prop('checked', false);
        products = [];
        $('#modalNotProducts').modal('hide');
    });
});