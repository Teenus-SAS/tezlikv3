$(document).ready(function () { 
    // loadAllDataCategories = async () => {
    //     try {
    //         const dataCategory = await searchData('/api/categories');

    //         // allCategories = dataCategory;

    //         if(dataCategory.length == 0){
    //             $('.categories').hide();
    //             visible = false;
    //         } else {
    //             $('.categories').show();
    //             visible = true;                
    //         }

    //         let $select = $(`#idCategory`);
    //         $select.empty();
    //         $select.append(`<option disabled selected value='0'>Seleccionar</option>`);
    //         $.each(dataCategory, function (i, value) {
    //             $select.append(
    //                 `<option value = ${value.id_category}> ${value.category} </option>`
    //             );
    //         });

    //         loadTblCategories(dataCategory);
    //     } catch (error) {
    //         console.error('Error loading data:', error);
    //     }
    // }  
    
    // loadAllDataCategories();

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
                    url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
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
                            // let check = '';
                            if (data.status == 0) icon = '/global/assets/images/trash_v.png';
                            else {
                                icon = '/global/assets/images/trash_x.png';
                                // check = `<a href="javascript:;" <i id="${data.id_category}" class="mdi mdi-playlist-check seeDetail" data-toggle='tooltip' title='Ver Detalle' style="font-size: 30px; color:black;"></i></a>`;
                            }

                            return `
                        <a href="javascript:;" <i id="${data.id_category}" class="bx bx-edit-alt updateCategory" data-toggle='tooltip' title='Actualizar Categoria' style="font-size: 30px;"></i></a>
                        <a href="javascript:;"><img src="${icon}" alt="Eliminar Categoria" id="${data.id_category}" title='Eliminar Categoria' style="width:30px;height:30px;margin-top:-20px" onclick="deleteCategory()"></a>
                    `;
                        },
                    },
                ],
            });
        }
    }
});