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

            // let lastData = await searchData('/api/lastHistorical');
            if (d_historical) {
                let lastDate = new Date(date_product);

                if (date.getFullYear() == lastDate.getFullYear() && date.getMonth() == lastDate.getMonth()) {
                    status = true;
                }
            }

            if (status == false && modalActive == false) { 
                $('#HistoricalContent').html(`¿Desea Guardar los productos creados el mes de ${months1[date.getMonth()]}?`);
                modalActive = true; 
                $('#modalHistorical').modal('show');
            } else {
                if (typeof checkFirstLogin === 'function')
                checkFirstLogin();
            }
        
        }

        if (date.getDay() == 1)
            checkFirstDay();

        $('.btnCloseHistorical').click(function (e) {
            e.preventDefault();
        
            $('#modalHistorical').modal('hide');
            modalActive = false;

            if (typeof checkFirstLogin === 'function')
                checkFirstLogin();
        });

        $('#btnSaveAutoHistorical').click(async function (e) {
            e.preventDefault();

            modalActive = false;
            await saveHistorical({ type: 'auto' });

            if (typeof checkFirstLogin === 'function')
                checkFirstLogin();
        });
    } else {
        $('#btnNewManualHistorical').click(function (e) {
            e.preventDefault();

            $('#datepicker').val('');

            $('#modalHistorical').modal('show');
        }); 

        $('#btnSaveManualHistorical').click(function (e) {
            e.preventDefault();

            let date = $('#datepicker').val();

            if (!date || date == '') {
                toastr.error('Ingrese la fecha');
                return false;
            }

            date = date.split('-');
            let data = {};

            data['year'] = date[0];
            data['month'] = date[1];

            let historicalProducts = historical.filter((item) => item.month == date[1] && item.year == date[0]);
            data['products'] = historicalProducts;
            $('#modalHistorical').modal('hide');

            bootbox.confirm({
                title: 'Confirmar',
                message:
                    '¿Desea guardar datos de ese mes? Esta acción no se puede reversar.',
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
                    }
                },
            }); 
        });

        
    
        $('#btnCloseHistorical').click(function (e) {
            e.preventDefault();
        
            $('#modalHistorical').modal('hide');
        });
    
    }
    
    const saveHistorical = (data) => {
        $.post('/api/saveHistorical', data,
            function (data, textStatus, jqXHR) {
                if (data.reload) {
                    location.reload();
                }
                
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