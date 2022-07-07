$(document).ready(function () {
  $('#rol').change(function (e) {
    e.preventDefault();

    if ($.fn.dataTable.isDataTable('#tblUsers')) {
      $('#tblUsers').DataTable().destroy();
      $('#tblUsers').empty();
    }

    value = this.value;
    if (value == 1) loadTableCost();
    if (value == 2) loadTablePlanning();
  });

  /* Cargue tabla de costos */
  loadTableCost = () => {
    tblUsers = $('#tblUsers').dataTable({
      pageLength: 50,
      ajax: {
        url: '/api/costUsersAccess',
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
          title: 'Nombres',
          data: 'firstname',
          className: 'uniqueClassName',
        },
        {
          title: 'Email',
          data: 'email',
          className: 'uniqueClassName',
        },
        {
          title: 'Crear Productos',
          data: 'create_product',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Crear Materiales',
          data: 'create_materials',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Crear Máquinas',
          data: 'create_machines',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Crear Procesos',
          data: 'create_process',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Ficha Técnica Materiales',
          data: 'product_materials',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Ficha Técnica Procesos',
          data: 'product_process',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },

        {
          title: 'Carga fabril',
          data: 'factory_load',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Servicios Externos',
          data: 'external_service',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Carga Nómina',
          data: 'payroll_load',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Asignación Gastos',
          data: 'expense',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Distribución Gastos',
          data: 'expense_distribution',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Usuarios',
          data: 'user',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Precios',
          data: 'price',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Analisis Materia Prima',
          data: 'analysis_material',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Herramientas',
          data: 'tool',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
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
      columnDefs: [
        {
          targets: [1],
          render: function (data, type, row) {
            return data + '  ' + row.lastname + ' ';
          },
        },
      ],
    });
  };

  /* Cargue tabla de planeacion */
  loadTablePlanning = () => {
    tblUsers = $('#tblUsers').dataTable({
      pageLength: 50,
      ajax: {
        url: '/api/planningUsersAccess',
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
          title: 'Nombres',
          data: 'firstname',
          className: 'uniqueClassName',
        },
        {
          title: 'Email',
          data: 'email',
          className: 'uniqueClassName',
        },
        {
          title: 'Crear Moldes',
          data: 'create_mold',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Crear Productos',
          data: 'create_product',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Crear Materiales',
          data: 'create_material',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Crear Máquinas',
          data: 'create_machine',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Crear Procesos',
          data: 'create_process',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Ficha Técnica Materiales',
          data: 'products_material',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Ficha Técnica Procesos',
          data: 'products_process',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },

        {
          title: 'Programación Maquinas',
          data: 'programs_machine',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Ciclos Maquinas',
          data: 'cicles_machine',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Categorias',
          data: 'inv_category',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Ventas',
          data: 'sale',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Usuarios',
          data: 'user',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Inventarios',
          data: 'inventory',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Pedidos',
          data: 'plan_order',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Programación',
          data: 'programming',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Cargues',
          data: 'plan_load',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Explosión de Materiales',
          data: 'explosion_of_material',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Despachos',
          data: 'office',
          className: 'uniqueClassName',
          render: function (data, type, row) {
            return data == 1
              ? '<i class="bx bx-check text-success fs-lg align-middle"></i>'
              : '<i class="bx bx-x text-danger fs-lg align-middle"></i>';
          },
        },
        {
          title: 'Acciones',
          data: 'id_user',
          className: 'uniqueClassName',
          render: function (data) {
            return `
                <a href="javascript:;" <i id="${data}" value="2" class="bx bx-edit-alt updateUser" data-toggle='tooltip' title='Actualizar Usuario' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" value="2" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Usuario' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
          },
        },
      ],
      columnDefs: [
        {
          targets: [1],
          render: function (data, type, row) {
            return data + '  ' + row.lastname + ' ';
          },
        },
      ],
    });
  };
});
