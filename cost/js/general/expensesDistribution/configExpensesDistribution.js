$(document).ready(function () {
  getExpense = async () => {
    await searchData('/api/totalExpense');
    let data = await searchData('/api/expenseTotal');

    data.total_expense == undefined || !data.total_expense
      ? (total_expense = 0)
      : (total_expense = data.total_expense);

    /* Carga gasto total */
    $('#expensesToDistribution').val(
      `$ ${total_expense.toLocaleString('es-CO')}`
    );
    $('#expensesToDistribution').prop('disabled', true);

    data = await searchData('/api/checkTypeExpense'); 

    if (data.flag_expense == 0) {
      /* Seleccionar tipo de gasto */
      bootbox.confirm({
        closeButton: false,
        title: 'Tipo de Gasto',
        message: 'Seleccione a cual tipo de gasto desea ingresar.',
        buttons: {
          confirm: {
            label: 'Distribución',
            className: 'btn-success',
          },
          cancel: {
            label: 'Recuperación',
            className: 'btn-info',
          },
        },
        callback: function (result) {
          result == true ? (option = 1) : (option = 2);
          changeTypeExpense();
        },
      });
    } else {
      option = data.flag_expense;
      flag_expense_distribution = data.flag_family;

      setDataExpense(data);
    }
  };

  getExpense();

  changeTypeExpense = async () => {
    resp = await searchData(`/api/changeTypeExpense/${option}`);
    if (resp.success) toastr.success(resp.message);
    else toastr.error(resp.message);
    setDataExpense();
  };

  changeTypeExpenseDistribution = async () => {
    if (option_distribution == false)
      option_distribution = flag_expense_distribution;
    else {
      resp = await searchData(
        `/api/changeTypeExpenseDistribution/${option_distribution}`
      );
      if (resp.success) toastr.success(resp.message);
      else toastr.error(resp.message);
    }

    if (option_distribution == 2) {
      let buttons = document.getElementsByClassName(
        'cardBtnExpensesDistribution'
      )[0];

      buttons.insertAdjacentHTML(
        'beforebegin',
        `<div class="col-xs-2 btnButtons mr-2">
          <button class="btn btn-secondary" id="btnAddNewFamily">Nueva Familia</button>
        </div>
        <div class="col-xs-2 btnButtons mr-2">
          <button class="btn btn-secondary btnAddProductsFamilies" id="btnAddProductsFamilies">Asignar Productos</button>
        </div>
        `
      );
      $('.distribution').hide();
      let form = document.getElementsByClassName('input-2')[0];

      form.insertAdjacentHTML(
        'afterend',
        `<div class="col-sm-5 floating-label enable-floating-label show-label" style="margin-bottom:20px;margin-top:7px">
          <select class="form-control families" name="idFamily" id="familiesDistribute"></select>
          <label for="families">Familia</label>
        </div>`
      );
      await loadFamilies(1);

      await loadTableExpensesDistributionFamilies();
    } else await loadTableExpensesDistribution();
  };

  setDataExpense = async () => {
    if ($.fn.dataTable.isDataTable('#tblExpenses')) {
      $('#tblExpenses').DataTable().destroy();
      $('#tblExpenses').empty();
    }

    if (option == 1) {
      $('.distributionExpenses').html('Distribución de Gastos');
      $('.cardBtnExpensesDistribution').show(800);
      $('.cardBtnImportExpenses').show(800);
      $('#btnImportNewExpenses').html('Importar Distribución');
      $('#lblImportExpense').html('Importar Distribución de Gasto');
      $('#descrExpense').html('Distribución Gastos Generales');

      if (flag_expense_distribution == 0) {
        bootbox.confirm({
          closeButton: false,
          title: 'Tipo de Distribución',
          message:
            '¿Desea realizar la distribucion por familia?. Esta acción no se puede reversar',
          buttons: {
            confirm: {
              label: 'Si',
              className: 'btn-success',
            },
            cancel: {
              label: 'No',
              className: 'btn-danger',
            },
          },
          callback: function (result) {
            result == true
              ? (option_distribution = 2)
              : (option_distribution = 1);
            flag_expense_distribution = option_distribution;
            changeTypeExpenseDistribution();
          },
        });
      } else {
        option_distribution = false;
        changeTypeExpenseDistribution();
      }
    }
    if (option == 2) {
      $('.cardCheckExpense').show(800);
      // $('.generalExpenses').hide();
      $('.distributionExpenses').html('Recuperación de Gastos');
      $('.cardBtnExpenseRecover').show(800);
      $('.cardBtnImportExpenses').show(800);
      $('#btnImportNewExpenses').html('Importar Recuperar Gastos');
      $('#lblImportExpense').html('Importar Recuperación');
      $('#descrExpense').html('Recuperación Gastos Generales');
      loadTableExpenseRecover();
    }
  };

  $('.typeExpense').click(function (e) { 
    e.preventDefault(); 

    let op = this.value;

    bootbox.confirm({
      title: 'Cambiar Tipo Distribución',
      message:
        `Está seguro de cambiar el tipo de distribución a ${op == '1' ? 'distribución por producto': 'distribución por familia'}?`,
      buttons: {
        confirm: {
          label: 'Si',
          className: 'btn-success',
        },
        cancel: {
          label: 'No',
          className: 'btn-danger',
        },
      },
      callback: function (result) {
        if (result == true) {
          $.get(
            `/api/changeTypeExpenseDistribution/${op}`,
            function (data, textStatus, jqXHR) {
              if (data.success) {
                toastr.success(data.message);
                flag_expense_distribution = data.flag;
                $('.btnButtons').remove();
                setDataExpense();
              }
              else toastr.error(data.message);
            }
          );
        }
      },
    });
    
  });
});
