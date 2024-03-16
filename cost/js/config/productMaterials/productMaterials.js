$(document).ready(function () {
  let idProduct;

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
      sessionStorage.removeItem('dataMaterials');
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

  $(document).on('click', '.updateMaterials', function (e) {
    $('.cardImportProductsMaterials').hide(800);
    $('.cardAddMaterials').show(800);
    $('.cardAddNewProduct').hide(800);
    $('#btnAddMaterials').html('Actualizar');
    $('#units').empty();

    let row = $(this).parent().parent()[0];
    let data = tblConfigMaterials.fnGetData(row);

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

  $('#btnDownloadXlsx').click(function (e) {
    e.preventDefault();

    let wb = XLSX.utils.book_new();

    let data = [];
    
    namexlsx = 'Ficha_Tecnica.xlsx';
    
    let id_product = $('#refProduct').val();

    let ref = $('#refProduct :selected').text().trim();
    let product = $('#selectNameProduct :selected').text().trim();

    if (id_product) {
      let productMaterials = tblConfigMaterials.fnGetData();

      for (i = 0; i < productMaterials.length; i++) {
        data.push({
          referencia_producto: ref,
          producto: product,
          referencia_material: productMaterials[i].reference,
          material: productMaterials[i].material,
          magnitud: productMaterials[i].magnitude,
          unidad: productMaterials[i].unit,
          Cantidad: parseFloat(productMaterials[i].quantity),
          Precio: parseFloat(productMaterials[i].cost_product_material),
        });
      }

      let ws = XLSX.utils.json_to_sheet(data);
      XLSX.utils.book_append_sheet(wb, ws, 'Ficha Tecnica Producto');
      XLSX.writeFile(wb, namexlsx);
    }     
  });
 
});
