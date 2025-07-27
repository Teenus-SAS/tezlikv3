
/* Cargue tabla de gastos recuperados */
loadTableExpenseRecover = async () => {
  /*$('#tblExpenses').empty();

  let data = await searchData('/api/expensesRecover');
  sessionStorage.setItem('dataExpensesRecover', JSON.stringify(data));

  let tblExpenses = document.getElementById('tblExpenses');

  tblExpenses.insertAdjacentHTML(
    'beforeend',
    `<thead>
        <tr>
          <th class="uniqueClassName">
            <label>Seleccionar Todos</label><br>
            <input class="form-control-updated checkExpense" type="checkbox" id="all">
          </th>
          <th class="uniqueClassName">No.</th>
          <th class="uniqueClassName">Referencia</th>
          <th class="uniqueClassName">Producto</th>
          <th class="uniqueClassName">Porcentaje recuperado</th>
          <th class="uniqueClassName">Acciones</th>
          </tr>
        </thead>
        <tbody id="tblExpensesBody"></tbody>`
  );

  let tblExpensesBody = document.getElementById('tblExpensesBody');
  for (let i = 0; i < data.length; i++) {
    tblExpensesBody.insertAdjacentHTML(
      'beforeend',
      `<tr>
          <td class="uniqueClassName">
            <input type="checkbox" class="form-control-updated checkExpense" id="check-${data[i].id_expense_recover
      }">
          </td>
          <td class="uniqueClassName">${i + 1}</td>
          <td class="uniqueClassName">${data[i].reference} </td>
          <td class="uniqueClassName">${data[i].product} <span>${data[i].manual_recovery}</span></td>
          <td class="uniqueClassName">${parseFloat(data[i].expense_recover).toFixed(2)} %</td>
          <td class="uniqueClassName">
            <a href="javascript:;" <i id="${data[i].id_expense_recover
      }" class="bx bx-edit-alt updateExpenseRecover" data-toggle='tooltip' title='Actualizar Gasto' style="font-size: 30px;"></i></a>
            <a href="javascript:;" <i id="${data[i].id_expense_recover
      }" class="mdi mdi-delete-forever deleteExpenseRecover" data-toggle='tooltip' title='Eliminar Gasto' style="font-size: 30px;color:red"></i></a>
          </td>
        </tr>`
    );
  }

  tblExpenseRecover = $('#tblExpenses').DataTable({
    destroy: true,
    dom: '<"datatable-error-console">frtip',
    language: {
      url: '/assets/plugins/i18n/Spanish.json',
    },
    fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
      if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
        console.error(oSettings.json.error);
      }
    },
  });*/

  tblExpenses = $('#tblExpenses').dataTable({
    destroy: true,
    pageLength: 50,
    ajax: {
      url: "/api/expensesRecover",
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

