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
      .then((data) => {
        if (data.length == 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProductsMaterials').val('');
          toastr.error('Archivo vacio. Verifique nuevamente');
          return false;
        }

        const expectedHeaders = ['referencia_producto', 'producto', 'referencia_material', 'material', 'magnitud', 'unidad', 'cantidad', 'desperdicio', 'tipo'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProductsMaterials').val('');

          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }
 
        let productMaterialsToImport = [];
        let importStatus = true;

        for (let i = 0; i < data.length; i++) {
          let arr = data[i];

          let quantity = '';
          let waste = '';

          if (arr.cantidad > 0) {
            quantity = arr.cantidad.toString();
          }

          if (arr.desperdicio >= 0) {
            waste = arr.desperdicio.toString();
          }

          // Validación de campos vacíos o nulos
          if (!arr.referencia_producto || !arr.producto || !arr.referencia_material || !arr.material || !arr.magnitud || !arr.unidad ||
            quantity.trim() == '' || waste.trim() == '' || !arr.tipo) {
            $('.cardLoading').remove();
            $('.cardBottons').show(400);
            $('#fileProductsMaterials').val('');
            importStatus = false;

            toastr.error(`Columna vacía en la fila: ${i + 2}`);
            break;
          }

          // Validación de campos que no están vacíos o nulos pero son solo espacios
          if (!arr.referencia_producto.toString().trim() || !arr.producto.toString().trim() || !arr.referencia_material.toString().trim() || !arr.material.toString().trim() || !arr.magnitud.toString().trim() || !arr.unidad.toString().trim()
            || !arr.tipo.toString().trim()) {
            $('.cardLoading').remove();
            $('.cardBottons').show(400);
            $('#fileProductsMaterials').val('');
            importStatus = false;

            toastr.error(`Columna vacía en la fila: ${i + 2}`);
            break;
          }

          let valQuantity = parseFloat(quantity.replace(',', '.')) * 1;
          // let valWaste = parseFloat(waste) * 1;
          if (isNaN(valQuantity) || valQuantity <= 0) {
            $('.cardLoading').remove();
            $('.cardBottons').show(400);
            $('#fileProductsMaterials').val('');
            importStatus = false;

            toastr.error(`La cantidad debe ser mayor a cero (0). Fila: ${i + 2}`);
            break;
          }

          // Validar Producto
          let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));
          let product = dataProducts.find(item => item.reference == arr.referencia_producto.toString().trim() &&
            item.product == arr.producto.toString().toUpperCase().trim());

          if (!product) {
            $('.cardLoading').remove();
            $('.cardBottons').show(400);
            $('#fileProductsMaterials').val('');
            importStatus = false;

            toastr.error(`Producto no existe en la base de datos. Fila: ${i + 2}`);
            break;
          }

          productMaterialsToImport.push({ idProduct: product.id_product });

          let type = arr.tipo.toUpperCase().trim();

          switch (type) {
            case 'MATERIAL':
              let dataMaterials = JSON.parse(sessionStorage.getItem('dataMaterials'));
              let material = dataMaterials.find(item => item.reference == arr.referencia_material.toString().trim() &&
                item.material == arr.material.toString().toUpperCase().trim());

              if (!material) {
                $('.cardLoading').remove();
                $('.cardBottons').show(400);
                $('#fileProductsMaterials').val('');
                importStatus = false;
                
                toastr.error(`Materia prima no existe en la base de datos. Fila: ${i + 2}`);
                break;
              }
              productMaterialsToImport[i].material = material['id_material'];

              break;
          
            case 'PRODUCTO':
              let product = dataProducts.find(item => item.reference == arr.referencia_material.toString().trim() &&
                item.product == arr.material.toString().toUpperCase().trim());
              
              if (!product) {
                $('.cardLoading').remove();
                $('.cardBottons').show(400);
                $('#fileProductsMaterials').val('');
                importStatus = false;
                
                toastr.error(`Producto no existe en la base de datos. Fila: ${i + 2}`);
                break;
              }

              if (product.composite == 0) {
                $('.cardLoading').remove();
                $('.cardBottons').show(400);
                $('#fileProductsMaterials').val('');
                importStatus = false;
                
                toastr.error(`Producto no esta definido como compuesto. Fila: ${i + 2}`);
                break;
              }

              productMaterialsToImport[i].compositeProduct = product['id_product'];
              break;
          }

          // Transformar el elemento y añadirlo al nuevo array
          productMaterialsToImport[i].referenceProduct = arr.referencia_producto;
          productMaterialsToImport[i].product = arr.producto;
          productMaterialsToImport[i].refRawMaterial = arr.referencia_material;
          productMaterialsToImport[i].nameRawMaterial = arr.material;
          productMaterialsToImport[i].magnitude = arr.magnitud;
          productMaterialsToImport[i].unit = arr.unidad;
          productMaterialsToImport[i].quantity = quantity;
          productMaterialsToImport[i].waste = waste;
          productMaterialsToImport[i].type = arr.tipo;
        }

        // $('.cardLoading').remove();
        // $('.cardBottons').show(400);
        // $('#fileProductsMaterials').val('');
        // console.log('true');
        if (importStatus == true)
          checkProductMaterial(productMaterialsToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileProductsMaterials').val('');

        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  const checkProductMaterial = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/productsMaterialsDataValidation',
      data: { importProductsMaterials: data },
      success: function (resp) {
        if (resp.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProductsMaterials').val('');
          toastr.error(resp.message);
          return false;
        }

        bootbox.confirm({
          title: '¿Desea continuar con la importación?',
          message: `Se han encontrado los siguientes registros:<br><br>Datos a insertar: ${resp.insert} <br>Datos a actualizar: ${resp.update}`,
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
