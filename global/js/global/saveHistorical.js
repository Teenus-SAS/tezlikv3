$(document).ready(function () {
    let date = new Date();

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

    // if(date.getDay() == 1)
        checkFirstDay();

    $('#btnCloseHistorical').click(function (e) { 
        e.preventDefault();
        
        $('#modalHistorical').modal('hide');
    });

    $('#btnSaveAutoHistorical').click(function (e) {
        e.preventDefault();
        
        $.get('/api/saveHistorical',
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

    $('#btnSaveManualHistorical').click(function (e) {
        e.preventDefault();
        
        bootbox.confirm({
            title: 'Ingrese Fecha De Ingreso!',
            message: `<div class="col-sm-12 floating-label enable-floating-label">
                        <input class="form-control" type="date" name="date" id="date"></input>
                        <label for="date">Fecha</span></label>
                      </div>`,
            buttons: {
                confirm: {
                    label: 'Agregar',
                    className: 'btn-success',
                },
                cancel: {
                    label: 'Cancelar',
                    className: 'btn-danger',
                },
            },
            callback: function (result) {
                if (result == true) {
                    let date = $('#date').val();

                    if (!date) {
                        toastr.error('Ingrese los campos');
                        return false;
                    }

          
                }
            },
        });
    });
});