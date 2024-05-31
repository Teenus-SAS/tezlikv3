$(document).ready(function () {
  // allProductMaterials = [];
  // allComposites = [];
  let visible;

  loadAllDataMaterials = async (id) => {
    try {
      const [dataProductMaterials, dataCompositeProduct] = await Promise.all([
        searchData(`/api/productsMaterials/${id}`),
        searchData(`/api/compositeProducts/${id}`)
      ]);

      sessionStorage.setItem('dataProductMaterials', JSON.stringify(dataProductMaterials));
      sessionStorage.setItem('dataCompositeProduct', JSON.stringify(dataCompositeProduct));
      // allProductMaterials = dataProductMaterials;
      // allComposites = dataCompositeProduct;

      // if (op != 1)
      let op = 1;
      if(flag_currency_usd == '1'){
        let selectPriceUSD = $('#selectPriceUSD').val();

        selectPriceUSD == '2' ? op = 2 : op = 1;
      }

      loadTableMaterials(dataProductMaterials, op);
    } catch (error) {
      console.error('Error loading data:', error);
    }
  }; 

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
    $('.btnDownloadXlsx').show(800);
    $('.cardAddNewProduct').hide(800);
    $('.cardAddMaterials').hide(800);
    loadAllDataMaterials(id);
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
    $('.btnDownloadXlsx').show(800);
    $('.cardAddNewProduct').hide(800);
    $('.cardAddMaterials').hide(800);

    loadAllDataMaterials(id);
  }); 

  /* Cargue tabla de Proyectos */
  loadTableMaterials = async (data, op) => {
    // let dataProductMaterials = JSON.parse(sessionStorage.getItem('dataProductMaterials'));
    // let data = dataProductMaterials.filter(item => item.id_product == idProduct);
    
    if (flag_composite_product == '1') {
      let dataCompositeProduct = JSON.parse(sessionStorage.getItem('dataCompositeProduct'));
      // let arr = dataCompositeProduct.filter(item => item.id_product == idProduct);

      data = [...data, ...dataCompositeProduct];
    } 

    let waste = 0;

    data.forEach(item => {
      waste += parseFloat(item.waste);
    });

    waste == 0 ? visible = false : visible = true;

    // if ($.fn.dataTable.isDataTable("#tblConfigMaterials")) {
    //   var table = $("#tblConfigMaterials").DataTable();
    //   var pageInfo = table.page.info(); // Guardar información de la página actual
    //   table.clear();
    //   table.rows.add(data).draw();
    //   table.page(pageInfo.page).draw('page'); // Restaurar la página después de volver a dibujar los datos 
    //   $('#tblConfigMaterials').DataTable().column(5).visible(visible);
    //   return;
    // }
    
    // Función para formatear los costos
    const renderCost = (data, op) => {
      let cost;

      if (flag_currency_usd == '1' && op == 2) {
        cost = parseFloat(data);
        if (Math.abs(cost) < 0.01) {
          cost = cost.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
        } else {
          cost = cost.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
      } else {
        cost = parseFloat(data);
        if (Math.abs(cost) < 0.01) {
          cost = cost.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
        } else {
          cost = cost.toLocaleString('es-CO', { maximumFractionDigits: 2 });
        }
      }
      return `$ ${cost}`;
    };
    
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
            data = data.toLocaleString('es-CO');
            
            return data;
          },
        },
        {
          title: 'Desperdicio',
          data: 'waste',
          className: 'classCenter',
          visible: visible,
          render: function (data) {
            data = parseFloat(data);
            data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
            return `${data} %`;
          },
        },
        {
          title: 'Precio Unitario',
          data: function (row, type, val, meta) {
            if (flag_currency_usd == '1' && op == 2) {
              return parseFloat(row.cost_product_material_usd);
            } else {
              return row.cost_product_material;
            }
          },
          className: 'classCenter',
          render: (data, type, row) => renderCost(data, op),
        },
        {
          title: 'Participacion',
          data: 'participation',
          className: 'classCenter',
          render: function (data) {
            data = parseFloat(data); 
            return `${data.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} %`;
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
        // let quantity = 0;
        let waste = 0;
        let cost = 0;
        let participation = 0;
        
        for (let i = 0; i < display.length; i++) {
          // quantity += parseFloat(data[display[i]].quantity);
          waste += parseFloat(data[display[i]].waste);
          cost += parseFloat(data[display[i]].cost_product_material);
          participation += parseFloat(data[display[i]].participation);
        }

        waste = waste / display.length;

        // $(this.api().column(4).footer()).html(
        //   quantity.toLocaleString('es-CO', {
        //     minimumFractionDigits: 2,
        //     maximumFractionDigits: 2,
        //   })
        // );

        $(this.api().column(5).footer()).html(
          `${waste.toLocaleString('es-CO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          })} %`
        );

        $(this.api().column(6).footer()).html(
          `$ ${cost.toLocaleString('es-CO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          })}`
        );
        $(this.api().column(7).footer()).html(
          `${participation.toLocaleString('es-CO', {
            maximumFractionDigits: 2,
          })} %`
        );
      },
    });
  };

  // loadAllDataMaterials(1);
});
