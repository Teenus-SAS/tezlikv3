$(document).ready(function () {
  /* Ocultar panel crear producto */
  $('.cardCreateProcess').hide();

  /* Abrir panel crear producto */
  $('#btnNewProcess').click(function (e) {
    e.preventDefault();

    $('.cardImportProcess').hide(800);
    $('.cardCreateProcess').toggle(800);
    $('#btnCreateProcess').html('Crear');

    sessionStorage.removeItem('id_process');

    $('#process').val('');
  });

  /* Crear nuevo proceso */

  $('#btnCreateProcess').click(function (e) {
    e.preventDefault();

    let idProcess = sessionStorage.getItem('id_process');

    if (idProcess == '' || idProcess == null) {
      checkDataProcess('/api/addProcess', idProcess);
    } else {
      checkDataProcess('/api/updateProcess', idProcess);
    }
  });

  /* Actualizar procesos */

  $(document).on('click', '.updateProcess', function (e) {
    $('.cardImportProcess').hide(800);
    $('.cardCreateProcess').show(800);
    $('#btnCreateProcess').html('Actualizar');

    let data = dataProcess.find(item => item.id_process == this.id);

    sessionStorage.setItem('id_process', data.id_process);
    $('#process').val(data.process);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision data procesos */
  const checkDataProcess = async (url, idProcess) => {
    let process = $('#process').val();

    if (process.trim() == '' || !process.trim()) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    let dataProcess1 = new FormData(formCreateProcess);

    if (idProcess != '' || idProcess != null)
      dataProcess1.append('idProcess', idProcess);

    let resp = await sendDataPOST(url, dataProcess1);

    message(resp);
  };

  /* Eliminar proceso */

  deleteFunction = (id) => { 
    let data = dataProcess.find(item => item.id_process == id);
    let count_payroll = parseInt(data.count_payroll);

    if (count_payroll != 0) {
      toastr.error('Este proceso no se puede eliminar, esta configurado a un producto o nomina');
      return false;
    }

    let id_process = data.id_process;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este proceso? Esta acción no se puede reversar.',
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
            `../../api/deleteProcess/${id_process}`,
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
    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    $('#fileProcess').val('');
    
    if (data.success == true) {
      $('.cardImportProcess').hide(800);
      $('#formImportProcess').trigger('reset');
      $('.cardCreateProcess').hide(800);
      $('#formCreateProcess').trigger('reset');
      
      loadTblProcess();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  }; 
});
