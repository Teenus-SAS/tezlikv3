$(document).ready(function () {
    /* Ocultar panel crear produccion */
    $('.cardAddNewProductionCenter').hide();

    /* Abrir panel crear produccion */
    $('#btnNewProductionCenter').click(function (e) {
        e.preventDefault();

        // $('.cardImportProcess').hide(800);
        $('.cardAddNewProductionCenter').toggle(800);
        $('#btnSavePCenter').html('Crear');

        sessionStorage.removeItem('id_production_center');

        $('#production').val('');
    });

    /* Crear nuevo produccion */
    $('#btnSavePCenter').click(function (e) {
        e.preventDefault();

        let id_production_center = sessionStorage.getItem('id_production_center');

        if (id_production_center == '' || id_production_center == null) {
            checkDataPCenter('/api/addPCenter', id_production_center);
        } else {
            checkDataPCenter('/api/updatePCenter', id_production_center);
        }
    });

    /* Actualizar produccion */
    $(document).on('click', '.updatePCenter', function (e) {
        // $('.cardImportProcess').hide(800);
        $('.cardAddNewProductionCenter').show(800);
        $('#btnSavePCenter').html('Actualizar');

        let row = $(this).parent().parent()[0];
        let data = tblPCenter.fnGetData(row);

        sessionStorage.setItem('id_production_center', data.id_production_center);
        $('#production').val(data.production_center);

        $('html, body').animate(
            {
                scrollTop: 0,
            },
            1000
        );
    });

    /* Revision data procesos */
    checkDataPCenter = async (url, id_production_center) => {
        let production_center = $('#production').val();

        if (production_center.trim() == '' || !production_center.trim()) {
            toastr.error('Ingrese todos los campos');
            return false;
        }

        let dataPCenter = new FormData(formAddPCenter);

        if (id_production_center != '' || id_production_center != null)
            dataPCenter.append('idProductionCenter', id_production_center);

        let resp = await sendDataPOST(url, dataPCenter);

        messagePCenter(resp);
    };

    /* Eliminar produccion */

    deletePCenter = () => {
        let row = $(this.activeElement).parent().parent()[0];
        let data = tblPCenter.fnGetData(row);
      
        let status = parseInt(data.status);

        if (status != 0) {
            toastr.error('Esta produccion no se puede eliminar, esta configurado a un gasto');
            return false;
        }

        let id_production_center = data.id_production_center;

        bootbox.confirm({
            title: 'Eliminar',
            message:
                'Está seguro de eliminar esta produccion? Esta acción no se puede reversar.',
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
                    $.get(
                        `../../api/deletePCenter/${id_production_center}`,
                        function (data, textStatus, jqXHR) {
                            messagePCenter(data);
                        }
                    );
                }
            },
        });
    };

    /* Mensaje de exito */
    messagePCenter = (data) => {
        // $('.cardLoading').remove();
        // $('.cardBottons').show(400);
        // $('#fileProcess').val('');
    
        if (data.success == true) {
            // $('.cardImportProcess').hide(800);
            // $('#formImportProcess').trigger('reset');
            $('.cardAddNewProductionCenter').hide(800);
            $('#formAddPCenter').trigger('reset');
      
            loadAllDataPCenter();
            toastr.success(data.message);
            return false;
        } else if (data.error == true) toastr.error(data.message);
        else if (data.info == true) toastr.info(data.message);
    };
});