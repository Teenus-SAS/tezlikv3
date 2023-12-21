$(document).ready(function () {
    let date = new Date();
    months1 = {
        1: 'Enero',
        2: 'Febrero',
        3: 'Marzo',
        4: 'Abril',
        5: 'Mayo',
        6: 'Junio',
        7: 'Julio',
        8: 'Agosto',
        9: 'Septiembre',
        10: 'Octubre',
        11: 'Noviembre',
        12: 'Diciembre'
    };

    if (type == 'auto') {
        checkFirstDay = async () => {
            let status = false;

            let lastData = await searchData('/api/lastHistorical');
            if (lastData) {
                let lastDate = new Date(lastData.date_product);

                if (date.getFullYear() == lastDate.getFullYear() && date.getMonth() == lastDate.getMonth()) {
                    status = true;
                }
            }

            if (status == false) {
                $('#HistoricalContent').html(`¿Desea Guardar los productos creados el mes de ${months1[date.getMonth()]}?`);
                
                $('#modalHistorical').modal('show');
            }
        
        }

        if (date.getDay() == 1)
            checkFirstDay();

        $('#btnCloseHistorical').click(function (e) {
            e.preventDefault();
        
            $('#modalHistorical').modal('hide');
        });

        $('#btnSaveAutoHistorical').click(function (e) {
            e.preventDefault();

            saveHistorical({ type: 'auto' });
        
            // $.post('/api/saveHistorical', { type: 'auto' },
            //     function (data, textStatus, jqXHR) {
            //         if (data.success == true) {
            //             toastr.success(data.message);
            //             $('#modalHistorical').modal('hide');
            //         }
            //         else if (data.error == true) toastr.error(data.message);
            //         else if (data.info == true) toastr.info(data.message);
            //     },
            // );
        });
    } else {
        $('#btnNewManualHistorical').click(function (e) {
            e.preventDefault();

            $('#datepicker').val('');

            $('#modalHistorical').modal('show');
        });

        $("#datepicker").datepicker({
            format: "mm/yyyy",
            startView: "year",
            minView: "year"
        });

        $('#btnSaveManualHistorical').click(function (e) {
            e.preventDefault();

            let date = $('#datepicker').val();

            if (!date || date == '') {
                toastr.error('Ingrese la fecha');
                return false;
            }

            date = date.split('/');
            let data = {};

            data['month'] = date[0];
            data['year'] = date[1];

            let historicalProducts = historical.filter((item) => item.month == date[0] && item.year == date[1]);
            data['products'] = historicalProducts;
            $('#modalHistorical').modal('hide');

            if (historicalProducts.length > 0) {
                bootbox.confirm({
                    title: 'Reescribir',
                    message:
                        '¿Desea reescribir datos de ese mes?',
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
                            saveHistorical({ data: data });
                        }
                    },
                });
            } else saveHistorical({ data: data });
            
        });

        
    
        $('#btnCloseHistorical').click(function (e) {
            e.preventDefault();
        
            $('#modalHistorical').modal('hide');
        });
    
    }
    
    const saveHistorical = (data) => {
        $.post('/api/saveHistorical', data,
            function (data, textStatus, jqXHR) {
                if (data.success == true) {
                    toastr.success(data.message);
                    $('#modalHistorical').modal('hide');
                    loadAllData();
                }
                else if (data.error == true) toastr.error(data.message);
                else if (data.info == true) toastr.info(data.message);
            },
        );
    }
});