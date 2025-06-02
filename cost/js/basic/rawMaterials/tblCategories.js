$(document).ready(function () {
    loadTblCategories = (data) => {
        if ($.fn.DataTable.isDataTable('#tblCategories')) {
            tblCategories.DataTable().clear().rows.add(data).draw();
        } else {
            tblCategories = $('#tblCategories').dataTable({
                destroy: true,
                pageLength: 50,
                data: data,
                dom: '<"datatable-error-console">frtip',
                language: {
                    url: '/assets/plugins/i18n/Spanish.json',
                },
                fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                    if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
                        console.error(oSettings.json.error);
                    }
                },
                columns: [
                    {
                        title: 'No.',
                        data: null,
                        className: 'uniqueClassName',
                        render: function (data, type, full, meta) {
                            return meta.row + 1;
                        },
                    },
                    {
                        title: 'Categoria',
                        data: 'category',
                        className: 'uniqueClassName',
                    },
                    {
                        title: 'Acciones',
                        data: null,
                        className: 'uniqueClassName',
                        render: function (data) {
                            if (data.status == 0) icon = '/global/assets/images/trash_v.png';
                            else {
                                icon = '/global/assets/images/trash_x.png';
                            }
                            return `
                        <a href="javascript:;" <i id="${data.id_category}" class="bx bx-edit-alt updateCategory" data-toggle='tooltip' title='Actualizar Categoria' style="font-size: 30px;"></i></a>
                        <a href="javascript:;"><img src="${icon}" alt="Eliminar Categoria" id="${data.id_category}" title='Eliminar Categoria' style="width:30px;height:30px;margin-top:-20px" onclick="deleteCategory()"></a>
                    `;
                        },
                    },
                ],
                headerCallback: function (thead, data, start, end, display) {
                    $(thead).find("th").css({
                        "background-color": "#386297",
                        color: "white",
                        "text-align": "center",
                        "font-weight": "bold",
                        padding: "10px",
                        border: "1px solid #ddd",
                    });
                },
            });
        }
    };
});