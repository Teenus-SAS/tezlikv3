$(document).ready(function () {
  let idProduct;
  let dataProductMaterial = {};

  /* Ocultar panel crear producto */

  $('.cardAddMaterials').hide();

  /* Abrir panel crear producto */

  $('#btnCreateProduct').click(function (e) {
    e.preventDefault();

    $('.cardImportProductsMaterials').hide(800);
    $('.cardAddMaterials').toggle(800);
    $('#btnAddMaterials').html('Asignar');
    $('#units').empty();

    sessionStorage.removeItem('id_product_material');

    $('#formAddMaterials').trigger('reset');
  });

  /* Adicionar unidad de materia prima */

  $('#material').change(async function (e) {
    e.preventDefault();
    let id = this.value;

    let data = sessionStorage.getItem('dataMaterials');
    if (data) {
      dataMaterials = JSON.parse(data);
      sessionStorage.removeItem('dataMaterials');
    }

    for (i = 0; i < dataMaterials.length; i++) {
      if (id == dataMaterials[i].id_material) {
        await loadUnitsByMagnitude(dataMaterials[i], 2);
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
    $('#btnAddMaterials').html('Actualizar');
    $('#units').empty();

    let row = $(this).parent().parent()[0];
    let data = tblConfigMaterials.fnGetData(row);

    sessionStorage.setItem('id_product_material', data.id_product_material);
    $(`#material option[value=${data.id_material}]`).prop('selected', true);

    $(`#magnitudes option[value=${data.id_magnitude}]`).prop('selected', true);
    await loadUnitsByMagnitude(data, 2);
    $(`#units option[value=${data.id_unit}]`).prop('selected', true);

    let quantity = `${data.quantity}`;

    $('#quantity').val(quantity.replace('.', ','));

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision data Productos materiales */
  checkDataProductsMaterials = async (url, idProductMaterial) => {
    let ref = parseInt($('#material').val());
    let unit = parseInt($('#units').val());
    let quan = $('#quantity').val();
    idProduct = parseInt($('#selectNameProduct').val());

    let data = ref * unit * idProduct;

    if (!data || quan == '') {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    quan = parseFloat(strReplaceNumber(quan));

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

    message(resp);
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
