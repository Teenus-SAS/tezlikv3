$(document).ready(function () {
  dataProcess = [];

  loadTblProcess = async () => {
    dataProcess = await searchData('/api/process');

    if ($.fn.dataTable.isDataTable("#tblProcess")) {
      $("#tblProcess").DataTable().destroy();
      $("#tblProcess").empty();
      $('#tblProcess').append(`<tbody id="tblProcessBody"></tbody>`);
    }
    // Encabezados de la tabla
    var headers = ['No.', 'Proceso', 'Acciones'];

    // Obtén la tabla
    var table = document.getElementById('tblProcess');

    // Crea la fila de encabezados
    var headerRow = table.createTHead().insertRow();
    headers.forEach(function (header) {
      var th = document.createElement('th');
      th.textContent = header;
      headerRow.appendChild(th);
    });

    $('#tblProcessBody').empty();
    var body = document.getElementById('tblProcessBody');

    dataProcess.forEach((arr, index) => {
      const i = index;
      const dataRow = body.insertRow();

      dataRow.classList.add('t-row'); // Agregar la clase 't-row' a la fila
      dataRow.setAttribute('data-index', index);
      dataRow.setAttribute('data-id', arr.id_process);

      headers.forEach((header, columnIndex) => {
        const cell = dataRow.insertCell();
        switch (header) {
          case 'No.':
            cell.textContent = i + 1;
            break;
          case 'Proceso':
            cell.textContent = arr.process;
            break;
          case 'Acciones':
            if (arr.count_payroll == 0)
              icon = '/global/assets/images/trash_v.png';
            else
              icon = '/global/assets/images/trash_x.png';

            cell.innerHTML = `<a href="javascript:;" <i id="${arr.id_process}" class="bx bx-edit-alt updateProcess" data-toggle='tooltip' title='Actualizar Proceso' style="font-size: 30px;"></i></a>
                <a href="javascript:;"><img src="${icon}" alt="Eliminar Proceso" id="${arr.id_process}" style="width:30px;height:30px;margin-top:-20px" onclick="deleteFunction(${arr.id_process})"></a>`
            break;
          default:
            cell.textContent = '';
            break;
        }
      });
    });

    $('#tblProcess').dataTable({
      pageLength: 50,
      dom: '<"datatable-error-console">frtip',
      language: {
        url: '/assets/plugins/i18n/Spanish.json',
      },
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
      fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
        if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
          console.error(oSettings.json.error);
        }
      },
    });

    dragula([document.getElementById('tblProcessBody')]).on('drop', async function (el, container, source, sibling) {
      let copy = [];

      // If the row was dropped within the same container,
      // move it to the specified position
      if (container === source) {
        var targetIndex = sibling ? sibling.rowIndex - 1 : container.children.length - 1;

        container.insertBefore(el, container.children[targetIndex]);
        var elements = $('.t-row');
        elements = elements.not('.gu-mirror');

        for (let i = 0; i < elements.length; i++) {
          copy.push({ id_process: elements[i].dataset.id, route: i + 1 });
        }

        $.ajax({
          type: "POST",
          url: "/api/saveRouteProcess",
          data: { data: copy },
          success: function (resp) {
            message(resp);
          }
        });
      } else {
        // If the row was dropped into a different container,
        // move it to the first position
        container.insertBefore(el, container.firstChild);
      }
    });
  };

  loadTblProcess();
});
