$(document).ready(function () {
  $('.addProd').hide();
  $('.inputProf').hide();

  products = [];

  $('#btnAddNewProduct').click(function (e) {
    e.preventDefault();
    $('.addProd').toggle(800);
    sessionStorage.removeItem('actualizar');
    
    $('.imgProduct').empty();
    
    $('.inputProf').hide();

    $('#quantity').val('');
    $('#price').val('');
    $('#discount').val('');
    $('#totalPrice').val('');
    $('#selectNameProduct option').removeAttr('selected');
    $(`#selectNameProduct option[value='0']`).prop('selected', true);
    $('#refProduct option').removeAttr('selected');
    $(`#refProduct option[value='0']`).prop('selected', true);
    $('#discount option[value=0]').prop('selected', true);
    $('#btnAddProduct').css('border', '');
  });

  $('#refProduct').change(async function (e) {
    e.preventDefault();
    let id = this.value;
    $('#selectNameProduct option').removeAttr('selected');
    $(`#selectNameProduct option[value=${id}]`).prop('selected', true);

    $('.inputProf').hide();
    
    let op = 1;
    if (custom_price == 1)
      op = await loadPriceListByProduct(id);

    loadDataProduct(id, op);
  });

  $('#selectNameProduct').change(async function (e) {
    e.preventDefault();
    let id = this.value;
    $('#pricesList option').removeAttr('selected');
    $(`#pricesList option[value='0']`).prop('selected', true);
    $('#price').val('');
    $('#totalPrice').val('');

    // if (flag_indirect == '1') {
    //   $('.inputProf').show();
    // }

    $('#refProduct option').removeAttr('selected');
    $(`#refProduct option[value=${id}]`).prop('selected', true);

    let op = 1;
    if (custom_price == 1)
      op = await loadPriceListByProduct(id);
    loadDataProduct(id, op);
  });

  loadDataProduct = async (id, op) => {
    let data = await searchData(`/api/productCost/${id}`);

    data.sale_price == '0' ? price = parseFloat(data.price).toFixed() : price = parseFloat(data.sale_price).toFixed();

    oldPrice = price;
    priceProduct = price;

    if (custom_price == 0 || op == 1)
      $('#price').val(price);

    if (data.profitability > 0 && flag_indirect == '1') {
      $('#profitability').val(data.profitability);
    } else if (flag_indirect == '1') {
      $('.inputProf').show();
    }

    $('.imgProduct').empty();

    if (data.img)
      $('.imgProduct').html(`
        <img src="${data.img}" id="imgProduct" style="width:80px">
      `);

    $('#quantity').click();
  };

  $(document).on('change', '#pricesList', async function () {
    let data = JSON.parse(sessionStorage.getItem('dataPriceList'));
    let price = 0;

    for (let i = 0; i < data.length; i++) {
      if (data[i].id_price_list == this.value) {
        price = parseFloat(data[i].price);
        break;
      }
    }

    oldPrice = price;

    $('#price').val(price);
  });

  /* Calcular precio total */
  $(document).on('blur', '#price', function (e) {
    let idProduct = $('#refProduct').val();
    if (idProduct > 0) {
      let price = this.value; 

      if (price < oldPrice) {
        $('#price').val(oldPrice);
        toastr.error('Ingrese un precio mayor al original');
        return false;
      }
    }
  });

  $(document).on('click keyup', '.calcPrice', function (e) {
    let quantity = parseFloat($('#quantity').val());
    let price = parseFloat($('#price').val());
    let discount = $('#discount').val();
    let profitability = parseFloat($('#profitability').val());

    quantity == '' || isNaN(quantity) ? (quantity = 0) : quantity;
    // quantity = strReplaceNumber(quantity);

    price == '' || isNaN(price)? (price = 0) : price;
    // price = strReplaceNumber(price);

    if (price >= parseInt(oldPrice)) {
      let val =
        parseFloat(quantity) *
        parseFloat(price) *
        (1 - parseFloat(discount) / 100);

      if (indirect == 1 && profitability > 0 && flag_indirect == '1')
        val = val / (1 - (profitability / 100));
      

      $('#totalPrice').val(parseFloat(val).toLocaleString('es-CO', { maximumFractionDigits: 2 }));
    }
  });

  /* Adicionar productos a la tabla */
  $('#btnAddProduct').on('click', function (e) {
    e.preventDefault();
    let ref = $('#refProduct :selected').text();
    let price = $('#price').val();
    let quantity = $('#quantity').val();
    let profitability = $('#profitability').val();

    if (
      ref == 'Seleccionar' ||
      price == '' ||
      quantity == '' ||
      quantity == 0
    ) {
      toastr.error('Para cotizar, ingrese todos los datos de los productos');
      return false;
    }

    if (price <= 0) {
      toastr.error('El precio debe ser mayor a cero');
      return false;
    }

    if (indirect == 1 && !profitability && flag_indirect == '1') {
      toastr.error('Ingrese rentabilidad');
      return false;
    }

    let idProduct = $('#selectNameProduct').val();
    let nameProduct = $('#selectNameProduct :selected').text();
    let discount = $('#discount').val();
    let totalPrice = $('#totalPrice').val();

    price = strReplaceNumber(price);

    // if (indirect == 1) {
    //   price = price / (1 - (profitability / 100));
    //   totalPrice = strReplaceNumber(totalPrice);
    //   totalPrice = totalPrice.replace('$ ', '');
    //   totalPrice = (totalPrice / (1 - (profitability / 100))).toLocaleString('es-CO', { maximumFractionDigits: 0 });
    // }

    op = sessionStorage.getItem('actualizar');

    if (!op || op == null) {
      let product = {
        idProduct: idProduct,
        idMaterial: 0,
        ref: ref.trim(),
        nameProduct: nameProduct.trim(),
        idPriceList: $('#pricesList').val(),
        price: `$ ${parseInt(price).toLocaleString('es-CO', { maximumFractionDigits: 0 })}`,
        quantity: quantity,
        quantityMaterial: 0,
        profitability: profitability,
        discount: discount,
        totalPrice: `$ ${totalPrice}`,
        indirect: 0
      };

      products.push(product);
    } else {
      products[op].idProduct = idProduct;
      products[op].ref = ref.trim();
      products[op].nameProduct = nameProduct.trim();
      products[op].idPriceList = $('#pricesList').val();
      products[op].quantity = quantity;
      products[op].price = `$ ${parseInt(price).toLocaleString('es-CO', { maximumFractionDigits: 0 })}`;
      products[op].profitability = profitability;
      products[op].discount = discount;
      products[op].totalPrice = `$ ${totalPrice}`;
    }
    
    $('.addProd').hide();
    $('.inputProf').hide();
    addProducts();
    
    $('#profitability').val('');
    $('#refProduct').prop('selectedIndex', 0);
    $('#selectNameProduct').prop('selectedIndex', 0);
    $('#pricesList').prop('selectedIndex', 0);
    $('#quantity').val('');
    $('#price').val('');
    $('#discount').val('');
    $('#totalPrice').val('');
  });

  /* Modificar producto */
  $(document).on('click', '.updateProduct', async function (e) {
    e.preventDefault();

    let id = this.id;
    let data = products[id];
    $('.addMaterial').hide();
    
    // $(`#refProduct option:contains(${data.ref})`).prop('selected', true);
    // $(`#selectNameProduct option:contains(${data.nameProduct})`).prop(
    //   'selected',
    //   true
    // );

    $(`#refProduct option[value=${data.idProduct}]`).prop('selected', true);
    $(`#selectNameProduct option[value=${data.idProduct}]`).prop('selected', true);

    $(`#pricesList option[value=${data.idPriceList}]`).prop('selected', true);

    $('#quantity').val(data.quantity);

    price = strReplaceNumber(data.price);
    price = price.replace('$ ', '');
    oldPrice = data.price;

    $('#price').val(price);

    $(`#discount option[value="${data.discount}"]`).prop('selected', true);

    totalPrice = data.totalPrice;
    totalPrice = totalPrice.replace('$ ', '');
    $('#totalPrice').val(totalPrice);
    $('#profitability').val(data.profitability);

    // $('#btnAddProduct').html('Actualizar producto');

    if (custom_price == 1)
      op = await loadPriceListByProduct(id);
    sessionStorage.setItem('actualizar', id);

    $('.addProd').show(1000);
    if (flag_indirect == '1')
      $('.inputProf').show(1000);
  });

  /* Borrar productos seleccionados de la tabla */
  $(document).on('click', '.deleteProduct', function (e) {
    e.preventDefault();
    let id = this.id;

    if (products[id].idMaterial == 0 && flag_indirect == '1') {
      let materials = products.filter(item => item.idProduct == products[id].idProduct && item.idMaterial != 0);

      if (materials.length > 0) {
        toastr.error('Elimine primero los materiales asociados a ese producto');
        return false;
      }
    }

    products.splice(id, 1);
    addProducts();
  });

  /* Cargar cada producto seleccionado a la tabla */

  addProducts = () => {
    $('#tableProductsQuoteBody').empty(); 
    $('#tableProductsQuote').empty(); 

    let tableProductsQuote = document.getElementById(
      'tableProductsQuote'
    );

    tableProductsQuote.insertAdjacentHTML('beforeend',
      `<thead>
        <tr>
          <th class="text-center">Referencia</th>
          <th class="text-center">Producto</th>
          <th class="text-center">Cantidad</th>
          <th class="text-center">Valor Unitario</th>
          <th class="text-center">Descuento</th>
          ${indirect == 1 && flag_indirect == '1' ? `<th class="text-center indirectMaterial">Rentabilidad</th>` : ''}
          <th class="text-center">Valor Total</th>
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody id="tableProductsQuoteBody"></tbody>`);
    
    

    let tableProductsQuoteBody = document.getElementById(
      'tableProductsQuoteBody'
    );

    for (let i = 0; i < products.length; i++) {

      let action = '';
      if (products[i].indirect == 1 && flag_indirect == '1')
        action = `<a href="javascript:;" id="${i}" <i class="bx bx-edit updateMaterial" data-toggle='tooltip' title='Actualizar Material' style="font-size: 18px"></i></a>
        <a href="javascript:;" id="${i}" <i class="bx bx-trash deleteProduct" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 18px;color:red"></i></a>`;
      else if (products[i].indirect == 0)
        action = `<a href="javascript:;" id="${i}" <i class="bx bx-edit updateProduct" data-toggle='tooltip' title='Actualizar Producto' style="font-size: 18px"></i></a>
        <a href="javascript:;" id="${i}" <i class="bx bx-trash deleteProduct" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 18px;color:red"></i></a>`;
      
      tableProductsQuoteBody.insertAdjacentHTML(
        'beforeend',
        `
        <tr>
            <td class="text-center">${products[i].ref}</td>              
            <td class="text-center">${products[i].nameProduct}</td>              
            <td class="text-center">${products[i].indirect == 1 && flag_indirect == '1' ? products[i].quantityMaterial : products[i].quantity}</td>              
            <td class="text-center">${products[i].price}</td>
            <td class="text-center">${products[i].discount} %</td>
            ${indirect == 1 && flag_indirect == '1' ? `<td class="text-center">${products[i].profitability} %</td>` : ''}
            <td class="text-center">${products[i].totalPrice}</td>
            <td class="text-center"> 
             ${action}
            </td>
        </tr>`
      );
    }    

    $('#tableProductsQuote').DataTable({
      destroy: true,
      scrollY: '150px',
      scrollCollapse: true,
      dom: '<"datatable-error-console">frtip',
      fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
        if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
          console.error(oSettings.json.error);
        }
      },
    });

    let tables = document.getElementsByClassName(
      'dataTables_scrollHeadInner'
    );

    let attr = tables[0];
    attr.style.width = '100%';
    attr = tables[0].firstElementChild;
    attr.style.width = '100%';
  };
});
