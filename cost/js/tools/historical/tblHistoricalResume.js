// Verificar que la configuración esté cargada
if (typeof historicalConfig === 'undefined') {
    console.error('Configuración histórica no definida');
}

tblHistoricalResume = $('#tblHistoricalResume').DataTable({
    destroy: true,
    pageLength: 50,
    dom: '<"datatable-error-console">frtip',
    ajax: {
        url: "/api/historical/historicalResume",
        dataSrc: "",
        complete: function () {
            //loadingModalLoad.modal('hide');
        }
    },
    language: {
        url: '/assets/plugins/i18n/Spanish.json',
    },
    columns: [
        {
            title: 'No.',
            data: null,
            render: function (data, type, full, meta) {
                return meta.row + 1;
            },
        },
        {
            title: historicalConfig.companyConfig === 0 ? 'Mes' : 'Semana',
            data: 'month',
            className: 'text-center',
        },
        {
            title: 'Año',
            data: 'year',
            className: 'text-center',
        },
        {
            title: 'Cantidad de Productos',
            data: 'total_productos',
            className: 'text-center',
        },
        {
            title: 'Fecha de Registro',
            data: 'ultima_fecha_registro',
            className: 'text-center',
        },
        {
            title: 'Acciones',
            data: null,
            className: 'text-center',
            orderable: false,
            render: function (data) {
                return `<a href="javascript:;" class="seeProducts">
                                <i class="bi bi-zoom-in" data-id="${data.year}/${data.month}" data-toggle='tooltip' title='Detalle de Productos' style="font-size: 30px;"></i>
                            </a>
                            <a href="javascript:;" class="deleteHistoricProducts">
                                <i class="bi bi-trash text-danger" data-id="${data.year}/${data.month}" data-toggle='tooltip' title='Detalle de Productos' style="font-size: 30px;"></i>
                            </a>`;
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


