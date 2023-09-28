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

    $('#btnNewCustomPercentage').click(async function (e) {
        e.preventDefault();
        
        op_price_list = false;

        $('#btnCreateCustomPercentage').html('Adicionar');

        sessionStorage.removeItem('id_custom_percentage');

        $('#formCreateCustomPercentage').trigger('reset'); 

        let visible = $('.cardCreateCustomPercentages').is(':visible');

        if (visible == false) await loadPriceList();

        $('.cardCreateCustomPercentages').toggle(800);
        $('.cardCreateCustomPrices').hide(800);
    });

    $('#btnCreateCustomPercentage').click(function (e) {
        e.preventDefault();

        let data = combinedData[0];

        let btxMessage = ''; 

        if (!data) {
            btxMessage = `<label>Seleccione Precio</label>
                       <select class="form-control" id="selectPricesCustom">
                            <option disabled selected>Seleccionar</option>
                            <option value="0">ACTUAL</option>
                            <option value="1">SUGERIDO</option>
                       </select>`;
        } else {
            btxMessage = `<select class="form-control" id="selectPricesCustom">
                            <option disabled>Seleccionar</option>
                            ${data.flag_price == '0' ?
                                '<option value="0" selected>ACTUAL</option>' :
                                '<option value="1">SUGERIDO</option>'}
                        </select>`;
        }

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
                    let priceList = parseFloat($('#pricesList2').val());
                    let percentage = parseFloat($('#percentage').val());
                    let typePrice = parseFloat($('#selectPricesCustom').val());

                    if (!priceList || (!typePrice && typePrice != 0)) {
                        toastr.error('Ingrese todos los datos');
                        return false;
                    }

                    if (percentage > 100) {
                        toastr.error('Ingrese un porcentaje valido');
                        return false;
                    }

                    let data = $('#formCreateCustomPercentage').serialize();
                    typePrice == '0' ? namePrice = 'sale_price' : namePrice = 'price';
                    data = `${data}&name=${namePrice}&typePrice=${typePrice}`;

                    $.post('/api/addCustomPercentage', data,
                        function (data, textStatus, jqXHR) {
                            message(data);
                            $('#modalNotProducts').modal('show');
                            loadPriceList(1);
                            loadTblNotProducts(data.dataNotData);
                        },
                    );
                }
            },
        });

        
    });

    $('#btnCloseNotProducts').click(function (e) { 
        e.preventDefault();
        $('#modalNotProducts').modal('hide');
    });
});