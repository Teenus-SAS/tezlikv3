$(document).ready(function () { 
  let dataExternalService = {};

  /* Ocultar panel Nuevo Servicio */
  $('.cardAddService').hide();

  /* Abrir panel crear servicio  */
  $('#btnNewService').click(function (e) {
    e.preventDefault();

    $('.cardImportExternalServices').hide(800);
    $('.cardAddService').toggle(800);
    $('#btnAddService').html('Adicionar');

    sessionStorage.removeItem('id_general_service');

    $('#formAddService').trigger('reset');
  });

  /* Adicionar nueva carga fabril */
  $('#btnAddService').click(function (e) {
    e.preventDefault();

    let idService = sessionStorage.getItem('id_general_service');

    if (idService == '' || idService == null) {
      checkDataServices('/api/addGExternalService', idService);
    } else {
      checkDataServices('/api/updateGExternalService', idService);
    }
  }); 

  /* Actualizar servicio */

  $(document).on('click', '.updateExternalService', function (e) {
    $('.cardImportExternalServices').hide(800);
    $('.cardAddService').show(800);
    $('#btnAddService').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblExternalServices.fnGetData(row);

    sessionStorage.setItem('id_general_service', data.id_general_service);

    $('#service').val(data.name_service);

    // let decimals = contarDecimales(data.cost);
    // let cost = formatNumber(data.cost, decimals);
    $('#costService').val(data.cost);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision data servicio */
  checkDataServices = async (url, idService) => {
    // let idProduct = parseInt($('#selectNameProduct').val());
    let service = $('#service').val();
    let cost = parseFloat($('#costService').val());

    // cost = parseFloat(strReplaceNumber(cost));

    if (service.trim() == '' || !service.trim() || isNaN(cost) || cost <= 0) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    let dataExternalService = new FormData(formAddService);
    // dataExternalService.append('idProduct', idProduct);

    if (idService != '' || idService != null)
      dataExternalService.append('idService', idService);

    let resp = await sendDataPOST(url, dataExternalService);

    messageServices(resp);
  };

  /* Eliminar servicio */

  deleteService = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblExternalServices.fnGetData(row);

    let idService = data.id_general_service;

    dataExternalService['idService'] = idService;
    // dataExternalService['idProduct'] = $('#selectNameProduct').val();

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
            '../../api/deleteGExternalService',
            dataExternalService,
            function (data, textStatus, jqXHR) {
              messageServices(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  messageServices = (data) => {
    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    $('#fileExternalServices').val('');
    
    if (data.success == true) {
      $('.cardImportExternalServices').hide(800);
      $('#formImportExternalServices').trigger('reset');
      $('.cardAddService').hide(800);
      // $('.cardProducts').show(800);
      $('#formAddService').trigger('reset');
      let idProduct = parseInt($('#selectNameProduct').val());
      // if (idProduct)
      loadAllDataServices(idProduct);
      toastr.success(data.message);
      //return false
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
