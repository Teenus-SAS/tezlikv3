$(document).ready(function () {
    $('#navChangeCompany').click(function (e) { 
        e.preventDefault();
        $('#formChangeCompany').trigger('reset');
        $('#modalChangeCompany').modal('show');
        let op = sessionStorage.getItem('op')
        $(`#sCompany option[value='${op}']`).prop('selected', true);
    });

    $('#btnCloseChangeCompany').click(function (e) {
        e.preventDefault();
        $('#modalChangeCompany').modal('hide'); 
    });

    $('#btnChangeCompany').click(function (e) {
        e.preventDefault();

        let company = $('#sCompany').val();

        if (!company || company == '') {
            toastr.error('Ingrese todos los datos');
            return false;
        }

        $('#user').prop('disabled', false);

        let data = $('#formChangeCompany').serialize();

        $.post("/api/changeCompany", data,
            function (data, textStatus, jqXHR) {
                if (data.success == true) {
                    $('#user').prop('disabled', true);

                    $('#modalChangeCompany').modal('hide');
                    $('#formChangeCompany').trigger('reset');
                    toastr.success(data.message);
                    return false;
                } else if (data.error == true) toastr.error(data.message);
                else if (data.info == true) toastr.info(data.message);
            },
        );
    });
});