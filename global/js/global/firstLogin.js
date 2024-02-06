$(document).ready(function () {
    checkFirstLogin = () => { 
        modalActive = true;
        $('#modalFirstLogin').modal('show'); 
    }

    if (modalActive == false)
        checkFirstLogin();

    $('#btnSaveFirstLogin').click(async function (e) {
        e.preventDefault();
        let firstname = $('#firstname').val();
        let lastname = $('#lastname').val();
        let telephone = $('#telephone').val();

        if (!firstname || !lastname || !telephone) {
            toastr.error('Ingrese todos los campos');
            return false;
        }

        let data = new FormData(formFirstLogin);

        let resp = await sendDataPOST('/api/saveFirstLogin', data);
    
        if (resp.success == true) {
            $('#modalFirstLogin').modal('hide');
            modalActive = false;
            $('.userName').html(`${firstname} ${lastname}`);
            toastr.success(resp.message);
            updateTable();
            return false;
        } else if (resp.error == true) toastr.error(resp.message);
        else if (resp.info == true) toastr.info(resp.message);
    });
});