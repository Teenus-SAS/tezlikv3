$(document).ready(function () {
  /* Seleccion producto */

  $("#refProduct").change(function (e) {
    e.preventDefault();
    let id = this.value;
    $("#selectNameProduct option").removeAttr("selected");
    $(`#selectNameProduct option[value=${id}]`).attr("selected", true);
    loadtableExternalServices(id);
  });

  $("#selectNameProduct").change(function (e) {
    e.preventDefault();
    let id = this.value;
    $("#refProduct option").removeAttr("selected");
    $(`#refProduct option[value=${id}]`).attr("selected", true);
    loadtableExternalServices(id);
  });

  /* Cargue tabla de Proyectos */

  const loadtableExternalServices = (idProduct) => {
    tblExternalServices = $("#tblExternalServices").dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: `../../api/externalservices/${idProduct}`,
        dataSrc: "",
      },
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
          title: "Referencia",
          data: "reference",
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
            let decimals = contarDecimales(data);
            let cost = formatNumber(data, decimals);

            return cost;
          },
          footer: "Total Costo", // Añadir el pie de columna
        },
        {
          title: "Acciones",
          data: "id_service",
          className: "uniqueClassName",
          render: function (data) {
            return `
                        <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateExternalService" data-toggle='tooltip' title='Actualizar Servicio' style="font-size: 30px;"></i></a>
                        <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Servicio' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
          },
        },
      ],
      footerCallback: function (row, data, start, end, display) {
       let totalCost = 0;

        for (let i = 0; i < data.length; i++) {
          totalCost += parseFloat(data[i].cost);
        }

        $(this.api().column(3).footer()).html(
          totalCost.toLocaleString("es-CO", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          })
        );
      },
    });
  };
});
