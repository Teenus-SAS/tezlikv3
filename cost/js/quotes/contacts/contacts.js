$(document).ready(function () {
  /* Ocultar panel para crear contactos */

  $('.cardCreateContact').hide();

  /* Abrir panel para crear contactos */

  $('#btnNewContact').click(function (e) {
    e.preventDefault();
    // $('.cardImportMaterials').hide(800);
    $('.cardCreateContact').toggle(800);
    $('#btnCreateContact').html('Crear');

    sessionStorage.removeItem('id_contact');

    $('#formCreateContact').trigger('reset');
  });

  /* Crear producto */

  $('#btnCreateContact').click(function (e) {
    e.preventDefault();
    let idContact = sessionStorage.getItem('id_contact');

    if (idContact == '' || idContact == null) {
      firstname = $('#firstname').val();
      lastname = $('#lastname').val();
      phone = $('#phone').val();
      email = $('#email').val();
      position = $('#position').val();
      company = $('#company').val();

      if (
        firstname == '' ||
        lastname == '' ||
        phone == '' ||
        email == '' ||
        position == '' ||
        company == 0 ||
        !company
      ) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      contact = $('#formCreateContact').serialize();

      $.post(
        '../../api/addContact',
        contact,
        function (data, textStatus, jqXHR) {
          message(data);
        }
      );
    } else {
      updateContact();
    }
  });

  /* Actualizar contacto */

  $(document).on('click', '.updateRawMaterials', function (e) {
    // $('.cardImportMaterials').hide(800);
    $('.cardCreateContact').show(800);
    $('#btnCreateContact').html('Actualizar');

    idContact = this.id;
    idContact = sessionStorage.setItem('id_contact', idContact);

    let row = $(this).parent().parent()[0];
    let data = tblContacts.fnGetData(row);

    $('#firstname').val(data.firstname);
    $('#lastname').val(data.lastname);
    $('#phone').val(data.phone);
    $('#phone1').val(data.phone1);
    $('#email').val(data.email);
    $('#position').val(data.position);
    $(`#company option[value=${data.id_quote_company}]`).prop('selected', true);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateContact = () => {
    let data = $('#formCreateContact').serialize();
    idContact = sessionStorage.getItem('id_contact');
    data = data + '&idContact=' + idContact;

    $.post('../../api/updateContact', data, function (data, textStatus, jqXHR) {
      message(data);
    });
  };

  /* Eliminar contacto */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblContacts.fnGetData(row);

    let idContact = data.id_contact;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este contacto? Esta acción no se puede reversar.',
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
            `/api/deleteContact/${idContact}`,
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
      $('.cardCreateContact').hide(800);
      $('#formCreateContact').trigger('reset');
      toastr.success(data.message);
      updateTable();
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblContacts').DataTable().clear();
    $('#tblContacts').DataTable().ajax.reload();
  }
});
