
/* Cargue tabla de gastos recuperados */
loadTableExpenseRecover = async () => {
  $('#tblExpenses').empty();

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
          <td class="uniqueClassName">${data[i].product} </td>
          <td class="uniqueClassName">${data[i].expense_recover} %</td>
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
  });
};

