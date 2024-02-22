$(document).ready(function () {
  let title3 = `${inyection == 1 ? 'Tiempo/Und' : 'Tiempo Alistamiento (min)'}`;
  let value3 = `${inyection == 1 ? 'unity_time' : 'enlistment_time'}`;
  let title4 = `${inyection == 1 ? '% Eficiencia' : 'Tiempo Operación (min)'}`;

  /* Seleccion producto */
  $('#refProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    $('#selectNameProduct option').removeAttr('selected');
    $(`#selectNameProduct option[value=${id}]`).attr('selected', true);

    loadtableProcess(id);
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    $('#refProduct option').removeAttr('selected');
    $(`#refProduct option[value=${id}]`).attr('selected', true);

    loadtableProcess(id);
  });

  flag_employee == '1' ? visible = true : visible = false;

  /* Cargue tabla de Proyectos */

  const loadtableProcess = (idProduct) => {
    $('.cardAddProcess').hide(800);
    
    tblConfigProcess = $('#tblConfigProcess').dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: `/api/productsProcess/${idProduct}`,
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
          title: 'Proceso',
          data: 'process',
        },
        {
          title: 'Máquina',
          data: 'machine',
        },
        {
          title: title3,
          data: value3,
          className: 'classCenter',
          render: function (data) {
            data = parseFloat(data);
            
if (Math.abs(data) < 0.0001) { 
              let decimals = contarDecimales(data);
              data = formatNumber(data, decimals);
            } else
              data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
            return data;
          },
        },
        {
          title: title4,
          data: 'operation_time',
          className: 'classCenter',
          render: function (data) {
                      data = parseFloat(data);

            if (Math.abs(data) < 0.0001) {
              let decimals = contarDecimales(data);
              data = formatNumber(data, decimals);
            } else
              data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
            return data;
          },
        },
        {
          title: 'Mano De Obra',
          data: 'workforce_cost',
          className: 'classCenter',
          render: function (data) {
                      data = parseFloat(data);

            if (Math.abs(data) < 0.0001) {
              let decimals = contarDecimales(data);
              data = formatNumber(data, decimals);
            } else
              data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
            return `$ ${data}`; 
          },
        },
        {
          title: 'Costo Indirecto',
          data: 'indirect_cost',
          className: 'classCenter',
          render: function (data) {
                                  data = parseFloat(data);

            if (Math.abs(data) < 0.0001) {
              let decimals = contarDecimales(data);
              data = formatNumber(data, decimals);
            } else
              data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
            return `$ ${data}`; 
          },
        },
        {
          title: '',
          data: null,
          className: 'uniqueClassName',
          visible: visible,
          render: function (data) {
            if (data.auto_machine === 0)
              return `<a href="javascript:;" <i id="${data.id_product_process}" class="ti-exchange-vertical updateEmployee" data-toggle='tooltip' title='Modificar Empleados' style="font-size: 30px; color:orange;"></i></a>`;
            else return '';
          },
        },
        {
          title: 'Acciones',
          data: 'id_product_process',
          className: 'uniqueClassName',
          render: function (data) {
            return `
            
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateProcess" data-toggle='tooltip' title='Actualizar Proceso' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Proceso' style="font-size: 30px;color:red" onclick="deleteProcess()"></i></a>`;
          },
        },
      ],
      footerCallback: function (row, data, start, end, display) {
        enlistmentTime = this.api()
          .column(3)
          .data()
          .reduce(function (a, b) {
            return parseFloat(a) + parseFloat(b);
          }, 0);

        $(this.api().column(3).footer()).html(
          new Intl.NumberFormat('de-DE').format(enlistmentTime)
        );
        operationTime = this.api()
          .column(4)
          .data()
          .reduce(function (a, b) {
            return parseFloat(a) + parseFloat(b);
          }, 0);

        $(this.api().column(4).footer()).html(
          new Intl.NumberFormat('de-DE').format(operationTime)
        );

        workForce = this.api()
          .column(5)
          .data()
          .reduce(function (a, b) {
            return parseFloat(a) + parseFloat(b);
          }, 0);

        $(this.api().column(5).footer()).html(
          `$ ${new Intl.NumberFormat('de-DE').format(workForce)}`
        );
        indirectCost = this.api()
          .column(6)
          .data()
          .reduce(function (a, b) {
            return parseFloat(a) + parseFloat(b);
          }, 0);

        $(this.api().column(6).footer()).html(
          `$ ${new Intl.NumberFormat('de-DE').format(indirectCost)}`
        );
      },
    });
  };
});
