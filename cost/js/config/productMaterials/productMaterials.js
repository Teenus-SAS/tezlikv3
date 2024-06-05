$(document).ready(function () {
  let idProduct;
  $(".btnDownloadXlsx").hide();

  $(".selectNavigation").click(function (e) {
    e.preventDefault();

    $(".cardProducts").show();

    if (this.id == "materials") {
      $(".cardProductsMaterials").show();
      $(".cardProductsProcess").hide();
      $(".cardAddProcess").hide();
      $(".cardServices").hide();
      $(".cardImportProductsProcess").hide();
      $(".cardAddService").hide();
      $(".cardImportExternalServices").hide();
    } else if (this.id == "process") {
      $(".cardProductsProcess").show();
      $(".cardProductsMaterials").hide();
      $(".cardAddMaterials").hide();
      $(".cardServices").hide();
      $(".cardImportProductsMaterials").hide();
      $(".cardAddNewProduct").hide();
      $(".cardAddService").hide();
      $(".cardImportExternalServices").hide();
    } else {
      $(".cardServices").show();
      $(".cardProductsProcess").hide();
      $(".cardAddProcess").hide();
      $(".cardProductsMaterials").hide();
      $(".cardAddMaterials").hide();
      $(".cardImportProductsMaterials").hide();
      $(".cardImportProductsProcess").hide();
      $(".cardAddNewProduct").hide();
      $(".cardAddService").hide();
      $(".cardImportExternalServices").hide();
    }

    let tables = document.getElementsByClassName("dataTable");

    for (let i = 0; i < tables.length; i++) {
      let attr = tables[i];
      attr.style.width = "100%";
      attr = tables[i].firstElementChild;
      attr.style.width = "100%";
    }
  });

  // Cambiar moneda
  $('#selectPriceUSD').change(function (e) {
    e.preventDefault();

    if ($('#selectNameProduct').val()) {
      $('.cardAddMaterials').hide(800);
      $('.cardImportProductsMaterials').hide(800);
      $('.cardAddNewProduct').hide(800);

      const selectPriceUSD = this.value;
      let op;

      switch (selectPriceUSD) {
        case '1': // Precios COP 
          op = 1;
          break;
        case '2': // Precios USD
          // titleText = 'Ingrese el valor de compra en USD';
          op = 2;
          break;
      }

      let dataMaterials = JSON.parse(sessionStorage.getItem('dataProductMaterials'));

      loadTableMaterials(dataMaterials, op);
    }
  });

  $("#categories").change(function (e) {
    e.preventDefault();

    let data = JSON.parse(sessionStorage.getItem("dataMaterials"));

    if (this.value != "0")
      data = data.filter((item) => item.id_category == this.value);

    addSelectsMaterials(data);
  });

  // Crear selects manualmente
  addSelectsMaterials = (data) => {
    let ref = sortFunction(data, "reference");

    $select = $(`#refMaterial`);
    $select.empty();
    $select.append(`<option disabled selected value='0'>Seleccionar</option>`);
    $.each(ref, function (i, value) {
      $select.append(
        `<option value = ${value.id_material}> ${value.reference} </option>`
      );
    });

    let name = sortFunction(data, "material");

    $select1 = $(`#nameMaterial`);
    $select1.empty();
    $select1.append(`<option disabled selected value='0'>Seleccionar</option>`);
    $.each(name, function (i, value) {
      $select1.append(
        `<option value = ${value.id_material}> ${value.material} </option>`
      );
    });
  };
  /* Ocultar panel crear producto */
  $(".cardAddMaterials").hide();

  /* Abrir panel crear producto */
  $("#btnCreateProduct").click(async function (e) {
    e.preventDefault();

    $(".cardImportProductsMaterials").hide(800);
    $(".cardAddNewProduct").hide(800);
    $(".cardAddMaterials").toggle(800);
    $("#btnAddMaterials").html("Asignar");
    $("#units").empty();

    let categories = JSON.parse(sessionStorage.getItem("dataCategories"));

    if (categories.length == 0) $(".categories").hide();
    else $(".categories").show(800);

    $(".cardProducts").show(800);

    sessionStorage.removeItem("id_product_material");

    $('.inputs').css("border-color", "");
    $("#formAddMaterials").trigger("reset");
  });

  /* Adicionar unidad de materia prima */
  $(".material").change(async function (e) {
    e.preventDefault();
    let id = this.value;

    let data = sessionStorage.getItem("dataMaterials");
    if (data) {
      dataMaterials = JSON.parse(data);
    }

    for (i = 0; i < dataMaterials.length; i++) {
      if (id == dataMaterials[i].id_material) {
        loadUnitsByMagnitude(dataMaterials[i], 2);
      }
    }
  });

  /* Seleccionar producto */
  $("#selectNameProduct").change(function (e) {
    e.preventDefault();
    idProduct = $("#selectNameProduct").val();
  });

  // Calcular cantidad total
  $(document).on("click keyup", ".quantity", function (e) {
    let quantity = parseFloat($("#quantityMP").val());
    let waste = parseFloat($("#waste").val());

    isNaN(quantity) ? (quantity = 0) : quantity;
    isNaN(waste) ? (waste = 0) : waste;

    // total
    let total = quantity * (1 + waste / 100);

    !isFinite(total) ? (total = 0) : total;

    $("#quantityYotal").val(total);
  });

  /* Adicionar nueva materia prima */
  $("#btnAddMaterials").click(function (e) {
    e.preventDefault();

    let idProductMaterial = sessionStorage.getItem("id_product_material");

    if (idProductMaterial == "" || idProductMaterial == null) {
      checkDataProductsMaterials(
        "/api/addProductsMaterials",
        idProductMaterial
      );
    } else {
      checkDataProductsMaterials(
        "/api/updateProductsMaterials",
        idProductMaterial
      );
    }
  });

  /* Actualizar productos materials */

  $(document).on("click", ".updateMaterials", async function (e) {
    $(".cardImportProductsMaterials").hide(800);
    $(".cardAddMaterials").show(800);
    $('.inputs').css("border-color", "");
    $(".cardAddNewProduct").hide(800);
    $(".categories").hide(800);
    $("#btnAddMaterials").html("Actualizar");
    let data = JSON.parse(sessionStorage.getItem("dataMaterials"));
    await addSelectsMaterials(data);

    $("#units").empty();

    let row = $(this).parent().parent()[0];
    data = tblConfigMaterials.fnGetData(row);

    sessionStorage.setItem("id_product_material", data.id_product_material);
    $(`#refMaterial option[value=${data.id_material}]`).prop("selected", true);
    $(`#nameMaterial option[value=${data.id_material}]`).prop("selected", true);

    if (data.id_magnitude == 0 || data.id_unit == 0) {
      let dataMaterials = JSON.parse(sessionStorage.getItem('dataMaterials'));

      let arr = dataMaterials.find(item => item.id_material == data.id_material);

      data.id_magnitude = arr.id_magnitude;
      data.id_unit = arr.id_unit;
    }

    $(`#magnitudes option[value=${data.id_magnitude}]`).prop("selected", true);
    loadUnitsByMagnitude(data, 2);
    $(`#units option[value=${data.id_unit}]`).prop("selected", true);

    $("#quantityMP").val(data.quantity);
    $("#waste").val(data.waste);

    $("#waste").click();

    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  function validateForm() {
    let emptyInputs = [];
    let refMaterial = parseInt($('#refMaterial').val());
    let units = parseInt($('#units').val()); 
    let quantity = parseFloat($('#quantityMP').val());

    // Verificar cada campo y agregar los vacíos a la lista
    if (!refMaterial) {
      emptyInputs.push("#refMaterial");
      emptyInputs.push("#nameMaterial");
    }
    if (!units) {
      emptyInputs.push("#units");
    }
    if (!quantity) {
      emptyInputs.push("#quantityMP");
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

  /* Revision data Productos materiales */
  checkDataProductsMaterials = async (url, idProductMaterial) => {
    if (!validateForm()) {
      return false;
    }
    // let ref = parseInt($("#nameMaterial").val());
    // let unit = parseInt($("#units").val());
    let quan = parseFloat($("#quantityMP").val());
    // // let waste = parseFloat($("#waste").val());/
    let idProduct = parseInt($("#selectNameProduct").val());

    // let data = ref * unit * idProduct;

    // if (!data || quan == "") {
    //   toastr.error("Ingrese todos los campos");
    //   return false;
    // }

    quant = 1 * quan;

    if (quan <= 0 || isNaN(quan)) {
      $('#quantityMP').css("border-color", "red");
      toastr.error("La cantidad debe ser mayor a cero (0)");
      return false;
    }
    
    let dataProductMaterial = new FormData(formAddMaterials);
    dataProductMaterial.append("idProduct", idProduct);
    
    if (idProductMaterial != "" || idProductMaterial != null)
      dataProductMaterial.append("idProductMaterial", idProductMaterial);
    
    let resp = await sendDataPOST(url, dataProductMaterial);
    
    // $('.inputs').css("border-color", "");
    messageMaterials(resp);
  };

  /* Eliminar materia prima */

  deleteMaterial = (op) => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblConfigMaterials.fnGetData(row);

    let idProduct = $("#selectNameProduct").val();
    let dataP = {};
    dataP["idProduct"] = idProduct;

    if (op == "1") {
      let idProductMaterial = data.id_product_material;
      dataP["idProductMaterial"] = idProductMaterial;
      url = "/api/deleteProductMaterial";
    } else {
      dataP["idCompositeProduct"] = data.id_composite_product;
      url = "/api/deleteCompositeProduct";
    }

    bootbox.confirm({
      title: "Eliminar",
      message: `Está seguro de eliminar ${
        op == "1" ? "esta Materia prima" : "este Producto Compuesto"
      }? Esta acción no se puede reversar.`,
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
          $.post(url, dataP, function (data, textStatus, jqXHR) {
            messageMaterials(data);
          });
        }
      },
    });
  };

  /* Mensaje de exito */

  messageMaterials = (data) => {
    $(".cardLoading").remove();
    $(".cardBottons").show(400);
    $("#fileProductsMaterials").val("");

    
    if (data.success) {
      $(".cardImportProductsMaterials").hide(800);
      $("#formImportProductMaterial").trigger("reset");
      $(".cardAddMaterials").hide(800);
      $(".cardAddNewProduct").hide(800);
      $(".cardImportProductsMaterials").hide(800);
      $(".cardProducts").show(800);

      $("#formAddMaterials").trigger("reset");
      let idProduct = $("#selectNameProduct").val();
      if(idProduct)
        loadAllDataMaterials(idProduct);

      toastr.success(data.message);
      return false;
    } else if (data.error) toastr.error(data.message);
    else if (data.info) toastr.info(data.message);
  };

  $(".btnDownloadXlsx").click(function (e) {
    e.preventDefault();

    let wb = XLSX.utils.book_new();
    let id_product = $("#refProduct").val();

    /* Materiales */
    let data = [];
    let arr = JSON.parse(sessionStorage.getItem('dataProductMaterials'));
    
    if (flag_composite_product == "1") {
      let dataCompositeProduct = JSON.parse(sessionStorage.getItem('dataCompositeProduct'));
      
      arr = [...arr, ...dataCompositeProduct];
    }

    // let arr = dataProductMaterials.filter(
    //   (item) => item.id_product == id_product
    // );

    if (arr.length > 0) {
      for (i = 0; i < arr.length; i++) {
        data.push({
          referencia_producto: arr[i].reference_product,
          producto: arr[i].product,
          referencia_material: arr[i].reference_material,
          material: arr[i].material,
          magnitud: arr[i].magnitude,
          unidad: arr[i].unit,
          cantidad: arr[i].quantity,
          desperdicio: arr[i].waste,
          tipo: arr[i].type,
        });
      }

      let ws = XLSX.utils.json_to_sheet(data);
      XLSX.utils.book_append_sheet(wb, ws, "Productos Materias");
    }

    /* Procesos */
    data = [];
    let dataProductProcess = JSON.parse(sessionStorage.getItem('dataProductProcess'));

    arr = dataProductProcess.filter((item) => item.id_product == id_product);
    if (arr.length > 0) {
      for (i = 0; i < arr.length; i++) {
        data.push({
          referencia_producto: arr[i].reference,
          producto: arr[i].product,
          proceso: arr[i].process,
          maquina: arr[i].machine,
          tiempo_enlistamiento: arr[i].enlistment_time,
          tiempo_operacion: arr[i].operation_time,
          eficiencia: arr[i].efficiency,
          maquina_autonoma: arr[i].auto_machine,
        });
      }

      ws = XLSX.utils.json_to_sheet(data);
      XLSX.utils.book_append_sheet(wb, ws, "Productos Procesos");
    }

    /* Servicios */
    data = [];

    arr = JSON.parse(sessionStorage.getItem('dataServices'));

    // arr = dataServices.filter((item) => item.id_product == id_product);
    if (arr.length > 0) {
      for (i = 0; i < arr.length; i++) {
        data.push({
          referencia_producto: arr[i].reference,
          producto: arr[i].product,
          servicio: arr[i].name_service,
          costo: arr[i].cost,
        });
      }

      ws = XLSX.utils.json_to_sheet(data);
      XLSX.utils.book_append_sheet(wb, ws, "Servicios Externos");
    }

    XLSX.writeFile(wb, "Ficha_Productos.xlsx");
  });
});
