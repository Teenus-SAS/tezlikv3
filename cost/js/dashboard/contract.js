$(document).ready(function () {
    loadContract = async () => {
        let data = await searchData('/api/contracts');
        
        if (date_contract == '0')
            bootbox.confirm({
                title: 'Contrato de Prestación de Servicios',
                message: data.content,
                buttons: {
                    confirm: {
                        label: 'Aceptar',
                        className: 'btn-success',
                    },
                    cancel: {
                        label: 'Rechazar',
                        className: 'btn-danger',
                    },
                },
                callback: function (result) {
                    if (result == true) {
                        $.get(
                            `/api/changeDateContract/1`,
                            function (data, textStatus, jqXHR) {
                                if (data.success == true) {
                                    toastr.success(data.message);
                                    return false;
                                } else if (data.error == true) toastr.error(data.message);
                                else if (data.info == true) toastr.info(data.message);
                            }
                        );
                    } else
                        $.get(
                            `/api/logoutInactiveUser`,
                            function (data, textStatus, jqXHR) {
                                location.href = '/';
                            }
                        );
                },
            }).find('div.modal-content').addClass('confirmWidth')
                .find('div.bootbox-body').addClass('bootbox1-body');
    };

    if (contract == '1') loadContract();
});