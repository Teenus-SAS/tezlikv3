$(document).ready(function () {
  /* Seleccion producto */
  $('#refProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    $('#selectNameProduct option').removeAttr('selected');
    $(`#selectNameProduct option[value=${id}]`).prop('selected', true);
    getDataMaterials(id);
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    $('#refProduct option').removeAttr('selected');
    $(`#refProduct option[value=${id}]`).prop('selected', true);
    getDataMaterials(id);
  });

  /* Cargar datos de proyectos */
  getDataMaterials = async (id) => {
    //let data = await searchData(`/api/rawMaterials/${id}`);
    // Enviar datos al servidor
    const response = await fetch(`/api/rawMaterials/${id}`);
    const data = await response.json();


    $('.colMaterials').empty();

    if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
    else {
      loadtableMaterials(data['allRawMaterials']);
      loadTableRawMaterialsAnalisys(data['80RawMaterials']);
    }
  };

  /* Cargue tabla de Proyectos */
  const loadtableMaterials = async (data) => {
    if ($.fn.DataTable.isDataTable('#tblMaterials')) {
      tblMaterials.DataTable().clear().rows.add(data).draw();
    } else {
      tblMaterials = $('#tblMaterials').dataTable({
        destroy: true,
        pageLength: 50,
        data: data,
        order: [[6, 'desc']],
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
            data: null,
            className: 'uniqueClassName',
            render: function (data) {
              data.abbreviation_material != data.abbreviation_product_material
                ? (cost = data.cost_product_material)
                : (cost = data.cost);

              return data.cost.toLocaleString('es-CO', {
                maximumFractionDigits: 0,
              });
            },
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
        footerCallback: function (row, data, start, end, display) {
          total = this.api()
            .column(5)
            .data()
            .reduce(function (a, b) {
              return parseInt(a) + parseInt(b);
            }, 0);

          $(this.api().column(5).footer()).html(
            new Intl.NumberFormat('en-US', {
              style: 'currency',
              currency: 'USD',
            }).format(total)
          );
          subTotal = this.api()
            .column(6)
            .data()
            .reduce(function (a, b) {
              return a + b;
            }, 0);

          $(this.api().column(6).footer()).html(`${subTotal.toFixed(0)} %`);
        },
      });
    }
  };
});
