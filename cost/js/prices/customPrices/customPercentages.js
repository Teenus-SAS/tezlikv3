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

        let priceList = parseFloat($('#pricesList2').val());
        let typePrice = parseFloat($('#typePrice').val());
        let percentage = parseFloat($('#percentage').val());

        if (!priceList || (!typePrice && typePrice != 0)) {
            toastr.error('Ingrese todos los datos');
            return false;
        }

        if (percentage > 100 || percentage == 0) {
            toastr.error('Ingrese un porcentaje valido');
            return false;
        }

        let  data = $('#formCreateCustomPercentage').serialize();
        typePrice == '0' ? namePrice = 'sale_price' : namePrice = 'price';
        data = `${data}&name=${namePrice}`;

        $.post('/api/addCustomPercentage', data,
            function (data, textStatus, jqXHR) { 
                message(data);
                $('#modalNotProducts').modal('show');
                loadPriceList(1);
                loadTblNotProducts(data.dataNotData);
            },
        );
    });

    $('#btnCloseNotProducts').click(function (e) { 
        e.preventDefault();
        $('#modalNotProducts').modal('hide');
    });
});