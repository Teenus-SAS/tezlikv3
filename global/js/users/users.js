$(document).ready(function () {
  /* Ocultar panel Nuevo usuario */

  $('.cardCreateUsers').hide();
  $('.cardCreateAccessUser').hide();

  /* Abrir panel Nuevo usuario */

  $('#btnNewUser').click(function (e) {
    e.preventDefault();
    $('.cardCreateUsers').toggle(800);
    $('.cardCreateAccessUser').toggle(800);
    $('#btnCreateUserAndAccess').html('Crear Usuario y Accesos');

    sessionStorage.removeItem('id_user_access');

    $('#nameUser').prop('disabled', false);
    $('#lastnameUser').prop('disabled', false);
    $('#emailUser').prop('disabled', false);

    $('#formCreateUser').trigger('reset');
    $('#formCreateAccessUser').trigger('reset');
  });

  /* Agregar nuevo usuario */

  $('#btnCreateUserAndAccess').click(function (e) {
    e.preventDefault();
    let idUserAccess = sessionStorage.getItem('id_user_access');

    if (idUserAccess == '' || idUserAccess == null) {
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

      for (let i = 1; i <= 13; i++) {
        if ($(`#checkbox-${i}`).is(':checked')) {
          if (i == 1) dataUser['createProducts'] = '1';
          if (i == 2) dataUser['createMaterials'] = '1';
          if (i == 3) dataUser['createMachines'] = '1';
          if (i == 4) dataUser['createProcess'] = '1';
          if (i == 5) dataUser['productMaterials'] = '1';
          if (i == 6) dataUser['productProcess'] = '1';
          if (i == 7) dataUser['factoryLoad'] = '1';
          if (i == 8) dataUser['externalService'] = '1';
          if (i == 9) dataUser['productLine'] = '1';
          if (i == 10) dataUser['payrollLoad'] = '1';
          if (i == 11) dataUser['expense'] = '1';
          if (i == 12) dataUser['expenseDistribution'] = '1';
          if (i == 13) dataUser['user'] = '1';
        } else {
          if (i == 1) dataUser['createProducts'] = '0';
          if (i == 2) dataUser['createMaterials'] = '0';
          if (i == 3) dataUser['createMachines'] = '0';
          if (i == 4) dataUser['createProcess'] = '0';
          if (i == 5) dataUser['productMaterials'] = '0';
          if (i == 6) dataUser['productProcess'] = '0';
          if (i == 7) dataUser['factoryLoad'] = '0';
          if (i == 8) dataUser['externalService'] = '0';
          if (i == 9) dataUser['productLine'] = '0';
          if (i == 10) dataUser['payrollLoad'] = '0';
          if (i == 11) dataUser['expense'] = '0';
          if (i == 12) dataUser['expenseDistribution'] = '0';
          if (i == 13) dataUser['user'] = '0';
        }
      }

      $.post('/api/addUser', dataUser, function (data, textStatus, jqXHR) {
        message(data);
      });
    } else {
      updateUserAccess();
    }
  });

  /* Actualizar User */

  $(document).on('click', '.updateUser', function (e) {
    $('.cardCreateUsers').show(800);
    $('.cardCreateAccessUser').show(800);
    $('#btnCreateUserAndAccess').html('Actualizar Accesos');

    $('#nameUser').prop('disabled', true);
    $('#lastnameUser').prop('disabled', true);
    $('#emailUser').prop('disabled', true);

    let row = $(this).parent().parent()[0];
    let data = tblUsers.fnGetData(row);

    let idUserAccess = this.id;
    sessionStorage.setItem('id_user_access', idUserAccess);

    $('#nameUser').val(data.firstname);
    $('#lastnameUser').val(data.lastname);
    $('#emailUser').val(data.email);

    if (data.create_product == 1) $('#checkbox-1').prop('checked', true);
    else $('#checkbox-1').prop('checked', false);
    if (data.create_materials == 1) $('#checkbox-2').prop('checked', true);
    else $('#checkbox-2').prop('checked', false);
    if (data.create_machines == 1) $('#checkbox-3').prop('checked', true);
    else $('#checkbox-3').prop('checked', false);
    if (data.create_process == 1) $('#checkbox-4').prop('checked', true);
    else $('#checkbox-4').prop('checked', false);
    if (data.product_materials == 1) $('#checkbox-5').prop('checked', true);
    else $('#checkbox-5').prop('checked', false);
    if (data.product_process == 1) $('#checkbox-6').prop('checked', true);
    else $('#checkbox-6').prop('checked', false);
    if (data.factory_load == 1) $('#checkbox-7').prop('checked', true);
    else $('#checkbox-7').prop('checked', false);
    if (data.external_service == 1) $('#checkbox-8').prop('checked', true);
    else $('#checkbox-8').prop('checked', false);
    if (data.product_line == 1) $('#checkbox-9').prop('checked', true);
    else $('#checkbox-9').prop('checked', false);
    if (data.payroll_load == 1) $('#checkbox-10').prop('checked', true);
    else $('#checkbox-10').prop('checked', false);
    if (data.expense == 1) $('#checkbox-11').prop('checked', true);
    else $('#checkbox-11').prop('checked', false);
    if (data.expense_distribution == 1) $('#checkbox-12').prop('checked', true);
    else $('#checkbox-12').prop('checked', false);
    if (data.user == 1) $('#checkbox-13').prop('checked', true);
    else $('#checkbox-13').prop('checked', false);
  });

  updateUserAccess = () => {
    idUserAccess = sessionStorage.getItem('id_user_access');

    dataUser = {};
    dataUser['idUser'] = idUserAccess;
    dataUser['nameUser'] = $('#nameUser').val();
    dataUser['lastnameUser'] = $('#lastnameUser').val();
    dataUser['emailUser'] = $('#emailUser').val();

    for (let i = 1; i <= 13; i++) {
      if ($(`#checkbox-${i}`).is(':checked')) {
        if (i == 1) dataUser['createProducts'] = '1';
        if (i == 2) dataUser['createMaterials'] = '1';
        if (i == 3) dataUser['createMachines'] = '1';
        if (i == 4) dataUser['createProcess'] = '1';
        if (i == 5) dataUser['productMaterials'] = '1';
        if (i == 6) dataUser['productProcess'] = '1';
        if (i == 7) dataUser['factoryLoad'] = '1';
        if (i == 8) dataUser['externalService'] = '1';
        if (i == 9) dataUser['productLine'] = '1';
        if (i == 10) dataUser['payrollLoad'] = '1';
        if (i == 11) dataUser['expense'] = '1';
        if (i == 12) dataUser['expenseDistribution'] = '1';
        if (i == 13) dataUser['user'] = '1';
      } else {
        if (i == 1) dataUser['createProducts'] = '0';
        if (i == 2) dataUser['createMaterials'] = '0';
        if (i == 3) dataUser['createMachines'] = '0';
        if (i == 4) dataUser['createProcess'] = '0';
        if (i == 5) dataUser['productMaterials'] = '0';
        if (i == 6) dataUser['productProcess'] = '0';
        if (i == 7) dataUser['factoryLoad'] = '0';
        if (i == 8) dataUser['externalService'] = '0';
        if (i == 9) dataUser['productLine'] = '0';
        if (i == 10) dataUser['payrollLoad'] = '0';
        if (i == 11) dataUser['expense'] = '0';
        if (i == 12) dataUser['expenseDistribution'] = '0';
        if (i == 13) dataUser['user'] = '0';
      }
    }

    $.post(
      '/api/updateUserAccess',
      dataUser,
      function (data, textStatus, jqXHR) {
        message(data);
        updateTable();
      }
    );
  };

  /* Eliminar usuario */

  $(document).on('click', '.deleteUser', function (e) {
    let row = $(this).parent().parent()[0];
    let data = tblUsers.fnGetData(row);

    let idUserAccess = this.id;
    let idUser = data.id_user;
    dataUser = {};
    dataUser['idUserAccess'] = idUserAccess;
    dataUser['idUser'] = idUser;

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
  });

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('.cardCreateUsers').hide(800);
      $('.cardCreateAccessUser').hide(800);
      $('#formCreateUser')[0].reset();
      $('#formCreateAccessUser')[0].reset();
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
