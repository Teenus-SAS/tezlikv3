$(document).ready(function () {
  let idProduct;
  let checkBoxEmployees;

  /* Ocultar panel crear producto */

  $(".cardAddProcess").hide();
  $(".checkMachine").hide();

  /* Abrir panel crear producto */

  $("#btnCreateProcess").click(function (e) {
    e.preventDefault();

    $(".cardImportProductsProcess").hide(800);
    $(".cardAddProcess").toggle(800);
    $("#btnAddProcess").html("Asignar");

    sessionStorage.removeItem("id_product_process");

    if (inyection == "1") $("#enlistmentTime").prop("readonly", true);

    $("#formAddProcess").trigger("reset");
    $('.inputs').css("border-color", "");
    $("#checkMachine").prop("checked", false);
  });

  /* Seleccionar producto */
  $("#selectNameProduct").change(function (e) {
    e.preventDefault();
    idProduct = $("#selectNameProduct").val();
  });

  /* Calcular el tiempo total proceso */
  $(document).on("click keyup", ".time", function (e) {
    let tOperation = parseFloat($("#operationTime").val());
    let tEnlistment = parseFloat($("#enlistmentTime").val());
    let efficiency = parseFloat($("#efficiency").val());

    isNaN(tOperation) ? (tOperation = 0) : tOperation;
    isNaN(tEnlistment) ? (tEnlistment = 0) : tEnlistment;
    isNaN(efficiency) || efficiency == 0 ? (efficiency = 100) : efficiency;

    // Subtotal
    if (inyection == 1)
      subtotal = (tEnlistment / (tOperation / 100)).toFixed(2);
    else subtotal = tEnlistment + tOperation;

    !isFinite(subtotal) ? (subtotal = 0) : subtotal;

    $("#subTotalTime").val(subtotal);

    // Total
    total = subtotal / (efficiency / 100);
    !isFinite(total) ? (total = 0) : (total = total.toFixed(2));

    $("#totalTime").val(total);
  });

  /* Adicionar nuevo proceso */
  $("#btnAddProcess").click(function (e) {
    e.preventDefault();
    let idProductProcess = sessionStorage.getItem("id_product_process");

    if (idProductProcess == "" || idProductProcess == null) {
      checkDataProductsProcess("/api/addProductsProcess", idProductProcess);
    } else {
      checkDataProductsProcess("/api/updateProductsProcess", idProductProcess);
    }
  });

  /* Actualizar productos Procesos */

  $(document).on("click", ".updateProcess", function (e) {
    $(".cardImportProductsProcess").hide(800);
    $(".cardAddProcess").show(800);
    $('.inputs').css("border-color", "");
    $("#btnAddProcess").html("Actualizar");

    let data = dataProductProcess.find(
      (item) => item.id_product_process == this.id
    );

    sessionStorage.setItem("id_product_process", data.id_product_process);

    $(`#idProcess option[value=${data.id_process}]`).prop("selected", true);

    data.id_machine == null ? (data.id_machine = 0) : data.id_machine;
    $(`#idMachine option[value=${data.id_machine}]`).prop("selected", true);

    if (inyection == "1") {
      $("#enlistmentTime").val(data.unity_time);
      $("#enlistmentTime").prop("readonly", true);
    } else $("#enlistmentTime").val(data.enlistment_time);

    $("#operationTime").val(data.operation_time);
    $("#efficiency").val(data.efficiency);

    $("#enlistmentTime").click();

    let employees = data.employee.toString().split(",");
    checkBoxEmployees = employees;

    if (data.auto_machine == 'SI') {
      $("#checkMachine").prop("checked", true);
    }
    $(".checkMachine").show();

    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  function validateForm() {
    let emptyInputs = [];

    let refP = parseInt($("#idProcess").val());
    let refM = parseInt($("#idMachine").val());
    let enlistmentTime = parseFloat($("#enlistmentTime").val());
    let operationTime = parseFloat($("#operationTime").val());

    // Verificar cada campo y agregar los vacíos a la lista
    if (!refP) {
      emptyInputs.push("#idProcess"); 
    }

    if (isNaN(refM)) {
      emptyInputs.push("#idMachine");
    }
    
    // if (inyection == "0") {
    //   if (enlistmentTime) {
    //     emptyInputs.push("#enlistmentTime");
    //   }
    // }

    if (!operationTime) {
      emptyInputs.push("#operationTime");
    }

    // Marcar los campos vacíos con borde rojo
    emptyInputs.forEach(function (selector) {
      $(selector).css("border-color", "red");
    });

    // Mostrar mensaje de error si hay campos vacíos
    if (emptyInputs.length > 0) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    return true;
  };

  /* Revision data productos procesos */
  checkDataProductsProcess = async (url, idProductProcess) => {
    if (!validateForm()) {
      return false;
    }
    let idProduct = parseInt($("#selectNameProduct").val());
    // let refP = parseInt($("#idProcess").val());
    // let refM = parseInt($("#idMachine").val());
    // let enlistmentTime = parseFloat($("#enlistmentTime").val());
    // let operationTime = parseFloat($("#operationTime").val());
    // // let efficiency = parseFloat($("#efficiency").val());
    let status = parseInt(
      $("#idProcess").find("option:selected").attr("class")
    );

    // let data = idProduct * refP * operationTime;

    // if (inyection == "0") data += enlistmentTime;

    // if (!data || isNaN(refM) || data == 0) {
    //   toastr.error("Ingrese todos los campos");
    //   return false;
    // }

    let dataProductProcess1 = new FormData(formAddProcess);
    let autoMachine = 1;

    if (!$("#checkMachine").is(":checked")) {
      if (status === 0) {
        $('#idProcess').css("border-color", "red");

        toastr.error(
          "Active los procesos creando la nomina antes de asignar los procesos y máquinas para un producto"
        );
        return false;
      }
      autoMachine = 0;
    }

    dataProductProcess1.append("autoMachine", autoMachine);
    dataProductProcess1.append("idProduct", idProduct);

    if (idProductProcess != "" || idProductProcess != null) {
      dataProductProcess1.append("idProductProcess", idProductProcess);

      flag_employee == "1" ? (employees = checkBoxEmployees) : (employees = "");
      dataProductProcess1.append("employees", employees);
    }

    let resp = await sendDataPOST(url, dataProductProcess1);

    messageProcess(resp);
  };

  /* Eliminar proceso */
  deleteProcess = (id) => {
    // let row = $(this.activeElement).parent().parent()[0];
    // let data = tblConfigProcess.fnGetData(row);
    let data = dataProductProcess.find((item) => item.id_product_process == id);

    let idProductProcess = data.id_product_process;
    idProduct = $("#selectNameProduct").val();
    let dataProductProcess1 = {};
    dataProductProcess1["idProductProcess"] = idProductProcess;
    dataProductProcess1["idProduct"] = idProduct;

    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar este proceso? Esta acción no se puede reversar.",
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
          $.post(
            "/api/deleteProductProcess",
            dataProductProcess1,
            function (data, textStatus, jqXHR) {
              messageProcess(data);
            }
          );
        }
      },
    });
  };

  /* Modificar empleados */
  $(document).on("click", ".updateEmployee", async function () {
    let data = dataProductProcess.find(
      (item) => item.id_product_process == this.id
    );

    let employees = data.employee.toString().split(",");
    id_product_process = data.id_product_process;
    id_product = data.id_product;

    let payroll = await searchData(`/api/employees/${id_product_process}`);

    let options = "";
    for (let i = 0; i < payroll.length; i++) {
      let checked = "";

      if (!employees[0] == "") {
        for (let j = 0; j < employees.length; j++) {
          if (payroll[i].id_payroll == employees[j]) {
            checked = "checked";
            break;
          }
        }
      }

      options += `<div class="checkbox checkbox-success">
                    <input class="checkboxEmployees" id="${payroll[i].id_payroll}" type="checkbox" ${checked}>
                    <label for="${payroll[i].id_payroll}">${payroll[i].employee}</label>
                  </div>`;
    }

    checkBoxEmployees = employees;

    bootbox.confirm({
      title: "Empleados",
      message: `${options}`,
      buttons: {
        confirm: {
          label: "Guardar",
          className: "btn-success",
        },
        cancel: {
          label: "Cancelar",
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result == true) {
          if (checkBoxEmployees.length == 0) {
            toastr.error("Seleccione un empleado");
            return false;
          }

          let data = {};
          data["idProductProcess"] = id_product_process;
          data["idProduct"] = id_product;
          data["employees"] = checkBoxEmployees;

          $.post(
            "/api/saveEmployees",
            data,
            function (data, textStatus, jqXHR) {
              messageProcess(data);
            }
          );
        }
      },
    });
  });

  $(document).on("click", ".checkboxEmployees", function () {
    $(`#${this.id}`).is(":checked") ? (op = true) : (op = false);
    $(`#${this.id}`).prop("checked", op);

    if (!$(`#${this.id}`).is(":checked")) {
      for (let i = 0; i < checkBoxEmployees.length; i++) {
        if (checkBoxEmployees[i] == this.id) checkBoxEmployees.splice(i, 1);
      }
    } else {
      if (checkBoxEmployees[0] == "") {
        checkBoxEmployees.splice(0, 1);
      }
      checkBoxEmployees.push(this.id);
    }
  });

  /* Mensaje de exito */

  messageProcess = (data) => {
    $("#fileProductsProcess").val("");
    $(".cardLoading").remove();
    $(".cardBottons").show(400);

    if (data.success == true) {
      $(".cardImportProductsProcess").hide(800);
      $("#formImportProductProcess").trigger("reset");
      $(".cardAddProcess").hide(800);
      $(".cardProducts").show(800);
      // $('.cardAddNewProduct').show(800);
      $("#formAddProcess").trigger("reset");
      let idProduct = $("#selectNameProduct").val();
      // if (idProduct)
      loadAllDataProcess(idProduct);

      toastr.success(data.message);
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
