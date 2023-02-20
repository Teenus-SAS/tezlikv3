$(document).ready(function () {
  let idProduct;
  let dataExternalService = {};

  /* Ocultar panel Nuevo Servicio */
  $('.cardAddService').hide();

  /* Abrir panel crear servicio  */
  $('#btnNewService').click(function (e) {
    e.preventDefault();

    $('.cardImportExternalServices').hide(800);
    $('.cardAddService').toggle(800);
    $('#btnAddService').html('Adicionar');

    sessionStorage.removeItem('id_service');

    $('#formAddService').trigger('reset');
  });

  /* Seleccionar producto */
  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    idProduct = $('#selectNameProduct').val();
  });

  /* Adicionar nueva carga fabril */
  $('#btnAddService').click(function (e) {
    e.preventDefault();

    let idService = sessionStorage.getItem('id_service');

    if (idService == '' || idService == null) {
      checkDataServices('/api/addExternalService', idService);
    } else {
      checkDataServices('/api/updateExternalService', idService);
    }
  });

  /* Actualizar servicio */

  $(document).on('click', '.updateExternalService', function (e) {
    $('.cardImportExternalServices').hide(800);
    $('.cardAddService').show(800);
    $('#btnAddService').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblExternalServices.fnGetData(row);

    sessionStorage.setItem('id_service', data.id_service);

    $('#service').val(data.name_service);
    $('#costService').val(data.cost.toLocaleString('es-CO'));

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision data servicio */
  checkDataServices = async (url, idService) => {
    let idProduct = parseInt($('#selectNameProduct').val());
    let service = $('#service').val();
    let cost = $('#costService').val();

    cost = parseFloat(strReplaceNumber(cost));

    let data = idProduct * cost;

    if (service == '' || service == 0 || isNaN(data) || data <= 0) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    let dataExternalService = new FormData(formAddService);
    dataExternalService.append('idProduct', idProduct);

    if (idService != '' || idService != null)
      dataExternalService.append('idService', idService);

    let resp = await sendDataPOST(url, dataExternalService);

    message(resp);
  };

  /* Eliminar servicio */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblExternalServices.fnGetData(row);

    let idService = data.id_service;

    dataExternalService['idService'] = idService;
    dataExternalService['idProduct'] = $('#selectNameProduct').val();

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este sevicio? Esta acción no se puede reversar.',
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
            '../../api/deleteExternalService',
            dataExternalService,
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
      $('.cardAddService').hide(800);
      $('#formAddService').trigger('reset');
      updateTable();
      toastr.success(data.message);
      //return false
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblExternalServices').DataTable().clear();
    $('#tblExternalServices').DataTable().ajax.reload();
  }
});
