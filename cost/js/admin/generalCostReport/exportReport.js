$(document).ready(function () {
    $(document).on('click', '.aGeneralCostReport',async function () {
        try {
            $('.loading').show(800);
            document.body.style.overflow = 'hidden';

            let wb = XLSX.utils.book_new();
            let data = [];

            let dataPrices = await searchData('/api/prices');
            if (dataPrices.length > 0) {
                data = [];

                for (i = 0; i < dataPrices.length; i++) {
                    data.push({
                        referencia:dataPrices[i].reference,
                        producto:dataPrices[i].product,
                        costo_material:dataPrices[i].cost_materials,
                        costo_mano_de_obra:dataPrices[i].cost_workforce,
                        costo_indirecto:dataPrices[i].cost_indirect_cost,
                        costo_servicios_externos:dataPrices[i].services,
                        costo_gasto_asignable:dataPrices[i].assignable_expense,
                    });
                }

                ws = XLSX.utils.json_to_sheet(data);
                XLSX.utils.book_append_sheet(wb, ws, 'Costos');
            }

            $('.loading').hide(800);
            document.body.style.overflow = '';
            execute = true;

            XLSX.writeFile(wb, 'reporte_general_costos.xlsx');
        } catch (error) {
            console.log(error);
        }
    });
});