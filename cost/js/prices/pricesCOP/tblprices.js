$(document).ready(function () {
  op1 = 1;

  currentDollar = 0;
  allPrices = [];
  parents = [];

  $("#btnComposite").click(function (e) {
    e.preventDefault();

    if (op1 == 1) {
      op1 = 2;
      loadTblPrices(composites, 1);
    } else {
      op1 = 1;
      loadTblPrices(parents, 1);
    }
  });

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

      parents = prices.filter((item) => item.composite == 0);
      composites = prices.filter((item) => item.composite == 1);

      if (flag_composite_product == "1") {
        loadTblPrices(parents, op1);
      } else loadTblPrices(prices, op1);
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

    /* if (id_company == '10')
      title = 'Margen';
    else
      title = 'Margen';  */
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
          title: `${op == 1 ? "Precio (Sugerido)" : op == 3 ? "Precio (Sugerido EUR)" : "Precio (Sugerido USD)"}`,
          data: null,
          className: "classCenter",
          render: function (data) {
            op == 1
              ? (price = parseFloat(data.price))
              : op == 2 ?
                (price = parseFloat(data.price_usd))
                : (price = parseFloat(data.price_eur));

            if (Math.abs(price) < 0.01) {
              price = price.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 9,
              });
            } else if (op == 1)
              price = price.toLocaleString("es-CO", {
                maximumFractionDigits: 0,
              });
            else
              price = price.toLocaleString("es-CO", {
                maximumFractionDigits: 2,
              });

            return `$ ${price}`;
          },
        },
        {
          title: "Precio (Sugerido USD) Sim",
          data: null,
          className: "classCenter",
          visible: op == 4 ? true : false,
          render: function (data) {
            if (!valueCoverage) return "";
            else price = parseFloat(data.price) / parseFloat(valueCoverage);

            if (Math.abs(price) < 0.01)
              price = price.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 9,
              });
            else
              price = price.toLocaleString("es-CO", {
                maximumFractionDigits: 2,
              });

            return `$ ${price}`;
          },
        },
        {
          title: `${op == 1 ? "Precio (Lista)" : op == 3 ? "Precio (Lista EUR)" : "Precio (Lista USD)"}`,
          data: null,
          className: "classCenter",
          visible: visible,
          render: function (data) {
            op == 1
              ? (price = parseFloat(data.sale_price))
              : op == 2 ?
              (price = parseFloat(data.sale_price_usd)) :
              (price = parseFloat(data.sale_price_eur));

            if (price > 0) {
              if (Math.abs(price) < 0.01) {
                price = price.toLocaleString("es-CO", {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 9,
                });
              } else if (op == 1)
                price = price.toLocaleString("es-CO", {
                  maximumFractionDigits: 0,
                });
              else
                price = price.toLocaleString("es-CO", {
                  maximumFractionDigits: 2,
                });

              return `$ ${price}`;
            } else return "";
          },
        },
        {
          title: "Precio (Lista) Sim",
          data: null,
          className: "classCenter",
          visible: op == 4 ? true : false,
          render: function (data) {
            if (!valueCoverage) return "";
            else
              price = parseFloat(data.sale_price) / parseFloat(valueCoverage);

            if (price > 0) {
              if (Math.abs(price) < 0.01)
                price = price.toLocaleString("es-CO", {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 9,
                });
              else
                price = price.toLocaleString("es-CO", {
                  maximumFractionDigits: 2,
                });

              return `$ ${price}`;
            } else return "";
          },
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

  loadAllData();
});
