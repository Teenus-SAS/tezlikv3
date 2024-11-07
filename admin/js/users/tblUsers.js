$(document).ready(function () {
  /* Cargue tabla de Proyectos */
  allUsers = [];

  loadAllData = async() => {
    try {
      const users = await searchData('/api/usersCompany'); 

      loadTblUsers(users);
        
      allUsers = users;
    } catch (error) {
      console.error('Error loading data:', error);
    }
  }

  loadTblUsers = (data) => {
    if ($.fn.DataTable.isDataTable('#tblUsers')) {
      tblUsers.DataTable().clear().rows.add(data).draw();
    } else {
      tblUsers = $('#tblUsers').dataTable({
        pageLength: 50,
        data: data, 
        language: {
          url: '/assets/plugins/i18n/Spanish.json',
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
            title: 'Nombres',
            data: 'firstname',
            className: 'uniqueClassName',
          },
          {
            title: 'Apellidos',
            data: 'lastname',
            className: 'uniqueClassName',
          },
          {
            title: 'Email',
            data: 'email',
            className: 'uniqueClassName',
          },
          {
            title: 'Posicion',
            data: 'position',
            className: 'uniqueClassName',
          },
          {
            title: 'Empresa',
            data: 'company',
            className: 'uniqueClassName',
          },
          {
            title: '',
            data: null,
            className: 'uniqueClassName',
            render: function (data) {
              if (data.contract == '1')
                return `<a href="javascript:;" <span id="${data.id_user}" class="badge badge-warning" onclick="showMsg()">Usuario Principal</span></a>`;
              else
                return `<input class="form-control-updated checkUser" type="checkbox" id="${data.id_user}">`;
            },
          },
          {
            title: 'Acciones',
            data: 'id_user',
            className: 'uniqueClassName',
            render: function (data) {
              return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateUser" data-toggle='tooltip' title='Actualizar Usuario' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Usuario' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
            },
          },
        ],
      });
    }
  }

  $('#company').change(function (e) { 
    e.preventDefault();

    let data = allUsers.filter(item => item.id_company == this.value);

    loadTblUsers(data);     
  });

  loadAllData();
  showMsg = () => {
    toastr.error('Debe haber por lo menos un usuario principal por empresa');
    return false;
  };
});
