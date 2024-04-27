$(document).ready(function () {
    loadAllData = async () => {
        const [dataProducts, dataEconomyScale] = await Promise.all([
            searchData('/api/saleObjectives'),
            searchData('/api/calcEconomyScale')
        ]);

        sessionStorage.setItem('dataProducts', JSON.stringify(dataProducts));
        sessionStorage.setItem('allEconomyScale', JSON.stringify(dataEconomyScale));

        loadTblProducts(dataProducts);
    }

    /* Cargue tabla de Proyectos */

    loadTblProducts = (data) => {
        if ($.fn.dataTable.isDataTable("#tblProducts")) {
            var table = $("#tblProducts").DataTable();
            var pageInfo = table.page.info(); // Guardar información de la página actual
            table.clear();
            table.rows.add(data).draw();
            table.page(pageInfo.page).draw('page'); // Restaurar la página después de volver a dibujar los datos
            return;
        }

        tblProducts = $('#tblProducts').dataTable({
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
                    title: 'Referencia',
                    data: 'reference',
                    className: 'uniqueClassName',
                },
                {
                    title: 'Producto',
                    data: 'product',
                    className: 'uniqueClassName',
                },
                {
                    title: 'Unidades',
                    data: null,
                    className: 'uniqueClassName',
                    render: function (data) { 
                        return `<input type="text" class="form-control text-center" id="unitsSold-${data.id_product}" readonly>`
                    },
                },
                {
                    title: 'Precio Real',
                    data: 'real_price',
                    className: 'classCenter',
                    render: function (data) {
                        let price = parseFloat(data);

                        if (isNaN(price)) {
                            price = 0;
                        } else if (Math.abs(price) < 0.01) { 
                            price = price.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
                        } else
                            price = price.toLocaleString('es-CO', { maximumFractionDigits: 0 });
            
                        return `$ ${price}`;
                    },
                },
                // {
                //   width: '150px',
                //   title: 'Acciones',
                //   data: null,
                //   className: 'uniqueClassName',
                //   render: function (data) {
                //     return `
                //           ${flag_composite_product == 1 ? `<a href="javascript:;" <i id="${data.id_product}" class="${data.composite == 0 ? 'bi bi-plus-square-fill' : 'bi bi-dash-square-fill'} composite" data-toggle='tooltip' title='${data.composite == 0 ? 'Agregar' : 'Eliminar'} a producto compuesto' style="font-size:25px; color: #3e382c;"></i></a>` : ''}
                //           <a href="javascript:;" <i id="${data.id_product}" class="bx bx-copy-alt" data-toggle='tooltip' title='Clonar Producto' style="font-size: 30px; color:green" onclick="copyFunction()"></i></a>
                //           <a href="javascript:;" <i id="${data.id_product}" class="bx bx-edit-alt updateProducts" data-toggle='tooltip' title='Actualizar Producto' style="font-size: 30px;"></i></a>
                //           <a href="javascript:;" <i id="${data.id_product}" class="mdi mdi-delete-forever deleteProduct" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 30px;color:red"></i></a>
                //           `;
                //   },
                // },
            ],
        });
    }

    loadAllData();
});