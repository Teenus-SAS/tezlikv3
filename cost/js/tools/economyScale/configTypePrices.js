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
                                if (op == 1) {
                                    $('#labelDescription').html(` Descripción (${flag_type_price == '0' ? 'Precio Actual' : 'Precio Sugerido'}) `);
                                } else if (op == 2) {
                                    $('#lblPrice').html(flag_type_price == '0' ? 'Precio Actual' : 'Precio Sugerido');
                                    loadTblMultiproducts();
                                    getUSDData(0);
                                } else {
                                    $('#headerPrices').html(` Precios (${flag_type_price == '0' ? 'Precio Actual' : 'Precio Sugerido'}) `);
                                    $('#tblProcess').DataTable().clear();
                                    $('#tblProcess').DataTable().ajax.reload();
                                }
                            }
                            else if (data.error == true) toastr.error(data.message);
                            else if (data.info == true) toastr.info(data.message);
                        },
                    );
                },
            });
        } else {
            if (op == 1) {
                $('#labelDescription').html(` Descripción (${flag_type_price == '0' ? 'Precio Actual' : 'Precio Sugerido'}) `);

                if (flag_type_price === '0') {
                    document.getElementById("actual").className =
                        "btn btn-sm btn-primary typePrice cardBottons";
                    document.getElementById("sugered").className =
                        "btn btn-sm btn-outline-primary typePrice cardBottons";
                } else {
                    document.getElementById("sugered").className =
                        "btn btn-sm btn-primary typePrice cardBottons";
                    document.getElementById("actual").className =
                        "btn btn-sm btn-outline-primary typePrice cardBottons";
                }
            }
            else if (op == 2) {
                $('#lblPrice').html(flag_type_price == '0' ? 'Precio Actual' : 'Precio Sugerido');
                loadTblMultiproducts();
            } else {
                $('#headerPrices').html(` Precios (${flag_type_price == '0' ? 'Actual' : 'Sugerido'}) `);
            }
        }
            
    };

    $(document).on("click", ".typePrice", function () {
        let op = this.value;
        let className = this.className;

        sessionStorage.setItem('flag_type_price', op);
        // typePrice = op;
        let id = $('#selectNameProduct').val();

        if (!id) {
            toastr.error('Seleccione un producto');
            return false;
        }

        // Precio Sugerido
        if (op == '1' && className.includes("btn-outline-primary")) {
            //$('#labelDescription').html(`Descripción (Precio Sugerido)`);

            document.getElementById("sugered").className =
                "btn btn-sm btn-primary typePrice cardBottons";
            document.getElementById("actual").className =
                "btn btn-sm btn-outline-primary typePrice cardBottons";
            document.getElementById("real").className =
                "btn btn-sm btn-outline-primary typePrice cardBottons";
        } else if (op == '2' && className.includes("btn-outline-primary")) { // Precio Lista
            $('#labelDescription').html(`Descripción (Precio Lista)`);

            document.getElementById("actual").className =
                "btn btn-sm btn-primary typePrice cardBottons";
            document.getElementById("sugered").className =
                "btn btn-sm btn-outline-primary typePrice cardBottons";
            document.getElementById("real").className =
                "btn btn-sm btn-outline-primary typePrice cardBottons"; 
        } else if (className.includes("btn-outline-primary")) { // Precio Real
            $('#labelDescription').html(`Descripción (Precio Real)`);

            document.getElementById("real").className =
                "btn btn-sm btn-primary typePrice cardBottons";
            document.getElementById("actual").className =
                "btn btn-sm btn-outline-primary typePrice cardBottons";
            document.getElementById("sugered").className =
                "btn btn-sm btn-outline-primary typePrice cardBottons";
        }

        loadDataProduct(id);
    });
});