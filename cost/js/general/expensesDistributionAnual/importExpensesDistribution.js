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
        ];

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

        let url = "/api/annualDistribution/expenseDistributionAnualDataValidation";
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

          return {
            referenceProduct: item.referencia_producto,
            product: item.producto,
            unitsSold: unitsSold,
            turnover: turnover,
          };

        });

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
