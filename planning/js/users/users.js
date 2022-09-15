$(document).ready(function () {
  /* Ocultar panel Nuevo usuario 

  $('.cardCreateUsers').hide();
  $('.cardCreateAccessUser').hide(); */

  // Ocultar Modal Nuevo usuario
  $('#btnCloseUser').click(function (e) {
    e.preventDefault();
    $('#createUserAccess').modal('hide');
  });

  /* Abrir panel Nuevo usuario */

  $('#btnNewUser').click(function (e) {
    e.preventDefault();
    // $('.cardCreateUsers').toggle(800);
    // $('.cardCreateAccessUser').toggle(800);
    $('#createUserAccess').modal('show');
    $('#btnCreateUserAndAccess').html('Crear Usuario y Accesos');

    sessionStorage.removeItem('id_user');

    $('#nameUser').prop('disabled', false);
    $('#lastnameUser').prop('disabled', false);
    $('#emailUser').prop('disabled', false);

    $('#formCreateUser').trigger('reset');
    // $('#formCreateAccessUser').trigger('reset');
  });

  /* Agregar nuevo usuario */

  $('#btnCreateUserAndAccess').click(function (e) {
    e.preventDefault();
    let idUser = sessionStorage.getItem('id_user');

    if (idUser == '' || idUser == null) {
      nameUser = $('#nameUser').val();
      lastnameUser = $('#lastnameUser').val();
      emailUser = $('#emailUser').val();

      if (
        nameUser == '' ||
        nameUser == null ||
        lastnameUser == '' ||
        lastnameUser == null ||
        emailUser == '' ||
        emailUser == null
      ) {
        toastr.error('Ingrese nombre, apellido y/o email');
        return false;
      }

      /* Validar que al menos un acceso sea otorgado */
      if ($('input[type=checkbox]:checked').length === 0) {
        toastr.error('Debe seleccionar al menos un acceso');
      }

      /* Obtener los checkbox seleccionados */

      dataUser = {};
      dataUser['nameUser'] = nameUser;
      dataUser['lastnameUser'] = lastnameUser;
      dataUser['emailUser'] = emailUser;

      dataUser = setCheckBoxes(dataUser);

      $.post('/api/addUser', dataUser, function (data, textStatus, jqXHR) {
        message(data);
      });
    } else {
      updateUserAccess();
    }
  });

  /* Actualizar User */

  $(document).on('click', '.updateUser', function (e) {
    $('#createUserAccess').modal('show');
    $('#btnCreateUserAndAccess').html('Actualizar Accesos');

    $('#nameUser').prop('disabled', true);
    $('#lastnameUser').prop('disabled', true);
    $('#emailUser').prop('disabled', true);

    let row = $(this).parent().parent()[0];
    let data = tblUsers.fnGetData(row);

    let idUser = this.id;
    sessionStorage.setItem('id_user', idUser);

    $('#nameUser').val(data.firstname);
    $('#lastnameUser').val(data.lastname);
    $('#emailUser').val(data.email);

    if (data.create_mold == 1) $('#checkbox-1').prop('checked', true);
    else $('#checkbox-1').prop('checked', false);
    if (data.create_product == 1) $('#checkbox-2').prop('checked', true);
    else $('#checkbox-2').prop('checked', false);
    if (data.create_material == 1) $('#checkbox-3').prop('checked', true);
    else $('#checkbox-3').prop('checked', false);
    if (data.create_machine == 1) $('#checkbox-4').prop('checked', true);
    else $('#checkbox-4').prop('checked', false);
    if (data.create_process == 1) $('#checkbox-5').prop('checked', true);
    else $('#checkbox-5').prop('checked', false);
    if (data.products_material == 1) $('#checkbox-6').prop('checked', true);
    else $('#checkbox-6').prop('checked', false);
    if (data.products_process == 1) $('#checkbox-7').prop('checked', true);
    else $('#checkbox-7').prop('checked', false);
    if (data.programs_machine == 1) $('#checkbox-8').prop('checked', true);
    else $('#checkbox-8').prop('checked', false);
    if (data.cicles_machine == 1) $('#checkbox-9').prop('checked', true);
    else $('#checkbox-9').prop('checked', false);
    if (data.inv_category == 1) $('#checkbox-10').prop('checked', true);
    else $('#checkbox-10').prop('checked', false);
    if (data.sale == 1) $('#checkbox-11').prop('checked', true);
    else $('#checkbox-11').prop('checked', false);
    if (data.user == 1) $('#checkbox-12').prop('checked', true);
    else $('#checkbox-12').prop('checked', false);
    if (data.inventory == 1) $('#checkbox-13').prop('checked', true);
    else $('#checkbox-13').prop('checked', false);
    if (data.plan_order == 1) $('#checkbox-14').prop('checked', true);
    else $('#checkbox-14').prop('checked', false);
    if (data.programming == 1) $('#checkbox-15').prop('checked', true);
    else $('#checkbox-15').prop('checked', false);
    if (data.plan_load == 1) $('#checkbox-16').prop('checked', true);
    else $('#checkbox-16').prop('checked', false);
    if (data.explosion_of_material == 1)
      $('#checkbox-17').prop('checked', true);
    else $('#checkbox-17').prop('checked', false);
    if (data.office == 1) $('#checkbox-18').prop('checked', true);
    else $('#checkbox-18').prop('checked', false);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateUserAccess = () => {
    idUser = sessionStorage.getItem('id_user');

    dataUser = {};
    dataUser['idUser'] = idUser;
    dataUser['nameUser'] = $('#nameUser').val();
    dataUser['lastnameUser'] = $('#lastnameUser').val();
    dataUser['emailUser'] = $('#emailUser').val();

    dataUser = setCheckBoxes(dataUser);

    $.post(
      '/api/updatePlanningUserAccess',
      dataUser,
      function (data, textStatus, jqXHR) {
        message(data);
        updateTable();
      }
    );
  };

  /* Metodo para definir checkboxes */
  setCheckBoxes = (dataUser) => {
    for (let i = 1; i <= 18; i++) {
      if ($(`#checkbox-${i}`).is(':checked')) {
        if (i == 1) dataUser['createMold'] = '1';
        if (i == 2) dataUser['planningCreateProduct'] = '1';
        if (i == 3) dataUser['planningCreateMaterial'] = '1';
        if (i == 4) dataUser['planningCreateMachine'] = '1';
        if (i == 5) dataUser['planningCreateProcess'] = '1';
        if (i == 6) dataUser['planningProductsMaterial'] = '1';
        if (i == 7) dataUser['planningProductsProcess'] = '1';
        if (i == 8) dataUser['programsMachine'] = '1';
        if (i == 9) dataUser['ciclesMachine'] = '1';
        if (i == 10) dataUser['invCategory'] = '1';
        if (i == 11) dataUser['sale'] = '1';
        if (i == 12) dataUser['plannigUser'] = '1';
        if (i == 13) dataUser['inventory'] = '1';
        if (i == 14) dataUser['order'] = '1';
        if (i == 15) dataUser['programming'] = '1';
        if (i == 16) dataUser['load'] = '1';
        if (i == 17) dataUser['explosionOfMaterial'] = '1';
        if (i == 18) dataUser['office'] = '1';
      } else {
        if (i == 1) dataUser['createMold'] = '0';
        if (i == 2) dataUser['planningCreateProduct'] = '0';
        if (i == 3) dataUser['planningCreateMaterial'] = '0';
        if (i == 4) dataUser['planningCreateMachine'] = '0';
        if (i == 5) dataUser['planningCreateProcess'] = '0';
        if (i == 6) dataUser['planningProductsMaterial'] = '0';
        if (i == 7) dataUser['planningProductsProcess'] = '0';
        if (i == 8) dataUser['programsMachine'] = '0';
        if (i == 9) dataUser['ciclesMachine'] = '0';
        if (i == 10) dataUser['invCategory'] = '0';
        if (i == 11) dataUser['sale'] = '0';
        if (i == 12) dataUser['plannigUser'] = '0';
        if (i == 13) dataUser['inventory'] = '0';
        if (i == 14) dataUser['order'] = '0';
        if (i == 15) dataUser['programming'] = '0';
        if (i == 16) dataUser['load'] = '0';
        if (i == 17) dataUser['explosionOfMaterial'] = '0';
        if (i == 18) dataUser['office'] = '0';
      }
    }

    return dataUser;
  };

  /* Eliminar usuario */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblUsers.fnGetData(row);

    // let idUser = data.id_user;
    let idUser = data.id_user;
    let programsMachine = data.programs_machine;
    dataUser = {};
    // dataUser['idUser'] = idUser;
    dataUser['idUser'] = idUser;
    dataUser['programsMachine'] = programsMachine;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este Usuario? Esta acción no se puede reversar.',
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
            '/api/deleteUser',
            dataUser,
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
      $('#createUserAccess').modal('hide');
      $('#formCreateUser').trigger('reset');
      // $('#formCreateAccessUser')[0].reset();
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblUsers').DataTable().clear();
    $('#tblUsers').DataTable().ajax.reload();
  }
});
