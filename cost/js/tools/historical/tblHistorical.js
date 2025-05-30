$(document).ready(function () {
    let historical = [];
    let maxProfitability = 0;
    let minProfitability = 0;

    loadTblPrices = async (data) => {
        if ($.fn.dataTable.isDataTable("#tblHistorical")) {
            $("#tblHistorical").DataTable().clear();
            $("#tblHistorical").DataTable().rows.add(data).draw();
            return;
        }

        tblHistorical = $('#tblHistorical').DataTable({
            destroy: true,
            pageLength: 50,
            data: data,
            dom: '<"datatable-error-console">frtip',
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
                    title: historicalConfig.companyConfig === 1 ? 'Año / Mes' : 'Periodo',
                    data: 'formattedPeriod'
                },
                {
                    title: 'Referencia',
                    data: 'reference'
                },
                {
                    title: 'Producto',
                    data: 'product'
                },
                {
                    title: 'Precio (Sugerido)',
                    data: 'price',
                    render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
                },
                {
                    title: 'Precio (Lista)',
                    data: 'sale_price',
                    render: function (data) {
                        return data > 0 ? `$ ${data.toLocaleString('es-CO', { maximumFractionDigits: 0 })}` : '';
                    },
                },
                {
                    title: 'Rentabilidad',
                    data: null,
                    render: function (data) {
                        const profitabilityText = `${data.min_profitability.toLocaleString("es-CO", { maximumFractionDigits: 2 })} %`;
                        let badgeClass = "badge badge-success";

                        if (data.min_profitability < data.profitability) {
                            badgeClass = data.min_profitability > 0 ? "badge badge-warning" : "badge badge-danger";
                        }

                        return `<span class="${badgeClass}" style="font-size: medium;">${profitabilityText}</span>`;
                    }
                },
                {
                    title: 'Acciones',
                    data: 'id_historic',
                    className: 'text-center',
                    orderable: false,
                    render: function (data) {
                        return `<a href="javascript:;" class="seeDetail">
                                    <i id="${data}" class="bi bi-zoom-in" data-toggle='tooltip' title='Ficha Técnica de Costos' style="font-size: 30px;"></i>
                                </a>`;
                    },
                },
            ],
        });
    }

    // Filtros
    $('#period, #year').change(function () {
        const period = $('#period').val();
        const year = $('#year').val();

        let filteredData = historical;

        if (year !== '0') {
            filteredData = filteredData.filter(item => item.year == year);
        }

        if (period !== '0') {
            if (companyConfig === 1) {
                filteredData = filteredData.filter(item => item.month == period);
            } else {
                filteredData = filteredData.filter(item => getWeekNumber(item.date) == period);
            }
        }

        loadTblPrices(filteredData);
    });

    loadAllData();
});
