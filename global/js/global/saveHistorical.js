$(document).ready(function () {
    let date = new Date();

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

            if (status == false)
                $('#modalHistorical').modal('show');
        
        }

        if (date.getDay() == 1)
            checkFirstDay();

        $('#btnCloseHistorical').click(function (e) {
            e.preventDefault();
        
            $('#modalHistorical').modal('hide');
        });

        $('#btnSaveAutoHistorical').click(function (e) {
            e.preventDefault();
        
            $.post('/api/saveHistorical', { type: 'auto' },
                function (data, textStatus, jqXHR) {
                    if (data.success == true) {
                        toastr.success(data.message);
                        $('#modalHistorical').modal('hide');
                    }
                    else if (data.error == true) toastr.error(data.message);
                    else if (data.info == true) toastr.info(data.message);
                },
            );
        });
    } else {
        $('#btnNewManualHistorical').click(function (e) {
            e.preventDefault();
        
            if (historical.length) {
                $('.lblRescribtion').show();
                $('#btnSaveManualHistorical').html('Reescribir');
            }

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

            data['type'] = 'manual';
            data['month'] = date[0];
            data['year'] = date[1];

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
        });
    
        $('#btnCloseHistorical').click(function (e) {
            e.preventDefault();
        
            $('#modalHistorical').modal('hide');
        });
    
    }
});