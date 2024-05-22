$(document).ready(function () {
  let op = 1;

  // $(document).on("click", "#btnSimulation", function () {
  //   let EURHeader = $("#EURHeader");

  //   // Oculta el contenido existente con animación
  //   EURHeader.hide(800, function () {
  //     // Vacía el contenido después de la animación de ocultar
  //     EURHeader.empty();

  //     if (op == 1) {
  //       op = 2;

  //       document.getElementById("EURHeader").className =
  //         "col-xl-8 form-inline justify-content-sm-end pt-2";
  //       // Agrega el nuevo contenido con animación después de vaciar el contenido
  //       EURHeader.append(`
  //               <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label EURInputs">
  //                 <label class="font-weight-bold text-dark">Dolar Hoy</label>
  //                 <input type="text" class="form-control text-center" name="valueDollar" id="valueDollar" style="background-color: aliceblue;"
  //                   value="$ ${currentDollar.toLocaleString("es-CO", {
  //                     minimumFractionDigits: 2,
  //                     maximumFractionDigits: 2,
  //                   })}" readonly>
  //               </div>
  //               <div class="col-xs-2 form-group floating-label enable-floating-label mr-2 EURInputs">
  //                 <label class="font-weight-bold text-dark">Dolar con Cobertura</label>
  //                 <input type="number" class="form-control text-center calcInputs" name="valueCoverageEUR" id="valueCoverageEUR"
  //                   value="${parseFloat(coverage_EUR1).toFixed(2)}">
  //               </div>
  //               <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label EURInputs">
  //                 <label class="font-weight-bold text-dark">Cobertura Cambiaria</label>
  //                 <input type="text" class="form-control text-center" name="exchangeCoverageEUR="exchangeCoverageEUR" style="background-color: aliceblue;" readonly>
  //               </div>
  //               <div class="col-xs-2 form-group floating-label enable-floating-label EURInputs">
  //                 <label class="font-weight-bold text-dark">Correción TRM</label>
  //                 <input type="number" class="form-control text-center calcInputs" name="deviation" id="deviation" value="${deviation}">
  //               </div>
  //               <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label EURInputs">
  //                 <label class="font-weight-bold text-dark">Valor Dolar</label>
  //                 <input type="number" class="form-control text-center" name="valueCoverageEUR" id="valueCoverage" style="background-color: aliceblue;"
  //                   value="${parseFloat(coverage_EUR).toFixed(2)}" readonly>
  //               </div>
  //               <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label EURInputs">
  //                 <label class="font-weight-bold text-dark">Cobertura Cambiaria</label>
  //                 <input type="text" class="form-control text-center" name="exchangeCoverageEUR" id="exchangeCoverageEUR" style="background-color: aliceblue;"
  //                   value="$ ${(
  //                     currentDollar - parseFloat(coverage_EUR)
  //                   ).toLocaleString("es-CO", {
  //                     minimumFractionDigits: 2,
  //                     maximumFractionDigits: 2,
  //                   })}" readonly>
  //               </div>
  //               <div class="col-xs-2 form-group floating-label enable-floating-label EURInputs">
  //                 <button class="btn btn-warning" id="btnSimulation">Simular</button>
  //               </div>
  //           `);

  //       // Realiza cualquier acción adicional después de agregar el contenido
  //       if (flag_composite_product == "1") {
  //         loadTblPrices(parents, 3);
  //       } else {
  //         loadTblPrices(allPrices, 3);
  //       }
  //     } else {
  //       op = 1;

  //       document.getElementById("EURHeader").className =
  //         "col-xl-8 form-inline justify-content-sm-end"; 
        
  //       // Agrega el nuevo contenido con animación después de vaciar el contenido
  //       EURHeader.append(`
  //               <div class="col-xs-2 mt-4 mr-2 EURInputs">
  //                 <button class="btn btn-warning" id="btnSimulation">Simular</button>
  //               </div>
  //               <div class="col-xs-2 mr-2 EURInputs">
  //                  <label class="ml-3 text-dark">Tipo moneda</label>
  //                  <select class="form-control selectCurrency" id="selectCurrency">
  //                      <option disabled>Seleccionar</option>
  //                      ${flag_currency_EUR == '' ? '<option value="1">COP</option>' : ''}
  //                      ${flag_currency_eur == '1' ? '<option value="2" selected>EUR</option>' : ''}
  //                      <option value="3">EUR</option>
  //                  </select>
  //               </div>
  //               <div class="col-xs-2 mt-4 mr-2 form-group floating-label enable-floating-label EURInputs" style="margin-bottom: 0px;">
  //                 <label class="mb-1 font-weight-bold text-dark">Valor Dolar</label>
  //                 <input type="number" class="form-control text-center calcInputs" name="valueCoverageEUR" id="valueCoverageEUR"
  //                   value="${parseFloat(coverage_EUR).toFixed(2)}">
  //               </div>
  //               <div class="col-xs-2 mt-4 mr-2 form-group floating-label enable-floating-label EURInputs" style="margin-bottom: 0px;">
  //                 <label class="font-weight-bold text-dark">Cobertura Cambiaria</label>
  //                 <input type="text" class="form-control text-center" name="exchangeCoverageEUR" id="exchangeCoverageEUR" style="background-color: aliceblue;"
  //                   value="$ ${(
  //                     currentDollar - parseFloat(coverage_EUR)
  //                   ).toLocaleString("es-CO", {
  //                     minimumFractionDigits: 2,
  //                     maximumFractionDigits: 2,
  //                   })}" readonly>
  //               </div>
  //           `);
  //       // Realiza cualquier acción adicional después de agregar el contenido
  //       if (flag_composite_product == "1") {
  //         loadTblPrices(parents, 2);
  //       } else {
  //         loadTblPrices(allPrices, 2);
  //       }
  //     }

  //     // Muestra el contenido con animación
  //     EURHeader.show(800);
  //   });
  // });

  /* Calcular valor de cobertura ingresando numero de desviación */
  $(document).on("blur", ".calcEURInputs", function (e) {
    let num = $("#deviation").val();
    let valueCoverage = parseFloat($("#valueCoverageEUR").val());

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
        title: "Valor Euro",
        message: "Está seguro de modificar el valor del Euro?",
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
            getEURData(valueCoverage, null, 1);
          }
        },
      });
    } else getEURData(valueCoverage, num, 2, this.id);
  });

  const getEURData = async (valueCoverage, deviation, op, id) => {
    $(".EURInputs").hide(400);

    let EURHeader = document.getElementById("EURHeader");

    EURHeader.insertAdjacentHTML(
      "beforeend",
      `<div class="spinner-border text-secondary" role="status">
        <span class="sr-only">Loading...</span>
      </div>`
    );

    if (op == 1) {
      let data = await searchData(`/api/priceEUR/${valueCoverage}`);

      if (data.success) {
        coverage_EUR = valueCoverage;

        $(".spinner-border").remove();
        $(".EURInputs").show(400);

        loadAllData();
      }
    }
    
    /*else {
      // Simulador
      let data = {};
      data["deviation"] = deviation;
      data["coverage_eur"] = valueCoverage;
      data["id"] = id;

      $.post("/api/simPriceEUR", data, function (resp, textStatus, jqXHR) {
        if (resp.success) {
          $("#exchangeCoverageEUR").val(
            `$ ${resp.exchangeCoverage.toLocaleString("es-CO", {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            })}`
          );

          coverage_EUR1 = resp.coverage_EUR;

          $("#valueCoverageEUR").val(parseFloat(resp.coverage_EUR).toFixed(2));

          $(".spinner-border").remove();
          $(".EURInputs").show(400);

          if (flag_composite_product == "1") {
            loadTblPrices(parents, 3, resp.coverage_EUR);
          } else loadTblPrices(allPrices, 3, resp.coverage_EUR);
        }
      });
    }*/
  };
});
