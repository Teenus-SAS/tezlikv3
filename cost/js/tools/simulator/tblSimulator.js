$(document).ready(function () {
  /* Productos */
  loadTblSimulatorProducts = (data) => {
    tblSimulator = $('#tblSimulator').DataTable({
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
                <td style="width:400px">${d.product}</td>
            </tr>
            <tr>
                <th>Comision:</th>
                <td>
                  <input type="number" class="text-center form-control inputSimulator 0" id="commission_sale" value="${d.commission_sale}">
                </td>
            </tr>
            <tr>
                <th>Rentabilidad:</th>
                <td>
                  <input type="number" class="text-center form-control inputSimulator 0" id="profitability" value="${d.profitability}">
                </td>
            </tr>
        </table>`;
  }

  $(document).on('click', '.products', function () {
    var tr = $(this).closest('tr');
    var row = tblSimulator.row(tr);

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
    data = data.filter((item) => item.machine !== 'PROCESO MANUAL');
    data = data.filter(
      (obj, index, self) =>
        index === self.findIndex((o) => o.id_machine === obj.id_machine)
    );

    tblSimulator = $('#tblSimulator').DataTable({
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
            return `<input type="number" class="form-control text-center inputSimulator id_machine ${data.id_machine}" id="cost_machine" value="${data.cost_machine}">`;
          },
        },
      ],
    });
  };

  function formatMachines(d) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-15px;">
            <tr>
                <th>Valor Residual:</td>
                <td style="width:400px">
                  <input type="number" class="form-control text-center inputSimulator id_machine ${d.id_machine}" id="residual_value" value="${d.residual_value}"
                </td>
            </tr>
            <tr>
                <th>Años Depreciacion:</th>
                <td>
                  <input type="number" class="form-control text-center number inputSimulator id_machine ${d.id_machine}" id="years_depreciation" value="${d.years_depreciation}"
                </td>
            </tr>
            <tr>
                <th>Horas de Trabajo:</th>
                <td>
                  <input type="number" class="form-control text-center number inputSimulator id_machine ${d.id_machine}" id="hours_machine" value="${d.hours_machine}"
                </td>
            </tr>
            <tr>
                <th>Dias de Trabajo:</th>
                <td>
                  <input type="number" class="form-control text-center number inputSimulator id_machine ${d.id_machine}" id="days_machine" value="${d.days_machine}"
                </td>
            </tr>
        </table>`;
  }

  $(document).on('click', '.machines', function () {
    var tr = $(this).closest('tr');
    var row = tblSimulator.row(tr);

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
    tblSimulator = $('#tblSimulator').DataTable({
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
            return `<input type="number" class="form-control text-center inputSimulator id_material ${data.id_material}" id="cost_material" value="${data.cost_material}">`;
          },
        },
      ],
    });
  };

  function formatMaterials(d) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-15px;">
            <tr>
                <th>Material:</td>
                <td style="width:400px">${d.material}</td>
            </tr>
            <tr>
                <th>Unidad:</th>
                <td>${d.abbreviation_material}</td>
            </tr>
        </table>`;
  }

  $(document).on('click', '.materials', function () {
    var tr = $(this).closest('tr');
    var row = tblSimulator.row(tr);

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
    tblSimulator = $('#tblSimulator').DataTable({
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
                <td style="width:400px">${d.material}</td>
            </tr>
            <tr>
                <th>Unidad:</th>
                <td>${d.abbreviation_p_materials}</td>
            </tr>
            <tr>
                <th>Cantidad:</th>
                <td>
                  <input type="number" class="form-control text-center inputSimulator id_product_material ${d.id_product_material}" id="quantity" value="${d.quantity}">
                </td>
            </tr> 
        </table>`;
  }

  $(document).on('click', '.products_materials', function () {
    var tr = $(this).closest('tr');
    var row = tblSimulator.row(tr);

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
    tblSimulator = $('#tblSimulator').DataTable({
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
                <td style="width:400px">
                  <input type="number" class="form-control text-center inputSimulator id_product_process ${d.id_product_process}" id="enlistment_time" value="${d.enlistment_time}">
                </td>
            </tr> 
            <tr>
                <th>Tiempo de Operacion:</th>
                <td>
                  <input type="number" class="form-control text-center inputSimulator id_product_process ${d.id_product_process}" id="operation_time" value="${d.operation_time}">
                </td>
            </tr> 
        </table>`;
  }

  $(document).on('click', '.products_process', function () {
    var tr = $(this).closest('tr');
    var row = tblSimulator.row(tr);

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
    tblSimulator = $('#tblSimulator').DataTable({
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
            return `<input type="number" class="text-center form-control inputSimulator id_manufacturing_load ${data.id_manufacturing_load}" id="cost" value="${data.cost}">`;
          },
        },
      ],
    });
  };

  function formatFactoryLoad(d) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-15px;"> 
            <tr>
                <th>Descripcion:</th>
                <td style="width:400px">
                  ${d.input}
                </td>
            </tr>
        </table>`;
  }

  $(document).on('click', '.factory_load', function () {
    var tr = $(this).closest('tr');
    var row = tblSimulator.row(tr);

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
    tblSimulator = $('#tblSimulator').DataTable({
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
          title: 'No.',
          data: null,
          className: 'uniqueClassName',
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
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
            return `<input type="number" class="text-center form-control inputSimulator id_service ${data.id_service}" id="cost" value="${data.cost}">`;
          },
        },
      ],
    });
  };

  /* Nomina */
  loadTblSimulatorPayroll = (data) => {
    tblSimulator = $('#tblSimulator').DataTable({
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
          className: 'uniqueClassName payroll',
        },
        {
          title: 'Salario',
          data: null,
          className: 'uniqueClassName payroll',
          render: function (data) {
            return `<input type="number" class="text-center form-control inputSimulator basicSalary id_payroll ${data.id_payroll}" id="salary" value="${data.salary}">`;
          },
        },
      ],
    });
  };

  function formatPayroll(d) {
    let optionsRisk = `
      <option value="1" ${d.id_risk == '1' ? 'selected' : ''}>CLASE I</option>
      <option value="2" ${d.id_risk == '2' ? 'selected' : ''}>CLASE II</option>
      <option value="3" ${d.id_risk == '3' ? 'selected' : ''}>CLASE III</option>
      <option value="4" ${d.id_risk == '4' ? 'selected' : ''}>CLASE IV</option>
      <option value="5" ${d.id_risk == '5' ? 'selected' : ''}>CLASE V</option>
      <option value="6" ${d.id_risk == '6' ? 'selected' : ''}>CLASE VI</option>
    `;

    let optionsFactor = `
    <option value="1" ${
      d.type_contract === 'Nomina' ? 'selected' : ''
    }>Nómina</option>
    <option value="2" ${
      d.type_contract === 'Servicios' ? 'selected' : ''
    }>Servicios</option>
    <option value="3" ${
      d.type_contract === 'Manual' ? 'selected' : ''
    }>Cálculo Manual</option>
    `;

    sessionStorage.setItem('percentage', d.percentage);
    sessionStorage.setItem('salary', d.salary);

    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-15px;"> 
            <tr>
                <th>Proceso:</th>
                <td style="width:400px">
                  ${d.process}
                </td>
            </tr>
            <tr>
                <th>Transporte:</th>
                <td>
                  <input type="number" class="text-center form-control inputSimulator id_payroll ${d.id_payroll}" id="transport" value="${d.transport}">
                </td>
            </tr>
            <tr>
                <th>Dotaciones:</th>
                <td>
                  <input type="number" class="text-center form-control inputSimulator id_payroll ${d.id_payroll}" id="endowment" value="${d.endowment}">
                </td>
            </tr>
            <tr>
                <th>Horas Extras:</th>
                <td>
                  <input type="number" class="text-center form-control inputSimulator id_payroll ${d.id_payroll}" id="extra_time" value="${d.extra_time}">
                </td>
            </tr>
            <tr>
                <th>Otros Ingresos:</th>
                <td>
                  <input type="number" class="text-center form-control inputSimulator id_payroll ${d.id_payroll}" id="bonification" value="${d.bonification}">
                </td>
            </tr>
            <tr>
                <th>Horas Trabajo x Día:</th>
                <td>
                  <input type="number" class="text-center form-control inputSimulator workingHoursDay id_payroll ${d.id_payroll}" id="hours_day" value="${d.hours_day}">
                </td>
            </tr>
            <tr>
                <th>Dias Trabajo x Mes:</th>
                <td>
                  <input type="number" class="text-center form-control inputSimulator workingDaysMonth id_payroll ${d.id_payroll}" id="working_days_month" value="${d.working_days_month}">
                </td>
            </tr>
            <tr>
              <th>Riesgo:</th>
              <td>
                <select id="risk" name="risk" class="form-control ${d.id_payroll}">
                  <option disabled value="0">Seleccionar</option>
                  ${optionsRisk}
                </select>
              </td>
            </tr>
            <tr>
              <th></th>
              <td>
                <input type="text" class="form-control text-center inputSimulator valueRisk id_payroll ${d.id_payroll}" value="${d.percentage}" id="percentage" readonly>
              </td>
            </tr>
            <tr>
                <th>Tipo Nómina:</th>
                <td>
                  <select class="form-control inputSimulator typeFactor id_payroll ${d.id_payroll}" id="type_contract">
                    <option disabled value="0">Seleccionar</option>
                    ${optionsFactor}
                  </select>
                </td>
            </tr>
            <tr>
                <th>Factor:</th>
                <td>
                  <input type="number" class="text-center form-control inputSimulator factor id_payroll ${d.id_payroll}" id="factor_benefit">
                </td>
            </tr>
        </table>`;
  }

  $(document).on('click', '.payroll', function () {
    var tr = $(this).closest('tr');
    var row = tblSimulator.row(tr);

    if (row.child.isShown()) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    } else {
      // Open this row
      row.child(formatPayroll(row.data())).show();
      tr.addClass('shown');
      $('.typeFactor').change();
      sessionStorage.removeItem('type_salary');
    }
  });

  /* Distribucion de Gastos */
  loadTblSimulatorDistribution = (data) => {
    let status = false;
    for (let i = 0; i < data.length; i++) {
      if (data[i].id_product == dataSimulator.products[0].id_product)
        status = false;
    }

    if (status == true)
      tblSimulator = $('#tblSimulator').DataTable({
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
            title: 'No.',
            data: null,
            className: 'uniqueClassName',
            render: function (data, type, full, meta) {
              return meta.row + 1;
            },
          },
          {
            title: 'Unidades Vendidas',
            data: null,
            className: 'uniqueClassName',
            render: function (data) {
              return `<input type="number" class="text-center form-control inputSimulator id_expenses_distribution ${data.id_expenses_distribution}" id="units_sold" value="${data.units_sold}">`;
            },
          },
          {
            title: 'Vol de Ventas',
            data: null,
            className: 'uniqueClassName',
            render: function (data) {
              return `<input type="number" class="text-center form-control inputSimulator id_expenses_distribution ${data.id_expenses_distribution}" id="turnover" value="${data.turnover}">`;
            },
          },
        ],
      });
  };

  /* Recuperacion de Gastos */
  loadTblSimulatorRecover = (data) => {
    tblSimulator = $('#tblSimulator').DataTable({
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
          title: 'No.',
          data: null,
          className: 'uniqueClassName',
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
        },
        {
          title: 'Porcentaje',
          data: null,
          className: 'uniqueClassName',
          render: function (data) {
            return `<input type="number" class="text-center form-control inputSimulator 0" id="expense_recover" value="${data.expense_recover}">`;
          },
        },
      ],
    });
  };
});
