$(document).ready(function () {
    // const loadAllPortfolio = async () => {
    //     const data = await searchData('/api/calcEconomyScale');

    //     if (data.reload) {
    //         location.reload();
    //     }

    //     if (data.info) {
    //         toastr.info(data.message);
    //         return false;
    //     }
 
    //     let totalNetUtility = 0;

    //     for (let i = 0; i < data.length; i++) {
    //         let price = data[i].turnover / data[i].units_sold;
    //         let totalVariableCost = data[i].variableCost * data[i].units_sold;
    //         let totalCostsAndExpense = data[i].costFixed + totalVariableCost;
    //         let totalRevenue = data[i].units_sold * price;
    //         let unityCost = totalCostsAndExpense / data[i].units_sold;
    //         let unitUtility = price - unityCost;
    //         let netUtility = unitUtility * data[i].units_sold;
    //         let profitMargin = (netUtility / totalRevenue) * 100;

    //         profitMargin < 0 ? profitMargin = 0 : profitMargin; 

    //         if (netUtility > 0) {
    //             totalNetUtility += netUtility;
    //             data[i]['net_utility'] = netUtility;
    //         }
            
    //         data[i]['profit_margin'] = profitMargin;
    //     }

    //     let totalProfit8020 = 0;
    //     let data8020 = [];

    //     for (let i = 0; i < data.length; i++) {
    //         if (data[i].net_utility && totalProfit8020 < 80) {
    //             let profit = (data[i].net_utility / totalNetUtility) * 100;
    //             data[i]['profit8020'] = profit;
    //             totalProfit8020 += profit;
    //             data8020.push(data[i]);
    //         }
    //     }

    //     loadTblPortfolio(data);
    //     loadTblPortfolio8020(data8020);
    // };

    // Portafolio de Rentabilidades
    
    const loadAllPortfolio = async () => {
        const data = await searchData('/api/calcEconomyScale');

        if (data.reload) return location.reload();
        if (data.info) return toastr.info(data.message), false;

        let totalNetUtility = 0;

        for (const item of data) {
            const { turnover, units_sold, variableCost, costFixed } = item;
        
            const price = turnover / units_sold;
            const totalVariableCost = variableCost * units_sold;
            const totalCostsAndExpense = costFixed + totalVariableCost;
            const unityCost = totalCostsAndExpense / units_sold;
            const unitUtility = price - unityCost;
            const netUtility = unitUtility * units_sold;
            const totalRevenue = units_sold * price;

            let profitMargin = netUtility > 0 ? (netUtility / totalRevenue) * 100 : 0;
            item.profit_margin = Math.max(profitMargin, 0);
        
            if (netUtility > 0) {
                totalNetUtility += netUtility;
                item.net_utility = netUtility;
            }
        }

        let totalProfit8020 = 0;
        const data8020 = data.filter((item) => {
            if (item.net_utility && totalProfit8020 < 80) {
                const profit = (item.net_utility / totalNetUtility) * 100;
                item.profit8020 = profit;
                totalProfit8020 += profit;
                return true;
            }
            return false;
        });

        loadTblPortfolio(data);
        loadTblPortfolio8020(data8020);
    };

    // const loadTblPortfolio = async (data) => {
    //     tblPortfolio = $("#tblPortfolio").DataTable({
    //         destroy: true,
    //         pageLength: 50,
    //         order: [3, 'desc'],
    //         data: data,
    //         dom: '<"datatable-error-console">frtip',
    //         language: {
    //             url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json",
    //         },
    //         fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
    //             if (oSettings.json && oSettings.json.hasOwnProperty("error")) {
    //                 console.error(oSettings.json.error);
    //             }
    //         },
    //         columns: [
    //             {
    //                 title: "No.",
    //                 data: null,
    //                 className: "uniqueClassName",
    //                 render: function (data, type, full, meta) {
    //                     return meta.row + 1;
    //                 },
    //             },
    //             {
    //                 title: "Referencia",
    //                 data: "reference",
    //                 className: "uniqueClassName",
    //             },
    //             {
    //                 title: "Producto",
    //                 data: "product",
    //                 className: "classCenter",
    //             },
    //             {
    //                 title: 'Margen',
    //                 data: 'profit_margin',
    //                 className: "classCenter",
    //                 render: function (data) {                         
    //                     return `${data.toLocaleString(
    //                         "es-CO",
    //                         {
    //                             minimumFractionDigits: 2,
    //                             maximumFractionDigits: 2
    //                         }
    //                     )} %`;
    //                 },
    //             },
    //         ],
    //         // footerCallback: function (row, data, start, end, display) {
    //         //     let profit_margin = 0;

    //         //     for (i = 0; i < data.length; i++) {
    //         //         profit_margin += parseFloat(data[i].profit_margin);
    //         //     }

    //         //     $('#totalProfit').html(
    //         //         `${profit_margin.toLocaleString('es-CO', {
    //         //             minimumFractionDigits: 0,
    //         //             maximumFractionDigits: 0,
    //         //         })} %`
    //         //     );
    //         // },
    //     });
    // };

    // // Portafolio 80/20
    // const loadTblPortfolio8020 = async (data) => {
    //     tblPortfolio8020 = $("#tblPortfolio8020").DataTable({
    //         destroy: true,
    //         pageLength: 50,
    //         order: [3, 'desc'],
    //         data: data,
    //         dom: '<"datatable-error-console">frtip',
    //         language: {
    //             url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json",
    //         },
    //         fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
    //             if (oSettings.json && oSettings.json.hasOwnProperty("error")) {
    //                 console.error(oSettings.json.error);
    //             }
    //         },
    //         columns: [
    //             {
    //                 title: "No.",
    //                 data: null,
    //                 className: "uniqueClassName",
    //                 render: function (data, type, full, meta) {
    //                     return meta.row + 1;
    //                 },
    //             },
    //             {
    //                 title: "Referencia",
    //                 data: "reference",
    //                 className: "uniqueClassName",
    //             },
    //             {
    //                 title: "Producto",
    //                 data: "product",
    //                 className: "classCenter",
    //             },
    //             {
    //                 title: 'Margen',
    //                 data: 'profit8020',
    //                 className: "classCenter",
    //                 render: function (data) {
    //                     return `${data.toLocaleString(
    //                         "es-CO",
    //                         {
    //                             minimumFractionDigits: 2,
    //                             maximumFractionDigits: 2
    //                         }
    //                     )} %`;
    //                 },
    //             },
    //         ],
    //         footerCallback: function (row, data, start, end, display) {
    //             let profit8020 = 0;

    //             for (i = 0; i < data.length; i++) {
    //                 profit8020 += parseFloat(data[i].profit8020);
    //             }

    //             $('#totalProfit8020').html(
    //                 `${profit8020.toLocaleString('es-CO', {
    //                     minimumFractionDigits: 0,
    //                     maximumFractionDigits: 0,
    //                 })} %`
    //             );
    //         },
    //     });
    // };

    const loadTable = async (selector, data, profitField, totalSelector = null) => {
        $(selector).DataTable({
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
                    render: (data, type, full, meta) => meta.row + 1,
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
                    title: 'Margen',
                    data: profitField,
                    className: "classCenter",
                    render: (data) =>
                        `${data.toLocaleString("es-CO", { minimumFractionDigits: 2, maximumFractionDigits: 2 })} %`,
                },
            ],
            footerCallback: function (row, data, start, end, display) {
                if (totalSelector) {
                    let totalProfit = data.reduce((acc, item) => acc + parseFloat(item[profitField] || 0), 0);
                    $(totalSelector).html(
                        `${totalProfit.toLocaleString('es-CO', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0,
                        })} %`
                    );
                }
            },
        });
    };

    // Usar la función genérica para cada tabla
    const loadTblPortfolio = async (data) => {
        await loadTable("#tblPortfolio", data, "profit_margin");
    };

    const loadTblPortfolio8020 = async (data) => {
        await loadTable("#tblPortfolio8020", data, "profit8020", '#totalProfit8020');
    };
    
    loadAllPortfolio();
});
