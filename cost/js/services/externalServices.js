$(document).ready(function () {
  let idProduct;

  /* Ocultar panel Nuevo Servicio */

  $('.cardAddService').hide();

  /* Abrir panel crear carga fabril */

  $('#btnNewService').click(function (e) {
    e.preventDefault();

    $('.cardImportExternalServices').hide(800);
    $('.cardAddService').toggle(800);
    $('#btnAddService').html('Adicionar');

    sessionStorage.removeItem('id_service');

    $('#service').val('');
    $('#costService').val('');
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
      idProduct = parseInt($('#selectNameProduct').val());
      service = $('#service').val();
      cost = parseInt($('#costService').val());

      data = idProduct * cost;

      if (!data || service == '' || service == 0) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      externalServices = $('#formAddService').serialize();

      externalServices = externalServices + '&idProduct=' + idProduct;

      $.post(
        '../../api/addExternalService',
        externalServices,
        function (data, textStatus, jqXHR) {
          message(data);
        }
      );
    } else {
      updateExternalService();
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
    $('#costService').val(data.cost.toLocaleString());

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateExternalService = () => {
    let data = $('#formAddService').serialize();
    idProduct = $('#selectNameProduct').val();
    idService = sessionStorage.getItem('id_service');

    data = data + '&idService=' + idService + '&idProduct=' + idProduct;

    $.post(
      '../../api/updateExternalService',
      data,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  };

  /* Eliminar servicio */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblExternalServices.fnGetData(row);

    let idService = data.id_service;

    dataExternalService = {};
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
      $('#formAddService')[0].reset();
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
