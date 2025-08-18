$(document).ready(function () {
  let selectedFile;

  $(".cardImportExpenses").hide();

  $("#btnImportNewExpenses").click(function (e) {
    e.preventDefault();
    $(".cardExpensesDistribution").hide(800);
    $(".cardExpenseRecover").hide(800);
    $("#lblImprotExpense").html(this.value);
    $(".cardImportExpenses").toggle(800);
  });

  $("#fileExpenses").change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $("#btnImportExpenses").click(function (e) {
    e.preventDefault();

    let expensesToDistribution = $("#expensesToDistribution").val();

    if (expensesToDistribution == "$ 0" || !expensesToDistribution) {
      $("#fileExpenses").val("");
      toastr.error("Asigne un gasto primero antes de distribuir");
      return false;
    }

    let file = $("#fileExpenses").val();

    if (!file) {
      toastr.error("Seleccione un archivo");
      return false;
    }

    if (option == 1) {
      bootbox.dialog({
        title: 'Importe',
        message: `Seleccione tipo de importe que desea realizar:<br><br>
                  <ul>
                    <li><b>Parcial:</b> Se actualizaran o insertaran todos los datos en el archivo y no se desactivaran los productos sin ventas.</li>
                    <li><b>Total:</b> Se actualizaran o insertaran todos los datos en el archivo y se desactivaran los productos sin ventas.</li>
                  </ul>`,
        backdrop: 'static', // Evita que el modal se cierre haciendo clic fuera de él
        closeButton: false, // Oculta el botón de cierre del modal
        // size: 'small',
        buttons: {
          parcial: {
            label: 'Parcial',
            className: 'btn-success',
            callback: function () {
              sessionStorage.setItem('typeExpenseD', 1);
              checkImportExpenseD();
            }
          },
          total: {
            label: 'Total',
            className: 'btn-danger',
            callback: function () {
              sessionStorage.setItem('typeExpenseD', 2);
              checkImportExpenseD();
            }
          }
        }
      });
    } else checkImportExpenseD();
  });

  const checkImportExpenseD = () => {
    $(".cardBottons").hide();

    let form = document.getElementById("formExpensesD");

    form.insertAdjacentHTML(
      "beforeend",
      `<div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
        <div class="spinner-border text-secondary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
      </div>`
    );

    importFile(selectedFile)
      .then((data) => {
        let arr = data.rowObject;

        if (arr.length == 0) {
          $(".cardLoading").remove();
          $(".cardBottons").show(400);
          $("#fileExpenses").val("");
          toastr.error("Archivo vacio. Verifique nuevamente");
          return false;
        }

        if (option == 1) {
          const expectedHeaders = [
            "unidades_vendidas",
            "volumen_ventas",
            "referencia_producto",
            "producto",
            "centro_produccion",
          ];
          if (production_center == "0" || flag_production_center == "0")
            expectedHeaders.splice(4, 1);

          const actualHeaders = data.actualHeaders;

          const missingHeaders = expectedHeaders.filter(
            (header) => !actualHeaders.includes(header)
          );

          if (missingHeaders.length > 0) {
            $(".cardLoading").remove();
            $(".cardBottons").show(400);
            $("#fileExpenses").val("");

            toastr.error(
              "Archivo no corresponde a el formato. Verifique nuevamente"
            );
            return false;
          }
        } else if (option == 2) {
          const expectedHeaders = [
            "referencia_producto",
            "producto",
            "porcentaje_recuperado",
          ];
          const actualHeaders = data.actualHeaders;

          const missingHeaders = expectedHeaders.filter(
            (header) => !actualHeaders.includes(header)
          );

          if (missingHeaders.length > 0) {
            $(".cardLoading").remove();
            $(".cardBottons").show(400);
            $("#fileExpenses").val("");

            toastr.error(
              "Archivo no corresponde a el formato. Verifique nuevamente"
            );
            return false;
          }
        }

        let expenseToImport = arr.map((item) => {
          if (option == 1) {
            url = "/api'/distribution/expenseDistributionDataValidation";
            let unitsSold = "";
            let turnover = "";

            if (item.unidades_vendidas)
              unitsSold = item.unidades_vendidas.toString().replace(".", ",");
            else unitsSold = 0;

            if (item.volumen_ventas)
              turnover = item.volumen_ventas.toString().replace(".", ",");
            else turnover = 0;

            if (production_center == "1" && flag_production_center == "1")
              production = item.centro_produccion;
            else production = 0;

            return {
              referenceProduct: item.referencia_producto,
              product: item.producto,
              unitsSold: unitsSold,
              turnover: turnover,
              production: production,
            };
          } else if (option == 2) {
            url = "/api/recoveringExpenses/expenseRecoverDataValidation";
            return {
              referenceProduct: item.referencia_producto,
              product: item.producto,
              percentage: item.porcentaje_recuperado,
            };
          }
        });

        if (option == 1) {
          let type = sessionStorage.getItem("typeExpenseD");
          expenseToImport[0]["type"] = type;
        }

        checkExpenseD(expenseToImport, url);
      })
      .catch(() => {
        console.log("Ocurrio un error. Intente Nuevamente");
      });
  };

  /* Mensaje de advertencia */
  const checkExpenseD = (data, url) => {
    $.ajax({
      type: "POST",
      url: url,
      data: { importExpense: data },
      success: function (resp) {
        if (resp.reload) {
          location.reload();
        }

        if (resp.error == true) {
          toastr.error(resp.message);
          $("#fileExpenses").val("");
          $(".cardLoading").remove();
          $(".cardBottons").show(400);

          return false;
        }

        bootbox.confirm({
          title: "¿Desea continuar con la importación?",
          message: `Se han encontrado los siguientes registros:<br><br>Datos a insertar: ${resp.insert} <br>Datos a actualizar: ${resp.update}`,
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
              option == 1
                ? (url = "/api/distribution/addExpensesDistribution")
                : (url = "/api/recoveringExpenses/addExpenseRecover");

              saveExpenses(data, url);
            } else {
              $(".cardLoading").remove();
              $(".cardBottons").show(400);
              $("#fileExpenses").val("");
            }
          },
        });
      },
    });
  };

  const saveExpenses = (data, url) => {
    $.ajax({
      type: "POST",
      url: url,
      data: { importExpense: data },
      success: function (r) {
        messageDistribution(r, 1);
      },
    });
  };

  /* Descargar formato */
  $("#btnDownloadImportsExpenses").click(async function (e) {
    e.preventDefault();

    let wb = XLSX.utils.book_new();
    let data = [];
    let namexlsx, url, op;

    if (flag_expense == "1") {
      if (flag_expense_distribution == "1") {
        namexlsx = (production_center == "1" && flag_production_center == "1")
          ? "Distribucion_Gastos(CP).xlsx"
          : "Distribucion_Gastos.xlsx";
        url = "/api/distribution/allProductsDistribution";
        op = 1;
      } else {
        namexlsx = "Distribucion_Gastos_Familia.xlsx";
        url = "/api/distributionByFamilies/expensesDistributionFamilies";
        op = 2;
      }
    } else {
      namexlsx = "Recuperacion_Gastos.xlsx";
      url = "/api/expensesRecover";
      op = 3;
    }

    const dataTypeExpense = await searchData(url);

    const addToSheet = (sheetName, jsonData) => {
      if (jsonData.length > 0) {
        let ws = XLSX.utils.json_to_sheet(jsonData);
        XLSX.utils.book_append_sheet(wb, ws, sheetName);
      }
    };

    const processProductData = (dataTypeExpense) => {
      return dataTypeExpense.map(item => {
        if (production_center == "1" && flag_production_center == "1") {
          return {
            referencia_producto: item.reference,
            producto: item.product,
            unidades_vendidas: parseFloat(item.units_sold),
            volumen_ventas: parseFloat(item.turnover),
            centro_produccion: item.production_center,
          };
        } else {
          // if (flag_composite_product == '1' && item.composite == 0 || flag_composite_product == '0') {
          return {
            referencia_producto: item.reference,
            producto: item.product,
            unidades_vendidas: parseFloat(item.units_sold),
            volumen_ventas: parseFloat(item.turnover),
          };
          // }
        }
      }).filter(item => item !== undefined);
    };

    const processFamilyData = (dataTypeExpense) => {
      return dataTypeExpense.map(item => ({
        familia: item.family,
        unidades_vendidas: parseFloat(item.units_sold),
        volumen_ventas: parseFloat(item.turnover),
      }));
    };

    const processRecoveryData = (dataTypeExpense) => {
      return dataTypeExpense.map(item => ({
        reference_producto: item.reference,
        producto: item.product,
        porcentaje_recuperado: parseFloat(item.expense_recover),
      }));
    };

    switch (op) {
      case 1:
        data = processProductData(dataTypeExpense);
        addToSheet("Distribucion Producto", data);
        break;
      case 2:
        data = processFamilyData(dataTypeExpense);
        addToSheet("Distribucion Familia", data);
        break;
      case 3:
        data = processRecoveryData(dataTypeExpense);
        addToSheet("Recuperacion Gasto", data);
        break;
    }

    XLSX.writeFile(wb, namexlsx);
  });
});
