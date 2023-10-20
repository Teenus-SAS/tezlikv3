$(document).ready(function () {
  /* Seleccion producto */
  $('#refProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    $('#selectNameProduct option').removeAttr('selected');
    $(`#selectNameProduct option[value=${id}]`).prop('selected', true);
    loadtableMaterials(id);
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    $('#refProduct option').removeAttr('selected');
    $(`#refProduct option[value=${id}]`).prop('selected', true);
    loadtableMaterials(id);
  });

  /* Cargue tabla de Proyectos */

  loadtableMaterials = (idProduct) => {
    if ($.fn.dataTable.isDataTable('#tblConfigMaterials')) {
      $('#tblConfigMaterials').DataTable().destroy();
      $('#tblConfigMaterials').empty();
    }
    
    tblConfigMaterials = $('#tblConfigMaterials').dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: `/api/productsMaterials/${idProduct}`,
        dataSrc: '',
      },
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
            let decimals = contarDecimales(data);
            let quantity = formatNumber(data, decimals);

            return quantity;
          },
        },
        {
          title: 'Precio Unitario',
          data: 'cost_product_material',
          className: 'classCenter',
          render: function (data) {
            let decimals = contarDecimales(data);
            let cost = formatNumber(data, decimals);

            return `$ ${cost}`;
          },
        },
        {
          title: 'Acciones',
          data: 'id_product_material',
          className: 'uniqueClassName',
          render: function (data) {
            return `<a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateMaterials" data-toggle='tooltip' title='Actualizar Materia Prima' style="font-size: 30px;"></i></a>
                        <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Materia Prima' style="font-size: 30px;color:red" onclick="deleteFunction(1)"></i></a>`;
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
});
