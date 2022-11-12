$(document).ready(function () {
  /* Cargue tabla de Contactos*/

  tblContacts = $('#tblContacts').dataTable({
    pageLength: 50,
    ajax: {
      url: '/api/contacts',
      dataSrc: '',
    },
    language: {
      url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
    },
    columns: [
      {
        title: 'No.',
        data: null,
        className: 'uniqueClassName',
        render: function (data, type, full, meta) {
          return meta.row + 1;
        },
      },
      {
        title: 'Nombre',
        data: 'firstname',
        className: 'uniqueClassName',
      },
      {
        title: 'Apellido',
        data: 'lastname',
        className: 'uniqueClassName',
      },
      {
        title: 'Telefono',
        data: 'phone',
        className: 'classCenter',
      },
      {
        title: 'Email',
        data: 'email',
        className: 'classRight',
      },
      {
        title: 'Cargo',
        data: 'position',
        className: 'classRight',
      },
      {
        title: 'Compañia',
        data: 'company_name',
        className: 'classRight',
      },
      {
        title: 'Acciones',
        data: 'id_contact',
        className: 'uniqueClassName',
        render: function (data) {
          return `
                  <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateRawMaterials" data-toggle='tooltip' title='Actualizar Materia Prima' style="font-size: 30px;"></i></a>
                  <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Materia Prima' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],
  });
});
