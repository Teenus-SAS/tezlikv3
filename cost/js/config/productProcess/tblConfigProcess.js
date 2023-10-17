$(document).ready(function () {
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

  /* Cargue tabla de Proyectos */

  const loadtableProcess = (idProduct) => {
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
          title: 'Tiempo Alistamiento (min)',
          data: 'enlistment_time',
          className: 'classCenter',
          render: function (data) {
            let decimals = contarDecimales(data);
            let enlistment_time = formatNumber(data, decimals);

            return enlistment_time;
          },
        },
        {
          title: 'Tiempo Operación  (min)',
          data: 'operation_time',
          className: 'classCenter',
          render: function (data) {
            let decimals = contarDecimales(data);
            let operation_time = formatNumber(data, decimals);

            return operation_time;
          },
        },
        {
          title: 'Mano De Obra',
          data: 'workforce_cost',
          className: 'classCenter',
          render: function (data) {
            return `$ ${parseFloat(data).toLocaleString('es-co',{maximumFractionDigits: 2})}`;
          },
        },
        {
          title: 'Costo Indirecto',
          data: 'indirect_cost',
          className: 'classCenter',
          render: function (data) {
            return `$ ${parseFloat(data).toLocaleString('es-co',{maximumFractionDigits: 2})}`;
          },
        },
        {
          title: '',
          data: 'id_product_process',
          className: 'uniqueClassName',
          render: function (data) {
            return `<a href="javascript:;" <i id="${data}" class="ti-exchange-vertical updateEmployee" data-toggle='tooltip' title='Modificar Empleados' style="font-size: 30px; color:orange;"></i></a>`;
          },
        },
        {
          title: 'Acciones',
          data: 'id_product_process',
          className: 'uniqueClassName',
          render: function (data) {
            return `
                
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateProcess" data-toggle='tooltip' title='Actualizar Proceso' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Proceso' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
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
