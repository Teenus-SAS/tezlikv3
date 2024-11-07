$(document).ready(function () {
    loadContract = async () => {
        // let data = await searchData('/api/contracts');
        
        if (d_contract === "1") {
            if (date_contract === "0" && modalActive === false) {
                modalActive = true;

                bootbox.confirm({
                    title: 'Contrato de Prestación de Servicios',
                    message: c_content,
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
                        modalActive = false;
                        if (result == true) {
                            $.get(
                                `/api/changeDateContract/1`,
                                function (data, textStatus, jqXHR) {
                                    if (data.reload) {
                                        location.reload();
                                    }
                                    
                                    if (data.success == true) {
                                        toastr.success(data.message);
                                        return false;
                                    } else if (data.error == true) toastr.error(data.message);
                                    else if (data.info == true) toastr.info(data.message);
                                }
                            );
                        } else {
                            $.get(
                                `/api/logoutInactiveUser`,
                                function (data, textStatus, jqXHR) {
                                    location.href = '/';
                                }
                            );
                        }

                        if (typeof checkFirstDay === 'function')
                            checkFirstDay();
                    
                        if (typeof checkFirstLogin === 'function')
                            checkFirstLogin();
                    },
                }).find('div.modal-content').addClass('confirmWidth')
                    .find('div.bootbox-body').addClass('bootbox1-body');
            }
        } else {                    
            if (typeof checkFirstLogin === 'function')
                checkFirstLogin();
        }
    };

    if (contract === "1" && d_contract === "1") loadContract();
});