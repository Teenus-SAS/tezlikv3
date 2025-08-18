$(document).ready(function () {
    let historical = [];
    let maxProfitability = 0;
    let minProfitability = 0;

    // Asegura variable global
    let loadingModal = null;

    window.loadTblHistoriProducts = async (period) => {
        loadingModal = showLoadingModal('Procesando Periodo Seleccionado...');

        try {
            if (!period || !/^\d{4}[\/-]?[wW]?\d{1,2}$/.test(period)) {
                throw new Error('Formato de período no válido. Use YYYY/WW o YYYY-WW');
            }

            const formattedPeriod = period.replace(/\//g, '-');
            const apiUrl = `/api/historicalData/historicalProducts/${formattedPeriod}`;
            //console.log('🔗 Solicitando:', apiUrl);

            // Pre-validación del endpoint antes de llamar a DataTable
            const res = await fetch(apiUrl);
            const json = await res.json();

            //if (!res.ok || !Array.isArray(json.data))
            if (!res.ok)
                throw new Error('Respuesta inválida del servidor.');

            // Reiniciar DataTable si ya existe
            if ($.fn.dataTable.isDataTable("#tblHistoricalProducts"))
                $('#tblHistoricalProducts').DataTable().clear().destroy();

            loadingModal.modal('hide');

            $('#tblHistoricalProducts').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: apiUrl,
                    type: 'GET',
                    dataSrc: function (json) {
                        return json;
                    },
                    error: function (xhr) {
                        let errorMsg = 'Error al cargar datos';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        }
                        toastr.error(errorMsg);
                    }
                },
                language: {
                    url: '/assets/plugins/i18n/Spanish.json',
                },
                columns: [
                    {
                        title: 'No.',
                        data: null,
                        render: (data, type, full, meta) => meta.row + 1
                    },
                    {
                        title: historicalConfig.companyConfig === 1 ? 'Año / Mes' : 'Periodo',
                        data: null,
                        render: function (data) {
                            return `${data.month}-${data.year}`;
                        }
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
                        render: $.fn.dataTable.render.number('.', ',', 0, '$ ')
                    },
                    {
                        title: 'Precio (Lista)',
                        data: 'sale_price',
                        render: function (data) {
                            return data > 0 ? `$ ${data.toLocaleString('es-CO', { maximumFractionDigits: 0 })}` : '';
                        }
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
                                    <i class="bi bi-zoom-in" data-id="${data}" data-toggle='tooltip' title='Ficha Técnica de Costos' style="font-size: 30px;"></i>
                                </a>`;
                        }
                    }
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
                initComplete: function () {
                    loadingModal.modal('hide');
                }
            });

        } catch (error) {
            console.error('⚠️ Error inicializando DataTable:', error.message);
            if (loadingModal) loadingModal.modal('hide');
            toastr.error(error.message || 'Error al inicializar la tabla de datos');
        }
    };

});