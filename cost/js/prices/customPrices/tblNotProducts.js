$(document).ready(function () {
    loadTblNotProducts = (data) => {
        tblNotProducts = $('#tblNotProducts').dataTable({
            destroy: true,
            scrollY: '150px',
            scrollCollapse: true,
            data: data,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
            },
            columns: [
                // {
                //     title: 'No.',
                //     data: null,
                //     className: 'uniqueClassName',
                //     render: function (data, type, full, meta) {
                //         return meta.row + 1;
                //     },
                // },
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
            ],
        });


        // setInterval(() => {
        //     let tables = document.getElementsByClassName(
        //         'dataTables_scrollHeadInner'
        //     );

        //     let attr = tables[0];
        //     attr.style.width = '100%';
        //     attr = tables[0].firstElementChild;
        //     attr.style.width = '100%';
        // }, 500);
        
    }
});