$(document).ready(function () {
  dataInactiveProducts = [];

  loadAllData = async () => {
    const [dataProducts, dataInactives, dataLimit] = await Promise.all([
      searchData('/api/products'),
      searchData('/api/inactivesProducts'),
      searchData('/api/productsLimit')
    ]);

    dataInactiveProducts = dataInactives; 

    if (dataLimit.quantity >= dataLimit.cant_products) $('.limitPlan').show(800);
    else $('.limitPlan').hide(800);

    loadTblProducts(dataProducts);
  }

  /* Cargue tabla de Proyectos */

  loadTblProducts = (data) => {
    tblProducts = $('#tblProducts').dataTable({
      destroy: true,
      pageLength: 50,
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
            if (Math.abs(data) < 0.0001) { 
              let decimals = contarDecimales(data);
              data = formatNumber(data, decimals);
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
            return ` <input type="checkbox" class="form-control-updated checkboxProduct" id="${data}" checked>`;
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
    });
  }
  /* Consultar limite de productos */

  // getCantProducts = async () => {
  //   let data = await searchData('/api/productsLimit');
  //   if (data.quantity >= data.cant_products) $('.limitPlan').show(800);
  //   else $('.limitPlan').hide(800);
  // };

  loadAllData();
});
