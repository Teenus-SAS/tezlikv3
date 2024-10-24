$(document).ready(function () {
  customPrices = [];
  
  /* Cargue tabla de Proyectos */
  loadAllData = async () => {
    try {
      const prices = await searchData('/api/customPrices');
      op = 1;

      // parents = prices.filter(item => item.composite == 0);
      // composites = prices.filter(item => item.composite == 1);

      // if (flag_composite_product == '1') {
      //   customPrices = parents;
      //   loadTblCustomPrices(parents);
      // } else {
        customPrices = prices;
        loadTblCustomPrices(prices);
      // }
    } catch (error) {
      console.error('Error loading data:', error);
    }
  }

  loadTblCustomPrices = async (data) => {
    try {
      sessionStorage.removeItem('dataPriceList');
      await loadPriceList(1);

      let dataPriceList = JSON.parse(sessionStorage.getItem('dataPriceList'));

      if (dataPriceList.length > 0) {
        // let data = await searchData('/api/customPrices');

        // if (flag_composite_product == '1')
        //   data = data.filter(item => item.composite == 0);

        let arr = data;
        let op = false;

        for (let i = 0; i < arr.length; i++) {
          for (let j = 0; j < type_custom_price.length; j++) {
            if (type_custom_price[j] == arr[i].id_price_list) {
              if (op == false) {
                data = [];
                op = true;
              }
              data.push(arr[i]);
            }
          
          }
        }

        $('#tblCustomPrices').empty();

        if ($.fn.dataTable.isDataTable('#tblCustomPrices')) {
          $('#tblCustomPrices').DataTable().destroy();
          $('#tblCustomPrices').empty();
        };

        let table = document.getElementById('tblCustomPrices');

        let headers = ''; 

        for (let i = 0; i < dataPriceList.length; i++) {
          type_custom_price[0] == '-1' ? headers += `<th>${dataPriceList[i].price_name}</th>` : headers += `<th>${dataPriceList[i].price_name}</th>`;
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
          ${type_custom_price[0] == '-1' ? `<th>${dataPriceList[0].type_price}</th>` : ''}
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
          } else {
            result.push({
              id_custom_price: [current.id_custom_price],
              id_price_list: [current.id_price_list],
              id_product: current.id_product,
              reference: current.reference,
              product: current.product,
              price_cost: current.price_cost,
              flag_price: current.flag_price,
              price_names: [current.price_name],
              prices: [current.price_custom],
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
          body.insertAdjacentHTML(
            'beforeend',
            `<tr>
          <td>${i + 1}</td>
          <td>${combinedData[i].reference}</td>
          <td>${combinedData[i].product}</td>
          ${type_custom_price[0] == '-1' ?
              `<td>$ ${parseFloat(combinedData[i].price_cost).toLocaleString('es-CO', {
                minimumFractionDigits: 0, maximumFractionDigits: 0,
              })}</td>` : ``}
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
            url: '/assets/plugins/i18n/Spanish.json',
          },
          fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
            if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
              console.error(oSettings.json.error);
            }
          },
        });
      } else {
        // combinedData = [];
        
        $('#tblCustomPrices').dataTable({
          destroy: true,
          pageLength: 50,
          data: [],
          autoWidth: true,
          dom: '<"datatable-error-console">frtip',
          language: {
            url: '/assets/plugins/i18n/Spanish.json',
          },
          fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
            if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
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
              data: '',
              className: "uniqueClassName",
            },
            {
              title: "Producto",
              data: '',
              className: "classCenter",
            },
            {
              title: "Acciones",
              data: '',
              className: "uniqueClassName",
            },
          ],
        });
      }
        
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

            i += 1;
        
          } else {
            price_custom = '';
          };
          tbody += `<td>${price_custom}</td>`;
        }
      } else {
        price_custom = `$ ${data.prices[i].toLocaleString('es-CO', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0,
        })}`;

        tbody += `<td>${price_custom}</td>`;
      }
    }

    return tbody;
  };

  // setTimeout(() => {
  loadAllData();
  // }, 1000);
});
