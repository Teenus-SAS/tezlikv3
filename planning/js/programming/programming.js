$(document).ready(function () {
  /* Ocultar panel crear producto */

  $('.cardCreateProgramming').hide();

  /* Abrir panel crear producto */

  $('#btnNewProgramming').click(function (e) {
    e.preventDefault();

    $('.cardImportProgramming').hide(800);
    $('.cardCreateProgramming').toggle(800);
    $('#btnCreateProgramming').html('Crear');

    sessionStorage.removeItem('id_Programming');

    $('#Programming').val('');
  });

  /* Crear nuevo proceso */

  $('#btnCreateProgramming').click(function (e) {
    e.preventDefault();

    let idProgramming = sessionStorage.getItem('id_Programming');

    if (idProgramming == '' || idProgramming == null) {
      Programming = $('#Programming').val();

      if (Programming == '' || Programming == 0) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      Programming = $('#formCreateProgramming').serialize();

      $.post(
        '../../api/addPlanProgramming',
        Programming,
        function (data, textStatus, jqXHR) {
          message(data);
        }
      );
    } else {
      updateProgramming();
    }
  });

  /* Actualizar procesos */

  $(document).on('click', '.updateProgramming', function (e) {
    $('.cardImportProgramming').hide(800);
    $('.cardCreateProgramming').show(800);
    $('#btnCreateProgramming').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblProgramming.fnGetData(row);

    sessionStorage.setItem('id_Programming', data.id_Programming);
    $('#Programming').val(data.Programming);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateProgramming = () => {
    let data = $('#formCreateProgramming').serialize();
    idProgramming = sessionStorage.getItem('id_Programming');
    data = data + '&idProgramming=' + idProgramming;

    $.post(
      '../../api/updatePlanProgramming',
      data,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  };

  /* Eliminar proceso */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblProgramming.fnGetData(row);

    let id_Programming = data.id_Programming;

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
            `../../api/deletePlanProgramming/${id_Programming}`,
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
      $('.cardCreateProgramming').hide(800);
      $('#formCreateProgramming')[0].reset();
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblProgramming').DataTable().clear();
    $('#tblProgramming').DataTable().ajax.reload();
  }
});
