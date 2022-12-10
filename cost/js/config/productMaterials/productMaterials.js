$(document).ready(function () {
  let idProduct;
  let dataProductMaterial = [];

  /* Ocultar panel crear producto */

  $('.cardAddMaterials').hide();

  /* Abrir panel crear producto */

  $('#btnCreateProduct').click(function (e) {
    e.preventDefault();

    $('.cardImportProductsMaterials').hide(800);
    $('.cardAddMaterials').toggle(800);
    $('#btnAddMaterials').html('Asignar');

    sessionStorage.removeItem('id_product_material');

    $('#formAddMaterials').trigger('reset');
  });

  /* Seleccionar producto */

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    idProduct = $('#selectNameProduct').val();
  });

  /* Adicionar unidad de materia prima */

  $('#material').change(function (e) {
    e.preventDefault();
    let id = this.value;

    let data = sessionStorage.getItem('dataMaterials');
    if (data) {
      let dataMaterials = JSON.parse(dataMaterials);
      sessionStorage.removeItem('dataMaterials');
    }

    for (i = 0; i < dataMaterials.length; i++) {
      if (id == dataMaterials[i]['id_material']) {
        $('#unity').val(dataMaterials[i].unit);
        break;
      }
    }
  });

  /* Adicionar nueva materia prima */

  $('#btnAddMaterials').click(function (e) {
    e.preventDefault();

    let idProductMaterial = sessionStorage.getItem('id_product_material');

    if (idProductMaterial == '' || idProductMaterial == null) {
      let ref = $('#material').val();
      let quan = $('#quantity').val();
      idProduct = $('#selectNameProduct').val();

      let data = ref * quan * idProduct;

      if (!data) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      let productMaterial = $('#formAddMaterials').serialize();
      productMaterial = productMaterial + '&idProduct=' + idProduct;

      $.post(
        '/api/addProductsMaterials',
        productMaterial,
        function (data, textStatus, jqXHR) {
          message(data);
        }
      );
    } else {
      updateMaterial();
    }
  });

  /* Actualizar productos materials */

  $(document).on('click', '.updateMaterials', function (e) {
    $('.cardImportProductsMaterials').hide(800);
    $('.cardAddMaterials').show(800);
    $('#btnAddMaterials').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblConfigMaterials.fnGetData(row);

    sessionStorage.setItem('id_product_material', data.id_product_material);
    $(`#material option[value=${data.id_material}]`).prop('selected', true);

    let quantity = data.quantity;

    if (quantity.isInteger) quantity = quantity.toLocaleString();
    else
      quantity = quantity.toLocaleString(undefined, {
        minimumFractionDigits: 4,
        maximumFractionDigits: 4,
      });
    $('#quantity').val(quantity);

    $('#unity').val(data.unit);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateMaterial = () => {
    let data = $('#formAddMaterials').serialize();
    idProduct = $('#selectNameProduct').val();
    let idProductMaterial = sessionStorage.getItem('id_product_material');
    data =
      data +
      '&idProductMaterial=' +
      idProductMaterial +
      '&idProduct=' +
      idProduct;

    $.post(
      '/api/updateProductsMaterials',
      data,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  };

  /* Eliminar materia prima */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblConfigMaterials.fnGetData(row);

    let idProductMaterial = data.id_product_material;

    let idProduct = $('#selectNameProduct').val();
    dataProductMaterial['idProductMaterial'] = idProductMaterial;
    dataProductMaterial['idProduct'] = idProduct;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar esta Materia prima? Esta acción no se puede reversar.',
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
            '/api/deleteProductMaterial',
            dataProductMaterial,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('.cardAddMaterials').hide(800);
      $('#formAddMaterials').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblConfigMaterials').DataTable().clear();
    $('#tblConfigMaterials').DataTable().ajax.reload();
  }
});
