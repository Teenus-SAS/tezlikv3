let dataExternalService = {};
sessionStorage.removeItem('dataGServices');

/* Ocultar panel Nuevo Servicio */
$('.cardAddService').hide();

/* Abrir panel crear servicio  */
$('#btnNewService').click(async function (e) {
  e.preventDefault();

  $('.cardImportExternalServices').hide(800);
  $('#btnAddService').html('Adicionar');

  let display = $('.cardAddService').css('display');
  let dataGServices = JSON.parse(sessionStorage.getItem('dataGServices'));

  if (display == 'none' && !dataGServices) {
    await loadAllDataGServices(2);
  };

  $('.cardAddService').toggle(800);
  sessionStorage.removeItem('id_service');
  $('.inputs').css('border-color', '');
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
    checkDataServices('/api/dataSheetServices/addExternalService', idService);
  } else {
    checkDataServices('/api/dataSheetServices/updateExternalService', idService);
  }
});

/* Cargar información de servicios generales */
$('#generalServices').change(function (e) {
  e.preventDefault();

  let dataServices = JSON.parse(sessionStorage.getItem('dataGServices'));

  let data = dataServices.find(item => item.id_general_service == this.value);
  $('#service').val(data.name_service);
  $('#costService').val(data.cost);
});

/* Actualizar servicio */

$(document).on('click', '.updateExternalService', async function (e) {
  $('.cardImportExternalServices').hide(800);
  $('.inputs').css('border-color', '');

  let dataGServices = JSON.parse(sessionStorage.getItem('dataGServices'));

  if (!dataGServices) {
    await loadAllDataGServices(2);
  };

  $('.cardAddService').show(800);
  $('#btnAddService').html('Actualizar');

  let row = $(this).parent().parent()[0];
  let data = tblExternalServices.fnGetData(row);

  sessionStorage.setItem('id_service', data.id_service);

  $('#service').val(data.name_service);
  $('#costService').val(data.cost);
  $(`#generalServices option[value=${data.id_general_service}]`).prop('selected', true);

  $('html, body').animate(
    {
      scrollTop: 0,
    },
    1000
  );
});

function validateFormExternals() {
  let emptyInputs = [];

  let service = $('#service').val();
  let costService = parseFloat($('#costService').val());

  // Verificar cada campo y agregar los vacíos a la lista
  if (!service) {
    emptyInputs.push('#service');
  }
  if (!costService) {
    emptyInputs.push('#costService');
  }

  // Marcar los campos vacíos con borde rojo
  emptyInputs.forEach(function (selector) {
    $(selector).css('border-color', 'red');
  });

  // Mostrar mensaje de error si hay campos vacíos
  if (emptyInputs.length > 0) {
    toastr.error('Ingrese todos los campos');
    return false;
  }

  return true;
};

/* Revision data servicio */
checkDataServices = async (url, idService) => {

  if (!validateFormExternals()) {
    return false;
  }

  let idProduct = parseInt($('#selectNameProduct').val());
  // let service = $('#service').val();
  // let cost = parseFloat($('#costService').val());
  let generalServices = parseFloat($('#generalServices').val());
  isNaN(generalServices) ? generalServices = 0 : generalServices;

  // cost = parseFloat(strReplaceNumber(cost));

  // let data = idProduct * cost;

  // if (service.trim() == '' || !service.trim() || isNaN(data) || data <= 0) {
  //   toastr.error('Ingrese todos los campos');
  //   return false;
  // }

  let dataExternalService = new FormData(formAddService);
  dataExternalService.append('idProduct', idProduct);
  dataExternalService.append('idGService', generalServices);

  if (idService != '' || idService != null)
    dataExternalService.append('idService', idService);

  let resp = await sendDataPOST(url, dataExternalService);

  messageServices(resp);
};

/* Eliminar servicio */

deleteService = () => {
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
          '/api/dataSheetServices/deleteExternalService',
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
  if (data.reload) {
    location.reload();
  }

  $('.cardLoading').remove();
  $('.cardBottons').show(400);
  $('#fileExternalServices').val('');

  if (data.success == true) {
    $('.cardImportExternalServices').hide(800);
    $('#formImportExternalServices').trigger('reset');
    $('.cardAddService').hide(800);
    $('.cardProducts').show(800);
    $('#formAddService').trigger('reset');
    let idProduct = parseInt($('#selectNameProduct').val());
    if (idProduct)
      loadAllDataServices(idProduct);

    loadAllDataGServices(2);
    toastr.success(data.message);
    //return false
  } else if (data.error == true) toastr.error(data.message);
  else if (data.info == true) toastr.info(data.message);
};

