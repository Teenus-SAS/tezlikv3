$(document).ready(function () {
  let selectedFile;

  $('.cardImportProductsMaterials').hide();

  $('#btnImportNewProductsMaterials').click(function (e) {
    e.preventDefault();
    $('.cardAddMaterials').hide(800);
    $('.cardAddNewProduct').hide(800);
    $('.cardImportProductsMaterials').toggle(800);
    $('.cardProducts').toggle(800);
  });

  $('#fileProductsMaterials').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportProductsMaterials').click(function (e) {
    e.preventDefault();

    let file = $('#fileProductsMaterials').val();
    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formProductMaterials');

    form.insertAdjacentHTML(
      'beforeend',
      `<div class='col-sm-1 cardLoading' style='margin-top: 7px; margin-left: 15px'>
        <div class='spinner-border text-secondary' role='status'>
            <span class='sr-only'>Loading...</span>
        </div>
      </div>`
    );

    importFile(selectedFile)
      .then(async (data) => {
        let arr = data.rowObject;
        
        if (arr.length == 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProductsMaterials').val('');
          toastr.error('Archivo vacio. Verifique nuevamente');
          return false;
        }

        const expectedHeaders = ['referencia_producto', 'producto', 'referencia_material', 'material', 'magnitud', 'unidad', 'cantidad', 'desperdicio', 'tipo'];
        const actualHeaders = data.actualHeaders;

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProductsMaterials').val('');

          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }
  
        let resp = await validateDataFTM(arr);
        // if (resp.importStatus == true)
          checkProductMaterial(resp.productMaterialsToImport, resp.debugg);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileProductsMaterials').val('');

        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Validar Data */
  const validateDataFTM = async (data) => {
    let productMaterialsToImport = [];
    let debugg = [];
    // let importStatus = true;

    const dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));
    let dataMaterials = JSON.parse(sessionStorage.getItem('dataMaterials'));

    for (let i = 0; i < data.length; i++) {
      let arr = data[i];

      let quantity = arr.cantidad > 0 ? arr.cantidad.toString() : '';
      let waste = arr.desperdicio >= 0 ? arr.desperdicio.toString() : '';

      !arr.referencia_producto ? arr.referencia_producto = '' : arr.referencia_producto;
      !arr.producto ? arr.producto = '' : arr.producto;
      !arr.referencia_material ? arr.referencia_material = '' : arr.referencia_material;
      !arr.material ? arr.material = '' : arr.material;
      !arr.magnitud ? arr.magnitud = '' : arr.magnitud;
      !arr.unidad ? arr.unidad = '' : arr.unidad;
      !arr.tipo ? arr.tipo = '' : arr.tipo;

      if (
        !arr.referencia_producto || !arr.producto || !arr.referencia_material || !arr.material || !arr.magnitud || !arr.unidad ||
        quantity.trim() === '' || waste.trim() === '' || !arr.tipo ||
        !arr.referencia_producto.toString().trim() || !arr.producto.toString().trim() || !arr.referencia_material.toString().trim() || !arr.material.toString().trim() || !arr.magnitud.toString().trim() || !arr.unidad.toString().trim() ||
        !arr.tipo.toString().trim()
      ) {
        // $('.cardLoading').remove();
        // $('.cardBottons').show(400);
        // $('#fileProductsMaterials').val('');
        debugg.push({ message: `Columna vacía en la fila: ${i + 2}` });
        // toastr.error(`Columna vacía en la fila: ${i + 2}`);
        // importStatus = false;
        // break;
      }

      let valQuantity = parseFloat(quantity.replace(',', '.')) * 1;
      if (isNaN(valQuantity) || valQuantity <= 0) {
        // $('.cardLoading').remove();
        // $('.cardBottons').show(400);
        // $('#fileProductsMaterials').val('');
        debugg.push({ message: `La cantidad debe ser mayor a cero (0). Fila: ${i + 2}` });
        // toastr.error(`La cantidad debe ser mayor a cero (0). Fila: ${i + 2}`);
        // importStatus = false;
        // break;
      }

      let product = dataProducts.find(item =>
        item.reference == arr.referencia_producto.toString().trim() &&
        item.product == arr.producto.toString().toUpperCase().trim()
      );

      if (!product) {
        // $('.cardLoading').remove();
        // $('.cardBottons').show(400);
        // $('#fileProductsMaterials').val('');
        debugg.push({ message: `Producto no existe en la base de datos. Fila: ${i + 2}` });
        product = { id_product: '' };
        // toastr.error(`Producto no existe en la base de datos. Fila: ${i + 2}`);
        // importStatus = false;
        // break;
      }

      productMaterialsToImport.push({
        referenceProduct: arr.referencia_producto,
        product: arr.producto,
        refRawMaterial: arr.referencia_material,
        nameRawMaterial: arr.material,
        magnitude: arr.magnitud,
        unit: arr.unidad,
        quantity: quantity,
        waste: waste,
        type: arr.tipo
      });

      let type = arr.tipo.toUpperCase().trim();

      switch (type) {
        case 'MATERIAL':
          if (!dataMaterials) {
            await loadDataMaterial(1, '/api/selectMaterials');
            dataMaterials = JSON.parse(sessionStorage.getItem('dataMaterials'));
          }

          let material = dataMaterials.find(item =>
            item.reference == arr.referencia_material.toString().trim() &&
            item.material == arr.material.toString().toUpperCase().trim()
          );

          if (!material) {
            // $('.cardLoading').remove();
            // $('.cardBottons').show(400);
            // $('#fileProductsMaterials').val('');
            debugg.push({ message: `Materia prima no existe en la base de datos. Fila: ${i + 2}` });
            // importStatus = false;
            // break;
          } else {
            productMaterialsToImport[i]['idProduct'] = product.id_product;
            productMaterialsToImport[i]['material'] = material.id_material;
          }
          break;

        case 'PRODUCTO':
          let compositeProduct = dataProducts.find(item =>
            item.reference == arr.referencia_material.toString().trim() &&
            item.product == arr.material.toString().toUpperCase().trim()
          );

          if (!compositeProduct) {
            // $('.cardLoading').remove();
            // $('.cardBottons').show(400);
            // $('#fileProductsMaterials').val('');
            debugg.push({ message: `Producto no existe en la base de datos. Fila: ${i + 2}` });
            // importStatus = false;
            // break;
          } else {
            if (typeof compositeProduct === 'object' && !Array.isArray(compositeProduct) && compositeProduct !== null &&
              compositeProduct.composite == 0) {
              // $('.cardLoading').remove();
              // $('.cardBottons').show(400);
              // $('#fileProductsMaterials').val('');
              debugg.push({ message: `Producto no está definido como compuesto. Fila: ${i + 2}` });
              // importStatus = false;
              // break;
            } else {
              productMaterialsToImport[i]['idProduct'] = product.id_product;
              productMaterialsToImport[i]['compositeProduct'] = compositeProduct.id_product;
            }
          }
          break;

        default:
          // $('.cardLoading').remove();
          // $('.cardBottons').show(400);
          // $('#fileProductsMaterials').val('');
          debugg.push({ message: `Tipo desconocido en la fila: ${i + 2}` });
          // importStatus = false;
        // break;
      }
    }

    return { productMaterialsToImport, debugg };
  };

  /* Mensaje de advertencia */
  const checkProductMaterial = (data, debugg) => {
    $.ajax({
      type: 'POST',
      url: '/api/productsMaterialsDataValidation',
      data: {
        importProductsMaterials: data,
        debugg: debugg
      },
      success: function (resp) {
        let arr = resp.import;

        if (arr.length > 0 && arr.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProductsMaterials').val('');
          toastr.error(arr.message);
          return false;
        }

        if (resp.debugg.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProductsMaterials').val('');

          // Generar el HTML para cada mensaje
          let concatenatedMessages = resp.debugg.map(item =>
            `<li>
              <span class="badge badge-danger" style="font-size: 16px;">${item.message}</span>
            </li>
            <br>`
          ).join('');

          // Mostramos el mensaje con Bootbox
          bootbox.alert({
            title: 'Errores',
            message: `
            <div class="container">
              <div class="col-12">
                <ul>
                  ${concatenatedMessages}
                </ul>
              </div> 
            </div>`,
            size: 'large',
            backdrop: true
          });
          return false;
        }

        if (typeof arr === 'object' && !Array.isArray(arr) && arr !== null && debugg.length == 0) {
          bootbox.confirm({
            title: '¿Desea continuar con la importación?',
            message: `Se han encontrado los siguientes registros:<br><br>Datos a insertar: ${arr.insert} <br>Datos a actualizar: ${arr.update}`,
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
                saveProductMaterialTable(data);
              } else {
                $('.cardLoading').remove();
                $('.cardBottons').show(400);
                $('#fileProductsMaterials').val('');
              }
            },
          });
        }
      },
    });
  };

  const saveProductMaterialTable = (data) => {
    // console.log(data);
    $.ajax({
      type: 'POST',
      url: '/api/addProductsMaterials',
      data: { importProductsMaterials: data },
      success: function (r) {
        $('.cardProducts').show(800);

        messageMaterials(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsProductsMaterials').click(function (e) {
    e.preventDefault();
     
    let url = 'assets/formatsXlsx/Productos_Materias.xlsx';

    let link = document.createElement('a');
    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link; 
  });
});
