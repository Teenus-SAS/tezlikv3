$(document).ready(function () {
  // getExpense = async () => {
  //   await searchData('/api/totalExpense');
  //   let data = await searchData('/api/expenseTotal');

  //   data.total_expense == undefined || !data.total_expense
  //     ? (total_expense = 0)
  //     : (total_expense = data.total_expense);

  //   /* Carga gasto total */
  //   $('#expensesToDistribution').val(
  //     `$ ${total_expense.toLocaleString('es-CO')}`
  //   );
  //   $('#expensesToDistribution').prop('disabled', true);

  //   if (flag_expense == 0) {
  //     /* Seleccionar tipo de gasto */
  //     bootbox.confirm({
  //       closeButton: false,
  //       title: 'Tipo de Gasto',
  //       message: 'Seleccione a cual tipo de gasto desea ingresar.',
  //       buttons: {
  //         confirm: {
  //           label: 'Distribución',
  //           className: 'btn-success',
  //         },
  //         cancel: {
  //           label: 'Recuperación',
  //           className: 'btn-info',
  //         },
  //       },
  //       callback: function (result) {
  //         result == true ? (option = 1) : (option = 2);
  //         changeTypeExpense();
  //       },
  //     });
  //   } else {
  //     option = flag_expense; 

  //     setDataExpense(data);
  //   }
  // };
  getExpense = async () => {
    try {
      await searchData('/api/totalExpense');

      const data = await searchData('/api/expenseTotal');
      const total_expense = data.total_expense || 0;

      /* Carga gasto total */
      $('#expensesToDistribution').val(`$ ${total_expense.toLocaleString('es-CO')}`);
      $('#expensesToDistribution').prop('disabled', true);

      if (parseInt(flag_expense) === 0) {
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
          callback: result => {
            option = result ? 1 : 2;
            changeTypeExpense();
          },
        });
      } else {
        option = flag_expense;
        setDataExpense(data);
      }
    } catch (error) {
      console.error('Error fetching expense data:', error);
    }
  };

  getExpense();

  // changeTypeExpense = async () => {
  //   resp = await searchData(`/api/changeTypeExpense/${option}`);
  //   if (resp.success) toastr.success(resp.message);
  //   else toastr.error(resp.message);
  //   setDataExpense();
  // };

  // changeTypeExpenseDistribution = async () => {
  //   if (option_distribution == false)
  //     option_distribution = flag_expense_distribution;
  //   else {
  //     resp = await searchData(
  //       `/api/changeTypeExpenseDistribution/${option_distribution}`
  //     );
  //     if (resp.success) toastr.success(resp.message);
  //     else toastr.error(resp.message);
  //   }

  //   if (option_distribution == 2) {
  //     let buttons = document.getElementsByClassName(
  //       'cardBtnExpensesDistribution'
  //     )[0];

  //     buttons.insertAdjacentHTML(
  //       'beforebegin',
  //       `<div class="col-xs-2 btnButtons mr-2">
  //         <button class="btn btn-secondary" id="btnAddNewFamily">Nueva Familia</button>
  //       </div>
  //       <div class="col-xs-2 btnButtons mr-2">
  //         <button class="btn btn-secondary btnAddProductsFamilies" id="btnAddProductsFamilies">Asignar Productos</button>
  //       </div>
  //       `
  //     );
  //     $('.distribution').hide();
  //     $('.distributionFamilies').show(); 
  //     await loadFamilies(1);

  //     await loadTableExpensesDistributionFamilies();
  //   } else {
  //     $('.distribution').show();
  //     $('.distributionFamilies').hide();
  //     await loadAllDataDistribution();
  //   }
  // };
  const changeTypeExpense = async () => {
    try {
      const resp = await searchData(`/api/changeTypeExpense/${option}`);
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
        const resp = await searchData(`/api/changeTypeExpenseDistribution/${option_distribution}`);
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

  // setDataExpense = async () => {
  //   if ($.fn.dataTable.isDataTable('#tblExpenses')) {
  //     $('#tblExpenses').DataTable().destroy();
  //     $('#tblExpenses').empty();
  //   }

  //   if (option == 1) {
  //     $('.distributionExpenses').html('Distribución de Gastos');
  //     $('.cardBtnExpensesDistribution').show(800);

  //     if (type_expense == '1') {
  //       let buttons = document.getElementsByClassName(
  //         'cardBtnExpensesDistribution'
  //         )[1];
  //       buttons.innerHTML = "";

  //       buttons.insertAdjacentHTML(
  //         'beforeend',
  //         `<button class="${flag_expense_distribution == '1' ? 'btn btn-sm btn-primary':'btn btn-sm btn-outline-primary'} typeExpense" id="distribute" value="1">Por Producto</button>
  //          <button class="${flag_expense_distribution == '2' ? 'btn btn-sm btn-primary':'btn btn-sm btn-outline-primary'} typeExpense" id="family" value="2">Por Familia</button>
  //       `
  //       );
  //     }

  //     $('.cardBtnImportExpenses').show(800); 
  //     // Cambiar el atributo title del botón
  //     $('#btnImportNewExpenses').attr('title', 'Importar Distribución');
  //     // Si estás utilizando Bootstrap Tooltip, necesitas actualizar el tooltip manualmente
  //     $('#btnImportNewExpenses').tooltip('dispose').tooltip();
  //     $('#lblImportExpense').html('Importar Distribución de Gasto');
  //     $('#descrExpense').html('Distribución Gastos Generales');

  //     if (flag_expense_distribution == 0) {
  //       bootbox.confirm({
  //         closeButton: false,
  //         title: 'Tipo de Distribución',
  //         message:
  //           '¿Desea realizar la distribucion por familia?. Esta acción no se puede reversar',
  //         buttons: {
  //           confirm: {
  //             label: 'Si',
  //             className: 'btn-success',
  //           },
  //           cancel: {
  //             label: 'No',
  //             className: 'btn-danger',
  //           },
  //         },
  //         callback: function (result) {
  //           result == true
  //             ? (option_distribution = 2)
  //             : (option_distribution = 1);
  //           flag_expense_distribution = option_distribution;
  //           changeTypeExpenseDistribution();
  //         },
  //       });
  //     } else {
  //       option_distribution = false;
  //       changeTypeExpenseDistribution();
  //     }
  //   }
  //   if (option == 2) {
  //     $('.cardNewProducts').hide();
  //     $('.cardCheckExpense').show(800);
  //     // $('.generalExpenses').hide();
  //     $('.distributionExpenses').html('Recuperación de Gastos');
  //     $('.cardBtnExpenseRecover').show(800);
  //     $('.cardBtnImportExpenses').show(800); 
  //     // Cambiar el atributo title del botón
  //     $('#btnImportNewExpenses').attr('title', 'Importar Recuperar Gastos');
  //     // Si estás utilizando Bootstrap Tooltip, necesitas actualizar el tooltip manualmente
  //     $('#btnImportNewExpenses').tooltip('dispose').tooltip();
  //     $('#lblImportExpense').html('Importar Recuperación');
  //     $('#descrExpense').html('Recuperación Gastos Generales');
  //     loadTableExpenseRecover();
  //   }
  // };

  // $(document).on('click','.typeExpense', function () { 
  //   let op = this.value;

  //   bootbox.confirm({
  //     title: 'Cambiar Tipo Distribución',
  //     message:
  //       `Está seguro de cambiar el tipo de distribución a ${op == '1' ? 'distribución por producto': 'distribución por familia'}?`,
  //     buttons: {
  //       confirm: {
  //         label: 'Si',
  //         className: 'btn-success',
  //       },
  //       cancel: {
  //         label: 'No',
  //         className: 'btn-danger',
  //       },
  //     },
  //     callback: function (result) {
  //       if (result == true) {
  //         $.get(
  //           `/api/changeTypeExpenseDistribution/${op}`,
  //           function (data, textStatus, jqXHR) {
  //             if (data.success) {
  //               toastr.success(data.message);
  //               flag_expense_distribution = data.flag;
  //               $('.btnButtons').remove();
  //               $('.cardExpensesDistribution').hide();

  //               setDataExpense();
  //             }
  //             else toastr.error(data.message);
  //           }
  //         );
  //       }
  //     },
  //   });
    
  // });
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
          // buttons.insertAdjacentHTML(
          //   'beforeend',
          //   `<button class="btn btn-sm ${flag_expense_distribution == '1' ? 'btn-primary' : 'btn-outline-primary'} typeExpense" id="distribute" value="1">Por Producto</button>
          //  <button class="btn btn-sm ${flag_expense_distribution == '2' ? 'btn-primary' : 'btn-outline-primary'} typeExpense" id="family" value="2">Por Familia</button>`
          // );
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
          $.get(`/api/changeTypeExpenseDistribution/${op}`, function (data, textStatus, jqXHR) {
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

});
