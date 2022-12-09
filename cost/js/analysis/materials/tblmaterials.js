$(document).ready(function () {
  /* Seleccion producto */
  $('#refProduct').change(function (e) {
    e.preventDefault();
    id = this.value;
    $('#selectNameProduct option').removeAttr('selected');
    $(`#selectNameProduct option[value=${id}]`).prop('selected', true);
    getDataMaterials(id);
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    id = this.value;
    $('#refProduct option').removeAttr('selected');
    $(`#refProduct option[value=${id}]`).prop('selected', true);
    getDataMaterials(id);
  });

  /* Cargar datos de proyectos */
  getDataMaterials = async (id) => {
    data = await searchData(`/api/rawMaterials/${id}`);

    if ($.fn.dataTable.isDataTable('#tblMaterials')) {
      $('#tblMaterials').DataTable().destroy();
      $('#tblMaterials').empty();
    }

    $('.colMaterials').empty();

    if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
    else {
      loadtableMaterials(data);
      loadTableRawMaterialsAnalisys(data);
    }
  };

  /* Cargue tabla de Proyectos */
  const loadtableMaterials = async (data) => {
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
          data: 'reference',
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
          render: $.fn.dataTable.render.number(',', '.', 2, '', '%'),
        },
      ],

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
  };
});
