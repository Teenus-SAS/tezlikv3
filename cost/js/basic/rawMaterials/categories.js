
$('.cardAddCategories').hide();

/* Abrir panel crear producto */
$('#btnNewCategory').click(function (e) {
    e.preventDefault();

    $('.cardImportCategories').hide(800);
    $('.cardAddCategories').toggle(800);
    $('#btnCreateCategory').html('Crear');

    sessionStorage.removeItem('id_category');

    $('#category').val('');
});

/* Crear nuevo categoria */

$('#btnCreateCategory').click(function (e) {
    e.preventDefault();

    let idCategory = sessionStorage.getItem('id_category');

    if (idCategory == '' || idCategory == null) {
        checkDataCategory('/api/categories/addCategory', idCategory);
    } else {
        checkDataCategory('/api/categories/updateCategory', idCategory);
    }
});

/* Actualizar categorias */

$(document).on('click', '.updateCategory', function (e) {
    $('.cardImportCategories').hide(800);
    $('.cardAddCategories').show(800);
    $('#btnCreateCategory').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblCategories.fnGetData(row);

    sessionStorage.setItem('id_category', data.id_category);
    $('#category').val(data.category);

    $('html, body').animate(
        {
            scrollTop: 0,
        },
        1000
    );
});

/* Revision data categorias */
const checkDataCategory = async (url, idCategory) => {
    let category = $('#category').val();

    if (category.trim() == '' || !category.trim()) {
        toastr.error('Ingrese todos los campos');
        return false;
    }

    let dataCategory = new FormData(formCreateCategory);

    if (idCategory != '' || idCategory != null)
        dataCategory.append('idCategory', idCategory);

    let resp = await sendDataPOST(url, dataCategory);

    messageCategories(resp, 3);
};

/* Eliminar categoria */

deleteCategory = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblCategories.fnGetData(row);

    let status = parseInt(data.status);

    if (status != 0) {
        toastr.error('Esta categoria no se puede eliminar, esta configurado a una materia prima');
        return false;
    }

    let id_category = data.id_category;

    bootbox.confirm({
        title: 'Eliminar',
        message:
            'Está seguro de eliminar esta categoria? Esta acción no se puede reversar.',
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
                    `../../api/deleteCategory/${id_category}`,
                    function (data, textStatus, jqXHR) {
                        messageCategories(data, 3);
                    }
                );
            }
        },
    });
};

/* Mensaje de exito */
messageCategories = async (data, op) => {
    if (data.reload) {
        location.reload();
    }

    $('#fileCategories').val('');
    $('.cardLoading').remove();
    $('.cardBottons').show(400);

    if (data.success == true) {
        $('.cardImportCategories').hide(800);
        $('#formImportCategories').trigger('reset');
        $('.cardAddCategories').hide(800);
        $('#formCreateCategory').trigger('reset');
        toastr.success(data.message);
        await loadAllData(op);
        return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
};
