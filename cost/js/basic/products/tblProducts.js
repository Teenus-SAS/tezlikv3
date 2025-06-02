$(document).ready(function () {
  loadAllData = async () => {
    const [dataAllProducts, dataLimit] = await Promise.all([
      searchData('/api/products'),
      searchData('/api/productsLimit')
    ]);

    let dataProducts = dataAllProducts.filter(item => item.active == 1);
    let dataInactiveProducts = dataAllProducts.filter(item => item.active == 0);
    sessionStorage.setItem('dataInactiveProducts', JSON.stringify(dataInactiveProducts));

    if (dataLimit.quantity >= dataLimit.cant_products) $('.limitPlan').show(800);
    else $('.limitPlan').hide(800);

    loadTblProducts(dataProducts);
  }

  /* Cargue tabla de Proyectos */
  const loadTblProducts = (data) => {
    if ($.fn.dataTable.isDataTable("#tblProducts")) {
      var table = $("#tblProducts").DataTable();
      var pageInfo = table.page.info(); // Guardar información de la página actual
      table.clear();
      table.rows.add(data).draw();
      table.page(pageInfo.page).draw('page'); // Restaurar la página después de volver a dibujar los datos
      return;
    }

    tblProducts = $('#tblProducts').dataTable({
      destroy: true,
      pageLength: 50,
      data: data,
      dom: '<"datatable-error-console">frtip',
      language: {
        url: '/assets/plugins/i18n/Spanish.json',
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
          data: 'reference',
          className: 'uniqueClassName',
        },
        {
          title: 'Producto',
          data: 'product',
          className: 'uniqueClassName',
        },
        {
          title: 'Img',
          data: 'img',
          className: 'uniqueClassName',
          render: (data, type, row) => {
            data == '' || !data
              ? (txt = '')
              : (txt = `<img src="${data}" alt="" style="width:50px;border-radius:100px">`);
            return txt;
          },
        },
        {
          title: 'Precio Vta',
          data: 'sale_price',
          className: 'classCenter',
          render: function (data) {
            data = parseFloat(data);
            if (Math.abs(data) < 0.01) {
              // let decimals = contarDecimales(data);
              // data = formatNumber(data, decimals);
              data = data.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            } else
              data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });

            return `$ ${data}`;
          },
        },
        {
          title: 'Rentabilidad Deseada',
          data: 'profitability',
          className: 'classCenter',
          render: function (data) {
            return data + ' %';
          },
        },
        {
          title: 'Comision',
          data: 'commission_sale',
          className: 'classCenter',
          render: function (data) {
            return data + ' %';
          },
        },
        {
          title: 'Activar/Inactivar',
          data: 'id_product',
          className: 'uniqueClassName',
          render: function (data) {
            // return ` <input type="checkbox" class="form-control-updated checkboxProduct" id="${data}" checked>`;
            return `<a href="javascript:;" <span id="${data}" class="badge badge-warning checkboxProduct">Inactivar</span></a>`;
          },
        },
        {
          width: '150px',
          title: 'Acciones',
          data: null,
          className: 'uniqueClassName',
          render: function (data) {
            return `
                  ${flag_composite_product == 1 ? `<a href="javascript:;" <i id="${data.id_product}" class="${data.composite == 0 ? 'bi bi-plus-square-fill' : 'bi bi-dash-square-fill'} composite" data-toggle='tooltip' title='${data.composite == 0 ? 'Agregar' : 'Eliminar'} a producto compuesto' style="font-size:25px; color: #3e382c;"></i></a>` : ''}
                  <a href="javascript:;" <i id="${data.id_product}" class="bx bx-copy-alt" data-toggle='tooltip' title='Clonar Producto' style="font-size: 30px; color:green" onclick="copyFunction()"></i></a>
                  <a href="javascript:;" <i id="${data.id_product}" class="bx bx-edit-alt updateProducts" data-toggle='tooltip' title='Actualizar Producto' style="font-size: 30px;"></i></a>
                  <a href="javascript:;" <i id="${data.id_product}" class="mdi mdi-delete-forever deleteProduct" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 30px;color:red"></i></a>
                  `;
          },
        },
      ],
      headerCallback: function (thead, data, start, end, display) {
        $(thead).find("th").css({
          "background-color": "#386297",
          color: "white",
          "text-align": "center",
          "font-weight": "bold",
          padding: "10px",
          border: "1px solid #ddd",
        });
      },
    });
  }

  loadAllData();
});
