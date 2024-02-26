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
      order: [[6, 'desc']],
      dom: '<"datatable-error-console">frtip',
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      },
      fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
        if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
          console.error(oSettings.json.error);
        }
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
          data: 'reference_material',
          className: 'uniqueClassName',
        },
        {
          title: 'Materia Prima',
          data: 'material',
          className: 'uniqueClassName',
        },
        // {
        //   title: 'Cantidad x Producto',
        //   data: null,
        //   className: 'uniqueClassName',
        //   render: function (data) {
        //     let quantity = parseFloat(data.quantity1);
        //     if (Math.abs(quantity) < 0.01) {
        //       quantity = quantity.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
        //     } else
        //       quantity = quantity.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
        //     return `${quantity} ${data.abbreviation_material}`;
        //   },
        // },
        {
          title: 'Cantidad Total',
          data: null,
          className: 'uniqueClassName',
          render: function (data) {
            let total_quantity = parseFloat(data.total_quantity);
            if (Math.abs(total_quantity) < 0.01) {
              total_quantity = total_quantity.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            } else
              total_quantity = total_quantity.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
            return `${total_quantity} ${data.abbreviation_material}`;
          },
        },
        {
          title: 'Costo Unitario',
          data: null,
          className: 'uniqueClassName',
          render: function (data) {
            let cost = 0;

            data.abbreviation_material != data.abbreviation_product_material
              ? (cost = data.cost_product_material)
              : (cost = data.cost);
            
            cost = parseFloat(cost);
            if (Math.abs(cost) < 0.001) {
              cost = cost.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            } else
              cost = cost.toLocaleString('es-CO', { maximumFractionDigits: 0 });

            return `$ ${cost}`;
          },
        },
        {
          title: 'Precio Total',
          data: 'cost_product_material',
          className: 'classCenter',
          render: function (data) {
            let cost = parseFloat(data);
            if (Math.abs(cost) < 0.001) {
              cost = cost.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            } else
              cost = cost.toLocaleString('es-CO', { maximumFractionDigits: 0 });

            return `$ ${cost}`;
          }
        },
        {
          title: 'Participacion',
          data: 'participation',
          className: 'classCenter',
          render: $.fn.dataTable.render.number('.', ',', 2, '', '%'),
        },
      ],
      footerCallback: function (row, data, start, end, display) {
        let costs = 0;
        let cost_product_material = 0;
        let participation = 0;

        for (i = 0; i < data.length; i++) {
          data[i].abbreviation_material != data[i].abbreviation_product_material
            ? (cost = data[i].cost_product_material)
            : (cost = data[i].cost);
          
          costs += parseFloat(cost);

          cost_product_material += parseFloat(data[i].cost_product_material);
          participation += parseFloat(data[i].participation);

        }

        $(this.api().column(4).footer()).html(
          `$ ${costs.toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })}`
        );

        $(this.api().column(5).footer()).html(
          `$ ${cost_product_material.toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })}`
        );

        $(this.api().column(6).footer()).html(
          `${participation.toLocaleString('es-CO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          })} %`
        );
      },
    });
  };

  /* Tabla analizar materias prima */
  loadTblAnalysisMaterials = async (data) => {
    dataAnalysisMaterials = data;

    $('#tblAnalysisMaterials').dataTable({
      destroy: true,
      pageLength: 20,
      data: data,
      dom: '<"datatable-error-console">frtip',
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      },
      fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
        if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
          console.error(oSettings.json.error);
        }
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
        // {
        //   title: 'Cantidad x Producto',
        //   data: null,
        //   className: 'uniqueClassName',
        //   render: function (data, type, full, meta) {
        //     let quantity = parseFloat(data.quantity1);
        //     if (Math.abs(quantity) < 0.01) { 
        //       quantity = quantity.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
        //     } else
        //       quantity = quantity.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
        //     return `<p id="quantity-${meta.row}">${quantity} ${data.abbreviation_material}</p>`;
        //   },
        // },
        {
          title: 'Cantidad Total',
          data: null,
          className: 'uniqueClassName',
          render: function (data, type, full, meta) {
            let total_quantity = parseFloat(data.total_quantity);
            if (Math.abs(total_quantity) < 0.01) {
              total_quantity = total_quantity.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            } else
              total_quantity = total_quantity.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
            return `<p id="totalQuantity-${meta.row}">${total_quantity} ${data.abbreviation_material}</p>`;
          },
        },
        {
          title: 'Precio Actual',
          data: null,
          className: 'uniqueClassName',
          render: function (data, type, full, meta) {
            data.abbreviation_material != data.abbreviation_product_material
              ? (cost = data.cost_product_material)
              : (cost = data.cost);

            return `<p id="aPrice-${meta.row}">${cost.toLocaleString('es-CO', { maximumFractionDigits: 0 })}</p>`;
          },
        },
        {
          title: 'Precio a Negociar',
          data: null,
          className: 'uniqueClassName',
          render: function (data, type, full, meta) {
            return `<input type="number" class="form-control text-center negotiatePrice" id="price-${meta.row}">`;
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
          data: 'cost_product_material',
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
    });
  };
});
