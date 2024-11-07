$(document).ready(function () {
  let selectedFile;

  $(".cardImportExpensesAnual").hide();

  $("#btnImportNewExpensesAnual").click(function (e) {
    e.preventDefault();
    $(".cardExpensesDistributionAnual").hide(800);
    // $(".cardExpenseRecover").hide(800);
    // $("#lblImprotExpense").html(this.value);
    $(".cardImportExpensesAnual").toggle(800);
  });

  $("#fileExpensesA").change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $("#btnImportExpensesA").click(function (e) {
    e.preventDefault();

    let expensesToDistributionAnual = $("#expensesToDistributionAnual").val();

    if (expensesToDistributionAnual == "$ 0" || !expensesToDistributionAnual) {
      $("#fileExpensesA").val("");
      toastr.error("Asigne un gasto primero antes de distribuir");
      return false;
    }

    let file = $("#fileExpensesA").val();

    if (!file) {
      toastr.error("Seleccione un archivo");
      return false;
    }

    // if (option == 1) {
    // bootbox.dialog({
    //   title: 'Importe',
    //   message: 'Seleccione tipo de importe que desea realizar.',
    //   backdrop: 'static', // Evita que el modal se cierre haciendo clic fuera de él
    //   closeButton: false, // Oculta el botón de cierre del modal
    //   size: 'small',
    //   buttons: {
    //     parcial: {
    //       label: 'Parcial',
    //       className: 'btn-success',
    //       callback: function () {
    //         sessionStorage.setItem('typeExpenseD', 1);
    //         checkImportExpenseD();
    //       }
    //     },
    //     total: {
    //       label: 'Total',
    //       className: 'btn-danger',
    //       callback: function () {
    //         sessionStorage.setItem('typeExpenseD', 2);
    //         checkImportExpenseDA();
    //       }
    //     }
    //   }
    // });
    // } else checkImportExpenseD();
    checkImportExpenseDA();
  });

  const checkImportExpenseDA = () => {
    $(".cardBottons").hide();

    let form = document.getElementById("formExpensesDA");

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
          $("#fileExpensesA").val("");
          toastr.error("Archivo vacio. Verifique nuevamente");
          return false;
        }

        // if (option == 1) {
        const expectedHeaders = [
          "unidades_vendidas",
          "volumen_ventas",
          "referencia_producto",
          "producto",
          // "centro_produccion",
        ];
        // if (production_center == "0" || flag_production_center == "0")
        //   expectedHeaders.splice(4, 1);

        const actualHeaders = data.actualHeaders;

        const missingHeaders = expectedHeaders.filter(
          (header) => !actualHeaders.includes(header)
        );

        if (missingHeaders.length > 0) {
          $(".cardLoading").remove();
          $(".cardBottons").show(400);
          $("#fileExpensesA").val("");

          toastr.error(
            "Archivo no corresponde a el formato. Verifique nuevamente"
          );
          return false;
        }
        // } else if (option == 2) {
        //   const expectedHeaders = [
        //     "referencia_producto",
        //     "producto",
        //     "porcentaje_recuperado",
        //   ];
        //   const actualHeaders = Object.keys(data[0]);

        //   const missingHeaders = expectedHeaders.filter(
        //     (header) => !actualHeaders.includes(header)
        //   );

        //   if (missingHeaders.length > 0) {
        //     $(".cardLoading").remove();
        //     $(".cardBottons").show(400);
        //     $("#fileExpensesA").val("");

        //     toastr.error(
        //       "Archivo no corresponde a el formato. Verifique nuevamente"
        //     );
        //     return false;
        //   }
        // }

        let url = "/api/expenseDistributionAnualDataValidation";
        let expenseToImport = arr.map((item) => {
          // if (option == 1) {
          let unitsSold = "";
          let turnover = "";

          if (item.unidades_vendidas)
            unitsSold = item.unidades_vendidas.toString().replace(".", ",");
          else unitsSold = 0;

          if (item.volumen_ventas)
            turnover = item.volumen_ventas.toString().replace(".", ",");
          else turnover = 0;

          // if (production_center == "1" && flag_production_center == "1")
          //   production = item.centro_produccion;
          // else production = 0;

          return {
            referenceProduct: item.referencia_producto,
            product: item.producto,
            unitsSold: unitsSold,
            turnover: turnover,
            // production: production,
          };
          // } else if (option == 2) {
          //   url = "/api/expenseRecoverDataValidation";
          //   return {
          //     referenceProduct: item.referencia_producto,
          //     product: item.producto,
          //     percentage: item.porcentaje_recuperado,
          //   };
          // }
        });

        // if (option == 1) {
        // let type = sessionStorage.getItem("typeExpenseD");
        // expenseToImport[0]["type"] = type;
        // }

        checkExpenseDA(expenseToImport, url);
      })
      .catch(() => {
        console.log("Ocurrio un error. Intente Nuevamente");
      });
  };

  /* Mensaje de advertencia */
  const checkExpenseDA = (data, url) => {
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
          $("#fileExpensesA").val("");
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
              saveExpensesA(data, '/api/addExpensesDistributionAnual');
            } else {
              $(".cardLoading").remove();
              $(".cardBottons").show(400);
              $("#fileExpensesA").val("");
            }
          },
        });
      },
    });
  };

  const saveExpensesA = (data, url) => {
    $.ajax({
      type: "POST",
      url: url,
      data: { importExpense: data },
      success: function (r) {
        messageDistributionA(r, 1);
      },
    });
  };

  /* Descargar formato */
  $("#btnDownloadImportsExpensesA").click(function (e) {
    e.preventDefault();

    let url = 'assets/formatsXlsx/Distribucion_Gastos.xlsx';
    let newFileName = 'Distribucion_Gastos_Anual.xlsx';

    fetch(url)
      .then(response => response.blob())
      .then(blob => {
        let link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = newFileName;

        document.body.appendChild(link);
        link.click();

        document.body.removeChild(link);
        URL.revokeObjectURL(link.href); // liberar memoria
      })
      .catch(console.error);
  });
});
