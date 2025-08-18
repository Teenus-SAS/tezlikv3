
window.getExpense = async () => {
  try {
    // Llamada directa con fetch
    const response = await fetch('/api/distribution/expenseTotal');
    if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
    const { total_expense = 0 } = await response.json();

    // Mostrar total de gasto
    $('#expensesToDistribution')
      .val(`$ ${total_expense.toLocaleString('es-CO')}`)
      .prop('disabled', true);

    if (parseInt(flag_expense) === 0) {
      // Solicitar tipo de gasto al usuario
      bootbox.confirm({
        closeButton: false,
        title: 'Tipo de Gasto',
        message: 'Seleccione a cual tipo de gasto desea ingresar.',
        buttons: {
          confirm: { label: 'Distribución', className: 'btn-success' },
          cancel: { label: 'Recuperación', className: 'btn-info' }
        },
        callback: result => {
          option = result ? 1 : 2;
          changeTypeExpense();
        }
      });
    } else {
      option = flag_expense;
      setDataExpense({ total_expense });
    }

  } catch (error) {
    console.error('Error al cargar el gasto total:', error);
    toastr.error('No se pudo obtener el total de gastos');
  }
};

$(document).ready(function () {
  getExpense();
});

const changeTypeExpense = async () => {
  try {
    const resp = await searchData(`/api/expenses/changeTypeExpense/${option}`);
    toastr[resp.success ? 'success' : 'error'](resp.message);
    setDataExpense();
  } catch (error) {
    console.error('Error changing expense type:', error);
  }
};

const changeTypeExpenseDistribution = async () => {
  try {
    if (!option_distribution)
      option_distribution = flag_expense_distribution;
    else {
      const resp = await searchData(`/api/distributionByFamilies/changeTypeExpenseDistribution/${option_distribution}`);
      toastr[resp.success ? 'success' : 'error'](resp.message);
    }

    if (option_distribution == 2) {
      const buttons = document.querySelector('.cardBtnExpensesDistribution');
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
      $('.distributionFamilies').show();
      await loadFamilies(1);
      await loadTableExpensesDistributionFamilies();
    } else {
      $('.distribution').show();
      $('.distributionFamilies').hide();
      await loadAllDataDistribution();
    }
  } catch (error) {
    console.error('Error changing expense distribution type:', error);
  }
};


const setDataExpense = async () => {
  try {
    if ($.fn.dataTable.isDataTable('#tblExpenses')) {
      $('#tblExpenses').DataTable().destroy();
      $('#tblExpenses').empty();
    }

    if (option == 1) {
      $('.distributionExpenses').html('Distribución de Gastos');
      $('.cardBtnExpensesDistribution').show(800);

      if (type_expense == '1') {
        const selects = document.querySelectorAll('.cardBtnExpensesDistribution')[1];
        selects.innerHTML = "";

        selects.insertAdjacentHTML('beforeend',
          `<label class="text-dark">Distribución</label>
            <select class="form-control typeExpense" id="selectTypeED">
              <option disabled>Seleccionar</option>
              <option value="1" ${flag_expense_distribution == '1' ? 'selected' : ''}>PRODUCTO</option>
              <option value="2" ${flag_expense_distribution == '2' ? 'selected' : ''}>FAMILIA</option>
            </select>`
        );
      }

      $('.cardBtnImportExpenses').show(800);
      $('#btnImportNewExpenses').attr('title', 'Importar Distribución').tooltip('dispose').tooltip();
      $('#lblImportExpense').html('Importar Distribución de Gasto');
      $('#descrExpense').html('Distribución Gastos Generales');

      if (flag_expense_distribution == 0) {
        bootbox.confirm({
          closeButton: false,
          title: 'Tipo de Distribución',
          message: '¿Desea realizar la distribucion por familia?. Esta acción no se puede reversar',
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
            result == true ? option_distribution = 2 : option_distribution = 1;
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
      $('.cardNewProducts').hide();
      $('.cardCheckExpense').show(800);
      $('.distributionExpenses').html('Recuperación de Gastos');
      $('.cardBtnExpenseRecover').show(800);
      $('.cardBtnImportExpenses').show(800);
      $('#btnImportNewExpenses').attr('title', 'Importar Recuperar Gastos').tooltip('dispose').tooltip();
      $('#lblImportExpense').html('Importar Recuperación');
      $('#descrExpense').html('Recuperación Gastos Generales');
      loadTableExpenseRecover();
    }
  } catch (error) {
    console.error('Error setting expense data:', error);
  }
};

$(document).on('change', '.typeExpense', function () {
  const op = this.value;

  bootbox.confirm({
    title: 'Cambiar Tipo Distribución',
    message: `Está seguro de cambiar el tipo de distribución a ${op == '1' ? 'distribución por producto' : 'distribución por familia'}?`,
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
        $.get(`/api/distributionByFamilies/changeTypeExpenseDistribution/${op}`, function (data, textStatus, jqXHR) {
          if (data.success) {
            toastr.success(data.message);
            flag_expense_distribution = data.flag;
            $('.btnButtons').remove();
            $('.cardExpensesDistribution').hide();
            setDataExpense();
          } else {
            toastr.error(data.message);
          }
        });
      } else
        $('#selectTypeED').val(flag_expense_distribution);


    },
  });
});

