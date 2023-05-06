$(document).ready(function () {
  /* Productos */
  loadTblSimulatorProducts = (data) => {
    tblSimulatorProducts = $('#tblSimulatorProducts').DataTable({
      destroy: true,
      scrollY: '150px',
      scrollCollapse: true,
      paging: false,
      data: data,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      },
      columns: [
        {
          className: 'dt-control products',
          orderable: false,
          data: null,
          defaultContent: '',
        },
        {
          title: 'Referencia',
          data: 'reference',
          className: 'uniqueClassName',
        },
        {
          title: 'Costo',
          data: 'price',
          className: 'uniqueClassName',
          render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
        },
      ],
    });

    if (data.length > 0)
      setInterval(() => {
        let tables = document.getElementsByClassName(
          'dataTables_scrollHeadInner'
        );

        let attr = tables[0].firstElementChild;
        attr.style.width = '380px';

        // table = document.getElementById('tblSimulatorProducts_wrapper');
        // atrr = table.firstElementChild.firstElementChild
      }, 1000);
  };

  function formatProducts(d) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-10px;">
            <tr>
                <th>Producto:</td>
                <td>${d.product}</td>
            </tr>
            <tr>
                <th>Comision:</th>
                <td>
                  <input type="number" class="text-center form-control" value="${d.commission_sale}">
                </td>
            </tr>
            <tr>
                <th>Rentabilidad:</th>
                <td>
                  <input type="number" class="text-center form-control" value="${d.profitability}">
                </td>
            </tr>
        </table>`;
  }

  $(document).on('click', '.products', function () {
    var tr = $(this).closest('tr');
    var row = tblSimulatorProducts.row(tr);

    if (row.child.isShown()) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    } else {
      // Open this row
      row.child(formatProducts(row.data())).show();
      tr.addClass('shown');
    }
  });

  /* Maquinas */
  loadTblSimulatorMachines = (data) => {
    tblSimulatorMachines = $('#tblSimulatorMachines').DataTable({
      destroy: true,
      scrollY: '150px',
      scrollCollapse: true,
      paging: false,
      data: data,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      },
      columns: [
        {
          className: 'dt-control machines',
          orderable: false,
          data: null,
          defaultContent: '',
        },
        {
          title: 'Maquina',
          data: 'machine',
          className: 'uniqueClassName',
        },
        {
          title: 'Costo',
          data: null,
          className: 'uniqueClassName',
          render: function (data) {
            return `<input type="number" class="form-control text-center" value="${data.cost_machine}">`;
          },
        },
      ],
    });

    if (data.length > 0)
      setInterval(() => {
        let tables = document.getElementsByClassName(
          'dataTables_scrollHeadInner'
        );

        let attr = tables[1].firstElementChild;
        attr.style.width = '380px';
      }, 1000);
  };

  function formatMachines(d) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-10px;">
            <tr>
                <th>Valor Residual:</td>
                <td>
                  <input type="number" class="form-control text-center" value="${d.residual_value}"
                </td>
            </tr>
            <tr>
                <th>Años Depreciacion:</th>
                <td>
                  <input type="number" class="form-control text-center number" value="${d.years_depreciation}"
                </td>
            </tr>
            <tr>
                <th>Horas de Trabajo:</th>
                <td>
                  <input type="number" class="form-control text-center number" value="${d.hours_machine}"
                </td>
            </tr>
            <tr>
                <th>Dias de Trabajo:</th>
                <td>
                  <input type="number" class="form-control text-center number" value="${d.days_machine}"
                </td>
            </tr>
        </table>`;
  }

  $(document).on('click', '.machines', function () {
    var tr = $(this).closest('tr');
    var row = tblSimulatorMachines.row(tr);

    if (row.child.isShown()) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    } else {
      // Open this row
      row.child(formatMachines(row.data())).show();
      tr.addClass('shown');
    }
  });

  /* Materia Prima */
  loadTblSimulatorMaterials = (data) => {
    tblSimulatorMaterials = $('#tblSimulatorMaterials').DataTable({
      destroy: true,
      scrollY: '150px',
      scrollCollapse: true,
      paging: false,
      data: data,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      },
      columns: [
        {
          className: 'dt-control materials',
          orderable: false,
          data: null,
          defaultContent: '',
        },
        {
          title: 'Referencia',
          data: 'reference',
          className: 'uniqueClassName',
        },
        {
          title: 'Costo',
          data: null,
          className: 'uniqueClassName',
          render: function (data) {
            return `<input type="number" class="form-control text-center" value="${data.cost_material}">`;
          },
        },
      ],
    });

    if (data.length > 0)
      setInterval(() => {
        let tables = document.getElementsByClassName(
          'dataTables_scrollHeadInner'
        );

        let attr = tables[2].firstElementChild;
        attr.style.width = '380px';
      }, 1000);
  };

  function formatMaterials(d) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-10px;">
            <tr>
                <th>Material:</td>
                <td>${d.material}</td>
            </tr>
            <tr>
                <th>Unidad:</th>
                <td>${d.abbreviation_material}</td>
            </tr>
        </table>`;
  }

  $(document).on('click', '.materials', function () {
    var tr = $(this).closest('tr');
    var row = tblSimulatorMaterials.row(tr);

    if (row.child.isShown()) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    } else {
      // Open this row
      row.child(formatMaterials(row.data())).show();
      tr.addClass('shown');
    }
  });

  /* Ficha Tecnica Materiales */
  loadTblSimulatorProductsMaterials = (data) => {
    tblSimulatorProductsMaterials = $(
      '#tblSimulatorProductsMaterials'
    ).DataTable({
      destroy: true,
      scrollY: '150px',
      scrollCollapse: true,
      paging: false,
      data: data,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      },
      columns: [
        {
          className: 'dt-control products_materials',
          orderable: false,
          data: null,
          defaultContent: '',
        },
        {
          title: 'Referencia',
          data: 'reference',
          className: 'uniqueClassName',
        },
        {
          title: 'Costo',
          data: 'cost_product_materials',
          className: 'uniqueClassName',
          render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
        },
      ],
    });

    if (data.length > 0)
      setInterval(() => {
        let tables = document.getElementsByClassName(
          'dataTables_scrollHeadInner'
        );

        let attr = tables[3].firstElementChild;
        attr.style.width = '380px';
      }, 1000);
  };

  function formatProductsMaterials(d) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-10px;">
            <tr>
                <th>Material:</td>
                <td>${d.material}</td>
            </tr>
            <tr>
                <th>Unidad:</th>
                <td>${d.abbreviation_p_materials}</td>
            </tr>
            <tr>
                <th>Cantidad:</th>
                <td>
                  <input type="number" class="form-control text-center" value="${d.quantity}">
                </td>
            </tr> 
        </table>`;
  }

  $(document).on('click', '.products_materials', function () {
    var tr = $(this).closest('tr');
    var row = tblSimulatorProductsMaterials.row(tr);

    if (row.child.isShown()) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    } else {
      // Open this row
      row.child(formatProductsMaterials(row.data())).show();
      tr.addClass('shown');
    }
  });

  /* Ficha Tecnica Procesos */
  loadTblSimulatorProductsProcess = (data) => {
    tblSimulatorProductsProcess = $('#tblSimulatorProductsProcess').DataTable({
      destroy: true,
      scrollY: '150px',
      scrollCollapse: true,
      paging: false,
      data: data,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      },
      columns: [
        {
          className: 'dt-control products_process',
          orderable: false,
          data: null,
          defaultContent: '',
        },
        {
          title: 'Proceso',
          data: 'process',
          className: 'uniqueClassName',
        },
        {
          title: 'Maquina',
          data: 'machine',
          className: 'uniqueClassName',
        },
      ],
    });

    if (data.length > 0)
      setInterval(() => {
        let tables = document.getElementsByClassName(
          'dataTables_scrollHeadInner'
        );

        let attr = tables[4].firstElementChild;
        attr.style.width = '380px';
      }, 1000);
  };

  function formatProductsProcess(d) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-10px;"> 
            <tr>
                <th>Tiempo de Enlistamiento:</th>
                <td>
                  <input type="number" class="form-control text-center" value="${d.enlistment_time}">
                </td>
            </tr> 
            <tr>
                <th>Tiempo de Operacion:</th>
                <td>
                  <input type="number" class="form-control text-center" value="${d.operation_time}">
                </td>
            </tr> 
        </table>`;
  }

  $(document).on('click', '.products_process', function () {
    var tr = $(this).closest('tr');
    var row = tblSimulatorProductsProcess.row(tr);

    if (row.child.isShown()) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    } else {
      // Open this row
      row.child(formatProductsProcess(row.data())).show();
      tr.addClass('shown');
    }
  });

  /* Carga Fabril */
  loadTblSimulatorFactoryLoad = (data) => {
    tblSimulatorFactoryLoad = $('#tblSimulatorFactoryLoad').DataTable({
      destroy: true,
      scrollY: '150px',
      scrollCollapse: true,
      paging: false,
      data: data,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      },
      columns: [
        {
          className: 'dt-control factory_load',
          orderable: false,
          data: null,
          defaultContent: '',
        },
        {
          title: 'Maquina',
          data: 'machine',
          className: 'uniqueClassName',
        },
        {
          title: 'Precio',
          data: null,
          className: 'uniqueClassName',
          render: function (data) {
            return `<input type="number" class="text-center form-control" value="${data.cost}">`;
          },
        },
      ],
    });

    if (data.length > 0)
      setInterval(() => {
        let tables = document.getElementsByClassName(
          'dataTables_scrollHeadInner'
        );

        let attr = tables[5].firstElementChild;
        attr.style.width = '380px';
      }, 1000);
  };

  function formatFactoryLoad(d) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-10px;"> 
            <tr>
                <th>Descripcion:</th>
                <td>
                  ${d.input}
                </td>
            </tr>
        </table>`;
  }

  $(document).on('click', '.factory_load', function () {
    var tr = $(this).closest('tr');
    var row = tblSimulatorFactoryLoad.row(tr);

    if (row.child.isShown()) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    } else {
      // Open this row
      row.child(formatFactoryLoad(row.data())).show();
      tr.addClass('shown');
    }
  });

  /* Servicios Externos */
  loadTblSimulatorExternalServices = (data) => {
    tblSimulatorExternalServices = $('#tblSimulatorExternalServices').DataTable(
      {
        destroy: true,
        scrollY: '150px',
        scrollCollapse: true,
        paging: false,
        data: data,
        language: {
          url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
        },
        columns: [
          {
            className: 'dt-control external_services',
            orderable: false,
            data: null,
            defaultContent: '',
          },
          {
            title: 'Servicio',
            data: 'name_service',
            className: 'uniqueClassName',
          },
          {
            title: 'Costo',
            data: null,
            className: 'uniqueClassName',
            render: function (data) {
              return `<input type="number" class="text-center form-control" value="${data.cost}">`;
            },
          },
        ],
      }
    );

    if (data.length > 0)
      setInterval(() => {
        let tables = document.getElementsByClassName(
          'dataTables_scrollHeadInner'
        );

        let attr = tables[6].firstElementChild;
        attr.style.width = '380px';
      }, 1000);
  };

  /* Nomina */

  /* Distribucion de Gastos */
  loadTblSimulatorDistribution = (data) => {
    tblSimulatorDistribution = $('#tblSimulatorExpensesDistribution').DataTable(
      {
        destroy: true,
        scrollY: '150px',
        scrollCollapse: true,
        paging: false,
        data: data,
        language: {
          url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
        },
        columns: [
          {
            className: 'dt-control',
            orderable: false,
            data: null,
            defaultContent: '',
          },
          {
            title: 'Unidades Vendidas',
            data: null,
            className: 'uniqueClassName',
            render: function (data) {
              return `<input type="number" class="text-center form-control" value="${data.units_sold}">`;
            },
          },
          {
            title: 'Vol de Ventas',
            data: null,
            className: 'uniqueClassName',
            render: function (data) {
              return `<input type="number" class="text-center form-control" value="${data.turnover}">`;
            },
          },
        ],
      }
    );

    if (data.length > 0)
      setInterval(() => {
        let tables = document.getElementsByClassName(
          'dataTables_scrollHeadInner'
        );

        let attr = tables[7].firstElementChild;
        attr.style.width = '380px';
      }, 1000);
  };

  /* Recuperacion de Gastos */ tblSimulatorExpensesRecover;
  loadTblSimulatorRecover = (data) => {
    tblSimulatorExpensesRecover = $('#tblSimulatorExpensesRecover').DataTable({
      destroy: true,
      scrollY: '150px',
      scrollCollapse: true,
      paging: false,
      data: data,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      },
      columns: [
        {
          className: 'dt-control',
          orderable: false,
          data: null,
          defaultContent: '',
        },
        {
          title: 'Porcentaje',
          data: null,
          className: 'uniqueClassName',
          render: function (data) {
            return `<input type="number" class="text-center form-control" value="${data.expense_recover}">`;
          },
        },
      ],
    });

    if (data.length > 0)
      setInterval(() => {
        let tables = document.getElementsByClassName(
          'dataTables_scrollHeadInner'
        );

        let attr = tables[8].firstElementChild;
        attr.style.width = '380px';
      }, 1000);
  };
});
