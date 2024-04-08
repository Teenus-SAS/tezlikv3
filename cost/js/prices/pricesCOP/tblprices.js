$(document).ready(function () {
  $('#btnComposite').click(function (e) {
    e.preventDefault();
    let typePrice = sessionStorage.getItem('typePrice');
    typePrice == '2' ? op1 = 2 : op1 = 1;

    if (op == 1) {
      op = 2;
      loadTblPrices(composites, op1);
    }
    else {
      op = 1;
      loadTblPrices(parents, op1);
    }
  });

  loadAllData = async () => {
    try {
      const prices = await searchData('/api/prices');

      let typePrice = sessionStorage.getItem('typePrice');
      typePrice == '2' ? op1 = 2 : op1 = 1;

      parents = prices.filter(item => item.composite == 0);
      composites = prices.filter(item => item.composite == 1);

      if (flag_composite_product == '1') {
        loadTblPrices(parents, op1);
      } else
        loadTblPrices(prices, op1);
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  /* Cargue tabla de Precios */
  loadTblPrices = async (data, op) => {
    let acumulated = 0;

    for (let i = 0; i < data.length; i++) {
      acumulated += data[i].sale_price;
    }

    acumulated == 0 ? (visible = false) : (visible = true);

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
          title: `${op == 1 ? 'Precio (Sugerido)' : 'Precio (Sugerido USD)'}`,
          data: null,
          className: "classCenter",
          render: function (data) {
            op == 1 ? price = parseFloat(data.price) : price = parseFloat(data.price_usd);

            if (Math.abs(price) < 0.01) {
              price = price.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
            } else if (op == 1)
              price = price.toLocaleString('es-CO', { maximumFractionDigits: 0 });
            else
              price = price.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
            return `$ ${price}`;
          },
        },
        {
          title: "Precio (Lista)",
          data: "sale_price",
          className: "classCenter",
          visible: visible,
          render: function (data) {
            op == 1 ? price = parseFloat(data.sale_price) : price = parseFloat(data.sale_price_usd);

            if (price > 0) {
              if (Math.abs(price) < 0.01) {
                price = price.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 9 });
              } else if (op == 1)
                price = price.toLocaleString('es-CO', { maximumFractionDigits: 0 });
              else
                price = price.toLocaleString('es-CO', { maximumFractionDigits: 2 });
            
              return `$ ${price}`;
            }
            else return '';
          },
        },
        {
          title: "Rentabilidad",
          data: null,
          className: "classCenter",
          visible: visible,
          render: function (data) {
            let dataCost = getDataCost(data);
            if (!isFinite(dataCost.actualProfitability3))
              dataCost.actualProfitability3 = 0;

            let profitabilityText = `${dataCost.actualProfitability3.toLocaleString(
              "es-CO",
              { maximumFractionDigits: 2 }
            )} %`;
            let badgeClass = "";

            if (
              dataCost.actualProfitability3 < data.profitability &&
              dataCost.actualProfitability3 > 0 &&
              data.sale_price > 0
            ) {
              badgeClass = "badge badge-warning"; // Use "badge badge-warning" for orange
            } else if (
              dataCost.actualProfitability3 < data.profitability &&
              data.sale_price > 0
            ) {
              badgeClass = "badge badge-danger"; // Use "badge badge-danger" for red
            } else badgeClass = "badge badge-success"; // Use "badge badge-danger" for red

            if (data.sale_price == 0) {
              badgeClass = "badge badge-primary"; // Use "badge badge-warning" for orange
              profitabilityText = `${data.profitability.toLocaleString(
                "es-CO",
                { maximumFractionDigits: 0 }
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
