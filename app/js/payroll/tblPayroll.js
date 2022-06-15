$(document).ready(function() {
    /* Cargue tabla de Máquinas */

    tblPayroll = $('#tblPayroll').dataTable({
        pageLength: 50,
        ajax: {
            url: '/api/payroll',
            dataSrc: '',
        },
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
        },
        columns: [{
                title: 'No.',
                data: null,
                className: 'uniqueClassName',
                render: function(data, type, full, meta) {
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
                render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
            },
            {
                title: 'Salario Neto',
                data: 'salary_net',
                className: 'classRight',
                render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
            },
            {
                title: 'Valor Minuto',
                data: 'minute_value',
                className: 'classRight',
                render: $.fn.dataTable.render.number('.', ',', 2, '$ '),
            },
            {
                title: 'Acciones',
                data: 'id_payroll',
                className: 'uniqueClassName',
                render: function(data) {
                    return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updatePayroll" data-toggle='tooltip' title='Actualizar Nomina' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever deletePayroll" data-toggle='tooltip' title='Eliminar Nomina' style="font-size: 30px;color:red"></i></a>`;
                },
            },
        ],

        footerCallback: function(row, data, start, end, display) {
            subtotal = this.api()
                .column(3)
                .data()
                .reduce(function(a, b) {
                    return parseInt(a) + parseInt(b);
                }, 0);

            $(this.api().column(3).footer()).html(
                new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                }).format(subtotal)
            );

            total = this.api()
                .column(4)
                .data()
                .reduce(function(a, b) {
                    return parseInt(a) + parseInt(b);
                }, 0);

            $(this.api().column(4).footer()).html(
                new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                }).format(total)
            );

            minTotal = this.api()
                .column(5)
                .data()
                .reduce(function(a, b) {
                    return parseInt(a) + parseInt(b);
                }, 0);

            $(this.api().column(5).footer()).html(
                new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                }).format(minTotal)
            );
        },
    });
});