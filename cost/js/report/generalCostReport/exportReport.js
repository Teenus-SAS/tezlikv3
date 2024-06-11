$(document).ready(function () {
    // Reportes Generales Costos
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

    // Reporte Procesos
    $(document).on('click', '.aProcessCostReport', async function () {
        try {
            $('.loading').show(800);
            document.body.style.overflow = 'hidden'; 

            let resp = await searchData('/api/processCostReport');
            let dataExport = resp.costWorkforce;
            let process = resp.process; 
            
            // Diccionario de procesos para búsqueda rápida
            const processDict = process.reduce((acc, process) => {
                acc[process.id_process] = process.process;
                return acc;
            }, {});

            // Agrupar los datos por referencia y producto
            const groupedData = dataExport.reduce((acc, item) => {
                const key = `${item.reference}-${item.product}`;
                if (!acc[key]) {
                    acc[key] = {
                        referencia: item.reference,
                        producto: item.product,
                        ...process.reduce((processAcc, process) => {
                            processAcc[process.process] = 0; // Inicializar cada proceso con 0
                            return processAcc;
                        }, {}),
                        costo_total: 0
                    };
                }
                acc[key][processDict[item.id_process]] = item.workforce;
                acc[key].costo_total += item.workforce;
                return acc;
            }, {});

            // Convertir los datos agrupados en un array
            const data = Object.values(groupedData);

            // Crear el libro de trabajo y la hoja de trabajo
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.json_to_sheet(data);

            // Añadir la hoja de trabajo al libro
            XLSX.utils.book_append_sheet(wb, ws, 'Costos');

            $('.loading').hide(800);
            document.body.style.overflow = '';
            execute = true;
            
            // Guardar el archivo 
            XLSX.writeFile(wb, 'reporte_costos_procesos.xlsx');
        } catch (error) {
            console.log(error);
        }
    });
});