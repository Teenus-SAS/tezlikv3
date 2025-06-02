$(document).ready(function () {
  /* Cargue tabla de Proyectos */
  loadTableGExternalServices = (data) => {
    if ($.fn.dataTable.isDataTable("#tblExternalServices")) {
      var table = $("#tblExternalServices").DataTable();
      var pageInfo = table.page.info(); // Guardar información de la página actual
      table.clear();
      table.rows.add(data).draw();
      table.page(pageInfo.page).draw('page'); // Restaurar la página después de volver a dibujar los datos
      return;
    }

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
          data: "cost",
          className: "classRight",
          render: function (data) {
            data = parseFloat(data);
            if (Math.abs(data) < 0.01) {
              // let decimals = contarDecimales(data);
              // data = formatNumber(data, decimals);
              data = data.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            } else
              data = data.toLocaleString('es-CO', { maximumFractionDigits: 2 });

            return `$ ${data}`;
          },
        },
        {
          title: "Acciones",
          data: "id_general_service",
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
          totalCost += parseFloat(data[i].cost);
        }

        $(this.api().column(2).footer()).html(
          `$ ${totalCost.toLocaleString("es-CO", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })}`
        );
      },
    });
  };
});
