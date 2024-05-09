$(document).ready(function () {
    loadTblNotProducts = (data, op) => {
        let type_price = parseInt($('#pricesList2').val());

        if ($.fn.dataTable.isDataTable("#tblNotProducts")) {
            $("#tblNotProducts").DataTable().destroy();
        }
        $('#tblNotProducts').empty();

        let tblNotProducts = document.getElementById('tblNotProducts');

        tblNotProducts.insertAdjacentHTML('beforeend',
            `<thead>
                <tr>
                    ${op === 1 ? '' : `<th class="colMin"><label>Seleccionar Todos</label><br><input class="form-control-updated check" type="checkbox" id="all"></th>`}
                    <th class="colMin">Referencia</th>
                    <th class="colMax">Producto</th> 
                </tr>
            </thead>
            <tbody id="tblNotProductsBody"></tbody>`);
        
        for (let i = 0; i < data.length; i++) {
            let checked = false;
            let arr = combinedData.filter(item => item.id_product === data[i].id_product);

            if (arr.length > 0) {
                for (let j = 0; j < arr[0].id_price_list.length; j++) {
                    if (type_price === arr[0].id_price_list[j]){
                        checked = true;
                        break;
                    }
                }
            }

            let body = document.getElementById('tblNotProductsBody');

            body.insertAdjacentHTML('beforeend',
                `<tr>
                    ${op === 1 ? '' : `<td class="colMin"><input type="checkbox" class="form-control-updated check ¨¨${data[i].reference}¨¨${data[i].product}¨¨${data[i].price}¨¨${data[i].sale_price}" id="check-${data[i].id_product}" ${checked === true ? 'checked' : ''}></td>`}
                    <td class="colMin">${data[i].reference}</td>
                    <td class="colMax">${data[i].product}</td>
                </tr>`);
        }

        tblNotProducts = $('#tblNotProducts').dataTable({
            // destroy: true,
            scrollY: '150px',
            scrollCollapse: true,
        });
 
        setTimeout(() => {
            let tables = document.getElementsByClassName(
                'dataTables_scrollHeadInner'
            );

            let attr = tables[0];
            attr.style.width = '100%';
            attr = tables[0].firstElementChild;
            attr.style.width = '100%';

            $('.colMin').css('width', ' 10%');
            $('.colMax').css('width', '80%');
        }, 500);
    }
});