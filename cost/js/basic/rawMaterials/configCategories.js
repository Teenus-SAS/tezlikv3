
getSelectCategories = async () => {
    await $.ajax({
        type: 'GET',
        url: '/api/categories',
        success: function (r) {
            if (r.reload) {
                location.reload();
            }

            let $select = $(`#categories`);
            $select.empty();
            sessionStorage.setItem('dataCategories', JSON.stringify(r));

            $select.append(`<option disabled selected>Seleccionar</option>`);
            $select.append(`<option value='0'>Todos</option>`);
            $.each(r, function (i, value) {
                $select.append(
                    `<option value ="${value.id_category}"> ${value.category} </option>`
                );
            });
        },
    });
}
