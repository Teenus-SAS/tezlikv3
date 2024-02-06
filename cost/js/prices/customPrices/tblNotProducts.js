$(document).ready(function () {
    loadTblNotProducts = (data, op) => {

        op === 1 ? visible = false : visible = true;

        if ($.fn.dataTable.isDataTable("#tblNotProducts")) {
            $("#tblNotProducts").DataTable().destroy();
            $("#tblNotProducts").empty();
        }

        tblNotProducts = $('#tblNotProducts').dataTable({
            destroy: true,
            scrollY: '150px',
            scrollCollapse: true,
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
                    visible: visible,
                    className: 'uniqueClassName',
                    render: function (data, type, full, meta) {
                        return `<input type="checkbox" class="form-control-updated check ¨¨${data.reference}¨¨${data.product}¨¨${data.price}¨¨${data.sale_price}" id="check-${data.id_product}">`;
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
            ],
        });
 
        setTimeout(() => {
            let tables = document.getElementsByClassName(
                'dataTables_scrollHeadInner'
            );

            let attr = tables[0];
            attr.style.width = '100%';
            attr = tables[0].firstElementChild;
            attr.style.width = '100%';
        }, 1000);
        
    }
});