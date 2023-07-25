$(document).ready(function () {
  let dataProducts = [];

  /* Inactivar productos */
  $(document).on('click', '.checkboxProduct', function () {
    let idProduct = this.id;

    bootbox.confirm({
      title: 'Inactivar producto',
      message: '¿Esta seguro de inactivar este producto?',
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
          $.ajax({
            url: `/api/inactiveProducts/${idProduct}`,
            success: function (data) {
              message(data);
              $('#tblProducts').DataTable().clear();
              $('#tblProducts').DataTable().ajax.reload();
            },
          });
        } else {
          $('.checkboxProduct').prop('checked', true);
        }
      },
    });
  });

  /* Ocultar modal productos inactivos */
  $('#btnCloseInactivesProducts').click(function (e) {
    e.preventDefault();
    $('#createInactivesProducts').modal('hide');
    $('#tblInactiveProductsBody').empty();
  });

  /* Mostrar productos inactivos */
  $('#btnActiveProducts').click(function (e) {
    e.preventDefault();
    $('#tblInactiveProductsBody').empty();

    setTblInactivesProducts();
  });

  setTblInactivesProducts = async () => {
    let data = await searchData('/api/inactivesProducts');

    let tblInactiveProductsBody = document.getElementById(
      'tblInactiveProductsBody'
    );

    for (i = 0; i < data.length; i++) {
      tblInactiveProductsBody.insertAdjacentHTML(
        'beforeend',
        `
        <tr>
            <td>${i + 1}</td>
            <td>${data[i].reference}</td>
            <td>${data[i].product}</td>
            <td>
                <input type="checkbox" class="form-control-updated checkInactiveProduct" id="checkIn-${
                  data[i].id_product
                }">
            </td>
        </tr>
      `
      );
    }

    $('#createInactivesProducts').modal('show');

    $('#tblInactiveProducts').DataTable({
      destroy: true,
      scrollY: '150px',
      scrollCollapse: true,
      // language: {
      //   url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      // },
    });

    let tables = document.getElementsByClassName(
        'dataTables_scrollHeadInner'
      );

    let attr = tables[0];
    attr.style.width = '100%';
    attr = tables[0].firstElementChild;
    attr.style.width = '100%';

  };

  /* Guardar productos a activar */
  $(document).on('click', '.checkInactiveProduct', function () {
    let id = this.id;
    let idProduct = id.slice(8, id.length);

    if ($(`#${id}`).is(':checked')) {
      let planeacion = {
        idProduct: idProduct,
      };

      dataProducts.push(planeacion);
    } else {
      for (i = 0; i < dataProducts.length; i++)
        if (dataProducts[i].idProduct == idProduct) dataProducts.splice(i, 1);
    }
  });

  /* Activar productos  */
  $('#btnActivesProducts').click(function (e) {
    e.preventDefault();
    if (dataProducts.length == 0) {
      toastr.error('Seleccione un producto para activar');
      return false;
    }

    $.ajax({
      type: 'POST',
      url: '/api/activeProducts',
      data: { data: dataProducts },
      success: function (data) {
        $('#createInactivesProducts').modal('hide');
        dataProducts = [];
        message(data);
        $('#tblProducts').DataTable().clear();
        $('#tblProducts').DataTable().ajax.reload();
      },
    });
  });
});
