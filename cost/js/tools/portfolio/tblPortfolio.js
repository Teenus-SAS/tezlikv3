$(document).ready(function () {
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
            let profitMargin1 = (netUtility / totalRevenue) * 100;
            item.profit_margin = Math.max(profitMargin, 0);
            item.profit_margin1 = profitMargin1;
            item.net_utility = netUtility < 0 ? 0 : netUtility;
            item.net_utility1 = netUtility;
        
            if (netUtility > 0) {
                totalNetUtility += netUtility;
            }
        }

        data.sort((a, b) => b.net_utility1 - a.net_utility1);

        let totalProfit8020 = 0;
        const data8020 = data.filter((item) => {
            const profit = (item.net_utility / totalNetUtility) * 100;
            item.profit8020 = profit;
            item.flag_80 = 0;

            if (item.net_utility > 0) {
                totalProfit8020 += profit;

                if (totalProfit8020 < 80) {
                    item.flag_80 = 1;
                    return true;
                }
            }
            return true;
        });

        // loadTblPortfolio(data);
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
    //                 data: 'profit_margin1',
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
    //     });
    // };

    // // Portafolio 80/20
    // const loadTblPortfolio8020 = async (data) => {
    //     tblPortfolio8020 = $("#tblPortfolio8020").DataTable({
    //         destroy: true,
    //         pageLength: 50,
    //         order: [5, 'desc'],
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
    //                 title: 'Rentabilidad',
    //                 data: 'net_utility',
    //                 className: "classCenter",
    //                 render: function (data) {                         
    //                     return `$ ${data.toLocaleString(
    //                         "es-CO",
    //                         {
    //                             minimumFractionDigits: 0,
    //                             maximumFractionDigits: 0
    //                         }
    //                     )}`;
    //                 },
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
    //             {
    //                 title: 'Participacion',
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
    
    const loadTable = async ({ selector, data, columns, order, footerSelector = null, footerDataField = null }) => {
        $(selector).DataTable({
            destroy: true,
            pageLength: 50,
            order: [order.column, order.direction],
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
            columns: columns,
            footerCallback: function (row, data, start, end, display) {
                if (footerSelector && footerDataField) {
                    let total = data.reduce((acc, item) => acc + parseFloat(item[footerDataField] || 0), 0);
                    $(footerSelector).html(
                        `${total.toLocaleString('es-CO', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0,
                        })} %`
                    );
                }
            },
        });
    };

    // Configuración de columnas para tblPortfolio
    // const columnsTblPortfolio = [
    //     { title: "No.", data: null, className: "uniqueClassName", render: (data, type, full, meta) => meta.row + 1 },
    //     { title: "Referencia", data: "reference", className: "uniqueClassName" },
    //     { title: "Producto", data: "product", className: "classCenter" },
    //     {
    //         title: 'Margen',
    //         data: 'profit_margin1',
    //         className: "classCenter",
    //         render: (data) => `${data.toLocaleString("es-CO", { minimumFractionDigits: 2, maximumFractionDigits: 2 })} %`,
    //     },
    // ];

    // Configuración de columnas para tblPortfolio8020
    const columnsTblPortfolio8020 = [
        { title: "No.", data: null, className: "uniqueClassName", render: (data, type, full, meta) => meta.row + 1 },
        { title: "Referencia", data: "reference", className: "uniqueClassName" },
        { title: "Producto", data: "product", className: "classCenter" },
        {
            title: 'Rentabilidad',
            data: 'net_utility1',
            className: "classCenter",
            // render: (data) => `$ ${data.toLocaleString("es-CO", { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`,
            render: function (data) {
                let netUtilityText = `$ ${data.toLocaleString("es-CO", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                let badgeClass;

                data < 0 ? badgeClass = 'badge badge-danger' : badgeClass = 'badge';


                return `<span class="${badgeClass}" style="font-size: small;" >${netUtilityText}</span>`; 
            },
        },
        {
            title: 'Margen',
            data: 'profit_margin1',
            className: "classCenter",
            render: (data) => `${data.toLocaleString("es-CO", { minimumFractionDigits: 2, maximumFractionDigits: 2 })} %`,
        },
        {
            title: 'Participacion',
            data: 'profit8020',
            className: "classCenter",
            render: (data) => `${data.toLocaleString("es-CO", { minimumFractionDigits: 2, maximumFractionDigits: 2 })} %`, 
        },
        {
            title: '',
            data: null,
            className: 'classCenter',
            render: function (data) {
                if (data.flag_80 == 1) {
                    return `<i class="bi bi-check-square-fill" style="font-size: large; color: green;"></i>`
                } else {
                    return `<i class="bi bi-x-square-fill" style="font-size: large; color: red;"></i>`;
                }
            }
        }
    ];

    // Inicializar las tablas con configuraciones específicas
    // const loadTblPortfolio = async (data) => {
    //     await loadTable({
    //         selector: "#tblPortfolio",
    //         data: data,
    //         columns: columnsTblPortfolio,
    //         order: { column: 3, direction: 'desc' },
    //     });
    // };

    const loadTblPortfolio8020 = async (data) => {
        await loadTable({
            selector: "#tblPortfolio8020",
            data: data,
            columns: columnsTblPortfolio8020,
            order: { column: 5, direction: 'desc' },
            footerSelector: '#totalProfit8020',
            footerDataField: 'profit8020'
        });
    };
    
    loadAllPortfolio();
});
