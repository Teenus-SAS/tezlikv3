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
  };

  function formatProducts(d) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-15px;">
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
  };

  function formatMachines(d) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-15px;">
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
  };

  function formatMaterials(d) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-15px;">
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
  };

  function formatProductsMaterials(d) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-15px;">
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
  };

  function formatProductsProcess(d) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-15px;"> 
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
  };

  function formatFactoryLoad(d) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-15px;"> 
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
    tblSimulatorServices = $('#tblSimulatorServices').DataTable({
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
    });
  };

  /* Nomina */
  loadTblSimulatorPayroll = (data) => {
    tblSimulatorPayroll = $('#tblSimulatorPayroll').DataTable({
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
          className: 'dt-control payroll',
          orderable: false,
          data: null,
          defaultContent: '',
        },
        {
          title: 'Empleado',
          data: 'employee',
          className: 'uniqueClassName',
        },
        {
          title: 'Salario',
          data: null,
          className: 'uniqueClassName',
          render: function (data) {
            return `<input type="number" class="text-center form-control" value="${data.salary}">`;
          },
        },
      ],
    });
  };

  function formatPayroll(d) {
    let options;
    if (d.type_contract == 'Nomina') {
      options = `<option selected value="1">Nómina</option>
                 <option value="2">Servicios</option>
                 <option value="3">Calculo Manual</option>`;
    } else if (d.type_contract == 'Servicios') {
      options = `<option value="1">Nómina</option>
                 <option selected value="2">Servicios</option>
                 <option value="3">Calculo Manual</option>`;
    } else if (d.type_contract == 'Manual') {
      options = `<option value="1">Nómina</option>
                 <option value="2">Servicios</option>
                 <option selected value="3">Calculo Manual</option>`;
    }

    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-15px;"> 
            <tr>
                <th>Proceso:</th>
                <td>
                  ${d.process}
                </td>
            </tr>
            <tr>
                <th>Transporte:</th>
                <td>
                  <input type="number" class="text-center form-control" value="${d.transport}">
                </td>
            </tr>
            <tr>
                <th>Dotaciones:</th>
                <td>
                  <input type="number" class="text-center form-control" value="${d.endowment}">
                </td>
            </tr>
            <tr>
                <th>Horas Extras:</th>
                <td>
                  <input type="number" class="text-center form-control" value="${d.extra_time}">
                </td>
            </tr>
            <tr>
                <th>Otros Ingresos:</th>
                <td>
                  <input type="number" class="text-center form-control" value="${d.bonification}">
                </td>
            </tr>
            <tr>
                <th>Horas Trabajo x Día:</th>
                <td>
                  <input type="number" class="text-center form-control" value="${d.hours_day}">
                </td>
            </tr>
            <tr>
                <th>Dias Trabajo x Mes:</th>
                <td>
                  <input type="number" class="text-center form-control" value="${d.working_days_month}">
                </td>
            </tr>
            <tr>
                <th>Tipo Nómina:</th>
                <td>
                  <select id="typeFactor" name="typeFactor" type="number" class="form-control">
                    <option disabled value="0">Seleccionar</option>
                    ${options}
                  </select>
                </td>
            </tr>
            <tr>
                <th>Factor:</th>
                <td>
                  <input type="number" class="text-center form-control" value="${d.factor_benefit}">
                </td>
            </tr>
        </table>`;
  }

  $(document).on('click', '.payroll', function () {
    var tr = $(this).closest('tr');
    var row = tblSimulatorPayroll.row(tr);

    if (row.child.isShown()) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    } else {
      // Open this row
      row.child(formatPayroll(row.data())).show();
      tr.addClass('shown');
    }
  });

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
  };

  /* Recuperacion de Gastos */
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
  };
});
