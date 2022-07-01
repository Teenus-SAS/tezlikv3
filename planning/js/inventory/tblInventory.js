$(document).ready(function () {
  /* Cargar Inventario productos */
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
        title: 'Producto',
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

  /* Cargar inventario materia prima */
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
        title: 'Materia Prima',
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

  /* Cargar inventario insumos */
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
        title: 'Materia Prima',
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
  });
});
