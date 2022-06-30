$(document).ready(function () {
  /* Ocultar panel crear molde */

  $('.cardCreateInvMold').hide();

  /* Abrir panel crear molde */

  $('#btnNewInvMold').click(function (e) {
    e.preventDefault();

    $('.cardImportInvMold').hide(800);
    $('.cardCreateInvMold').toggle(800);
    $('#btnCreateInvMold').html('Crear');

    sessionStorage.removeItem('id_mold');

    $('#formCreateInvMold').trigger('reset');
  });

  /* Crear nuevo molde */

  $('#btnCreateInvMold').click(function (e) {
    e.preventDefault();

    let idMold = sessionStorage.getItem('id_mold');

    if (idMold == '' || idMold == null) {
      mold = $('#mold').val();
      assemblyTime = $('#assemblyTime').val();

      if (mold == '' || mold == 0 || assemblyTime == '' || assemblyTime == 0) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      invMold = $('#formCreateInvMold').serialize();

      $.post('../../api/addMold', invMold, function (data, textStatus, jqXHR) {
        message(data);
      });
    } else {
      updateMold();
    }
  });

  /* Actualizar moldes */

  $(document).on('click', '.updateMold', function (e) {
    $('.cardImportInvMold').hide(800);
    $('.cardCreateInvMold').show(800);
    $('#btnCreateInvMold').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblInvMold.fnGetData(row);

    sessionStorage.setItem('id_mold', data.id_mold);
    $('#mold').val(data.mold);
    $('#assemblyTime').val(data.assembly_time);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateMold = () => {
    let data = $('#formCreateInvMold').serialize();
    idMold = sessionStorage.getItem('id_mold');
    data = data + '&idMold=' + idMold;

    $.post('../../api/updateMold', data, function (data, textStatus, jqXHR) {
      message(data);
    });
  };

  /* Eliminar molde */

  $(document).on('click', '.deleteMold', function (e) {
    let id_mold = this.id;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este molde? Esta acción no se puede reversar.',
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
            `../../api/deleteMold/${id_mold}`,
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
      $('.cardCreateInvMold').hide(800);
      $('#formCreateInvMold')[0].reset();
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblInvMold').DataTable().clear();
    $('#tblInvMold').DataTable().ajax.reload();
  }
});
