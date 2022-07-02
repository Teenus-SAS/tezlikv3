$(document).ready(function () {
  // Seleccionar categoria
  $('#category').change(function (e) {
    e.preventDefault();
    debugger;
    // $('#tblInventories').DataTable().destroy();

    value = this.value;

    if (value == 1) {
      link = '../../api/planProducts';
      columns = [
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
          title: 'Descripción',
          data: 'product',
          className: 'uniqueClassName',
        },
        {
          title: 'Categoria',
          data: null,
          className: 'classCenter',
          render: function () {
            return 'Producto';
          },
        },
        {
          title: 'Unidad',
          data: null,
          className: 'classCenter',
          render: function () {
            return 'Unidad';
          },
        },
        {
          title: 'Cantidad',
          data: 'quantity',
          className: 'uniqueClassName',
        },
      ];
    }
    if (value == 2) {
      link = '../../api/inventory/2';
      columns = [
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
          title: 'Descripción',
          data: 'material',
          className: 'uniqueClassName',
        },
        {
          title: 'Categoria',
          data: null,
          className: 'classCenter',
          render: function () {
            return 'Materia Prima';
          },
        },
        {
          title: 'Unidad',
          data: 'unit',
          className: 'classCenter',
        },
        {
          title: 'Cantidad',
          data: 'quantity',
          className: 'uniqueClassName',
        },
      ];
    }
    if (value == 3) {
      link = '../../api/inventory/1';
      columns = [
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
          title: 'Descripción',
          data: 'material',
          className: 'uniqueClassName',
        },
        {
          title: 'Categoria',
          data: null,
          className: 'classCenter',
          render: function () {
            return 'Materia Prima';
          },
        },
        {
          title: 'Unidad',
          data: 'unit',
          className: 'classCenter',
        },
        {
          title: 'Cantidad',
          data: 'quantity',
          className: 'uniqueClassName',
        },
      ];
    }
    if (value == 4) {
    }

    loadTable(link, columns);
  });

  /* Cargar Inventario productos */
  loadTable = (link, columns) => {
    tblInventories = $('#tblInventories').dataTable({
      pageLength: 10,
      ajax: {
        url: link,
        dataSrc: '',
      },
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      },
      columns: columns,
    });
  };
  /* Cargar Inventario productos 
  tblInvProducts = $('#tblInvProducts').dataTable({
    pageLength: 10,
    ajax: {
      url: '../../api/planProducts',
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
        title: 'Descripción',
        data: 'product',
        className: 'uniqueClassName',
      },
      {
        title: 'Cantidad',
        data: 'quantity',
        className: 'uniqueClassName',
      },
    ],
  });

  // Cargar inventario materia prima
  tblInvMaterials = $('#tblInvMaterials').dataTable({
    pageLength: 10,
    ajax: {
      url: '../../api/inventory/2',
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
        title: 'Descripción',
        data: 'material',
        className: 'classCenter',
      },
      {
        title: 'Categoria',
        data: null,
        className: 'classCenter',
        render: function () {
          return 'Material';
        },
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
      },
    ],
  });

  // Cargar inventario insumos 
  tblInvSupplies = $('#tblInvSupplies').dataTable({
    pageLength: 10,
    ajax: {
      url: '../../api/inventory/1',
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
        title: 'Descripción',
        data: 'material',
        className: 'classCenter',
      },
      {
        title: 'Categoria',
        data: null,
        className: 'classCenter',
        render: function () {
          return 'Insumo';
        },
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
      },
    ],
  }); */
});
