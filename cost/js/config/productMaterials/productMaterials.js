$(document).ready(function () {
  let idProduct;
  $('.btnDownloadXlsx').hide(); 

  $('.selectNavigation').click(function (e) {
    e.preventDefault();

    $('.cardProducts').show();

    if (this.id == 'materials') {
      $('.cardProductsMaterials').show();
      $('.cardProductsProcess').hide(); 
      $('.cardAddProcess').hide();
      $('.cardServices').hide();
      $('.cardImportProductsProcess').hide(); 
      $('.cardAddService').hide(); 
      $('.cardImportExternalServices').hide(); 
    } else if (this.id == 'process') {
      $('.cardProductsProcess').show();
      $('.cardProductsMaterials').hide(); 
      $('.cardAddMaterials').hide();
      $('.cardServices').hide();
      $('.cardImportProductsMaterials').hide();
      $('.cardAddNewProduct').hide(); 
      $('.cardAddService').hide();
      $('.cardImportExternalServices').hide();
    } else {
      $('.cardServices').show();
      $('.cardProductsProcess').hide();
      $('.cardAddProcess').hide();
      $('.cardProductsMaterials').hide(); 
      $('.cardAddMaterials').hide();
      $('.cardImportProductsMaterials').hide();
      $('.cardAddNewProduct').hide();
      $('.cardAddService').hide(); 
      $('.cardImportExternalServices').hide();       
    }
    
    let tables = document.getElementsByClassName(
      'dataTable'
    );

    for (let i = 0; i < tables.length; i++) {
      let attr = tables[i];
      attr.style.width = '100%';
      attr = tables[i].firstElementChild;
      attr.style.width = '100%';
    }
  });

  $('#categories').change(function (e) {
    e.preventDefault();

    let data = JSON.parse(sessionStorage.getItem('dataMaterials'));

    if (this.value != '0') data = data.filter(item => item.id_category == this.value);

    addSelectsMaterials(data);
  });


  // Crear selects manualmente
  addSelectsMaterials = (data) => {
    let ref = sortFunction(data, 'reference');

    $select = $(`#refMaterial`);
    $select.empty();
    $select.append(`<option disabled selected value='0'>Seleccionar</option>`);
    $.each(ref, function (i, value) {
      $select.append(
        `<option value = ${value.id_material}> ${value.reference} </option>`
      );
    });

    let name = sortFunction(data, 'material');

    $select1 = $(`#nameMaterial`);
    $select1.empty();
    $select1.append(`<option disabled selected value='0'>Seleccionar</option>`);
    $.each(name, function (i, value) {
      $select1.append(
        `<option value = ${value.id_material}> ${value.material} </option>`
      );
    });
  }
  /* Ocultar panel crear producto */

  $('.cardAddMaterials').hide();

  /* Abrir panel crear producto */

  $('#btnCreateProduct').click(async function (e) {
    e.preventDefault();

    $('.cardImportProductsMaterials').hide(800);
    $('.cardAddNewProduct').hide(800);
    $('.cardAddMaterials').toggle(800);
    $('#btnAddMaterials').html('Asignar');
    $('#units').empty();

    let categories = JSON.parse(sessionStorage.getItem('dataCategories'));

    if(categories.length == 0)
      $('.categories').hide(); 
    else
      $('.categories').show(800); 
    
    $('.cardProducts').show(800); 

    sessionStorage.removeItem('id_product_material');

    $('#formAddMaterials').trigger('reset');
  });

  /* Adicionar unidad de materia prima */

  $('.material').change(async function (e) {
    e.preventDefault();
    let id = this.value;

    let data = sessionStorage.getItem('dataMaterials');
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

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    idProduct = $('#selectNameProduct').val();
  });

  /* Adicionar nueva materia prima */

  $('#btnAddMaterials').click(function (e) {
    e.preventDefault();

    let idProductMaterial = sessionStorage.getItem('id_product_material');

    if (idProductMaterial == '' || idProductMaterial == null) {
      checkDataProductsMaterials(
        '/api/addProductsMaterials',
        idProductMaterial
      );
    } else {
      checkDataProductsMaterials(
        '/api/updateProductsMaterials',
        idProductMaterial
      );
    }
  });

  /* Actualizar productos materials */

  $(document).on('click', '.updateMaterials', async function (e) {
    $('.cardImportProductsMaterials').hide(800);
    $('.cardAddMaterials').show(800);
    $('.cardAddNewProduct').hide(800);
    $('.categories').hide(800);
    $('#btnAddMaterials').html('Actualizar');
    let data = JSON.parse(sessionStorage.getItem('dataMaterials'));
    await addSelectsMaterials(data);

    $('#units').empty();

    let row = $(this).parent().parent()[0];
    data = tblConfigMaterials.fnGetData(row);

    sessionStorage.setItem('id_product_material', data.id_product_material);
    $(`#refMaterial option[value=${data.id_material}]`).prop('selected', true);
    $(`#nameMaterial option[value=${data.id_material}]`).prop('selected', true);

    $(`#magnitudes option[value=${data.id_magnitude}]`).prop('selected', true);
    loadUnitsByMagnitude(data, 2);
    $(`#units option[value=${data.id_unit}]`).prop('selected', true);

    // let quantity = `${data.quantity}`;

    $('#quantity').val(data.quantity);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision data Productos materiales */
  checkDataProductsMaterials = async (url, idProductMaterial) => {
    let ref = parseInt($('#nameMaterial').val());
    let unit = parseInt($('#units').val());
    let quan = parseFloat($('#quantity').val());
    idProduct = parseInt($('#selectNameProduct').val());

    let data = ref * unit * idProduct;

    if (!data || quan == '') {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    // quan = parseFloat(strReplaceNumber(quan));

    quant = 1 * quan;

    if (quan <= 0 || isNaN(quan)) {
      toastr.error('La cantidad debe ser mayor a cero (0)');
      return false;
    }

    let dataProductMaterial = new FormData(formAddMaterials);
    dataProductMaterial.append('idProduct', idProduct);

    if (idProductMaterial != '' || idProductMaterial != null)
      dataProductMaterial.append('idProductMaterial', idProductMaterial);

    let resp = await sendDataPOST(url, dataProductMaterial);

    messageMaterials(resp);
  };

  /* Eliminar materia prima */

  deleteMaterial = (op) => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblConfigMaterials.fnGetData(row);

    
    let idProduct = $('#selectNameProduct').val();
    let dataP = {};
    dataP['idProduct'] = idProduct;

    if (op == '1') {
      let idProductMaterial = data.id_product_material;
      dataP['idProductMaterial'] = idProductMaterial;
      url = '/api/deleteProductMaterial';
    } else {
      dataP['idCompositeProduct'] = data.id_composite_product; 
      url = '/api/deleteCompositeProduct';
    }

    bootbox.confirm({
      title: 'Eliminar',
      message:
        `Está seguro de eliminar ${op == '1' ? 'esta Materia prima' : 'este Producto Compuesto'}? Esta acción no se puede reversar.`,
      buttons: {
        confirm: {
          label: 'Si',
          className: 'btn-success',
        },
        cancel: {
          label: 'No',
          className: 'btn-danger',
        },
      },
      callback: function (result) {
        if (result == true) {
          $.post(url, dataP,
            function (data, textStatus, jqXHR) {
              messageMaterials(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  messageMaterials = (data) => {
    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    $('#fileProductsMaterials').val('');
    
    if (data.success == true) {
      $('.cardImportProductsMaterials').hide(800);
      $('#formImportProductMaterial').trigger('reset');
      $('.cardAddMaterials').hide(800);
      $('.cardAddNewProduct').hide(800);
      $('.cardImportProductsMaterials').hide(800);
      $('.cardProducts').show(800);

      $('#formAddMaterials').trigger('reset');
      let idProduct = $('#selectNameProduct').val();
      // if (idProduct)
      loadAllDataMaterials(idProduct);

      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  $('.btnDownloadXlsx').click(function (e) {
    e.preventDefault();

    let wb = XLSX.utils.book_new();
    let id_product = $('#refProduct').val();

    /* Materiales */
    let data = [];

    if (flag_composite_product == '1')
      allProductMaterials = [...allProductMaterials, ...allComposites];

    let arr = allProductMaterials.filter(item => item.id_product == id_product);
    
    if (arr.length > 0) {
      for (i = 0; i < arr.length; i++) {
        data.push({
          referencia_producto: arr[i].reference_product,
          producto: arr[i].product,
          referencia_material: arr[i].reference_material,
          material: arr[i].material,
          magnitud: arr[i].magnitude,
          unidad: arr[i].unit,
          quantity: arr[i].quantity,
          tipo: arr[i].type,
        });
      }
      
      let ws = XLSX.utils.json_to_sheet(data);
      XLSX.utils.book_append_sheet(wb, ws, 'Productos Materias');
    }
      
    /* Procesos */
    data = [];
    
    arr = dataProductProcess.filter(item => item.id_product == id_product);
    if (arr.length > 0) {
      for (i = 0; i < arr.length; i++) {
        data.push({
          referencia_producto: arr[i].reference,
          producto: arr[i].product,
          proceso: arr[i].process,
          maquina: arr[i].machine,
          tiempo_enlistamiento: arr[i].enlistment_time,
          tiempo_operacion: arr[i].operation_time,
          maquina_autonoma: arr[i].auto_machine
        });
      }

      ws = XLSX.utils.json_to_sheet(data);
      XLSX.utils.book_append_sheet(wb, ws, 'Productos Procesos');
    }
    
    /* Servicios */
    data = [];

    arr = dataServices.filter(item => item.id_product == id_product);
    if (arr.length > 0) {
      for (i = 0; i < dataServices.length; i++) {
        data.push({
          referencia_producto: dataServices[i].reference,
          producto: dataServices[i].product,
          servicio: dataServices[i].name_service,
          costo: dataServices[i].cost,
        });
      }

      ws = XLSX.utils.json_to_sheet(data);
      XLSX.utils.book_append_sheet(wb, ws, 'Servicios Externos');
    }
      
    XLSX.writeFile(wb, 'Ficha_Productos.xlsx');
  });
});
