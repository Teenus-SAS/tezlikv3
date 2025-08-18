$(document).ready(function () {
  // Reporte General de Costos
  $(document).on("click", ".aGeneralCostReport", async function () {
    try {
      $(".loading").show(800);
      document.body.style.overflow = "hidden";

      let dataCost = await searchData("/api/reports/generalCostReport");

      // Crear la hoja de cálculo
      const worksheet = XLSX.utils.json_to_sheet(dataCost);

      // Crear el libro de trabajo
      const workbook = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(workbook, worksheet, "Datos");

      $(".loading").hide(800);
      document.body.style.overflow = "";
      execute = true;

      // Exportar el archivo
      XLSX.writeFile(workbook, "Reporte_General_de_Costos.xlsx");
    } catch (error) {
      console.log(error);
    }
  });

  // Reporte Procesos
  $(document).on("click", ".aProcessCostReport", async function () {
    try {
      $(".loading").show(800);
      document.body.style.overflow = "hidden";

      let data = await searchData("/api/reports/processCostReport");

      // Paso 1: Obtener todos los procesos únicos
      const uniqueProcesses = [...new Set(data.map((item) => item.process))];

      // Paso 2: Transformar los datos en una estructura con filas completas
      const transformedData = [];

      // Crear el encabezado con los procesos
      const header = ["REFERENCIA", "PRODUCTO", ...uniqueProcesses];
      transformedData.push(header);

      // Agrupar los datos por producto
      const groupedData = data.reduce((acc, item) => {
        const key = `${item.reference}_${item.product}`;
        if (!acc[key]) {
          acc[key] = {
            reference: item.reference,
            product: item.product,
            processes: {},
          };
        }
        acc[key].processes[item.process] = item.workforce_cost;
        return acc;
      }, {});

      // Crear filas para cada producto
      Object.values(groupedData).forEach(
        ({ reference, product, processes }) => {
          const row = [reference, product];
          uniqueProcesses.forEach((process) => {
            row.push(processes[process] || 0);
          });
          transformedData.push(row);
        }
      );

      // Paso 3: Crear la hoja de Excel
      const worksheet = XLSX.utils.aoa_to_sheet(transformedData);
      const workbook = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(workbook, worksheet, "Costos");

      $(".loading").hide(800);
      document.body.style.overflow = "";
      execute = true;

      // Paso 4: Exportar el archivo Excel
      XLSX.writeFile(workbook, "Reporte_Costos_por_Procesos.xlsx");
    } catch (error) {
      console.log(error);
    }
  });
});
