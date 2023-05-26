$(document).ready(function () {
  /* Cargue tabla de Proyectos */
  loadTblCustomPrices = async () => {
    try {
      let data = await searchData('/api/customPrices');

      $('#tblCustomPrices').empty();

      let table = document.getElementById('tblCustomPrices');

      let headers = '';
      let dataPriceList = sessionStorage.getItem('dataPriceList');
      dataPriceList = JSON.parse(dataPriceList);

      for (let i = 0; i < dataPriceList.length; i++) {
        headers += `<th>${dataPriceList[i].price_name}</th>`;
      }

      table.insertAdjacentHTML(
        'beforeend',
        `<thead>
        <tr>
          <th>No</th>
          <th>REFERENCIA</th>
          <th>PRODUCTO</th>
          <th>PRECIO - COSTO</th>
          ${headers}
          <th>ACCIONES</th>
        </tr>
      </thead>
      <tbody id="tblCustomPricesBody"></tbody>
    `
      );

      let body = document.getElementById('tblCustomPricesBody');

      combinedData = data.reduce(function (result, current) {
        let existing = result.find(function (item) {
          return item.id_product === current.id_product;
        });

        if (existing) {
          existing.id_custom_price.push(current.id_custom_price);
          existing.id_price_list.push(current.id_price_list);
          existing.price_names.push(current.price_name);
          existing.prices.push(current.price_custom);
        } else {
          result.push({
            id_custom_price: [current.id_custom_price],
            id_price_list: [current.id_price_list],
            id_product: current.id_product,
            reference: current.reference,
            product: current.product,
            price_cost: current.price_cost,
            price_names: [current.price_name],
            prices: [current.price_custom],
          });
        }

        return result;
      }, []);

      for (let i = 0; i < combinedData.length; i++) {
        body.insertAdjacentHTML(
          'beforeend',
          `<tr>
          <td>${i + 1}</td>
          <td>${combinedData[i].reference}</td>
          <td>${combinedData[i].product}</td>
          <td>$ ${combinedData[i].price_cost.toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })}</td>
          ${(tbody = addColsPricesCombines(
            combinedData[i].prices,
            dataPriceList
          ))}
          <td>
            <a href="javascript:;" <i id="${i}" class="bx bx-edit-alt updateCustomPrice" data-toggle='tooltip' title='Actualizar Precio' style="font-size: 30px;"></i></a>
          </td>
        </tr>`
        );
      }
    } catch (error) {
      console.log(error);
    }
  };

  addColsPricesCombines = (prices, dataPriceList) => {
    let tbody = '';
    for (let i = 0; i < dataPriceList.length; i++) {
      !prices[i] ? (price_custom = 0) : (price_custom = prices[i]);

      tbody += `<td>$ ${price_custom.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      })}</td>`;
    }

    return tbody;
  };

  setTimeout(() => {
    loadTblCustomPrices();
  }, 1000);
});
