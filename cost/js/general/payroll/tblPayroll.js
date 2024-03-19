$(document).ready(function () {
  /* Cargue tabla de Máquinas */
  type_payroll == '1' ? visible = true : visible = false;
  allPayroll = [];
  allProductProcess = [];

  loadAllTblData = async () => {
    try {
      const [payroll, dataCost, dataProductProcess] = await Promise.all([
        searchData('/api/payroll'),
        searchData('/api/salarynet'),
        searchData('/api/allProductsProcess')
      ]);

      allPayroll = payroll;
      allProductProcess = dataProductProcess;

      loadTblPayroll(payroll);

      $('#totalSalary').html(`$ ${parseFloat(dataCost.salary).toLocaleString('es-CO', { maximumFractionDigits: 2 })}`);
      $('#totalSalarynet').html(`$ ${parseFloat(dataCost.salary_net).toLocaleString('es-CO', { maximumFractionDigits: 2 })}`);
      $('#totalMinuteValue').html(`$ ${parseFloat(dataCost.minute_value).toLocaleString('es-CO', { maximumFractionDigits: 2 })}`);
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  loadAllTblData();

  const loadTblPayroll = (data) => {
    if ($.fn.dataTable.isDataTable("#tblPayroll")) {
      $("#tblPayroll").DataTable().destroy();
      $("#tblPayroll").empty();
      $('#tblPayroll').append(`
      <tbody id="tblPayrollBody"></tbody>
      ${type_payroll == '1' ?
      `<tfoot>
        <tr>
          <th></th>
          <th></th>
          <th>Total:</th>
          <th id="totalSalary"></th>
          <th id="totalSalarynet"></th>
          <th id="totalMinuteValue"></th>
        </tr>
      </tfoot>`: ''}
      `);
    }
    // Encabezados de la tabla
    var headers = ['No.', 'Nombre Empleado', 'Proceso', 'Salario Base', 'Salario Neto', 'Valor Minuto', 'Acciones'];
    
    // Obtén la tabla
    var table = document.getElementById('tblPayroll');

    // Crea la fila de encabezados
    var headerRow = table.createTHead().insertRow();
    headers.forEach(function (header) {
      var th = document.createElement('th');
      th.textContent = header;
      headerRow.appendChild(th);
    });

    $('#tblPayrollBody').empty();
    var body = document.getElementById('tblPayrollBody');

    data.forEach((arr, index) => {
      const i = index;
      const dataRow = body.insertRow();

      dataRow.classList.add('t-row'); // Agregar la clase 't-row' a la fila
      dataRow.setAttribute('data-index', index);
      dataRow.setAttribute('data-id', arr.id_payroll);

      headers.forEach((header, columnIndex) => {
        const cell = dataRow.insertCell();
        switch (header) {
          case 'No.':
            cell.textContent = i + 1;
            break;
          case 'Nombre Empleado':
            cell.textContent = arr.employee;
            break;
          case 'Proceso':
            cell.textContent = arr.process;
            break;
          case 'Salario Base':
            let salary = parseFloat(arr.salary);

            if (Math.abs(salary) < 0.01) 
              salary = salary.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            else
              salary = salary.toLocaleString('es-CO', { maximumFractionDigits: 0 });
            
            cell.textContent = salary;
            break;
          case 'Salario Neto':
            let salary_net = parseFloat(arr.salary_net);

            if (Math.abs(salary_net) < 0.01) 
              salary_net = salary_net.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            else
              salary_net = salary_net.toLocaleString('es-CO', { maximumFractionDigits: 0 });
            
            cell.textContent = salary_net;
            break;
          case 'Valor Minuto':
            let minute_value = parseFloat(arr.minute_value);

            if (Math.abs(minute_value) < 0.01) 
              minute_value = minute_value.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            else
              minute_value = minute_value.toLocaleString('es-CO', { maximumFractionDigits: 0 });
            
            cell.textContent = minute_value;
            break;
          case 'Acciones':
            cell.innerHTML = `
            <a href="javascript:;" <i id="${arr.id_payroll}" class="bx bx-copy-alt" data-toggle='tooltip' title='Clonar Nomina' style="font-size: 30px; color:green" onclick="copyFunction(${arr.id_payroll}, '${arr.employee}')"></i></a>
            <a href="javascript:;" <i id="${arr.id_payroll}" class="bx bx-edit-alt updatePayroll" data-toggle='tooltip' title='Actualizar Nomina' style="font-size: 30px;"></i></a>
            <a href="javascript:;" <i id="${arr.id_payroll}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Nomina' style="font-size: 30px;color:red" onclick="deleteFunction(${arr.id_payroll})"></i></a>`;
            break;
          default:
            cell.textContent = '';
            break;
        }
      });
    });

    $('#tblPayroll').dataTable({
      pageLength: 50,
      dom: '<"datatable-error-console">frtip',
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      },
      fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
        if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
          console.error(oSettings.json.error);
        }
      },
    });

    dragula([document.getElementById('tblPayrollBody')]).on('drop', async function (el, container, source, sibling) {
      let copy = [];
            
      // If the row was dropped within the same container,
      // move it to the specified position
      if (container === source) {
        var targetIndex = sibling ? sibling.rowIndex - 1 : container.children.length - 1;
        
        container.insertBefore(el, container.children[targetIndex]);
        var elements = $('.t-row');
        elements = elements.not('.gu-mirror');

        for (let i = 0; i < elements.length; i++) {
          copy.push({ id_payroll: elements[i].dataset.id, route: i + 1 });
        }  

        $.ajax({
          type: "POST",
          url: "/api/saveRoutePayroll",
          data: { data: copy },
          success: function (resp) {
            message(resp);
          }
        });
      } else {
        // If the row was dropped into a different container,
        // move it to the first position
        container.insertBefore(el, container.firstChild);
      }
    });
    // tblPayroll = $('#tblPayroll').dataTable({
    //   pageLength: 50,
    //   data: data,
    //   dom: '<"datatable-error-console">frtip',
    //   language: {
    //     url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
    //   },
    //   fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
    //     if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
    //       console.error(oSettings.json.error);
    //     }
    //   },
    //   columns: [
    //     {
    //       title: 'No.',
    //       data: null,
    //       className: 'uniqueClassName',
    //       render: function (data, type, full, meta) {
    //         return meta.row + 1;
    //       },
    //     },
    //     {
    //       title: 'Nombre Empleado',
    //       data: 'employee',
    //     },
    //     {
    //       title: 'Proceso',
    //       data: 'process',
    //     },
    //     {
    //       title: 'Salario Base',
    //       data: 'salary',
    //       className: 'classRight',
    //       visible: visible,
    //       render: function (data) {
    //         data = parseFloat(data);

    //         if (Math.abs(data) < 0.01) {
    //           // let decimals = contarDecimales(data);
    //           // data = formatNumber(data, decimals);
    //           data = data.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
    //         } else
    //           data = data.toLocaleString('es-CO', { maximumFractionDigits: 0 });
            
    //         return data;
    //       },
    //     },
    //     {
    //       title: 'Salario Neto',
    //       data: 'salary_net',
    //       className: 'classRight',
    //       visible: visible,
    //       render: function (data) {
    //         data = parseFloat(data);

    //         if (Math.abs(data) < 0.01) {
    //           // let decimals = contarDecimales(data);
    //           // data = formatNumber(data, decimals);
    //           data = data.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
    //         } else
    //           data = data.toLocaleString('es-CO', { maximumFractionDigits: 0 });
            
    //         return data;
    //       }
    //     },
    //     {
    //       title: 'Valor Minuto',
    //       data: 'minute_value',
    //       className: 'classRight',
    //       render: function (data) {
    //         data = parseFloat(data);

    //         if (Math.abs(data) < 0.01) {
    //           // let decimals = contarDecimales(data);
    //           // data = formatNumber(data, decimals);
    //           data = data.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
    //         } else
    //           data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
    //         return data;
    //       }
    //     },
    //     {
    //       title: 'Acciones',
    //       data: null,
    //       className: 'uniqueClassName',
    //       render: function (data) {
    //         return `
    //       <a href="javascript:;" <i id="${data.id_payroll}" class="bx bx-copy-alt" data-toggle='tooltip' title='Clonar Nomina' style="font-size: 30px; color:green" onclick="copyFunction('${data.employee}')"></i></a>
    //       <a href="javascript:;" <i id="${data.id_payroll}" class="bx bx-edit-alt updatePayroll" data-toggle='tooltip' title='Actualizar Nomina' style="font-size: 30px;"></i></a>
    //       <a href="javascript:;" <i id="${data.id_payroll}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Nomina' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
    //       },
    //     },
    //   ],

    //   footerCallback: function (row, data, start, end, display) {
    //     if (type_payroll == '1') {
    //       let salary = 0;
    //       let salary_net = 0;
    //       let minute_value = 0;

    //       for (i = 0; i < display.length; i++) {
    //         salary += parseFloat(data[display[i]].salary);
    //         salary_net += parseFloat(data[display[i]].salary_net);
    //         minute_value += parseFloat(data[display[i]].minute_value);
    //       }

    //       $(this.api().column(3).footer()).html(
    //         `$ ${salary.toLocaleString('es-CO', {
    //           minimumFractionDigits: 0,
    //           maximumFractionDigits: 0,
    //         })}`
    //       );

    //       $(this.api().column(4).footer()).html(
    //         `$ ${salary_net.toLocaleString('es-CO', {
    //           minimumFractionDigits: 0,
    //           maximumFractionDigits: 0,
    //         })}`
    //       );

    //       $(this.api().column(5).footer()).html(
    //         `$ ${minute_value.toLocaleString('es-CO', {
    //           minimumFractionDigits: 2,
    //           maximumFractionDigits: 2,
    //         })}`
    //       );
    //     }
    //   },
    // });
  }
});
