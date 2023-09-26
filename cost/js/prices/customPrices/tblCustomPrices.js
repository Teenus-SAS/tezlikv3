$(document).ready(function () {
  
  /* Cargue tabla de Proyectos */
  loadTblCustomPrices = async () => {
    try {
      let data = await searchData('/api/customPrices');
      let arr = data;
      let op = false;

      for (let i = 0; i < arr.length; i++) {  
        for (let j = 0; j < type_custom_price.length; j++) {
          if (type_custom_price[j] == arr[i].id_price_list) {
            if(op == false){
              data = [];
              op = true;
            }
            // data[i] = arr[i];
            data.push(arr[i]);
            // break;
          }
          
        }
      }

      $('#tblCustomPrices').empty();

      let table = document.getElementById('tblCustomPrices');

      let headers = '';
      let dataPriceList = sessionStorage.getItem('dataPriceList');
      dataPriceList = JSON.parse(dataPriceList);

      for (let i = 0; i < dataPriceList.length; i++) {
        type_custom_price[0] == '-1' ? headers += `<th>${dataPriceList[i].type_price}</th><th>${dataPriceList[i].price_name}</th>`: headers += `<th>${dataPriceList[i].price_name}</th>`;
      }
      let actions = '';

      if (type_custom_price[0] == '-1') {
        actions = '<th>ACCIONES</th>';
      }
//${type_custom_price == '-1' ? '<th></th>' : ''}
      table.insertAdjacentHTML(
        'beforeend',
        `<thead>
        <tr>
          <th>No</th>
          <th>REFERENCIA</th>
          <th>PRODUCTO</th> 
          ${headers}
          ${actions}
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
          existing.price_cost.push(current.price_cost);
          //existing.profitability_customs.push(current.profitability_custom);
        } else {
          result.push({
            id_custom_price: [current.id_custom_price],
            id_price_list: [current.id_price_list],
            id_product: current.id_product,
            reference: current.reference,
            product: current.product,
            price_cost: [current.price_cost], 
            //profitability_price: current.profitability_price,
            price_names: [current.price_name],
            prices: [current.price_custom],
            //profitability_customs: [current.profitability_custom],
          });
        }

        return result;
      }, []);

      for (let i = 0; i < combinedData.length; i++) {
        let actions = '';
        if (type_custom_price[0] == '-1')
          actions = `<td>
            <a href="javascript:;" <i id="${i}" class="bx bx-edit-alt updateCustomPrice" data-toggle='tooltip' title='Actualizar Precio' style="font-size: 30px;"></i></a>
            <a href="javascript:;" <i id="${i}" class="mdi mdi-delete-forever deleteFunction" data-toggle='tooltip' title='Eliminar Precio' style="font-size: 30px;color:red"></i></a>
          </td>`;
        /*${type_custom_price == '-1' ? `<td>${combinedData[i].profitability_price.toLocaleString('es-CO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          })} %</td>` : ''} 
          <td>$ ${combinedData[i].price_cost.toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })}</td>*/
        body.insertAdjacentHTML(
          'beforeend',
          `<tr>
          <td>${i + 1}</td>
          <td>${combinedData[i].reference}</td>
          <td>${combinedData[i].product}</td>
          
          
          ${(tbody = addColsPricesCombines(combinedData[i], dataPriceList))}
          ${actions}
        </tr>`
        );
      }

      $('#tblCustomPrices').dataTable({
        destroy: true,
        pageLength: 50,
        autoWidth: true,
        dom: '<"datatable-error-console">frtip',
        language: {
          url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
        },
        fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
          if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
            console.error(oSettings.json.error);
          }
        },
      });
      
    } catch (error) {
      console.log(error);
    }
  };

  addColsPricesCombines = (data, dataPriceList) => {
    let tbody = '';
    for (let i = 0; i < data.id_price_list.length; i++) {
      if (data.id_price_list.length < dataPriceList.length) {
        for (let j = 0; j < dataPriceList.length; j++) {
          if (data.id_price_list[i] == dataPriceList[j].id_price_list) {
            price_custom = `$ ${data.prices[i].toLocaleString('es-CO', {
              minimumFractionDigits: 0,
              maximumFractionDigits: 0,
            })}`;
  
            // profitability_custom = `${data.profitability_customs[
            //   i
            // ].toLocaleString('es-CO', {
            //   minimumFractionDigits: 2,
            //   maximumFractionDigits: 2,
            // })} %`;
        
          } else {
            price_custom = '';
            //profitability_custom = '';
          };
          
          !data.price_cost[i] ? price_cost = '' : price_cost = `$ ${data.price_cost[i].toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })}`;
          i += 1;
          
          tbody += type_custom_price[0] == '-1' ? `<td>${price_cost}</td><td>${price_custom}</td>` : `<td>${price_custom}</td>`;
          //${type_custom_price == '-1' ? `<td>${profitability_custom}</td>` : ''}
        }
      }
      else {
        price_custom = `$ ${data.prices[i].toLocaleString('es-CO', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0,
        })}`;
  
        // profitability_custom = `${data.profitability_customs[
        //   i
        // ].toLocaleString('es-CO', {
        //   minimumFractionDigits: 2,
        //   maximumFractionDigits: 2,
        // })} %`;
        !data.price_cost[i] ? price_cost = '' : price_cost = `$ ${data.price_cost[i].toLocaleString('es-CO', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0,
        })}`; 
        tbody += type_custom_price[0] == '-1' ? `<td>${price_cost}</td><td>${price_custom}</td>` : `<td>${price_custom}</td>`;

        // tbody += `<td>${price_custom}</td>`;
        //${type_custom_price == '-1' ? `<td>${profitability_custom}</td>` : ''}
      
      }
    }

    return tbody;
  };

  setTimeout(() => {
    loadTblCustomPrices();
  }, 1000);
});
