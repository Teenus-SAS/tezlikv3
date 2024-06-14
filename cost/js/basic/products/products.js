$(document).ready(function () {
  /* Ocultar panel crear producto */
  $('.cardCreateProduct').hide();
  /* Abrir panel crear producto */

  $('#btnNewProduct').click(function (e) {
    e.preventDefault();

    $('.cardCreateProduct').toggle(800);
    $('.cardImportProducts').hide(800);
    $('#btnCreateProduct').html('Crear Producto');

    sessionStorage.removeItem('id_product');

    $('#formCreateProduct').trigger('reset');
  });

  /* Crear producto */

  $('#btnCreateProduct').click(function (e) {
    e.preventDefault();
    let idProduct = sessionStorage.getItem('id_product');

    if (idProduct == '' || idProduct == null) {
      checkDataProducts('/api/addProducts', idProduct);
    } else {
      checkDataProducts('/api/updateProducts', idProduct);
    }
  });

  /* Actualizar productos */

  $(document).on('click', '.updateProducts', function (e) {
    $('.cardImportProducts').hide(800);
    $('.cardCreateProduct').show(800);
    $('#btnCreateProduct').html('Actualizar Producto');

    let idProduct = this.id;
    sessionStorage.setItem('id_product', idProduct);
    
    let row = $(this).parent().parent()[0];
    let data = tblProducts.fnGetData(row);

    $('#referenceProduct').val(data.reference);
    $('#product').val(data.product);
    $('#profitability').val(data.profitability);
    $('#commisionSale').val(data.commission_sale);
    $('#salePrice').val(data.sale_price);

    $('html, body').animate({ scrollTop: 0 }, 1000);
  });

  /* Revisar datos */
  const checkDataProducts = async (url, idProduct) => {
    let ref = $('#referenceProduct').val();
    let prod = $('#product').val();
    let prof = parseFloat($('#profitability').val());
    let comission = parseFloat($('#commisionSale').val()); 

    if (ref.trim() == '' || !ref.trim() || prod.trim() == '' || !prod.trim()) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    if (prof > 100 || comission > 100) {
      toastr.error('La rentabilidad y comision debe ser menor al 100%');
      return false;
    }

    let imageProd = $('#formFile')[0].files[0];

    let dataProduct = new FormData(formCreateProduct);
    dataProduct.append('img', imageProd);

    if (idProduct != '' || idProduct != null) {
      dataProduct.append('idProduct', idProduct);
    }

    let resp = await sendDataPOST(url, dataProduct);

    message(resp);
  };

  /* Eliminar productos */
  $(document).on('click', '.deleteProduct', function () {
    let dataProduct = {};
    dataProduct['idProduct'] = this.id;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este producto? Esta acción no se puede reversar.',
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
          $.post(
            '/api/deleteProduct',
            dataProduct,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  });

  /* Copiar Producto */
  copyFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblProducts.fnGetData(row);

    bootbox.confirm({
      title: 'Clonar producto',
      message: `<div class="row">
                  <div class="col-12">
                    <label for="referenceNewProduct">Referencia</label>
                    <input type="text" class="form-control mb-2" name="referenceNewProduct" id="referenceNewProduct">
                  </div>
                  <div class="col-12">
                    <label for="newProduct">Nombre Producto</label>
                    <input type="text" class="form-control" name="newProduct" id="newProduct">
                  </div>
                  <div class="col-4">
                    <label for="newSalePrice">Precio de Venta</label>
                    <input type="text" class="form-control text-center number" name="newSalePrice" id="newSalePrice">
                  </div>
                </div>`,
      buttons: {
        confirm: {
          label: 'Ok',
          className: 'btn-success',
        },
        cancel: {
          label: 'Cancel',
          className: 'btn-danger',
        },
      },
      callback: function (result) {
        if (result == true) {
          let ref = $('#referenceNewProduct').val();
          let prod = $('#newProduct').val();
          let sale_price = $('#newSalePrice').val();

          if (!ref.trim() || ref.trim() == '' || !prod.trim() || prod.trim() == '') {
            toastr.error('Ingrese todos los campos');
            return false;
          }

          let dataProduct = {};
          dataProduct['idOldProduct'] = data.id_product;
          dataProduct['referenceProduct'] = ref;
          dataProduct['product'] = prod;
          dataProduct['profitability'] = data.profitability;
          dataProduct['commissionSale'] = data.commission_sale;
          dataProduct['salePrice'] = sale_price;
          dataProduct['idFamily'] = data.id_family;

          $.post(
            '/api/copyProduct',
            dataProduct,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  $(document).on('click', '.composite', function () {
    let row = $(this).parent().parent()[0];
    let data = tblProducts.fnGetData(row);

    bootbox.confirm({
      //title: data.composite == '0' ? 'Agregar' : 'Eliminar',
      title: 'Producto Compuesto',
      message:
        `Está seguro de que este producto ${data.composite == '0' ? 'se <b>convierta en un subproducto</b> para ser agregado a un producto compuesto' : 'se <b>Elimine</b> como subproducto'}?`,
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
          $.get(
            `/api/changeComposite/${data.id_product}/${data.composite == '0' ? '1' : '0'}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  });

  /* Mensaje de exito */
  message = (data) => {
    $('#fileProducts').val('');
    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    
    if (data.success == true) {
      $('.cardImportProducts').hide(800);
      $('#formImportProduct').trigger('reset');
      $('#createInactivesProducts').modal('hide');
      $('.cardCreateProduct').hide(800);
      $('#formCreateProduct').trigger('reset');
      toastr.success(data.message);
      loadAllData(); 
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
