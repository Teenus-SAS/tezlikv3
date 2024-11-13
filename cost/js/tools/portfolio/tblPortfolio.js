$(document).ready(function () {
     
    tblPortfolio = $("#tblPortfolio").DataTable({
        destroy: true,
        pageLength: 50,
        ajax: function (data, callback, settings) {
            fetch(`/api/prices`)
                .then(response => response.json())
                .then(data => {
                    // Si el servidor indica recargar la página
                    if (data.reload) {
                        location.reload();
                    } else if (Array.isArray(data) && data.length > 0) {
                        // Si `data` es un array, se envía en un objeto para que DataTables lo interprete correctamente
                        callback({ data: data });
                    } else if (data && data.data && Array.isArray(data.data) && data.data.length > 0) {
                        // Verificar estructura `{ data: [...] }`
                        callback(data);
                    } else {
                        console.error("Formato de datos inesperado o datos vacíos:", data);
                        callback({ data: [] }); // Envía un array vacío para evitar errores en la tabla
                    }
                })
                .catch(error => {
                    console.error("Error en la carga de datos:", error);
                    callback({ data: [] }); // Enviar un array vacío en caso de error
                });
        },
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
                // visible: visible,
                render: function (data) {
                    let dataCost = getDataCost(data);
                    if (!isFinite(dataCost.actualProfitability))
                        dataCost.actualProfitability = 0;

                    let profitabilityText = `${dataCost.actualProfitability.toLocaleString(
                        "es-CO",
                        {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }
                    )} %`;
                    let badgeClass = "";

                    if (
                        dataCost.actualProfitability < data.profitability &&
                        dataCost.actualProfitability > 0 &&
                        data.sale_price > 0
                    ) {
                        badgeClass = "badge badge-warning"; // Use "badge badge-warning" for orange
                    } else if (
                        dataCost.actualProfitability < data.profitability &&
                        data.sale_price > 0
                    ) {
                        badgeClass = "badge badge-danger"; // Use "badge badge-danger" for red
                    } else badgeClass = "badge badge-success"; // Use "badge badge-danger" for red

                    // if (data.sale_price == 0) {
                    //     badgeClass = "badge badge-primary"; // Use "badge badge-warning" for orange
                    //     profitabilityText = `${data.profitability.toLocaleString(
                    //         "es-CO",
                    //         {
                    //             minimumFractionDigits: 2,
                    //             maximumFractionDigits: 2
                    //         }
                    //     )} %`;
                    // }

                    if (badgeClass) {
                        return `<span class="${badgeClass}" style="font-size: medium;" >${profitabilityText}</span>`;
                    } else {
                        return profitabilityText;
                    }
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
    
});
