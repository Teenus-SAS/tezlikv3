$(document).ready(function () {
  dataMaterials = [];

  loadTblAnalysisMaterials = async (data) => {
    try {
      dataAnalysisMaterials = await $.ajax({
        url: '/api/rawMaterialsLots',
        type: 'POST',
        data: { data },
      });
    } catch (error) {
      console.error(error);
    }

    $('#tblAnalysisMaterials').dataTable({
      destroy: true,
      pageLength: 20,
      data: dataAnalysisMaterials,
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
          data: 'reference_product',
          visible: false,
        },
        {
          title: 'Producto',
          data: 'product',
          visible: false,
        },
        {
          title: 'Participación',
          data: 'participation',
          className: 'uniqueClassName',
          render: function (data) {
            return `${data.toLocaleString('es-CO', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            })} %`;
          },
        },
        {
          title: 'Material',
          data: 'material',
          className: 'uniqueClassName',
        },
        {
          title: 'Cantidad',
          data: 'quantity',
          className: 'uniqueClassName',
          render: function (data, type, full, meta) {
            return `<p id="quantity-${meta.row}">${data}</p>`;
          },
        },
        {
          title: 'Precio Actual',
          data: 'cost',
          className: 'uniqueClassName',
          render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
        },
        {
          title: 'Precio a Negociar',
          data: null,
          className: 'uniqueClassName',
          render: function (data, type, full, meta) {
            return `<input type="text" class="form-control text-center number negotiatePrice" id="price-${meta.row}">`;
          },
        },
        {
          title: 'Porcentaje',
          data: null,
          className: 'uniqueClassName',
          render: function (data, type, full, meta) {
            return `<p id="percentage-${meta.row}"></p>`;
          },
        },
        {
          title: 'Costo Unidad',
          data: 'unityCost',
          className: 'uniqueClassName',
          render: function (data, type, full, meta) {
            return `<p id="unityCost-${meta.row}">${data.toLocaleString(
              'es-CO',
              { minimumFractionDigits: 0, maximumFractionDigits: 0 }
            )}</p>`;
          },
        },
        {
          title: 'Costo Total',
          data: null,
          className: 'uniqueClassName',
          render: function (data, type, full, meta) {
            return `<p id="totalCost-${meta.row}"></p>`;
          },
        },
        {
          title: 'Costo Proyectado',
          data: null,
          className: 'uniqueClassName',
          render: function (data, type, full, meta) {
            return `<p id="projectedCost-${meta.row}"></p>`;
          },
        },
      ],
      rowGroup: {
        dataSrc: function (row) {
          return `
            <th class="text-center" colspan="5" style="font-weight: bold;"> ${row.reference_product}</th>
            <th class="text-center" colspan="5" style="font-weight: bold;"> ${row.product} </th>
          `;
        },
        startRender: function (rows, group) {
          return $('<tr/>').append(group);
        },
        className: 'odd',
      },
    });
  };
});
