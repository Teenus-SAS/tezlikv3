$(document).ready(function () {
  /* Cargue tabla de Materias Primas */
  allProductMaterials = [];
  allMaterials = [];
  visible = true;

  loadAllData = async () => {
    try {
      const [dataCategory, dataMaterials, dataProductMaterials] = await Promise.all([
        searchData('/api/categories'),
        searchData('/api/materials'),
        searchData('/api/allProductsMaterials')
      ]);

      if (dataCategory.length === 0) {
        $('.categories').hide();
        visible = false;
      } else {
        $('.categories').show();
        visible = true;
      }

      let $selectCategory = $(`#idCategory`);
      $selectCategory.empty();
      $selectCategory.append(`<option disabled selected value='0'>Seleccionar</option>`);
      $.each(dataCategory, function (i, value) {
        $selectCategory.append(
          `<option value="${value.id_category}">${value.category}</option>`
        );
      });

      loadTblCategories(dataCategory);
      loadTblRawMaterials(dataMaterials);
        
      allCategories = dataCategory;
      allMaterials = dataMaterials;
      allProductMaterials = dataProductMaterials;
    } catch (error) {
      console.error('Error loading data:', error);
    }
  }
  // loadAllDataMaterials = async () => {
  //   try {
  //     const [dataMaterials, dataProductMaterials] = await Promise.all([
  //       searchData('/api/materials'),
  //       searchData('/api/allProductsMaterials')
  //     ]);
  
  //     allMaterials = dataMaterials;
  //     allProductMaterials = dataProductMaterials;

  //     setTimeout(() => {
  //       loadTblRawMaterials(dataMaterials);
  //     }, 500);
  //   } catch (error) {
  //     console.error('Error loading data:', error);
  //   }
  // }

  // loadAllDataMaterials();

  loadTblRawMaterials = (data) => {
    tblRawMaterials = $('#tblRawMaterials').dataTable({
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
          data: null,
          className: 'classRight',
          render: function (data) {
            cost = parseFloat(data.cost);
            if (Math.abs(cost) < 0.01) { 
              cost = cost.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            } else
              cost = cost.toLocaleString('es-CO', { maximumFractionDigits: 2 }); 

            if (price_usd == '1' && plan_cost_price_usd == '1') {
              if (data.flag_usd == 0)
                text = `$ ${cost}`;
              else
                text = `<p style="color: #2eb92e;">$ ${cost}</p>`;
            } else
              text = `$ ${cost}`;

            return text;
          },
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

  loadAllData();
});
