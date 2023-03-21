$(document).ready(function () {
  dataMaterials = [];

  fetchData = async (data) => {
    try {
      data = await $.ajax({
        url: '/api/rawMaterialsLots',
        type: 'POST',
        data: { data },
      });
    } catch (error) {
      console.error(error);
    }

    await loadtableMaterials(data['allRawMaterials']);
    await loadTblAnalysisMaterials(data['80RawMaterials']);
  };

  /* Tabla todas las materias prima */
  loadtableMaterials = async (data) => {
    tblMaterials = $('#tblMaterials').dataTable({
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
          data: 'reference_product',
          visible: false,
        },
        {
          title: 'Producto',
          data: 'product',
          visible: false,
        },
        {
          title: 'Referencia',
          data: 'reference_material',
          className: 'uniqueClassName',
        },
        {
          title: 'Materia Prima',
          data: 'material',
          className: 'uniqueClassName',
        },
        {
          title: 'Cantidad',
          data: 'quantity',
          className: 'uniqueClassName',
        },
        {
          title: 'Costo Unitario',
          data: 'cost',
          className: 'uniqueClassName',
          render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
        },
        {
          title: 'Precio Total',
          data: 'unityCost',
          className: 'classCenter',
          render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
        },
        {
          title: 'Participacion',
          data: 'participation',
          className: 'classCenter',
          render: $.fn.dataTable.render.number('.', ',', 2, '', '%'),
        },
      ],
      rowGroup: {
        dataSrc: function (row) {
          return `
            <th class="text-center" colspan="3" style="font-weight: bold;"> ${row.reference_product}</th>
            <th class="text-center" colspan="4" style="font-weight: bold;"> ${row.product} </th>
          `;
        },
        startRender: function (rows, group) {
          return $('<tr/>').append(group);
        },
        className: 'odd',
      },
      /* footerCallback: function (row, data, start, end, display) {
        total = this.api()
          .column(7)
          .data()
          .reduce(function (a, b) {
            return parseInt(a) + parseInt(b);
          }, 0);

        $(this.api().column(7).footer()).html(
          new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
          }).format(total)
        );
        subTotal = this.api()
          .column(8)
          .data()
          .reduce(function (a, b) {
            return a + b;
          }, 0);

        $(this.api().column(8).footer()).html(`${subTotal.toFixed(0)} %`);
      }, */
    });
  };

  /* Tabla analizar materias prima */
  loadTblAnalysisMaterials = async (data) => {
    dataAnalysisMaterials = data;

    $('#tblAnalysisMaterials').dataTable({
      destroy: true,
      pageLength: 20,
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
