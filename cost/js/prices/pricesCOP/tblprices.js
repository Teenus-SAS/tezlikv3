$(document).ready(function () {
  op1 = 1;

  currentDollar = 0;
  allPrices = [];
  parents = []; 

  loadAllData = async () => {
    try {
      const [prices, actualTrm] = await Promise.all([
        searchData("/api/prices"),
        searchData("/api/currentDollar"),
      ]);

      allPrices = prices;
      currentDollar = actualTrm[0]["valor"];

      // price_usd == '1' &&
      if (flag_currency_usd == "1") {
        $("#exchangeCoverageUSD").val(
          `$ ${(currentDollar - parseFloat(coverage_usd)).toLocaleString("es-CO", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          })}`
        );
      }

      let typeCurrency = sessionStorage.getItem("typeCurrency");
      typeCurrency == "2" && flag_currency_usd == "1" ?
        (op1 = 2)
        :
        typeCurrency == "3" && flag_currency_eur == "1" ?
          (op1 = 3) :
          (op1 = 1);

      // parents = prices.filter((item) => item.composite == 0);
      // composites = prices.filter((item) => item.composite == 1);

      // if (flag_composite_product == "1") {
      //   loadTblPrices(parents, op1);
      // } else
      loadTblPrices(prices, op1);
    } catch (error) {
      console.error("Error loading data:", error);
    }
  };

  /* Cargue tabla de Precios */
  loadTblPrices = async (data, op, valueCoverage) => {
    let acumulated = 0;

    for (let i = 0; i < data.length; i++) {
      acumulated += data[i].sale_price;
    }

    acumulated == 0 ? (visible = false) : (visible = true);
 
    title = "Margen";

    tblPrices = $("#tblPrices").DataTable({
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
          title: `${op == 1 ? "Precio Sugerido" : op == 3 ? "Precio Sugerido (EUR)" : "Precio Sugerido (USD)"}`,
          data: function (data, type, val, meta) {
            switch (op) {
              case 1:// COP
                return parseFloat(data.price);
              case 3:// EUR
                return parseFloat(data.price_eur);
              default:// USD
                return parseFloat(data.price_usd);
            }
          },
          className: "classCenter",
          render: (data, type, row) => renderCost(data, op),
        },
        {
          title: "Precio Simulación",
          data: function (data, type, val, meta) {
            if (!valueCoverage) return "";
            else
              return parseFloat(data.price) / parseFloat(valueCoverage);
          },
          className: "classCenter",
          visible: op == 4 ? true : false,
          render: (data, type, row) => renderCost(data, op),
        },
        {
          title: `${op == 1 ? "Precio (Lista)" : op == 3 ? "Precio Lista (EUR)" : "Precio Lista (USD)"}`,
          data: function (data, type, val, meta) {
            switch (op) {
              case 1:// COP
                return parseFloat(data.sale_price);
              case 3:// EUR
                return parseFloat(data.sale_price_eur);
              default:// USD
                return parseFloat(data.sale_price_usd);
            }
          },
          className: "classCenter",
          visible: visible,
          render: (data, type, row) => renderCost(data, op), 
        },
        {
          title: "Precio (Lista) Simulación",
          data: function (data, type, val, meta) {
            if (!valueCoverage) return "";
            else
              return parseFloat(data.sale_price) / parseFloat(valueCoverage);
          },
          className: "classCenter",
          visible: op == 4 && visible == true ? true : false,
          render: (data, type, row) => renderCost(data, op),
        },
        {
          title: title,
          data: null,
          className: "classCenter",
          visible: visible,
          render: function (data) {
            let dataCost = getDataCost(data);
            if (!isFinite(dataCost.actualProfitability2))
              dataCost.actualProfitability2 = 0;

            let profitabilityText = `${dataCost.actualProfitability2.toLocaleString(
              "es-CO",
              {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              }
            )} %`;
            let badgeClass = "";

            if (
              dataCost.actualProfitability2 < data.profitability &&
              dataCost.actualProfitability2 > 0 &&
              data.sale_price > 0
            ) {
              badgeClass = "badge badge-warning"; // Use "badge badge-warning" for orange
            } else if (
              dataCost.actualProfitability2 < data.profitability &&
              data.sale_price > 0
            ) {
              badgeClass = "badge badge-danger"; // Use "badge badge-danger" for red
            } else badgeClass = "badge badge-success"; // Use "badge badge-danger" for red

            if (data.sale_price == 0) {
              badgeClass = "badge badge-primary"; // Use "badge badge-warning" for orange
              profitabilityText = `${data.profitability.toLocaleString(
                "es-CO",
                {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                }
              )} %`;
            }

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
    });
  };

  const renderCost = (data, op) => {
    if (data > 0 || data != '') {
      if (Math.abs(data) < 0.01) {
        data = data.toLocaleString("es-CO", {
          minimumFractionDigits: 2,
          maximumFractionDigits: 9,
        });
      } else if (op == 1)
        data = data.toLocaleString("es-CO", {
          maximumFractionDigits: 0,
        });
      else
        data = data.toLocaleString("es-CO", {
          maximumFractionDigits: 2,
        });

      return `$ ${data}`;
    } else return "";
  }

  loadAllData();
});
