$(document).ready(function () {
  /* Cargue tabla de Precios */

  loadTblPrices = async () => {
    let data = await searchData("/api/prices");
    let acumulated = 0;

    for (let i = 0; i < data.length; i++) {
      acumulated += data[i].sale_price;
    }

    acumulated == 0 ? (visible = false) : (visible = true);

    tblPrices = $("#tblPrices").DataTable({
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
          title: "Referencia",
          data: "reference",
          className: "uniqueClassName",
        },
        {
          title: "Producto",
          data: "product",
          className: "classCenter",
        },
        {
          title: "Precio (Sugerido)",
          data: "price",
          className: "classCenter",
          render: $.fn.dataTable.render.number(".", ",", 0, "$ "),
        },
        {
          title: "Precio (Actual)",
          data: "sale_price",
          className: "classCenter",
          visible: visible,
          render: function (data) {
            if (data > 0)
              return `$ ${data.toLocaleString("es-CO", {
                maximumFractionDigits: 0,
              })}`;
            else return "";
          },
        },
        {
          title: "Rentabilidad",
          data: null,
          className: "classCenter",
          visible: visible,
          render: function (data) {
            let dataCost = getDataCost(data);
            if (!isFinite(dataCost.actualProfitability))
              dataCost.actualProfitability = 0;

            let profitabilityText = `${dataCost.actualProfitability.toLocaleString(
              "es-CO",
              { maximumFractionDigits: 0 }
            )} %`;
            let badgeClass = "";

            if (
              dataCost.actualProfitability < data.profitability &&
              dataCost.actualProfitability > 0 &&
              data.sale_price > 0
            ) {
              badgeClass = "badge badge-warning"; // Use "badge badge-warning" for orange
            } else if (
              dataCost.actualProfitability < data.profitability &&
              data.sale_price > 0
            ) {
              badgeClass = "badge badge-danger"; // Use "badge badge-danger" for red
            } else badgeClass = "badge badge-success"; // Use "badge badge-danger" for red
            if (badgeClass) {
              return `<span class="${badgeClass}" style="font-size: medium;" >${profitabilityText}</span>`;
            } else {
              return profitabilityText;
            }
          },
        },
        {
          title: "Img",
          data: "img",
          className: "uniqueClassName",
          render: (data, type, row) => {
            data == "" || !data
              ? (txt = "")
              : (txt = `<img src="${data}" alt="" style="width:50px;border-radius:100px">`);
            return txt;
          },
        },
        {
          title: "Acciones",
          data: "id_product",
          className: "uniqueClassName",
          render: function (data) {
            return `<a href="/cost/details-prices" <i id="${data}" class="bi bi-zoom-in seeDetail" data-toggle='tooltip' title='Ficha de Costos' style="font-size: 30px;"></i></a>`;
          },
        },
      ],
      /* rowCallback: function (row, data, index) {
        let dataCost = getDataCost(data);
        !isFinite(dataCost.actualProfitability)
          ? (dataCost.actualProfitability = 0)
          : dataCost.actualProfitability;

        if (
          dataCost.actualProfitability < data.profitability &&
          dataCost.actualProfitability > 0 &&
          data.sale_price > 0
        )
          $(row).css("color", "orange");
        else if (
          dataCost.actualProfitability < data.profitability &&
          data.sale_price > 0
        )
          $(row).css("color", "red");

        if (data.details_product == 0) {
          tblPrices.column(7).visible(false);
        }
      },*/
    });
  };

  loadTblPrices();
});
