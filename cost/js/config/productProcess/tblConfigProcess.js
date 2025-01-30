$(document).ready(function () {
  let title3 = `${inyection == 1 ? "Tiempo/Und" : "Tiempo Alistamiento (min)"}`;
  let value3 = `${inyection == 1 ? "unity_time" : "enlistment_time"}`;
  let title4 = `${inyection == 1 ? "% Eficiencia" : "Tiempo Operación (min)"}`;
  let visible;

  // Variables para rastrear si los tiempos están en minutos o segundos
  let isAlistmentMinutes = true;
  let isOperationMinutes = true;

  // Función para convertir minutos a segundos y viceversa
  const convertTime = (time, toSeconds) => {
    return toSeconds ? time * 60 : time / 60;
  };

  // Función para actualizar los tiempos en la columna de Tiempo Alistamiento
  updateAlistmentTimes = (toSeconds) => {
    $("#tblConfigProcessBody tr").each(function () {
      const cells = $(this).find("td");
      const alistmentCell = cells.eq(4); // Columna de Tiempo Alistamiento

      // Convertir el texto a número, manejando correctamente el separador decimal
      const alistmentTime = parseFloat(alistmentCell.text().replace(",", "."));

      // Convertir el tiempo y formatearlo correctamente
      alistmentCell.text(
        convertTime(alistmentTime, toSeconds).toLocaleString("es-CO", {
          minimumFractionDigits: 1,
          maximumFractionDigits: 2,
        })
      );
    });

    // Actualizar el total de Tiempo Alistamiento
    const totalAlistment = parseFloat(
      $("#totalAlistment").text().replace(",", ".")
    );
    $("#totalAlistment").text(
      convertTime(totalAlistment, toSeconds).toLocaleString("es-CO", {
        minimumFractionDigits: 1,
        maximumFractionDigits: 2,
      })
    );
  };

  // Función para actualizar los tiempos en la columna de Tiempo Operación
  updateOperationTimes = (toSeconds) => {
    $("#tblConfigProcessBody tr").each(function () {
      const cells = $(this).find("td");
      const operationCell = cells.eq(5); // Columna de Tiempo Operación

      // Convertir el texto a número, manejando correctamente el separador decimal
      const operationTime = parseFloat(operationCell.text().replace(",", "."));

      // Convertir el tiempo y formatearlo correctamente
      operationCell.text(
        convertTime(operationTime, toSeconds).toLocaleString("es-CO", {
          minimumFractionDigits: 1,
          maximumFractionDigits: 2,
        })
      );
    });

    // Actualizar el total de Tiempo Operación
    const totalOperation = parseFloat(
      $("#totalOperation").text().replace(",", ".")
    );
    $("#totalOperation").text(
      convertTime(totalOperation, toSeconds).toLocaleString("es-CO", {
        minimumFractionDigits: 1,
        maximumFractionDigits: 2,
      })
    );
  };

  // Delegación de eventos para detectar clics en los encabezados
  $("#tblConfigProcess").on("click", "th", function () {
    const headerText = $(this).text();

    // Verificar si el clic fue en "Tiempo Alistamiento (min)" o "Tiempo Alistamiento (seg)"
    if (headerText.includes("Tiempo Alistamiento")) {
      isAlistmentMinutes = !isAlistmentMinutes; // Cambiar el estado
      updateAlistmentTimes(!isAlistmentMinutes); // Convertir los tiempos de la columna correspondiente

      // Actualizar el texto del encabezado
      $(this).text(
        `Tiempo Alistamiento (${isAlistmentMinutes ? "min" : "seg"})`
      );
    }

    // Verificar si el clic fue en "Tiempo Operación (min)" o "Tiempo Operación (seg)"
    if (headerText.includes("Tiempo Operación")) {
      isOperationMinutes = !isOperationMinutes; // Cambiar el estado
      updateOperationTimes(!isOperationMinutes); // Convertir los tiempos de la columna correspondiente

      // Actualizar el texto del encabezado
      $(this).text(`Tiempo Operación (${isOperationMinutes ? "min" : "seg"})`);
    }
  });

  // Evento de clic en los encabezados
  $("#tblConfigProcess th").on("click", function () {
    const headerText = $(this).text();

    if (
      headerText.includes("Tiempo Alistamiento") ||
      headerText.includes("Tiempo Operación")
    ) {
      isMinutes = !isMinutes; // Cambiar el estado
      updateTableTimes(!isMinutes); // Convertir los tiempos

      // Actualizar el texto del encabezado
      $(this).text(
        headerText.replace(
          isMinutes ? "(seg)" : "(min)",
          isMinutes ? "(min)" : "(seg)"
        )
      );
    }
  });

  /* Seleccion producto */
  $("#refProduct").change(function (e) {
    e.preventDefault();
    let id = this.value;

    $("#selectNameProduct option").prop("selected", function () {
      return $(this).val() == id;
    });

    loadAllDataProcess(id);
  });

  $("#selectNameProduct").change(function (e) {
    e.preventDefault();
    let id = this.value;

    $("#refProduct option").prop("selected", function () {
      return $(this).val() == id;
    });

    loadAllDataProcess(id);
  });

  loadAllDataProcess = async (id) => {
    try {
      const productsProcess = await searchData(`/api/productsProcess/${id}`);

      sessionStorage.setItem(
        "dataProductProcess",
        JSON.stringify(productsProcess)
      );

      let op = 1;
      if (flag_currency_usd == "1") {
        let selectPriceUSD = $("#selectPriceUSD2").val();

        selectPriceUSD == "2" ? (op = 2) : (op = 1);
      }
      // dataProductProcess = productsProcess;

      loadTableProcess(productsProcess, op);
    } catch (error) {
      console.error("Error loading data:", error);
    }
  };

  // loadAllDataProcess(0);

  flag_employee == "1" ? (visible = true) : (visible = false);

  /* Cargue tabla de Proyectos */

  loadTableProcess = (data, op) => {
    $(".cardAddProcess").hide(800);

    if ($.fn.dataTable.isDataTable("#tblConfigProcess")) {
      $("#tblConfigProcess").DataTable().destroy();
      $("#tblConfigProcess").empty();
      $("#tblConfigProcess").append(`
        <tbody id="tblConfigProcessBody"></tbody>
        <tfoot>
          <tr>
            <th></th>
            <th></th>
            <th></th>
            <th>Total:</th>
            <th id="totalAlistment"></th>
            <th id="totalOperation"></th>
            <th id="totalEfficiency"></th>
            <th id="totalWorkforce"></th>
            <th id="totalIndirect"></th>
            <th></th>
            </tr>
            </tfoot>`);
    }

    let totalEfficiency = 0;

    data.forEach((item) => {
      totalEfficiency += parseFloat(item.efficiency);
    });

    // Encabezados de la tabla
    var headers = [
      "No.",
      "Proceso",
      "Máquina",
      "Operarios",
      title3,
      title4,
      "Eficiencia",
      "Mano de Obra",
      "Costo Indirecto",
      "Acciones",
    ];

    if (totalEfficiency == 0) headers.splice(6, 1);

    // Obtén la tabla
    var table = document.getElementById("tblConfigProcess");

    // Crea la fila de encabezados
    var headerRow = table.createTHead().insertRow();
    headers.forEach(function (header) {
      var th = document.createElement("th");
      th.textContent = header;
      headerRow.appendChild(th);
    });

    $("#tblConfigProcessBody").empty();
    var body = document.getElementById("tblConfigProcessBody");

    data.forEach((arr, index) => {
      const i = index;
      const dataRow = body.insertRow();

      dataRow.classList.add("t-row"); // Agregar la clase 't-row' a la fila
      dataRow.setAttribute("data-index", index);
      dataRow.setAttribute("data-id", arr.id_product_process);

      headers.forEach((header, columnIndex) => {
        const cell = dataRow.insertCell();

        switch (header) {
          case "No.":
            cell.textContent = i + 1;
            break;
          case "Proceso":
            cell.textContent = arr.process;
            break;
          case "Máquina":
            cell.textContent = arr.machine;
            break;
          case "Operarios":
            let count_employee = arr.count_employee;

            let employees = arr.employee.toString().split(",");
            if (employees[0] != "") {
              count_employee = employees.length;
            }

            cell.textContent = count_employee;
            break;
          case title3:
            let value = parseFloat(arr[value3]);

            if (Math.abs(value) < 0.01) {
              value = value.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 9,
              });
            } else
              value = value.toLocaleString("es-CO", {
                maximumFractionDigits: 2,
              });

            cell.textContent = value;
            break;
          case title4:
            let operation_time = parseFloat(arr.operation_time);

            if (Math.abs(operation_time) < 0.01) {
              operation_time = operation_time.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 9,
              });
            } else
              operation_time = operation_time.toLocaleString("es-CO", {
                maximumFractionDigits: 2,
              });

            cell.textContent = operation_time;
            break;
          case "Eficiencia":
            let efficiency = parseFloat(arr.efficiency);

            if (Math.abs(efficiency) < 0.01) {
              efficiency = efficiency.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 9,
              });
            } else
              efficiency = efficiency.toLocaleString("es-CO", {
                maximumFractionDigits: 2,
              });

            cell.textContent = `${efficiency} %`;
            break;
          case "Mano de Obra":
            let workforce_cost = parseFloat(arr.workforce_cost);

            if (flag_currency_usd == "1" && op == 2)
              workforce_cost = workforce_cost / parseFloat(coverage_usd);
            workforce_cost = renderCost(workforce_cost, op);

            cell.textContent = workforce_cost;
            break;
          case "Costo Indirecto":
            let indirect_cost = parseFloat(arr.indirect_cost);

            if (flag_currency_usd == "1" && op == 2)
              indirect_cost = indirect_cost / parseFloat(coverage_usd);
            indirect_cost = renderCost(indirect_cost, op);

            cell.textContent = indirect_cost;
            break;
          case "Acciones":
            cell.innerHTML = `<a href="javascript:;" <i id="${arr.id_product_process}" class="bx bx-edit-alt updateProcess" data-toggle='tooltip' title='Actualizar Proceso' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${arr.id_product_process}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Proceso' style="font-size: 30px;color:red" onclick="deleteProcess(${arr.id_product_process})"></i></a>`;
            break;
          default:
            cell.textContent = "";
            break;
        }
      });
    });

    if (totalEfficiency == 0) $("#totalEfficiency").remove();

    $("#tblConfigProcess").dataTable({
      pageLength: 50,
      dom: '<"datatable-error-console">frtip',
      language: {
        url: "/assets/plugins/i18n/Spanish.json",
      },
      fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
        if (oSettings.json && oSettings.json.hasOwnProperty("error")) {
          console.error(oSettings.json.error);
        }
      },
    });

    dragula([document.getElementById("tblConfigProcessBody")]).on(
      "drop",
      function (el, container, source, sibling) {
        let copy = [];

        // If the row was dropped within the same container,
        // move it to the specified position
        if (container === source) {
          var targetIndex = sibling
            ? sibling.rowIndex - 1
            : container.children.length - 1;

          container.insertBefore(el, container.children[targetIndex]);

          var elements = $(".t-row");
          elements = elements.not(".gu-mirror");

          for (let i = 0; i < elements.length; i++) {
            copy.push({
              id_product_process: elements[i].dataset.id,
              route: i + 1,
            });
          }

          $.ajax({
            type: "POST",
            url: "/api/saveRouteProductProcess",
            data: { data: copy },
            success: function (resp) {
              messageProcess(resp);
            },
          });
        } else {
          // If the row was dropped into a different container,
          // move it to the first position
          container.insertBefore(el, container.firstChild);
        }
      }
    );

    let alistment = 0;
    let operation = 0;
    let efficiency = 0;
    let workForce = 0;
    let indirect = 0;

    data.forEach((item) => {
      alistment += parseFloat(item[value3]);
      operation += parseFloat(item.operation_time);
      efficiency += parseFloat(item.efficiency);
      workForce +=
        flag_currency_usd == "1" && op == 2
          ? parseFloat(item.workforce_cost) / parseFloat(coverage_usd)
          : parseFloat(item.workforce_cost);
      indirect +=
        flag_currency_usd == "1" && op == 2
          ? parseFloat(item.indirect_cost) / parseFloat(coverage_usd)
          : parseFloat(item.indirect_cost);
      // indirect += parseFloat(item.indirect_cost);
    });

    efficiency = efficiency / data.length;
    workForce = renderCost(workForce, op);
    indirect = renderCost(indirect, op);

    $("#totalAlistment").html(
      alistment.toLocaleString("es-CO", { maximumFractionDigits: 2 })
    );
    $("#totalOperation").html(
      operation.toLocaleString("es-CO", { maximumFractionDigits: 2 })
    );
    $("#totalEfficiency").html(
      `${efficiency.toLocaleString("es-CO", { maximumFractionDigits: 2 })} %`
    );
    $("#totalWorkforce").html(workForce);
    $("#totalIndirect").html(indirect);
  };
});
