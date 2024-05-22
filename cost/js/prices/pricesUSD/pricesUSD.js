$(document).ready(function () {
  let op = 1;

  $(document).on("click", "#btnSimulation", function () {
    let USDHeader = $("#USDHeader");

    // Oculta el contenido existente con animación
    USDHeader.hide(800, function () {
      // Vacía el contenido después de la animación de ocultar
      USDHeader.empty();

      if (op == 1) {
        op = 2;

        document.getElementById("USDHeader").className =
          "col-xl-8 form-inline justify-content-sm-end pt-2";
        // Agrega el nuevo contenido con animación después de vaciar el contenido
        USDHeader.append(`
                <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label USDInputs">
                  <label class="font-weight-bold text-dark">Dolar Hoy</label>
                  <input type="text" class="form-control text-center" name="valueDollar" id="valueDollar" style="background-color: aliceblue;"
                    value="$ ${currentDollar.toLocaleString("es-CO", {
                      minimumFractionDigits: 2,
                      maximumFractionDigits: 2,
                    })}" readonly>
                </div>
                <div class="col-xs-2 form-group floating-label enable-floating-label mr-2 USDInputs">
                  <label class="font-weight-bold text-dark">Dolar con Cobertura</label>
                  <input type="number" class="form-control text-center calcUSDInputs" name="valueCoverageUSD" id="valueCoverageUSD"
                    value="${parseFloat(coverage_usd1).toFixed(2)}">
                </div>
                <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label USDInputs">
                  <label class="font-weight-bold text-dark">Cobertura Cambiaria</label>
                  <input type="text" class="form-control text-center" name="exchangeCoverageUSD="exchangeCoverageUSD" style="background-color: aliceblue;" readonly>
                </div>
                <div class="col-xs-2 form-group floating-label enable-floating-label USDInputs">
                  <label class="font-weight-bold text-dark">Correción TRM</label>
                  <input type="number" class="form-control text-center calcUSDInputs" name="deviation" id="deviation" value="${deviation}">
                </div>
                <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label USDInputs">
                  <label class="font-weight-bold text-dark">Valor Dolar</label>
                  <input type="number" class="form-control text-center" name="valueCoverageUSD" id="valueCoverage" style="background-color: aliceblue;"
                    value="${parseFloat(coverage_usd).toFixed(2)}" readonly>
                </div>
                <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label USDInputs">
                  <label class="font-weight-bold text-dark">Cobertura Cambiaria</label>
                  <input type="text" class="form-control text-center" name="exchangeCoverageUSD" id="exchangeCoverageUSD" style="background-color: aliceblue;"
                    value="$ ${(
                      currentDollar - parseFloat(coverage_usd)
                    ).toLocaleString("es-CO", {
                      minimumFractionDigits: 2,
                      maximumFractionDigits: 2,
                    })}" readonly>
                </div>
                <div class="col-xs-2 form-group floating-label enable-floating-label USDInputs">
                  <button class="btn btn-warning" id="btnSimulation">Simular</button>
                </div>
            `);

        // Realiza cualquier acción adicional después de agregar el contenido
        if (flag_composite_product == "1") {
          loadTblPrices(parents, 4);
        } else {
          loadTblPrices(allPrices, 4);
        }
      } else {
        op = 1;

        document.getElementById("USDHeader").className =
          "col-xl-8 form-inline justify-content-sm-end"; 
        
        // Agrega el nuevo contenido con animación después de vaciar el contenido
        USDHeader.append(`
                <div class="col-xs-2 mt-4 mr-2 USDInputs">
                  <button class="btn btn-warning" id="btnSimulation">Simular</button>
                </div>
                <div class="col-xs-2 mr-2 floating-label enable-floating-label show-label USDInputs">
                   <label class="ml-3 text-dark">Moneda</label>
                   <select class="form-control selectCurrency" id="selectCurrency">
                       <option disabled>Seleccionar</option>
                       ${flag_currency_usd == '' ? '<option value="1">COP</option>' : ''}
                       ${flag_currency_eur == '1' ? '<option value="2" selected>USD</option>' : ''}
                       <option value="3">EUR</option>
                   </select>
                </div>
                <div class="col-xs-2 mt-4 mr-2 form-group floating-label enable-floating-label USDInputs" style="margin-bottom: 0px;">
                  <label class="mb-1 font-weight-bold text-dark">Valor Dolar</label>
                  <input type="number" class="form-control text-center calcUSDInputs" name="valueCoverageUSD" id="valueCoverageUSD"
                    value="${parseFloat(coverage_usd).toFixed(2)}">
                </div>
                <div class="col-xs-2 mt-4 mr-2 form-group floating-label enable-floating-label USDInputs" style="margin-bottom: 0px;">
                  <label class="font-weight-bold text-dark">Cobertura Cambiaria</label>
                  <input type="text" class="form-control text-center" name="exchangeCoverageUSD" id="exchangeCoverageUSD" style="background-color: aliceblue;"
                    value="$ ${(
                      currentDollar - parseFloat(coverage_usd)
                    ).toLocaleString("es-CO", {
                      minimumFractionDigits: 2,
                      maximumFractionDigits: 2,
                    })}" readonly>
                </div>
            `);
        // Realiza cualquier acción adicional después de agregar el contenido
        if (flag_composite_product == "1") {
          loadTblPrices(parents, 2);
        } else {
          loadTblPrices(allPrices, 2);
        }
      }

      // Muestra el contenido con animación
      USDHeader.show(800);
    });
  });

  /* Calcular valor de cobertura ingresando numero de desviación */
  $(document).on("blur", ".calcUSDInputs", function (e) {
    let num = $("#deviation").val();
    let valueCoverage = parseFloat($("#valueCoverageUSD").val());

    if (isNaN(valueCoverage) || valueCoverage <= 0) {
      toastr.error("Ingrese valor covertura valido");
      return false;
    }

    if (num == "" || num <= 0) {
      toastr.error("Ingrese valor de TRM valido");
      return false;
    } else if (!num) {
      // Guardar cobertura que se puso en el input
      bootbox.confirm({
        title: "Valor Dolar",
        message: "Está seguro de modificar el valor del Dolar?",
        size: "small",
        buttons: {
          confirm: {
            label: '<i class="fa fa-check"></i> Si',
            className: "btn-success",
          },
          cancel: {
            label: '<i class="fa fa-times"></i> No',
            className: "btn-danger",
          },
        },
        callback: function (result) {
          if (result == true) {
            getUSDData(valueCoverage, null, 1);
          }
        },
      });
    } else getUSDData(valueCoverage, num, 2, this.id);
  });

  getUSDData = async (valueCoverage, deviation, op, id) => {
    $(".USDInputs").hide(400);

    let USDHeader = document.getElementById("USDHeader");

    USDHeader.insertAdjacentHTML(
      "beforeend",
      `<div class="spinner-border text-secondary" role="status">
        <span class="sr-only">Loading...</span>
      </div>`
    );

    if (op == 1) {
      let data = await searchData(`/api/priceUSD/${valueCoverage}`);

      if (data.success) {
        coverage_usd = valueCoverage;

        $(".spinner-border").remove();
        $(".USDInputs").show(400);

        loadAllData();
      }
    } else {
      // Simulador
      let data = {};
      data["deviation"] = deviation;
      data["coverage_usd"] = valueCoverage;
      data["id"] = id;

      $.post("/api/simPriceUSD", data, function (resp, textStatus, jqXHR) {
        if (resp.success) {
          $("#exchangeCoverageUSD").val(
            `$ ${resp.exchangeCoverage.toLocaleString("es-CO", {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            })}`
          );

          coverage_usd1 = resp.coverage_usd;

          $("#valueCoverageUSD").val(parseFloat(resp.coverage_usd).toFixed(2));

          $(".spinner-border").remove();
          $(".USDInputs").show(400);

          if (flag_composite_product == "1") {
            loadTblPrices(parents, 4, resp.coverage_usd);
          } else loadTblPrices(allPrices, 4, resp.coverage_usd);
        }
      });
    }
  };
});
