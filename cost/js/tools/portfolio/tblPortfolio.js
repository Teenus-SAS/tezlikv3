$(document).ready(function () {
    loadTblPortfolio = async (data) => {
        tblPortfolio = $("#tblPortfolio").DataTable({
            destroy: true,
            pageLength: 50,
            order: [3, 'desc'],
            data: data,
            dom: '<"datatable-error-console">frtip',
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json",
            },
            fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
                if (oSettings.json && oSettings.json.hasOwnProperty("error")) {
                    console.error(oSettings.json.error);
                }
            },
            columns: [
                {
                    title: "No.",
                    data: null,
                    className: "uniqueClassName",
                    render: function (data, type, full, meta) {
                        return meta.row + 1;
                    },
                },
                {
                    title: "Referencia",
                    data: "reference",
                    className: "uniqueClassName",
                },
                {
                    title: "Producto",
                    data: "product",
                    className: "classCenter",
                },
                {
                    title: 'Rentabilidad',
                    data: null,
                    className: "classCenter", 
                    render: function (data) {
                        const totalVariableCost = data.variableCost * data.units_sold;
                        const totalCostsAndExpense = data.costFixed + totalVariableCost;
                        const totalRevenue = data.units_sold * data.price;
                        const unityCost = totalCostsAndExpense / data.units_sold;
                        const unitUtility = data.price - unityCost;
                        const netUtility = unitUtility * data.units_sold;
                        const profitMargin = (netUtility / totalRevenue) * 100;
                        
                        return `${profitMargin.toLocaleString(
                            "es-CO",
                            {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }
                        )} %`;
                    },
                },
                // {
                //   title: "Acciones",
                //   data: "id_product",
                //   className: "uniqueClassName",
                //   render: function (data) {
                //     return `<a href="/cost/details-prices" <i id="${data}" class="bi bi-zoom-in seeDetail" data-toggle='tooltip' title='Ficha de Costos' style="font-size: 30px;"></i></a>`;
                //   },
                // },
            ],
        });
    }
});
