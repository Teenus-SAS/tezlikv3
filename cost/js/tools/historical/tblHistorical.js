$(document).ready(function () {
    const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    
    $(document).on('click', '.seeDetail', function (e) {
        sessionStorage.removeItem('idHistoric');
        let id_historic = this.id;
        sessionStorage.setItem('idHistoric', id_historic);
    });
    /* Cargue tabla de Precios */

    loadAllData = async () => {
        try {
            historical = await searchData('/api/historical');
            const mesesInvertidos = {};
            const yearsInvertidos = {};

            historical.forEach(item => {
                mesesInvertidos[item.month] = months[item.month - 1];
            });

            historical.forEach(item => {
                yearsInvertidos[item.year] = item.year;
            });

            historicalIndicatiors(historical);

            let $select = $(`#month`);
            $select.empty();
            $select.append(`<option disabled selected>Seleccionar</option>`);
            $select.append('<option value="0">Todo</option>');
            $.each(mesesInvertidos, function (i, value) {
                $select.append(
                    `<option value="${i}">${value}</option>`
                );
            });

            let $select1 = $('#year');
            $select1.empty();
            $select1.append('<option disabled selected>Seleccionar</option>');
            $select1.append('<option value="0">Todo</option>');

            $.each(yearsInvertidos, function (i, value) {
                $select1.append(
                    `<option value="${i}">${value}</option>`
                );
            });

            loadTblPrices(historical);
        } catch (error) {
            console.error('Error loading data:', error);
        }
    }

    historicalIndicatiors = (data) => {
        maxProfitability = 0;
        minProfitability = 0;
        let totalProfitability = 0;
        let averageProfitability = 0;

        if (data.length > 0) {
            maxProfitability = Math.max(...data.map(obj => obj.min_profitability));
            minProfitability = Math.min(...data.map(obj => obj.min_profitability));
            totalProfitability = data.reduce((acc, obj) => acc + obj.min_profitability, 0);
            averageProfitability = totalProfitability / data.length;
        }

        $('#lblMaxProfitability').html(` Rentab +Alta: ${maxProfitability.toLocaleString('es-CO', { maximumFractionDigits: 2 })} %`);
        $('#lblMinProfitability').html(` Rentab +Baja: ${minProfitability.toLocaleString('es-CO', { maximumFractionDigits: 2 })} %`);
        $('#lblAverageProfitability').html(` Rentab Prom: ${averageProfitability.toLocaleString('es-CO', { maximumFractionDigits: 2 })} %`);
    }
  
    loadTblPrices = async (data) => {
        // if (key && value) {
        //     if (key.includes(',') && value.includes(',')) {
        //         key = key.split(',');
        //         value = value.split(',');
        //         data = data.filter((item) => item[key[0]] == value[0] && item[key[1]] == value[1]);
        //     }
        //     else
        //         data = data.filter((item) => item[key] == value);
        // }

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
                    title: 'Año / Mes',
                    data: null,
                    className: 'uniqueClassName',
                    render: function (data) {
                        return `${data.year} / ${months1[data.month]}`;
                    }
                },
                {
                    title: 'Referencia',
                    data: 'reference',
                    className: 'uniqueClassName',
                },
                {
                    title: 'Producto',
                    data: 'product',
                    className: 'classCenter',
                },
                {
                    title: 'Precio (Sugerido)',
                    data: 'price',
                    className: 'classCenter',
                    render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
                },
                {
                    title: 'Precio (Actual)',
                    data: 'sale_price',
                    className: 'classCenter',
                    // visible: visible,
                    render: function (data) {
                        if (data > 0)
                            return `$ ${data.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`;
                        else return '';
                    },
                },
                {
                    title: 'Rentabilidad',
                    data: null,
                    className: 'classCenter',
                    render: function (data) {
                        let profitabilityText = `${data.min_profitability.toLocaleString(
                            "es-CO",
                            { maximumFractionDigits: 2 }
                        )} %`;
                        let badgeClass = "";

                        if (
                            data.min_profitability < data.profitability &&
                            data.min_profitability > 0 &&
                            data.sale_price > 0
                        ) {
                            badgeClass = "badge badge-warning"; // Use "badge badge-warning" for orange
                        } else if (
                            data.min_profitability < data.profitability &&
                            data.sale_price > 0
                        ) {
                            badgeClass = "badge badge-danger"; // Use "badge badge-danger" for red
                        } else badgeClass = "badge badge-success"; // Use "badge badge-danger" for red
                        if (badgeClass) {
                            return `<span class="${badgeClass}" style="font-size: medium;" >${profitabilityText}</span>`;
                        } else {
                            return profitabilityText;
                        }
                    }
                },
                {
                    title: 'Acciones',
                    data: 'id_historic',
                    className: 'uniqueClassName',
                    render: function (data) {
                        return `<a href="/cost/details-historical" <i id="${data}" class="bi bi-zoom-in seeDetail" data-toggle='tooltip' title='Ficha Técnica de Costos' style="font-size: 30px;"></i></a>`;
                    },
                },
            ],
        });
    }

    loadAllData();

    $('.btnsProfit').click(function (e) {
        e.preventDefault();

        let data = [];

        if (this.id == 'max')
            data = historical.filter(item => item.min_profitability == maxProfitability);
        else
            data = historical.filter(item => item.min_profitability == minProfitability);
            
        loadTblPrices(data);
    });
});
