$(document).ready(function () {
  /* Cargue tabla de Materias Primas */
  // allProductMaterials = [];
  // allMaterials = [];
  // visible = true;
  let visibleCost = true;
  export_import == '0' && flag_export_import == '1' ? visibleCost = false : visibleCost;

  loadAllData = async (op) => {
    try {
      const [dataCategory, dataMaterials, dataProductMaterials] = await Promise.all([
        op == 1 ? searchData('/api/categories') : '',
        searchData('/api/materials'),
        op == 1 ? searchData('/api/allProductsMaterials') : ''
      ]);
      let visible;
      let dataCategory1;
      sessionStorage.setItem('dataMaterials', JSON.stringify(dataMaterials));

      if (op == 1) {
        let $selectCategory = $(`#idCategory`);
        $selectCategory.empty();
        $selectCategory.append(`<option disabled selected value='0'>Seleccionar</option>`);
        $.each(dataCategory, function (i, value) {
          $selectCategory.append(
            `<option value="${value.id_category}">${value.category}</option>`
          );
        });
        sessionStorage.setItem('dataCategory', JSON.stringify(dataCategory));
        loadTblCategories(dataCategory);
        sessionStorage.setItem('dataProductMaterials', JSON.stringify(dataProductMaterials));
        dataCategory1 = dataCategory;
        // allProductMaterials = dataProductMaterials;
      } else
        dataCategory1 = JSON.parse(sessionStorage.getItem('dataCategory'));
        
      if (dataCategory1.length == 0) {
        $('.categories').hide();
        visible = false;
      } else {
        $('.categories').show();
        visible = true;
      }

      let op1 = 1;

      if(flag_currency_usd == '1'){
        let selectPriceUSD = $('#selectPriceUSD').val();

        selectPriceUSD == '2' ? op1 = 2 : op1 = 1;
      }

      loadTblRawMaterials(dataMaterials, visible, op1);
        
      // allMaterials = dataMaterials;
    } catch (error) {
      console.error('Error loading data:', error);
    }
  }

  loadTblRawMaterials = (data, visible, op) => {
    
    const tableId = '#tblRawMaterials';

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
    }

    // Si la tabla ya está inicializada
    // if ($.fn.DataTable.isDataTable(tableId)) {
    //   // Obtener la instancia del DataTable existente
    //   let table = $(tableId).DataTable();

    //   // Limpiar y agregar los nuevos datos
    //   table.clear().rows.add(data).draw();

    //   // Actualizar la visibilidad de las columnas
    //   table.column(3).visible(visible);
    //   table.column(6).visible(visibleCost);
    //   table.column(7).visible(visibleCost);
    //   table.column(8).visible(visibleCost);

    //   // Actualizar las columnas con las nuevas funciones de renderizado
    //   table.columns().every(function (index) {
    //     if (index >= 6 && index <= 8) { // Ajusta los índices según tus columnas
    //       this.visible(true);
    //       this.data().each(function (cell, rowIdx) {
    //         let cellData = table.cell(rowIdx, index).data();
    //         table.cell(rowIdx, index).data(renderCost(cellData, op));
    //       });
    //     }
    //   });
    // } else {
    tblRawMaterials = $(tableId).dataTable({
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
          title: 'Materia Prima',
          data: 'material',
          className: 'uniqueClassName',
        },
        {
          title: 'Categoria',
          data: 'category',
          className: 'classCenter',
          visible: visible,
        },
        {
          title: 'Unidad',
          data: 'abbreviation',
          className: 'classCenter',
        },
        {
          width: '80px',
          title: 'Precio',
          data: function (row, type, val, meta) {
            if (flag_currency_usd == '1' && op == 2) {
              return parseFloat(row.cost_usd);
            } else {
              return row.cost;
            }
          },
          className: 'classRight',
          render: (data, type, row) => renderCost(data, op),
        },
        {
          // width: '80px',
          title: 'Costo Importacion',
          data: function (row, type, val, meta) {
            if (flag_currency_usd == '1' && op == 2) {
              return parseFloat(row.cost_import_usd);
            } else {
              return row.cost_import;
            }
          },
          className: 'classRight',
          visible: visibleCost,
          render: (data, type, row) => renderCost(data, op),
        },
        {
          // width: '80px',
          title: 'Costo Nacionalizacion',
          data: function (row, type, val, meta) {
            if (flag_currency_usd == '1' && op == 2) {
              return parseFloat(row.cost_export_usd);
            } else {
              return row.cost_export;
            }
          },
          className: 'classRight',
          visible: visibleCost,
          render: (data, type, row) => renderCost(data, op),
        },
        {
          // width: '80px',
          title: 'Total',
          data: function (row, type, val, meta) {
            if (flag_currency_usd == '1' && op == 2) {
              return parseFloat(row.cost_usd) + parseFloat(row.cost_import_usd) + parseFloat(row.cost_export_usd);
            } else {
              return row.cost_total;
            }
          },
          className: 'classRight',
          visible: visibleCost,
          render: (data, type, row) => renderCost(data, op),
        },
        {
          title: 'Acciones',
          data: null,
          className: 'uniqueClassName',
          render: function (data) {
            let check = '';
            if (data.status == 0) icon = '/global/assets/images/trash_v.png';
            else {
              icon = '/global/assets/images/trash_x.png';
              check = `<a href="javascript:;" <i id="${data.id_material}" class="mdi mdi-playlist-check seeDetailMaterials" data-toggle='tooltip' title='Ver Detalle' style="font-size: 30px; color:black;"></i></a>`;
            }

            return `
                <i class="badge badge-danger badge-pill ml-3" style="position: absolute !important; font-size: 0.625rem;">${!data.date_material ? 0 : 1}</i>
                <a href="javascript:;" <i id="${data.id_material}" class="mdi mdi-paperclip billRawMaterial" aria-hidden="true" data-toggle='tooltip' title='Adicionar Observaciones' style="font-size: 30px; color:orange;"></i></a>
                <a href="javascript:;" <i id="${data.id_material}" class="bx bx-edit-alt updateRawMaterials" data-toggle='tooltip' title='Actualizar Materia Prima' style="font-size: 30px;"></i></a>
                <a href="javascript:;"><img src="${icon}" alt="Eliminar Materia Prima" id="${data.id_material}" title='Eliminar Materia Prima' style="width:30px;height:30px;margin-top:-20px" onclick="deleteMaterials()"></a>
                ${flag_indirect == '1' ? `<a href="javascript:;" <i id="${data.id_material}" class="${data.flag_indirect == 0 ? 'bi bi-plus-square-fill' : 'bi bi-dash-square-fill'} indirect" data-toggle='tooltip' title='${data.flag_indirect == 0 ? 'Agregar' : 'Eliminar'} material indirecto' style="font-size:25px; color: #3e382c;"></i></a>` : ''}
                ${check}`;
          },
        },
      ],
    });
    // }
  };

  $(document).on('click', '.img', function () {
    var src = $(this).attr('src');
    $('<div>').css({
      background: 'RGBA(0,0,0,.5) url(' + src + ') no-repeat center',
      backgroundSize: 'contain',
      width: '100%', height: '100%',
      position: 'fixed',
      zIndex: '10000',
      top: '0', left: '0',
      cursor: 'zoom-out'
    }).click(function () {
      $(this).remove();
    }).appendTo('body');
  });

  loadAllData(1);
});
