$(document).ready(function () {
  /* Cargue tabla de Materias Primas */
  let visibleCost = true;
  export_import == '0' || flag_export_import == '0' ? visibleCost = false : visibleCost;

  loadAllData = async (op) => {
    try {
      // Definir las promesas basadas en la opción
      const promises = [
        op === 3 || op === 1 ? searchData('/api/categories') : Promise.resolve(null),
        searchData('/api/materials'),
        op === 1 ? searchData('/api/productsMaterialsBasic') : Promise.resolve(null)
      ];

      // Esperar a que todas las promesas se resuelvan en paralelo
      const [dataCategory, dataMaterials, dataProductMaterials] = await Promise.all(promises);

      // Almacenar los materiales en sessionStorage
      sessionStorage.setItem('dataMaterials', JSON.stringify(dataMaterials));

      if (op === 1 && dataCategory) {
        const $selectCategory = $('#idCategory');
        $selectCategory.empty();
        $selectCategory.append(`<option disabled selected value='0'>Seleccionar</option>`);
        dataCategory.forEach(value => {
          $selectCategory.append(
            `<option value="${value.id_category}">${value.category}</option>`
          );
        });

        sessionStorage.setItem('dataCategory', JSON.stringify(dataCategory));
        loadTblCategories(dataCategory);
        sessionStorage.setItem('dataProductMaterials', JSON.stringify(dataProductMaterials));
      }

      // Obtener categorías de sessionStorage si no se cargaron en esta llamada
      const dataCategory1 = op === 1 ? dataCategory : JSON.parse(sessionStorage.getItem('dataCategory'));

      // Mostrar o esconder categorías basadas en la disponibilidad de datos
      const visible = dataCategory1 && dataCategory1.length > 0;
      $('.categories').toggle(visible);

      // Determinar la opción de precios basada en la bandera de moneda USD
      let op1 = 1;
      if (flag_currency_usd === '1') {
        const selectPriceUSD = $('#selectPriceUSD').val();
        op1 = selectPriceUSD === '2' ? 2 : 1;
      }

      // Cargar la tabla de materias primas
      loadTblRawMaterials(dataMaterials, visible, op1);
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  loadTblRawMaterials = (data, visible, op) => {
    const tableId = '#tblRawMaterials';

    // Función para formatear los costos
    const renderCost = (data, op) => {
      let cost = parseFloat(data);
      const options = Math.abs(cost) < 0.01
        ? { minimumFractionDigits: 2, maximumFractionDigits: 9 }
        : op == 2 ? { minimumFractionDigits: 2, maximumFractionDigits: 2 }
          : { minimumFractionDigits: 0, maximumFractionDigits: 0 };

      cost = cost.toLocaleString('es-CO', options);
      return `$ ${cost}`;
    };

    // Preprocesar los datos para evitar cálculos repetidos en las funciones de renderizado
    const preprocessedData = data.map(row => ({
      ...row,
      price: flag_currency_usd == '1' && op == 2 ? parseFloat(row.cost_usd) : row.cost,
      costImport: flag_currency_usd == '1' && op == 2 ? parseFloat(row.cost_import_usd) : row.cost_import,
      costExport: flag_currency_usd == '1' && op == 2 ? parseFloat(row.cost_export_usd) : row.cost_export,
      total: flag_currency_usd == '1' && op == 2
        ? parseFloat(row.cost_usd) + parseFloat(row.cost_import_usd) + parseFloat(row.cost_export_usd)
        : row.cost_total,
    }));

    const renderActions = (data) => {
      const icon = parseInt(data.status) === 0
        ? '/public/assets/images/trash_v.png'
        : '/public/assets/images/trash_x.png';

      const check = parseInt(data.status) !== 0
        ? `<a href="javascript:;" <i id="${data.id_material}" class="mdi mdi-playlist-check seeDetailMaterials" data-toggle='tooltip' title='Ver Detalle' style="font-size: 30px; color:black;"></i></a>`
        : '';

      return `
      <i class="badge badge-danger badge-pill ml-3" style="position: absolute !important; font-size: 0.625rem;">${!data.date_material ? 0 : 1}</i>
      <a href="javascript:;" <i id="${data.id_material}" class="mdi mdi-paperclip billRawMaterial" aria-hidden="true" data-toggle='tooltip' title='Adicionar Observaciones' style="font-size: 30px; color:orange;"></i></a>
      <a href="javascript:;" <i id="${data.id_material}" class="bx bx-edit-alt updateRawMaterials" data-toggle='tooltip' title='Actualizar Materia Prima' style="font-size: 30px;"></i></a>
      <a href="javascript:;"><img src="${icon}" alt="Eliminar Materia Prima" id="${data.id_material}" title='Eliminar Materia Prima' style="width:30px;height:30px;margin-top:-20px" onclick="deleteMaterials()"></a>
      ${flag_indirect == '1' ? `<a href="javascript:;" <i id="${data.id_material}" class="${data.flag_indirect == 0 ? 'bi bi-plus-square-fill' : 'bi bi-dash-square-fill'} indirect" data-toggle='tooltip' title='${data.flag_indirect == 0 ? 'Agregar' : 'Eliminar'} material indirecto' style="font-size:25px; color: #3e382c;"></i></a>` : ''}
      ${check}`;
    };

    const columns = [
      {
        title: 'No.',
        data: null,
        className: 'uniqueClassName',
        render: (data, type, full, meta) => meta.row + 1,
      },
      { title: 'Referencia', data: 'reference', className: 'uniqueClassName' },
      { title: 'Materia Prima', data: 'material', className: 'uniqueClassName' },
      { title: 'Categoria', data: 'category', className: 'classCenter', visible: visible },
      { title: 'Unidad', data: 'abbreviation', className: 'classCenter' },
      {
        width: '80px',
        title: 'Precio',
        data: 'price',
        className: 'classRight',
        render: (data) => renderCost(data, op),
      },
    ];

    if (visibleCost == true) {
      columns.push(
        {
          title: 'Costo Importacion',
          data: row => flag_currency_usd === '1' && op === 2 ? parseFloat(row.cost_import_usd) : row.cost_import,
          className: 'classRight',
          render: (data, type, row) => renderCost(data, op),
        },
        {
          title: 'Costo Nacionalizacion',
          data: row => flag_currency_usd === '1' && op === 2 ? parseFloat(row.cost_export_usd) : row.cost_export,
          className: 'classRight',
          render: (data, type, row) => renderCost(data, op),
        },
        {
          title: 'Total',
          data: row => flag_currency_usd === '1' && op === 2 ? parseFloat(row.cost_usd) + parseFloat(row.cost_import_usd) + parseFloat(row.cost_export_usd) : row.cost_total,
          className: 'classRight',
          render: (data, type, row) => renderCost(data, op),
        }
      );
    }

    columns.push({
      title: 'Acciones',
      data: null,
      className: 'uniqueClassName',
      render: data => renderActions(data),
    });

    tblRawMaterials = $(tableId).dataTable({
      destroy: true,
      pageLength: 50,
      data: preprocessedData,
      dom: '<"datatable-error-console">frtip',
      language: {
        url: '/assets/plugins/i18n/Spanish.json',
      },
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
      deferRender: true, // Mejorar rendimiento para tablas grandes
      fnInfoCallback: (oSettings, iStart, iEnd, iMax, iTotal, sPre) => {
        if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
          console.error(oSettings.json.error);
        }
      },
      columns: columns,
    });
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
