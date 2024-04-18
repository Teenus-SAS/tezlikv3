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
            <th id="totalEfficiency"></th>
            <th id="totalWorkforce"></th>
            <th id="totalIndirect"></th>
            ${flag_employee == '1' ? '<th></th>' : ''}
            <th></th>
          </tr>
        </tfoot>`);
    }

    let totalEfficiency = 0;

    data.forEach(item => {
      totalEfficiency += parseFloat(item.efficiency);
    });
 
    // Encabezados de la tabla
    var headers = ['No.', 'Proceso', 'Máquina', title3, title4, 'Eficiencia', 'Mano de Obra', 'Costo Indirecto', '', 'Acciones'];

    if (visible == false)
      headers.splice(8, 1);
    
    if (totalEfficiency == 0)
      headers.splice(5, 1);
    
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

      dataRow.classList.add('t-row'); // Agregar la clase 't-row' a la fila
      dataRow.setAttribute('data-index', index);
      dataRow.setAttribute('data-id', arr.id_product_process);

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
          case 'Eficiencia':
            let efficiency = parseFloat(arr.efficiency);
            
            if (Math.abs(efficiency) < 0.01) { 
              efficiency = efficiency.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            } else
              efficiency = efficiency.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
            cell.textContent = `${efficiency} %`;
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

    if (totalEfficiency == 0) $('#totalEfficiency').remove();

    $('#tblConfigProcess').dataTable({
      pageLength: 50,
      dom: '<"datatable-error-console">frtip',
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      },
      fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
        if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
          console.error(oSettings.json.error);
        }
      },
    });

    dragula([document.getElementById('tblConfigProcessBody')]).on('drop', function (el, container, source, sibling) {
      let copy = [];

      // If the row was dropped within the same container,
      // move it to the specified position
      if (container === source) {
        var targetIndex = sibling ? sibling.rowIndex - 1 : container.children.length - 1;
        
        container.insertBefore(el, container.children[targetIndex]);
        
        var elements = $('.t-row');
        elements = elements.not('.gu-mirror');

        for (let i = 0; i < elements.length; i++) {
          copy.push({ id_product_process: elements[i].dataset.id, route: i + 1 });
        } 

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
    let efficiency = 0;
    let workForce = 0;
    let indirect = 0;

    data.forEach(item => {
      alistment += parseFloat(item[value3]);
      operation += parseFloat(item.operation_time);
      efficiency += parseFloat(item.efficiency);
      workForce += parseFloat(item.workforce_cost);
      indirect += parseFloat(item.indirect_cost);
    });

    efficiency = efficiency / data.length;

    $('#totalAlistment').html(alistment.toLocaleString('es-CO', { maximumFractionDigits: 2 }));
    $('#totalOperation').html(operation.toLocaleString('es-CO', { maximumFractionDigits: 2 }));
    $('#totalEfficiency').html(`${efficiency.toLocaleString('es-CO', { maximumFractionDigits: 2 })} %`);
    $('#totalWorkforce').html(`$ ${workForce.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);
    $('#totalIndirect').html(`$ ${indirect.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);
  };
});
