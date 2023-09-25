$(document).ready(function () { 
    checkFlagPrice = async (op) => {
        if (flag_type_price == '') {
            bootbox.confirm({
                closeButton: false,
                title: 'Tipo de Precio',
                message: 'Seleccione a cual tipo de precio desea ejecutar.',
                buttons: {
                    confirm: {
                        label: 'Sugerido',
                        className: 'btn-success',
                    },
                    cancel: {
                        label: 'Actual',
                        className: 'btn-info',
                    },
                },
                callback: function (result) {
                    result == false ? (flag_type_price = '0') : (flag_type_price = '1');
                    
                    $.get(`/api/changeFlagPrice/${flag_type_price}`,
                        function (data, textStatus, jqXHR) {
                            if (data.success == true) {
                                toastr.success(data.message);
                                if (op == 1)
                                    $('#labelDescription').html(` Descripción (${flag_type_price == '0' ? 'Precio Actual' : 'Precio Sugerido'}) `);
                                else {
                                    $('#lblPrice').html(flag_type_price == '0' ? 'Precio Actual' : 'Precio Sugerido');
                                    loadTblMultiproducts();
                                }
                            }
                            else if (data.error == true) toastr.error(data.message);
                            else if (data.info == true) toastr.info(data.message);
                        },
                    );
                },
            });
        } else {
            if (op == 1)
                $('#labelDescription').html(` Descripción (${flag_type_price == '0' ? 'Precio Actual' : 'Precio Sugerido'}) `);
            else {
                $('#lblPrice').html(flag_type_price == '0' ? 'Precio Actual' : 'Precio Sugerido');
                loadTblMultiproducts();
            }
        }
            
    }; 
});