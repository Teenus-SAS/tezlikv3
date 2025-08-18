
$(".cardImportProductsProcess").hide();

$("#btnImportNewProductProcess").click(function (e) {
  e.preventDefault();
  $(".cardAddNewProduct").hide(800);
  $(".cardAddProcess").hide(800);
  $(".cardProducts").toggle(800);

  $(".cardImportProductsProcess").toggle(800);
});

$("#fileProductsProcess").change(function (e) {
  e.preventDefault();
  selectedFile = e.target.files[0];
});

$("#btnImportProductsProcess").click(function (e) {
  e.preventDefault();

  let file = $("#fileProductsProcess").val();
  if (!file) {
    toastr.error("Seleccione un archivo");
    return false;
  }

  $(".cardBottons").hide();

  let form = document.getElementById("formProductProcess");

  form.insertAdjacentHTML(
    "beforeend",
    `<div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
        <div class="spinner-border text-secondary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
      </div>`
  );

  importFile(selectedFile)
    .then(async (data) => {
      let arr = data.rowObject;

      if (arr.length == 0) {
        $(".cardLoading").remove();
        $(".cardBottons").show(400);
        $("#fileProductsProcess").val("");
        toastr.error("Archivo vacio. Verifique nuevamente");
        return false;
      }

      const expectedHeaders = [
        "referencia_producto",
        "producto",
        "proceso",
        "maquina",
        "tiempo_enlistamiento",
        "tiempo_operacion",
        "eficiencia",
        "empleados",
        "maquina_autonoma",
      ];
      const actualHeaders = data.actualHeaders;

      if (flag_employee == "0") {
        expectedHeaders.splice(expectedHeaders.length - 2, 1);
      }

      const missingHeaders = expectedHeaders.filter(
        (header) => !actualHeaders.includes(header)
      );

      if (missingHeaders.length > 0) {
        $(".cardLoading").remove();
        $(".cardBottons").show(400);
        $("#fileProductsProcess").val("");

        toastr.error(
          "Archivo no corresponde a el formato. Verifique nuevamente"
        );
        return false;
      }

      let resp = await validateDataFTP(arr);

      checkProductProcess(resp.productProcessToImport, resp.debugg);
    })
    .catch(() => {
      $(".cardLoading").remove();
      $(".cardBottons").show(400);
      $("#fileProductsProcess").val("");

      console.log("Ocurrio un error. Intente Nuevamente");
    });
});

/* Validar data */
const validateDataFTP = async (data) => {
  let productProcessToImport = [];
  let debugg = [];

  const dataProducts = JSON.parse(sessionStorage.getItem("dataProducts"));
  let dataProcess = JSON.parse(sessionStorage.getItem("dataProcess"));
  let dataMachines = JSON.parse(sessionStorage.getItem("dataMachines"));

  if (!dataProcess) {
    await findSelectProcess();
    dataProcess = JSON.parse(sessionStorage.getItem("dataProcess"));
  }

  if (!dataMachines) {
    await getSelectMachine("/api/selectMachines");
    dataMachines = JSON.parse(sessionStorage.getItem("dataMachines"));
  }

  for (let i = 0; i < data.length; i++) {
    let arr = data[i];

    let enlistmentTime =
      arr.tiempo_enlistamiento > 0
        ? arr.tiempo_enlistamiento.toString()
        : "0";
    let operationTime =
      arr.tiempo_operacion > 0 ? arr.tiempo_operacion.toString() : "0";
    let efficiency = arr.eficiencia > 0 ? arr.eficiencia.toString() : "0";

    !arr.referencia_producto
      ? (arr.referencia_producto = "")
      : arr.referencia_producto;
    !arr.producto ? (arr.producto = "") : arr.producto;
    !arr.proceso ? (arr.proceso = "") : arr.proceso;
    !arr.maquina ? (arr.maquina = "") : arr.maquina;
    !arr.maquina_autonoma
      ? (arr.maquina_autonoma = "")
      : arr.maquina_autonoma;
    !arr.empleados ? (arr.empleados = "") : arr.empleados;

    if (
      !arr.referencia_producto ||
      !arr.producto ||
      !arr.proceso ||
      !arr.maquina ||
      enlistmentTime.trim() === "" ||
      operationTime.trim() === "" ||
      efficiency.trim() === "" ||
      !arr.maquina_autonoma ||
      !arr.referencia_producto.toString().trim() ||
      !arr.producto.toString().trim() ||
      !arr.proceso.toString().trim() ||
      !arr.maquina.toString().trim() ||
      !arr.maquina_autonoma.toString().trim()
    ) {
      debugg.push({ message: `Columna vacía en la fila: ${i + 2}` });
    }

    // if (flag_employee == '1' && arr.maquina_autonoma == 'NO') {
    //   if (!arr.empleados || !arr.empleados.toString().trim()) {
    //     debugg.push({ message: `Columna vacía en la fila: ${i + 2}` });
    //   }
    // }

    let valOT = parseFloat(operationTime.replace(",", ".")) * 1;
    if (isNaN(valOT) || valOT <= 0) {
      debugg.push({
        message: `El tiempo de operación debe ser mayor a cero (0). Fila: ${i + 2
          }`,
      });
    }

    let reference = arr.referencia_producto.toString().trim();
    let nameProduct = arr.producto.toString().toUpperCase().trim();

    let product = dataProducts.find(
      (item) => item.reference == reference && item.product == nameProduct
    );

    if (!product) {
      debugg.push({
        message: `Producto no existe en la base de datos. Fila: ${i + 2}`,
      });
      product = { id_product: "" };
    }

    let process = dataProcess.find(
      (item) => item.process == arr.proceso.toString().toUpperCase().trim()
    );

    if (!process) {
      debugg.push({
        message: `Proceso no existe en la base de datos. Fila: ${i + 2}`,
      });
    }

    let idMachine = 0;
    if (arr.maquina.toString().toUpperCase().trim() !== "PROCESO MANUAL") {
      let machine = dataMachines.find(
        (item) => item.machine == arr.maquina.toString().toUpperCase().trim()
      );

      if (!machine) {
        debugg.push({
          message: `Máquina no existe en la base de datos. Fila: ${i + 2}`,
        });
      } else idMachine = machine.id_machine;
    }

    productProcessToImport.push({
      idProduct: !product ? "" : product.id_product,
      idProcess: !process ? "" : process.id_process,
      idMachine: idMachine,
      referenceProduct: arr.referencia_producto,
      product: arr.producto,
      process: arr.proceso,
      machine: arr.maquina,
      enlistmentTime: enlistmentTime,
      operationTime: operationTime,
      efficiency: efficiency,
      employees: arr.empleados,
      autoMachine: arr.maquina_autonoma,
    });
  }

  return { productProcessToImport, debugg };
};

/* Mensaje de advertencia */
const checkProductProcess = (data, debugg) => {
  $.ajax({
    type: "POST",
    url: "/api/dataSheetProcess/productsProcessDataValidation",
    data: {
      importProductsProcess: data,
      debugg: debugg,
    },
    success: function (resp) {
      if (resp.reload) {
        location.reload();
      }

      let arr = resp.import;

      if (arr.length > 0 && arr.error == true) {
        $(".cardLoading").remove();
        $(".cardBottons").show(400);
        $("#fileProductsProcess").val("");
        toastr.error(resp.message);
        return false;
      }

      if (resp.debugg.length > 0) {
        $(".cardLoading").remove();
        $(".cardBottons").show(400);
        $("#fileProductsProcess").val("");

        // Ordenar el array
        const sortedDebugg = resp.debugg.sort((a, b) => {
          // Extraer tipo de mensaje
          const typeA = a.message.split(".")[0];
          const typeB = b.message.split(".")[0];

          // Extraer número de fila
          const filaA = parseInt(
            a.message.match(/Fila: (\d+)/)?.[1] || 0,
            10
          );
          const filaB = parseInt(
            b.message.match(/Fila: (\d+)/)?.[1] || 0,
            10
          );

          // Ordenar por tipo de mensaje alfabéticamente, luego por fila
          if (typeA < typeB) return -1;
          if (typeA > typeB) return 1;
          return filaA - filaB;
        });

        //console.log(sortedDebugg);

        // Generar el HTML para cada mensaje
        let concatenatedMessages = sortedDebugg
          .map(
            (item) =>
              `<li>
              <span class="badge badge-danger" style="font-size: 16px;">${item.message}</span>
            </li>
            <br>`
          )
          .join("");

        // Mostramos el mensaje con Bootbox
        bootbox.alert({
          title: "Errores",
          message: `
            <div class="container">
              <div class="col-12">
                <ul>
                  ${concatenatedMessages}
                </ul>
              </div> 
            </div>`,
          size: "large",
          backdrop: true,
        });
        return false;
      }

      if (
        typeof arr === "object" &&
        !Array.isArray(arr) &&
        arr !== null &&
        debugg.length == 0
      ) {
        bootbox.confirm({
          title: "¿Desea continuar con la importación?",
          message: `Se han encontrado los siguientes registros:<br><br>Datos a insertar: ${arr.insert} <br>Datos a actualizar: ${arr.update}`,
          buttons: {
            confirm: {
              label: "Si",
              className: "btn-success",
            },
            cancel: {
              label: "No",
              className: "btn-danger",
            },
          },
          callback: function (result) {
            if (result == true) {
              saveProductProcessTable(data);
            } else {
              $(".cardLoading").remove();
              $(".cardBottons").show(400);
              $("#fileProductsProcess").val("");
            }
          },
        });
      }
    },
  });
};

const saveProductProcessTable = (data) => {
  $.ajax({
    type: "POST",
    url: "/api/dataSheetProcess/addProductsProcess",
    data: { importProductsProcess: data },
    success: function (r) {
      messageProcess(r);
    },
  });
};

/* Descargar formato */
$("#btnDownloadImportsProductsProcess").click(function (e) {
  e.preventDefault();

  let url = "assets/formatsXlsx/Productos_Procesos.xlsx";

  if (flag_employee == "1") {
    url = "assets/formatsXlsx/Productos_Procesos(Empleados).xlsx";
  }

  let newFileName = "Productos_Procesos.xlsx";

  fetch(url)
    .then((response) => response.blob())
    .then((blob) => {
      let link = document.createElement("a");
      link.href = URL.createObjectURL(blob);
      link.download = newFileName;

      document.body.appendChild(link);
      link.click();

      document.body.removeChild(link);
      URL.revokeObjectURL(link.href); // liberar memoria
    })
    .catch(console.error);
});

