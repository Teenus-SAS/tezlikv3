$(document).ready(function () {

    loadDataContract = async () => {
        let data = await searchData('/api/contracts');

        if (data) {
            $('#idContract').val(data.id_contract);
            setContent(data.content);
        }
    }

    /* Crear nuevo proceso */

    $('#btnSave').click(async function (e) {
        e.preventDefault();

        let content = getContent(2);

        if (content.trim() == '' || !content.trim()) {
            toastr.error('Ingrese todos los campos');
            return false;
        }

        let dataContract = new FormData(formSaveContract);

        dataContract.append('content', content);

        let resp = await sendDataPOST('/api/saveContract', dataContract);

        message(resp);
    });

    /* Mensaje de exito */
    message = (data) => {
        if (data.success == true) {
            loadDataContract();
            toastr.success(data.message);
            return false;
        } else if (data.error == true) toastr.error(data.message);
        else if (data.info == true) toastr.info(data.message);
    };

    loadDataContract();
});
