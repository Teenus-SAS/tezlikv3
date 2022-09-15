$(document).ready(function () {
  $('.cardTableProductsInProcess').hide();

  /* Seleccion producto */

  $('#refProduct').change(function (e) {
    e.preventDefault();
    id = this.value;
    $(`#selectNameProduct option[value=${id}]`).prop('selected', true);
    loadtableMaterials(id);
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    id = this.value;
    $(`#refProduct option[value=${id}]`).prop('selected', true);
    loadtableMaterials(id);
  });

  /* Cargue tabla de Productos Materiales */

  const loadtableMaterials = (idProduct) => {
    debugger;
    if ($.fn.dataTable.isDataTable('#tblConfigMaterials')) {
      $('#tblConfigMaterials').DataTable().destroy();
      $('#tblConfigMaterials').empty();
    }

    tblConfigMaterials = $('#tblConfigMaterials').dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: `/api/productsMaterials/${idProduct}`,
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
          title: 'Referencia',
          data: 'reference',
          className: 'uniqueClassName',
        },
        {
          title: 'Materia Prima',
          data: 'material',
          className: 'classCenter',
        },
        {
          title: 'Unidad',
          data: 'unit',
          className: 'classCenter',
        },
        {
          title: 'Cantidad',
          data: 'quantity',
          className: 'classCenter',
          render: $.fn.dataTable.render.number('.', ',', 4, ''),
        },
        {
          title: 'Acciones',
          data: 'id_product_material',
          className: 'uniqueClassName',
          render: function (data) {
            return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateMaterials" data-toggle='tooltip' title='Actualizar Materia Prima' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Materia Prima' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
          },
        },
      ],
    });
  };
  /* Cargue tabla de Productos en proceso */

  loadTableProcess = async () => {
    if ($.fn.dataTable.isDataTable('#tblConfigMaterials')) {
      $('#tblConfigMaterials').DataTable().destroy();
      $('#tblConfigMaterials').empty();
    }

    data = await fetchinData();

    tblProductsInProcess = $('#tblConfigMaterials').dataTable({
      destroy: true,
      pageLength: 50,
      data: data,
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
          title: 'Referencia',
          data: 'reference',
          className: 'uniqueClassName',
        },
        {
          title: 'Producto',
          data: 'product',
          className: 'classCenter',
        },
        {
          title: 'Acciones',
          data: 'id_product_in_process',
          className: 'uniqueClassName',
          render: function (data) {
            return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateProduct" data-toggle='tooltip' title='Actualizar Producto' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 30px;color:red" onclick="deleteProduct()"></i></a>`;
          },
        },
      ],
    });
  };

  fetchinData = async () => {
    try {
      result = await $.ajax({
        url: '/api/productsInProcessByCompany',
      });
      return result;
    } catch (error) {
      console.error(error);
    }
  };
});
