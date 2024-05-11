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
        message: 'Seleccione tipo de importe que desea realizar.',
        backdrop: 'static', // Evita que el modal se cierre haciendo clic fuera de él
        closeButton: false, // Oculta el botón de cierre del modal
        size: 'small',
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

  checkImportExpenseD = () => {
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
        if (data.length == 0) {
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

          const actualHeaders = Object.keys(data[0]);

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
          const actualHeaders = Object.keys(data[0]);

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

        let expenseToImport = data.map((item) => {
          if (option == 1) {
            url = "/api/expenseDistributionDataValidation";
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
            url = "/api/expenseRecoverDataValidation";
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
  checkExpenseD = (data, url) => {
    $.ajax({
      type: "POST",
      url: url,
      data: { importExpense: data },
      success: function (resp) {
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
                ? (url = "/api/addExpensesDistribution")
                : (url = "/api/addExpenseRecover");

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

  saveExpenses = (data, url) => {
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
    if (flag_expense == "1") {
      if (flag_expense_distribution == "1") {
        production_center == "1" && flag_production_center == "1"
          ? (namexlsx = "distribucion_gastos(CP).xlsx")
          : (namexlsx = "distribucion_gastos.xlsx");
        url = "/api/allProductsDistribution";
        op = 1;
      } else {
        namexlsx = "distribucion_gastos_familia.xlsx";
        url = "/api/expensesDistributionFamilies";
        op = 2;
      }
    } else {
      namexlsx = "recuperacion_gastos.xlsx";
      url = "/api/expensesRecover";
      op = 3;
    }
    dataTypeExpense = await searchData(url);

    if (op == 1) {
      if (dataTypeExpense.length > 0) {
        for (i = 0; i < dataTypeExpense.length; i++) {
          if (production_center == "1" && flag_production_center == "1")
            data.push({
              referencia_producto: dataTypeExpense[i].reference,
              producto: dataTypeExpense[i].product,
              unidades_vendidas: parseFloat(dataTypeExpense[i].units_sold),
              volumen_ventas: parseFloat(dataTypeExpense[i].turnover),
              centro_produccion: dataTypeExpense[i].production_center,
            });
          else
            data.push({
              referencia_producto: dataTypeExpense[i].reference,
              producto: dataTypeExpense[i].product,
              unidades_vendidas: parseFloat(dataTypeExpense[i].units_sold),
              volumen_ventas: parseFloat(dataTypeExpense[i].turnover),
            });
        }

        let ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, "Distribucion Producto");
      }
    } else if (op == 2) {
      if (dataTypeExpense.length > 0) {
        for (i = 0; i < dataTypeExpense.length; i++) {
          data.push({
            // referencia: dataProducts[i].id_family,
            familia: dataTypeExpense[i].family,
            unidades_vendidas: parseFloat(dataTypeExpense[i].units_sold),
            volumen_ventas: parseFloat(dataTypeExpense[i].turnover),
          });
        }

        let ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, "Distribucion Familia");
      }
    } else {
      if (dataTypeExpense.length > 0) {
        for (i = 0; i < dataTypeExpense.length; i++) {
          data.push({
            reference_producto: dataProducts[i].reference,
            producto: dataTypeExpense[i].product,
            porcentaje_recuperado: parseFloat(
              dataTypeExpense[i].expense_recover
            ),
          });
        }

        let ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, "Recuperacion Gasto");
      }
    }

    XLSX.writeFile(wb, namexlsx);
  });
});
