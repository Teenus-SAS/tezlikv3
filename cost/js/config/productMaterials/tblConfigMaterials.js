$(document).ready(function () {
  allProductMaterials = [];
  allComposites = [];

  /* Seleccion producto */
  $('#refProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    let composite = parseInt($(this).find('option:selected').attr('class'));

    if (composite == 0) {
      $('#btnAddNewProduct').show();
    } else
      $('#btnAddNewProduct').hide();

    $('#selectNameProduct option').prop('selected', function () {
      return $(this).val() == id;
    });
    $('.cardAddNewProduct').hide(800);
    $('.cardAddMaterials').hide(800);
    loadtableMaterials(id);
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    let composite = parseInt($(this).find('option:selected').attr('class'));

    if (composite == 0) {
      $('#btnAddNewProduct').show();
    } else
      $('#btnAddNewProduct').hide();

    $('#refProduct option').prop('selected', function () {
      return $(this).val() == id;
    });
    $('.cardAddNewProduct').hide(800);
    $('.cardAddMaterials').hide(800);

    loadtableMaterials(id);
  });

  loadAllData = async (op) => {
    const [dataProductMaterials, dataCompositeProduct] = await Promise.all([
      searchData('/api/allProductsMaterials'),
      searchData('/api/allCompositeProducts')
    ]);

    allProductMaterials = dataProductMaterials;
    allComposites = dataCompositeProduct;

    if (op != 1)
      loadtableMaterials(op);
  }

  /* Cargue tabla de Proyectos */
  loadtableMaterials = async (idProduct) => {
    // let data = await searchData(`/api/productsMaterials/${idProduct}`);
    // if (flag_composite_product == '1') {
    //   let dataCompositeProduct = await searchData(`/api/compositeProducts/${idProduct}`);

    //   data = [...data, ...dataCompositeProduct];
    // }
    let data = allProductMaterials.filter(item => item.id_product == idProduct);

    if (flag_composite_product == '1') {
      let dataCompositeProduct = allComposites.filter(item => item.id_product == idProduct);

      data = [...data, ...dataCompositeProduct];
    }
    
    tblConfigMaterials = $('#tblConfigMaterials').dataTable({
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
        // {
        //   title: 'Referencia',
        //   data: 'reference',
        //   className: 'uniqueClassName',
        // },
        {
          title: 'Referencia',
          data: 'reference_material',
          className: 'uniqueClassName',
        },
        {
          title: 'Materia Prima',
          data: 'material',
          className: 'classCenter',
        },
        {
          title: 'Unidad',
          data: 'abbreviation',
          className: 'classCenter',
        },
        {
          title: 'Cantidad Usada',
          data: 'quantity',
          className: 'classCenter',
          render: function (data) {
            data = parseFloat(data);
            // if (Math.abs(data) < 0.01) { 
            //   data = data.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            // } else
            //   data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            data = data.toLocaleString('es-CO');
            
            return data;
          },
        },
        {
          title: 'Precio Unitario',
          data: 'cost_product_material',
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
          title: 'Acciones',
          data: null,
          className: 'uniqueClassName',
          render: function (data) {
            return `<a href="javascript:;" <i id="${data.id_product_material != 0 ? data.id_product_material : data.id_composite_product}" class="bx bx-edit-alt ${data.id_product_material != 0 ? 'updateMaterials' : 'updateComposite'}" data-toggle='tooltip' title='Actualizar Materia Prima' style="font-size: 30px;"></i></a>
                        <a href="javascript:;" <i id="${data.id_product_material != 0 ? data.id_product_material : data.id_composite_product}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Materia Prima' style="font-size: 30px;color:red" onclick="deleteMaterial(${data.id_product_material != 0 ? '1' : '2'})"></i></a>`;
          },
        },
      ],
      footerCallback: function (row, data, start, end, display) {
        let quantity = 0;
        let cost = 0;

        for (let i = 0; i < data.length; i++) {
          quantity += parseFloat(data[i].quantity);
          cost += parseFloat(data[i].cost_product_material);
        }

        $(this.api().column(4).footer()).html(
          quantity.toLocaleString('es-CO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          })
        );

        $(this.api().column(5).footer()).html(
          `$ ${cost.toLocaleString('es-CO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          })}`
        );
      },
    });
  };

  loadAllData(1);
});
