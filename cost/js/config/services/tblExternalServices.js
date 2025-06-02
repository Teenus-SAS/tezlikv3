$(document).ready(function () {
  /* Seleccion producto */
  $("#refProduct").change(function (e) {
    e.preventDefault();
    let id = this.value;

    $('#selectNameProduct option').prop('selected', function () {
      return $(this).val() == id;
    });

    loadAllDataServices(id);
  });

  $("#selectNameProduct").change(function (e) {
    e.preventDefault();
    let id = this.value;

    $('#refProduct option').prop('selected', function () {
      return $(this).val() == id;
    });

    loadAllDataServices(id);
  });

  loadAllDataServices = async (id) => {
    try {
      const services = await searchData(`/api/externalServices/${id}`);

      sessionStorage.setItem('dataServices', JSON.stringify(services));
      // dataServices = services;

      let op = 1;
      if (flag_currency_usd == '1') {
        let selectPriceUSD = $('#selectPriceUSD3').val();

        selectPriceUSD == '2' ? op = 2 : op = 1;
      }

      loadTableExternalServices(services, op);
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  // loadAllDataServices(0);

  /* Cargue tabla de Proyectos */
  loadTableExternalServices = (data, op) => {
    $('.cardAddService').hide(800);
    // let dataServices = JSON.parse(sessionStorage.getItem('dataServices'));
    // let data = dataServices.filter(item => item.id_product == id);

    // if ($.fn.dataTable.isDataTable("#tblExternalServices")) {
    //   $("#tblExternalServices").DataTable().clear();
    //   $("#tblExternalServices").DataTable().rows.add(data).draw();
    //   return;
    // }
    // if ($.fn.dataTable.isDataTable("#tblExternalServices")) {
    //   var table = $("#tblExternalServices").DataTable();
    //   var pageInfo = table.page.info(); // Guardar información de la página actual
    //   table.clear();
    //   table.rows.add(data).draw();
    //   table.page(pageInfo.page).draw('page'); // Restaurar la página después de volver a dibujar los datos
    //   return;
    // }

    tblExternalServices = $("#tblExternalServices").dataTable({
      destroy: true,
      pageLength: 50,
      data: data,
      dom: '<"datatable-error-console">frtip',
      language: {
        url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json",
      },
      fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
        if (oSettings.json && oSettings.json.hasOwnProperty("error")) {
          console.error(oSettings.json.error);
        }
      },
      columns: [
        {
          title: "No.",
          data: null,
          className: "uniqueClassName",
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
        },
        {
          title: "Servicio",
          data: "name_service",
        },
        {
          title: "Costo",
          data: function (row, type, val, meta) {
            if (flag_currency_usd == '1' && op == 2) {
              return parseFloat(row.cost) / parseFloat(coverage_usd);
            } else {
              return row.cost;
            }
          },
          className: "classRight",
          render: (data, type, row) => renderCost(data, op)
        },
        {
          title: "Acciones",
          data: "id_service",
          className: "uniqueClassName",
          render: function (data) {
            return `
                        <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateExternalService" data-toggle='tooltip' title='Actualizar Servicio' style="font-size: 30px;"></i></a>
                        <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Servicio' style="font-size: 30px;color:red" onclick="deleteService()"></i></a>`;
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
      footerCallback: function (row, data, start, end, display) {
        let totalCost = 0;

        for (let i = 0; i < data.length; i++) {
          totalCost += flag_currency_usd == '1' && op == 2 ? parseFloat(data[i].cost) / parseFloat(coverage_usd) : parseFloat(data[i].cost);
        }

        totalCost = renderCost(totalCost, op);

        $(this.api().column(2).footer()).html(totalCost);
      },
    });
  };
});
