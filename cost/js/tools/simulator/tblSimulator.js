$(document).ready(function () {
  /* Productos */
  loadTblSimulatorProducts = (data) => {
    tblSimulator = $('#tblSimulator').DataTable({
      destroy: true,
      scrollY: '150px',
      scrollCollapse: true,
      paging: false,
      data: data,
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
    });

    setTimeout(() => {
      let products = document.getElementsByClassName('dt-control');
      var tr = $(products[2]).closest('tr');
      var row = tblSimulator.row(tr);
      row.child(formatProducts(row.data())).show();
      tr.addClass('shown');

      $('.dataTables_filter').css('display', 'none');
      $('#tblSimulator_info').css('display', 'none');

      let dataTables_scrollBody = document.getElementsByClassName(
        'dataTables_scrollBody'
      )[0];
      dataTables_scrollBody.style = '';
      dataTables_scrollBody.style.position = 'relative';
      dataTables_scrollBody.style.maxHeight = '300px';
      dataTables_scrollBody.style.width = '100%';
    }, 1000);
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
          render: function (data, type, full, meta) {
            return `<input type="number" class="form-control text-center inputSimulator id_machine ${data.id_machine
              }" id="cost_machine-${meta.row + 1
              }" value="${data.cost_machine.toLocaleString('es-CO')}">`;
          },
        },
      ],
    });
  };

  function formatMachines(d, row) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-15px;">
            <tr>
                <th>Valor Residual:</td>
                <td style="width:400px">
                  <input type="number" class="form-control text-center inputSimulator id_machine ${d.id_machine
      }" id="residual_value-${row}" value="${d.residual_value.toLocaleString('es-CO')}"
                </td>
            </tr>
            <tr>
                <th>Años Depreciacion:</th>
                <td>
                  <input type="number" class="form-control text-center inputSimulator id_machine ${d.id_machine
      }" id="years_depreciation-${row}" value="${d.years_depreciation}"
                </td>
            </tr>
            <tr>
                <th>Horas de Trabajo:</th>
                <td>
                  <input type="number" class="form-control text-center inputSimulator id_machine ${d.id_machine
      }" id="hours_machine-${row}" value="${d.hours_machine}"
                </td>
            </tr>
            <tr>
                <th>Dias de Trabajo:</th>
                <td>
                  <input type="number" class="form-control text-center inputSimulator id_machine ${d.id_machine
      }" id="days_machine-${row}" value="${d.days_machine}"
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
      row.child(formatMachines(row.data(), row[0][0])).show();
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
          render: function (data, type, full, meta) {
            return `<input type="number" class="form-control text-center inputSimulator id_material ${data.id_material
              }" id="cost_material-${meta.row + 1
              }" value="${data.cost_material.toLocaleString('es-CO')}">`;
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

  function formatProductsMaterials(d, row) {
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
                  <input type="number" class="form-control text-center inputSimulator id_product_material ${d.id_product_material
      }" id="quantity-${row}" value="${d.quantity.toLocaleString('es-CO')}">
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
      row.child(formatProductsMaterials(row.data(), row[0][0])).show();
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
          data: null,
          className: 'uniqueClassName',
          render: function (data) {
            let dataMachines = sessionStorage.getItem('dataMachines');

            dataMachines = JSON.parse(dataMachines);

            var options = ``;

            for (var i = 0; i < dataMachines.length; i++) {
              options += `<option value="${dataMachines[i].id_machine}" ${data.id_machine == dataMachines[i].id_machine ? 'selected' : ''
                }>
                ${dataMachines[i].machine}
              </option>`;
            }

            var select = `<select class="form-control id_product_process ${data.id_product_process
              }" id="machines">
              <option disabled>Seleccionar</option>
              <option value="0" ${data.id_machine == '0' ? 'selected' : ''
              }>PROCESO MANUAL</option>
              ${options}
            </select>`;

            return select;
          },
        },
      ],
    });
  };

  function formatProductsProcess(d, row) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-15px;"> 
            <tr>
                <th>Tiempo de Enlistamiento:</th>
                <td style="width:400px">
                  <input type="number" class="form-control text-center inputSimulator id_product_process ${d.id_product_process}" id="enlistment_time-${row}" value="${d.enlistment_time}">
                </td>
            </tr> 
            <tr>
                <th>Tiempo de Operacion:</th>
                <td>
                  <input type="number" class="form-control text-center inputSimulator id_product_process ${d.id_product_process}" id="operation_time-${row}" value="${d.operation_time}">
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
      row.child(formatProductsProcess(row.data(), row[0][0])).show();
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
          render: function (data, type, full, meta) {
            return `<input type="number" class="text-center form-control inputSimulator id_manufacturing_load ${data.id_manufacturing_load
              }" id="cost-${meta.row + 1}" value="${data.cost.toLocaleString(
                'es-CO'
              )}">`;
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
    $('#cardAddDataSimulator').empty();
    let form = document.getElementById('cardAddDataSimulator');

    form.insertAdjacentHTML(
      'beforeend',
      `<div class="col-sm-4 floating-label enable-floating-label show-label" style="margin-bottom:5px">
            <label>Servicio</label>
            <input type="text" class="form-control data" id="name_service">
        </div>
        <div class="col-sm-4 floating-label enable-floating-label show-label" style="margin-bottom:5px">
            <label>Costo</label>
            <input type="number" class="form-control data" id="cost">
        </div>
        <div class="col-xs-2 floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:4px">
          <button class="btn btn-success btnCreateDataSimulator" id="externalServices">Crear Servicio</button>
        </div>`
    );
    $('.cardAddDataSimulator').show(800);

    tblSimulator = $('#tblSimulator').DataTable({
      destroy: true,
      scrollY: '150px',
      scrollCollapse: true,
      paging: false,
      data: data,
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
          title: 'Servicio',
          data: 'name_service',
          className: 'uniqueClassName',
          render: function (data) {
            return data.toUpperCase();
          },
        },
        {
          title: 'Costo',
          data: null,
          className: 'uniqueClassName',
          render: function (data, type, full, meta) {
            return `<input type="number" class="text-center form-control inputSimulator id_service ${data.id_service
              }" id="cost-${meta.row + 1}" value="${data.cost.toLocaleString(
                'es-CO'
              )}">`;
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
          render: function (data, type, full, meta) {
            return `<input type="number" class="text-center form-control inputSimulator basicSalary id_payroll ${data.id_payroll
              }" id="salary-${meta.row + 1}" value="${data.salary.toLocaleString(
                'es-CO'
              )}">`;
          },
        },
      ],
    });
  };

  function formatPayroll(d, row) {
    let optionsRisk = `
      <option value="1" ${d.id_risk == '1' ? 'selected' : ''}>CLASE I</option>
      <option value="2" ${d.id_risk == '2' ? 'selected' : ''}>CLASE II</option>
      <option value="3" ${d.id_risk == '3' ? 'selected' : ''}>CLASE III</option>
      <option value="4" ${d.id_risk == '4' ? 'selected' : ''}>CLASE IV</option>
      <option value="5" ${d.id_risk == '5' ? 'selected' : ''}>CLASE V</option>
      <option value="6" ${d.id_risk == '6' ? 'selected' : ''}>CLASE VI</option>
    `;

    let optionsFactor = `
    <option value="1" ${d.type_contract === 'Nomina' ? 'selected' : ''
      }>Nómina</option>
    <option value="2" ${d.type_contract === 'Servicios' ? 'selected' : ''
      }>Servicios</option>
    <option value="3" ${d.type_contract === 'Manual' ? 'selected' : ''
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
                  <input type="number" class="text-center form-control inputSimulator id_payroll ${d.id_payroll
      }" id="transport-${row}" value="${d.transport.toLocaleString('es-CO')}">
                </td>
            </tr>
            <tr>
                <th>Dotaciones:</th>
                <td>
                  <input type="number" class="text-center form-control inputSimulator id_payroll ${d.id_payroll
      }" id="endowment-${row}" value="${d.endowment.toLocaleString('es-CO')}">
                </td>
            </tr>
            <tr>
                <th>Horas Extras:</th>
                <td>
                  <input type="number" class="text-center form-control inputSimulator id_payroll ${d.id_payroll
      }" id="extra_time-${row}" value="${d.extra_time.toLocaleString('es-CO')}">
                </td>
            </tr>
            <tr>
                <th>Otros Ingresos:</th>
                <td>
                  <input type="number" class="text-center form-control inputSimulator id_payroll ${d.id_payroll
      }" id="bonification-${row}" value="${d.bonification.toLocaleString('es-CO')}">
                </td>
            </tr>
            <tr>
                <th>Horas Trabajo x Día:</th>
                <td>
                  <input type="number" class="text-center form-control inputSimulator workingHoursDay id_payroll ${d.id_payroll
      }" id="hours_day-${row}" value="${d.hours_day}">
                </td>
            </tr>
            <tr>
                <th>Dias Trabajo x Mes:</th>
                <td>
                  <input type="number" class="text-center form-control inputSimulator workingDaysMonth id_payroll ${d.id_payroll
      }" id="working_days_month-${row}" value="${d.working_days_month}">
                </td>
            </tr>
            <tr>
              <th>Riesgo:</th>
              <td>
                <select id="risk" name="risk" class="form-control ${d.id_payroll
      }">
                  <option disabled value="0">Seleccionar</option>
                  ${optionsRisk}
                </select>
              </td>
            </tr>
            <tr>
              <th></th>
              <td>
                <input type="text" class="form-control text-center inputSimulator valueRisk id_payroll ${d.id_payroll
      }" value="${d.percentage}" id="percentage-${row}" readonly>
              </td>
            </tr>
            <tr>
                <th>Tipo Nómina:</th>
                <td>
                  <select class="form-control inputSimulator typeFactor id_payroll ${d.id_payroll
      }" id="type_contract-${row}">
                    <option disabled value="0">Seleccionar</option>
                    ${optionsFactor}
                  </select>
                </td>
            </tr>
            <tr>
                <th>Factor:</th>
                <td>
                  <input type="number" class="text-center form-control inputSimulator factor id_payroll ${d.id_payroll
      }" id="factor_benefit-${row}">
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
      row.child(formatPayroll(row.data(), row[0][0])).show();
      tr.addClass('shown');
      $('.typeFactor').change();
      sessionStorage.removeItem('type_salary');
    }
  });

  /* Distribucion de Gastos */
  loadTblSimulatorDistribution = (data) => {
    let status = false;
    $('.cardAddDataSimulator').hide();

    for (let i = 0; i < data.length; i++) {
      if (data[i].id_product == dataSimulator.products[0].id_product) {
        status = true;
        break;
      }
    }

    if (status == false) {
      $('#cardAddDataSimulator').empty();

      let form = document.getElementById('cardAddDataSimulator');

      form.insertAdjacentHTML(
        'beforeend',
        `<div class="col-sm-4 floating-label enable-floating-label show-label" style="margin-bottom:5px">
            <label>Und Vendidas (Mes)</label>
            <input type="number" class="form-control data" id="units_sold">
        </div>
        <div class="col-sm-4 floating-label enable-floating-label show-label" style="margin-bottom:5px">
            <label>Total Ventas (Mes)</label>
            <input type="number" class="form-control data" id="turnover">
        </div>
        <div class="col-xs-2 floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:4px">
          <button class="btn btn-success btnCreateDataSimulator" id="expensesDistribution">Crear Gasto</button>
        </div>`
      );
      $('.cardAddDataSimulator').show(800);
      data = [];
    }

    tblSimulator = $('#tblSimulator').DataTable({
      destroy: true,
      scrollY: '150px',
      scrollCollapse: true,
      paging: false,
      data: data,
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
          className: 'dt-control distribution',
          orderable: false,
          data: null,
          defaultContent: '',
        },
        {
          title: 'Producto',
          data: 'product',
          className: 'uniqueClassName',
        },
        {
          title: 'Unidades Vendidas',
          data: null,
          className: 'uniqueClassName',
          render: function (data, type, full, meta) {
            return `<input type="number" class="text-center form-control inputSimulator id_expenses_distribution ${data.id_expenses_distribution
              }" id="units_sold-${meta.row + 1
              }" value="${data.units_sold.toLocaleString('es-CO')}">`;
          },
        },
      ],
    });
  };

  function formatDistribution(d, row) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-15px;"> 
            <tr>
                <th>Vol de Ventas:</th>
                <td style="width:400px">
                  <input type="number" class="text-center form-control inputSimulator id_expenses_distribution ${d.id_expenses_distribution
      }" id="turnover-${row}" value="${d.turnover.toLocaleString('es-CO')}">
                </td>
            </tr>
        </table>`;
  }

  $(document).on('click', '.distribution', function () {
    var tr = $(this).closest('tr');
    var row = tblSimulator.row(tr);

    if (row.child.isShown()) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    } else {
      // Open this row
      row.child(formatDistribution(row.data(), row[0][0])).show();
      tr.addClass('shown');
    }
  });

  /* Distribucion de Gastos x Familia*/
  loadTblSimulatorDistributionFamily = (data) => {
    let status = false;
    let options = ``;
    $('.cardAddDataSimulator').hide();
    $('#cardAddDataSimulator').empty();

    for (let i = 0; i < data.length; i++) {
      options += `<option value="${data[i].id_family}"> ${data[i].family} </option>`;

      if (data[i].id_family == dataSimulator.products[0].id_family) {
        status = true;
        break;
      }
    }

    if (status == false) {
      let form = document.getElementById('cardAddDataSimulator');

      form.insertAdjacentHTML(
        'beforeend',
        ` <div class="col-sm-5 floating-label enable-floating-label show-label" style="margin-bottom:5px">
            <label>Producto</label>
            <select class="form-control data">
              <option selected value="${dataSimulator.products[0].id_product}"> ${dataSimulator.products[0].product}</option>
            </select>
          </div>
          <div class="col-sm-5 floating-label enable-floating-label show-label" style="margin-bottom:5px">
            <label>Familia</label>
            <select id="id_family" class="form-control data">
              <option disabled selected>Seleccionar</option>
              ${options}
            </select>
          </div>
          <div class="col-sm-2" style="margin-bottom:0px;margin-top:4px">
            <button class="btn btn-success btnCreateDataSimulator" id="family">Adicionar</button>
          </div>`
      );

      $('.cardAddDataSimulator').show(800);
    }

    tblSimulator = $('#tblSimulator').DataTable({
      destroy: true,
      scrollY: '150px',
      scrollCollapse: true,
      paging: false,
      data: data,
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
          className: 'dt-control family',
          orderable: false,
          data: null,
          defaultContent: '',
        },
        {
          title: 'Familia',
          data: 'family',
          className: 'uniqueClassName',
        },
        {
          title: 'Unidades Vendidas',
          data: null,
          className: 'uniqueClassName',
          render: function (data, type, full, meta) {
            return `<input type="number" class="text-center form-control inputSimulator id_family ${data.id_family
              }" id="units_sold-${meta.row + 1
              }" value="${data.units_sold.toLocaleString('es-CO')}">`;
          },
        },
      ],
    });
  };

  function formatDistributionFamily(d, row) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-15px;"> 
            <tr>
                <th>Vol de Ventas:</th>
                <td style="width:400px">
                  <input type="number" class="text-center form-control inputSimulator id_family ${d.id_family
      }" id="turnover-${row}" value="${d.turnover.toLocaleString('es-CO')}">
                </td>
            </tr>
        </table>`;
  }

  $(document).on('click', '.family', function () {
    var tr = $(this).closest('tr');
    var row = tblSimulator.row(tr);

    if (row.child.isShown()) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    } else {
      // Open this row
      row.child(formatDistributionFamily(row.data(), row[0][0])).show();
      tr.addClass('shown');
    }
  });

  /* Recuperacion de Gastos */
  loadTblSimulatorRecover = (data) => {
    $('.cardAddDataSimulator').hide();

    if (data.length == 0) {
      $('#cardAddDataSimulator').empty();

      let form = document.getElementById('cardAddDataSimulator');

      form.insertAdjacentHTML(
        'beforeend',
        `<div class="col-sm-6 floating-label enable-floating-label show-label" style="margin-bottom:5px">
            <label>Porcentaje</label>
            <input type="number" class="form-control data" id="expense_recover">
        </div>
        <div class="col-xs-2 floating-label enable-floating-label show-label" style="margin-bottom:0px;margin-top:4px">
          <button class="btn btn-success btnCreateDataSimulator" id="expenseRecover">Crear Gasto</button>
        </div>`
      );

      $('.cardAddDataSimulator').show(800);
    }

    tblSimulator = $('#tblSimulator').DataTable({
      destroy: true,
      scrollY: '150px',
      scrollCollapse: true,
      paging: false,
      data: data,
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
          title: 'Porcentaje',
          data: null,
          className: 'uniqueClassName',
          render: function (data, type, full, meta) {
            return `<input type="number" class="text-center form-control inputSimulator 0" id="expense_recover-${meta.row + 1
              }" value="${data.expense_recover}">`;
          },
        },
      ],
    });
  };
});
