/* Cargue tabla de gastos recuperados */
loadTableExpenseRecover = async () => {
  tblExpenses = $('#tblExpenses').dataTable({
    destroy: true,
    pageLength: 50,
    ajax: {
      url: "/api/recoveringExpenses",
      dataSrc: "",
    },
    dom: '<"datatable-error-console">frtip',
    language: {
      url: '/assets/plugins/i18n/Spanish.json',
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
        className: "uniqueClassName dt-head-center",
      },
      {
        title: 'Producto',
        data: null,
        className: "uniqueClassName dt-head-center",
        render: function (data, type, row) {
          const recoveryType = row.manual_recovery === 1
            ? '<span class="text-primary">Manual</span>'
            : '<span class="text-success">Automático</span>';
          return `${row.product}<br>${recoveryType}`;
        }
      },
      {
        title: 'Porcentaje recuperado',
        data: 'expense_recover',
        className: "uniqueClassName dt-head-center",
      },
      {
        title: 'Acciones',
        data: null,
        className: 'uniqueClassName',
        render: function (data) {
          const editIcon = `
      <a href="javascript:;">
        <i id="${data.id_expense_recover}" class="bx bx-edit-alt updateExpenseRecover" data-toggle='tooltip' title='Actualizar Gasto' style="font-size: 30px;"></i>
      </a>
    `;

          const manualRecoveryIcon = id_company == 1 ? `
      <a href="javascript:;">
        <i id="${data.id_expense_recover}" data-product="${data.id_product}" class="fas fa-sync-alt manual_recovery" data-toggle='tooltip' title='Modificar acción Manual/Automática' style="font-size: 20px; color: teal;"></i>
      </a>` : '';

          return editIcon + manualRecoveryIcon;
        },
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
};

