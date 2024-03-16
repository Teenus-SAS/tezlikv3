$(document).ready(function () {
  let title3 = `${inyection == 1 ? 'Tiempo/Und' : 'Tiempo Alistamiento (min)'}`;
  let value3 = `${inyection == 1 ? 'unity_time' : 'enlistment_time'}`;
  let title4 = `${inyection == 1 ? '% Eficiencia' : 'Tiempo Operación (min)'}`;
  dataProductProcess = []; 

  /* Seleccion producto */
  $('#refProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;

    $('#selectNameProduct option').prop('selected', function () {
      return $(this).val() == id;
    });

    loadtableProcess(id);
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;

    $('#refProduct option').prop('selected', function () {
      return $(this).val() == id;
    });

    loadtableProcess(id);
  });

  loadAllDataProcess = async (id) => {
    try {
      const productsProcess = await searchData('/api/allProductsProcess');

      dataProductProcess = productsProcess;

      if (id != 0) loadtableProcess(id);
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  loadAllDataProcess(0);

  flag_employee == '1' ? visible = true : visible = false;

  /* Cargue tabla de Proyectos */

  const loadtableProcess = (id) => {
    $('.cardAddProcess').hide(800);

    let data = dataProductProcess.filter(item => item.id_product == id);

    if ($.fn.dataTable.isDataTable("#tblConfigProcess")) {
      $("#tblConfigProcess").DataTable().destroy();
      $("#tblConfigProcess").empty();
      $('#tblConfigProcess').append(`
        <tbody id="tblConfigProcessBody"></tbody>
        <tfoot>
          <tr>
            <th></th>
            <th></th>
            <th>Total:</th>
            <th id="totalAlistment"></th>
            <th id="totalOperation"></th>
            <th id="totalWorkforce"></th>
            <th id="totalIndirect"></th>
            ${flag_employee == '1' ? '<th></th>' : ''}
            <th></th>
          </tr>
        </tfoot>`);
    }
    // Encabezados de la tabla
    var headers = ['No.', 'Proceso', 'Máquina', title3, title4, 'Mano de Obra', 'Costo Indirecto', '', 'Acciones'];

    if (visible == false)
      headers.splice(7, 1);
    
    // Obtén la tabla
    var table = document.getElementById('tblConfigProcess');

    // Crea la fila de encabezados
    var headerRow = table.createTHead().insertRow();
    headers.forEach(function (header) {
      var th = document.createElement('th');
      th.textContent = header;
      headerRow.appendChild(th);
    });

    $('#tblConfigProcessBody').empty();
    var body = document.getElementById('tblConfigProcessBody');

    data.forEach((arr, index) => {
      const i = index;
      const dataRow = body.insertRow();
      dataRow.setAttribute('data-index', index);
      headers.forEach((header, columnIndex) => {
        const cell = dataRow.insertCell();
        switch (header) {
          case 'No.':
            cell.textContent = i + 1;
            break;
          case 'Proceso':
            cell.textContent = arr.process;
            break;
          case 'Máquina':
            cell.textContent = arr.machine;
            break;
          case title3:
            let value = parseFloat(arr[value3]);
            
            if (Math.abs(value) < 0.01) {
              // let decimals = contarDecimales(data);
              // data = formatNumber(data, decimals);
              value = value.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            } else
              value = value.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
            cell.textContent = value;
            break;
          case title4:
            let operation_time = parseFloat(arr.operation_time);
            
            if (Math.abs(operation_time) < 0.01) { 
              operation_time = operation_time.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            } else
              operation_time = operation_time.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
            cell.textContent = operation_time;
            break;
          case 'Mano de Obra':
            let workforce_cost = parseFloat(arr.workforce_cost);
            
            if (Math.abs(workforce_cost) < 0.01) { 
              workforce_cost = workforce_cost.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            } else
              workforce_cost = workforce_cost.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
            cell.textContent = `$ ${workforce_cost}`;
            break;
          case 'Costo Indirecto':
            let indirect_cost = parseFloat(arr.indirect_cost);
            
            if (Math.abs(indirect_cost) < 0.01) { 
              indirect_cost = indirect_cost.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            } else
              indirect_cost = indirect_cost.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
            cell.textContent = `$ ${indirect_cost}`;
            break;
          case '':
            let textContent = '';

            if(parseInt(arr.auto_machine) === 0)
              textContent `<a href="javascript:;" <i id="${arr.id_product_process}" class="bi bi-arrow-down-up updateEmployee" data-toggle='tooltip' title='Modificar Empleados' style="font-size: 30px; color:orange;"></i></a>`;

            cell.innerHTML = textContent;
            break;
          case 'Acciones':  
            cell.innerHTML =
               `<a href="javascript:;" <i id="${arr.id_product_process}" class="bx bx-edit-alt updateProcess" data-toggle='tooltip' title='Actualizar Proceso' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${arr.id_product_process}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Proceso' style="font-size: 30px;color:red" onclick="deleteProcess(${arr.id_product_process})"></i></a>`;
            break;
          default:
            cell.textContent = '';
            break;
        }
      });
    });

    $('#tblConfigProcess').dataTable();

    dragula([document.getElementById('tblConfigProcessBody')]).on('drop', function (el, container, source, sibling) {
      // Obtener el indice de la fila anterior
      var previousIndex = parseInt(el.dataset.index) + 1;      

      // Obtener el índice de fila actual
      var currentIndex = el.closest('tr').rowIndex;
      let copy = [];

      // If the row was dropped within the same container,
      // move it to the specified position
      if (container === source) {
        var targetIndex = sibling ? sibling.rowIndex - 1 : container.children.length - 1;
        
        container.insertBefore(el, container.children[targetIndex]);
        
        var targetIndex = sibling ? sibling.rowIndex - 1 : container.children.length - 1;
        
        container.insertBefore(el, container.children[targetIndex]);

        let data = dataProductProcess.filter(item => item.id_product == $('#refProduct').val());

        copy.push(data[previousIndex - 1]);
        copy.push(data[currentIndex - 1]); 

        copy[0]['route'] = currentIndex;
        copy[1]['route'] = previousIndex; 

        $.ajax({
          type: "POST",
          url: "/api/saveRouteProductProcess",
          data: { data: copy },
          success: function (resp) {
            messageProcess(resp);
          }
        });
      } else {
        // If the row was dropped into a different container,
        // move it to the first position
        container.insertBefore(el, container.firstChild);
      }
    });

    let alistment = 0;
    let operation = 0;
    let workForce = 0;
    let indirect = 0;

    data.forEach(item => {
      alistment += item[value3];
      operation += item.operation_time;
      workForce += item.workforce_cost;
      indirect += item.indirect_cost;
    });

    $('#totalAlistment').html(alistment.toLocaleString('es-CO', { maximumFractionDigits: 2 }));
    $('#totalOperation').html(operation.toLocaleString('es-CO', { maximumFractionDigits: 2 }));
    $('#totalWorkforce').html(`$ ${workForce.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);
    $('#totalIndirect').html(`$ ${indirect.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);
    
    // tblConfigProcess = $('#tblConfigProcess').dataTable({
    //   destroy: true,
    //   pageLength: 50,
    //   ajax: {
    //     url: `/api/productsProcess/${idProduct}`,
    //     dataSrc: '',
    //   },
    //   dom: '<"datatable-error-console">frtip',
    //   language: {
    //     url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
    //   },
    //   fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
    //     if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
    //       console.error(oSettings.json.error);
    //     }
    //   },
    //   columns: [
    //     {
    //       title: 'No.',
    //       data: null,
    //       className: 'uniqueClassName',
    //       render: function (data, type, full, meta) {
    //         return meta.row + 1;
    //       },
    //     },
    //     {
    //       title: 'Proceso',
    //       data: 'process',
    //     },
    //     {
    //       title: 'Máquina',
    //       data: 'machine',
    //     },
    //     {
    //       title: title3,
    //       data: value3,
    //       className: 'classCenter',
    //       render: function (data) {
    //         data = parseFloat(data);
            
    //         if (Math.abs(data) < 0.01) {
    //           // let decimals = contarDecimales(data);
    //           // data = formatNumber(data, decimals);
    //           data = data.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
    //         } else
    //           data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
    //         return data;
    //       },
    //     },
    //     {
    //       title: title4,
    //       data: 'operation_time',
    //       className: 'classCenter',
    //       render: function (data) {
    //         data = parseFloat(data);

    //         if (Math.abs(data) < 0.01) {
    //           // let decimals = contarDecimales(data);
    //           // data = formatNumber(data, decimals);
    //           data = data.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
    //         } else
    //           data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
    //         return data;
    //       },
    //     },
    //     {
    //       title: 'Mano De Obra',
    //       data: 'workforce_cost',
    //       className: 'classCenter',
    //       render: function (data) {
    //         data = parseFloat(data);

    //         if (Math.abs(data) < 0.01) {
    //           // let decimals = contarDecimales(data);
    //           // data = formatNumber(data, decimals);
    //           data = data.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
    //         } else
    //           data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
    //         return `$ ${data}`;
    //       },
    //     },
    //     {
    //       title: 'Costo Indirecto',
    //       data: 'indirect_cost',
    //       className: 'classCenter',
    //       render: function (data) {
    //         data = parseFloat(data);

    //         if (Math.abs(data) < 0.01) {
    //           // let decimals = contarDecimales(data);
    //           // data = formatNumber(data, decimals);
    //           data = data.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
    //         } else
    //           data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
    //         return `$ ${data}`;
    //       },
    //     },
    //     {
    //       title: '',
    //       data: null,
    //       className: 'uniqueClassName',
    //       visible: visible,
    //       render: function (data) {
    //         if (parseInt(data.auto_machine) === 0)
    //           return `<a href="javascript:;" <i id="${data.id_product_process}" class="bi bi-arrow-down-up updateEmployee" data-toggle='tooltip' title='Modificar Empleados' style="font-size: 30px; color:orange;"></i></a>`;
    //         else return '';
    //       },
    //     },
    //     {
    //       title: 'Acciones',
    //       data: 'id_product_process',
    //       className: 'uniqueClassName',
    //       render: function (data) {
    //         return `
            
    //             <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateProcess" data-toggle='tooltip' title='Actualizar Proceso' style="font-size: 30px;"></i></a>
    //             <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Proceso' style="font-size: 30px;color:red" onclick="deleteProcess()"></i></a>`;
    //       },
    //     },
    //   ],
    //   footerCallback: function (row, data, start, end, display) {
    //     enlistmentTime = this.api()
    //       .column(3)
    //       .data()
    //       .reduce(function (a, b) {
    //         return parseFloat(a) + parseFloat(b);
    //       }, 0);

    //     $(this.api().column(3).footer()).html(
    //       new Intl.NumberFormat('de-DE').format(enlistmentTime)
    //     );
    //     operationTime = this.api()
    //       .column(4)
    //       .data()
    //       .reduce(function (a, b) {
    //         return parseFloat(a) + parseFloat(b);
    //       }, 0);

    //     $(this.api().column(4).footer()).html(
    //       new Intl.NumberFormat('de-DE').format(operationTime)
    //     );

    //     workForce = this.api()
    //       .column(5)
    //       .data()
    //       .reduce(function (a, b) {
    //         return parseFloat(a) + parseFloat(b);
    //       }, 0);

    //     $(this.api().column(5).footer()).html(
    //       `$ ${new Intl.NumberFormat('de-DE').format(workForce)}`
    //     );
    //     indirectCost = this.api()
    //       .column(6)
    //       .data()
    //       .reduce(function (a, b) {
    //         return parseFloat(a) + parseFloat(b);
    //       }, 0);

    //     $(this.api().column(6).footer()).html(
    //       `$ ${new Intl.NumberFormat('de-DE').format(indirectCost)}`
    //     );
    //   },
    // });
  };
});
