$(document).ready(function () {
  /* Cargue tabla de Máquinas */
  type_payroll == '1' ? visible = true : visible = false;

  tblPayroll = $('#tblPayroll').dataTable({
    pageLength: 50,
    ajax: {
      url: '/api/payroll',
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
        title: 'Nombre Empleado',
        data: 'employee',
      },
      {
        title: 'Proceso',
        data: 'process',
      },
      {
        title: 'Salario Base',
        data: 'salary',
        className: 'classRight',
        visible: visible,
        render: function (data) {
          if (Math.abs(data) < 0.0001) {
            let decimals = contarDecimales(data);
            data = formatNumber(data, decimals);
          } else
            data = data.toLocaleString('es-CO', { maximumFractionDigits: 0 });
            
          return data;
        },
      },
      {
        title: 'Salario Neto',
        data: 'salary_net',
        className: 'classRight',
        visible: visible,
        render: function (data) {
          if (Math.abs(data) < 0.0001) {
            let decimals = contarDecimales(data);
            data = formatNumber(data, decimals);
          } else
            data = data.toLocaleString('es-CO', { maximumFractionDigits: 0 });
            
          return data;
        }
      },
      {
        title: 'Valor Minuto',
        data: 'minute_value',
        className: 'classRight',
        render: function (data) {
          if (Math.abs(data) < 0.0001) {
            let decimals = contarDecimales(data);
            data = formatNumber(data, decimals);
          } else
            data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
          return data;
        }
      },
      {
        title: 'Acciones',
        data: null,
        className: 'uniqueClassName',
        render: function (data) {
          return `
          <a href="javascript:;" <i id="${data.id_payroll}" class="bx bx-copy-alt" data-toggle='tooltip' title='Clonar Nomina' style="font-size: 30px; color:green" onclick="copyFunction('${data.employee}')"></i></a>
          <a href="javascript:;" <i id="${data.id_payroll}" class="bx bx-edit-alt updatePayroll" data-toggle='tooltip' title='Actualizar Nomina' style="font-size: 30px;"></i></a>
          <a href="javascript:;" <i id="${data.id_payroll}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Nomina' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
        },
      },
    ],

    footerCallback: function (row, data, start, end, display) {
      if (type_payroll == '1') {
        let salary = 0;
        let salary_net = 0;
        let minute_value = 0;

        for (i = 0; i < display.length; i++) {
          salary += parseFloat(data[display[i]].salary);
          salary_net += parseFloat(data[display[i]].salary_net);
          minute_value += parseFloat(data[display[i]].minute_value);
        }

        $(this.api().column(3).footer()).html(
          `$ ${salary.toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })}`
        );

        $(this.api().column(4).footer()).html(
          `$ ${salary_net.toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })}`
        );

        $(this.api().column(5).footer()).html(
          `$ ${minute_value.toLocaleString('es-CO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          })}`
        );
      }
    },
  });

  loadFooterPayroll = async () => {
    let data = await searchData('/api/salarynet');

    $('#totalSalary').html(`$ ${parseFloat(data.salary).toLocaleString('es-CO',{maximumFractionDigits:2})}`);
    $('#totalSalarynet').html(`$ ${parseFloat(data.salary_net).toLocaleString('es-CO',{maximumFractionDigits:2})}`);
    $('#totalMinuteValue').html(`$ ${parseFloat(data.minute_value).toLocaleString('es-CO',{maximumFractionDigits:2})}`);
  } 

  setTimeout(() => {
    loadFooterPayroll(); 
  }, 1000);
});
