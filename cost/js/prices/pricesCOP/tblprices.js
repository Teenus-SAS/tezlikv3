$(document).ready(function () {
  let op1 = 1;
  let currentDollar = 0;
  let allPrices = [];

  const loadAllData = async () => {
    try {
      const [prices, actualTrm] = await Promise.all([
        searchData("/api/prices"),
        searchData("/api/currentDollar"),
      ]);

      allPrices = prices;
      currentDollar = parseFloat(actualTrm[0]["valor"]);

      if (flag_currency_usd === "1") {
        $("#exchangeCoverageUSD").val(
          `$ ${(currentDollar - parseFloat(coverage_usd)).toLocaleString(
            "es-CO",
            {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2,
            }
          )}`
        );
      }

      const typeCurrency = sessionStorage.getItem("typeCurrency");
      op1 =
        typeCurrency === "2" && flag_currency_usd === "1"
          ? 2
          : typeCurrency === "3" && flag_currency_eur === "1"
            ? 3
            : 1;

      loadTblPrices(allPrices, op1);
    } catch (error) {
      console.error("Error loading data:", error);
    }
  };

  const loadTblPrices = (data, op, valueCoverage) => {
    const isVisible = data.some((item) => item.sale_price > 0);
    const title = "Margen";

    $("#tblPrices").DataTable({
      destroy: true,
      pageLength: 50,
      data,
      dom: '<"datatable-error-console">frtip',
      language: {
        url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json",
      },
      columns: [
        {
          title: "No.",
          data: null,
          className: "uniqueClassName",
          render: (_, __, ___, meta) => meta.row + 1,
        },
        {
          title: "Referencia",
          data: "reference",
          className: "uniqueClassName",
        },
        { title: "Producto", data: "product", className: "classCenter" },
        {
          title: getPriceTitle(op),
          data: (data) => getPrice(data, op, "price"),
          className: "classCenter",
          render: (data) => renderCost(data, op),
        },
        {
          title: "Precio Simulación",
          data: (data) =>
            valueCoverage ? (data.price / valueCoverage).toFixed(2) : "",
          className: "classCenter",
          visible: op === 4,
          render: (data) => renderCost(data, op),
        },
        {
          title: getPriceTitle(op, true),
          data: (data) => getPrice(data, op, "sale_price"),
          className: "classCenter",
          visible: isVisible,
          render: (data) => renderCost(data, op),
        },
        {
          title: "Precio (Lista) Simulación",
          data: (data) =>
            valueCoverage ? (data.sale_price / valueCoverage).toFixed(2) : "",
          className: "classCenter",
          visible: op === 4 && isVisible,
          render: (data) => renderCost(data, op),
        },
        {
          title,
          data: null,
          className: "classCenter",
          visible: isVisible,
          render: (data) => renderProfitability(data),
        },
        {
          title: "Img",
          data: "img",
          className: "uniqueClassName",
          render: (data) =>
            data
              ? `<img src="${data}" alt="" style="width:50px;border-radius:100px">`
              : "",
        },
        {
          title: "Acciones",
          data: "id_product",
          className: "uniqueClassName",
          render: (data) =>
            `<a href="/cost/details-prices"><i id="${data}" class="bi bi-zoom-in seeDetail" data-toggle='tooltip' title='Ficha de Costos' style="font-size: 30px;"></i></a>`,
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
    });
  };

  const getPrice = (data, op, key) => {
    switch (op) {
      case 1:
        return parseFloat(data[key]); // COP
      case 3:
        return parseFloat(data[`${key}_eur`]); // EUR
      default:
        return parseFloat(data[`${key}_usd`]); // USD
    }
  };

  const getPriceTitle = (op, isList = false) => {
    const base = isList ? "Precio (Lista)" : "Precio Sugerido";
    return op === 1 ? base : `${base} (${op === 3 ? "EUR" : "USD"})`;
  };

  const renderCost = (data, op) => {
    if (data == null || isNaN(data)) return ""; // Verifica que data sea válido
    const options = {
      minimumFractionDigits: op === 1 ? 0 : 2,
      maximumFractionDigits: op === 1 ? 0 : 2, // Asegura coherencia entre los valores
    };
    return `$ ${parseFloat(data).toLocaleString("es-CO", options)}`;
  };

  const renderProfitability = (data) => {
    const { profitability, sale_price } = data;
    const { actualProfitability2 = 0 } = getDataCost(data);

    let badgeClass = "badge badge-primary"; // Valor por defecto
    let text = `${actualProfitability2.toFixed(2)} %`; // Por defecto, muestra el porcentaje actual

    if (sale_price === 0) {
      text = `${profitability.toFixed(2)} %`; // Si el precio de venta es 0, muestra el profitability
    } else if (actualProfitability2 < 0) {
      badgeClass = "badge badge-danger"; // Si la rentabilidad actual es negativa
    } else if (actualProfitability2 < profitability) {
      badgeClass = "badge badge-warning"; // Si la rentabilidad actual es menor a la esperada
    } else if (actualProfitability2 => profitability) {
      badgeClass = "badge badge-success"; // Si la rentabilidad actual es mayor a la esperada
    } else if (
      actualProfitability2 >= 0 &&
      actualProfitability2 <= profitability
    ) {
      badgeClass = "badge badge-warning"; // Si la rentabilidad actual está entre 0 y profitability
    }

    return `<span class="${badgeClass}" style="font-size: medium;">${text}</span>`;
  };

  loadAllData();
});
