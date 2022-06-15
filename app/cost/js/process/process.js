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
      process = $('#process').val();

      if (process == '' || process == 0) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      process = $('#formCreateProcess').serialize();

      $.post(
        '../../api/addProcess',
        process,
        function (data, textStatus, jqXHR) {
          message(data);
        }
      );
    } else {
      updateProcess();
    }
  });

  /* Actualizar procesos */

  $(document).on('click', '.updateProcess', function (e) {
    $('.cardImportProcess').hide(800);
    $('.cardCreateProcess').show(800);
    $('#btnCreateProcess').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblProcess.fnGetData(row);

    sessionStorage.setItem('id_process', data.id_process);
    $('#process').val(data.process);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateProcess = () => {
    let data = $('#formCreateProcess').serialize();
    idProcess = sessionStorage.getItem('id_process');
    data = data + '&idProcess=' + idProcess;

    $.post('../../api/updateProcess', data, function (data, textStatus, jqXHR) {
      message(data);
    });
  };

  /* Eliminar proceso */

  $(document).on('click', '.deleteProcess', function (e) {
    let id_process = this.id;

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
  });

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('.cardCreateProcess').hide(800);
      $('#formCreateProcess')[0].reset();
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblProcess').DataTable().clear();
    $('#tblProcess').DataTable().ajax.reload();
  }
});
