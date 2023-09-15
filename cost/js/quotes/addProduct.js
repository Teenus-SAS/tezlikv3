$(document).ready(function () {
  $('.addProd').hide();

  products = [];

  $('#btnAddNewProduct').click(function (e) {
    e.preventDefault();
    $('.addProd').toggle(800);
    sessionStorage.removeItem('actualizar');

    $('.imgProduct').empty();

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
    loadDataProduct(id);
    await loadPriceListByProduct(id);
  });

  $('#selectNameProduct').change(async function (e) {
    e.preventDefault();
    let id = this.value;
    $('#pricesList option').removeAttr('selected');
    $(`#pricesList option[value='0']`).prop('selected', true);
    $('#price').val('');
    $('#totalPrice').val('');

    $('#refProduct option').removeAttr('selected');
    $(`#refProduct option[value=${id}]`).prop('selected', true);
    let op = await loadPriceListByProduct(id);
    loadDataProduct(id, op);
  });

  loadDataProduct = async (id) => {
    let data = await searchData(`/api/productCost/${id}`);

    if (data.price == false) {
      price = 0;
    } else {
      price = parseInt(data.price).toLocaleString('es-CO');
    }
    oldPrice = data.price;
    priceProduct = data.price;

    if (op == 1)
      $('#price').val(price);

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

    $('#price').val(price.toLocaleString('es-CO', { maximumFractionDigits: 2 }));
  });

  /* Calcular precio total */

  $(document).on('blur', '#price', function (e) {
    let idProduct = $('#refProduct').val();
    if (idProduct > 0) {
      let price = strReplaceNumber(this.value);
      oldPrice = strReplaceNumber(oldPrice);

      if (price < oldPrice) {
        $('#price').val(oldPrice.toLocaleString('es-CO'));
        toastr.error('Ingrese un precio mayor al original');
        return false;
      }
    }
  });

  $(document).on('click keyup', '.calcPrice', function (e) {
    let quantity = $('#quantity').val();
    let price = $('#price').val();
    let discount = $('#discount').val();

    quantity == '' ? (quantity = '0') : quantity;
    quantity = strReplaceNumber(quantity);

    price == '' ? (price = '0') : price;
    price = strReplaceNumber(price);

    if (price >= parseInt(oldPrice)) {
      let val =
        parseFloat(quantity) *
        parseFloat(price) *
        (1 - parseFloat(discount) / 100);

      $('#totalPrice').val(parseInt(val).toLocaleString('es-CO'));
    }
  });

  /* Adicionar productos a la tabla */

  $('#btnAddProduct').on('click', function (e) {
    e.preventDefault();
    let ref = $('#refProduct :selected').text();
    let price = $('#price').val();
    let quantity = $('#quantity').val();

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

    let idProduct = $('#selectNameProduct').val();
    let nameProduct = $('#selectNameProduct :selected').text();
    let discount = $('#discount').val();
    let totalPrice = $('#totalPrice').val();

    price = strReplaceNumber(price);

    op = sessionStorage.getItem('actualizar');

    if (!op || op == null) {
      let product = {
        idProduct: idProduct,
        ref: ref.trim(),
        nameProduct: nameProduct.trim(),
        idPriceList: $('#pricesList').val(),
        price: `$ ${parseInt(price).toLocaleString('es-CO')}`,
        quantity: quantity,
        discount: discount,
        totalPrice: `$ ${totalPrice}`,
      };

      products.push(product);
    } else {
      products[op].idProduct = idProduct;
      products[op].ref = ref.trim();
      products[op].nameProduct = nameProduct.trim();
      products[op].idPriceList = $('#pricesList').val();
      products[op].quantity = quantity;
      products[op].price = `$ ${parseInt(price).toLocaleString('es-CO')}`;
      products[op].discount = discount;
      products[op].totalPrice = `$ ${totalPrice}`;
    }

    $('.addProd').hide();
    addProducts();

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

    $(`#refProduct option:contains(${data.ref})`).prop('selected', true);
    $(`#selectNameProduct option:contains(${data.nameProduct})`).prop(
      'selected',
      true
    );

    $(`#pricesList option[value=${data.idPriceList}]`).prop('selected', true);

    $('#quantity').val(data.quantity.toLocaleString());

    price = data.price;
    price = price.replace('$ ', '');
    oldPrice = price;

    $('#price').val(price.toLocaleString());

    $(`#discount option[value="${data.discount}"]`).prop('selected', true);

    totalPrice = data.totalPrice;
    totalPrice = totalPrice.replace('$ ', '');
    $('#totalPrice').val(totalPrice);

    $('#btnAddProduct').html('Actualizar producto');

    sessionStorage.setItem('actualizar', id);

    $('.addProd').show(1000);
  });

  /* Borrar productos seleccionados de la tabla */
  $(document).on('click', '.deleteProduct', function (e) {
    e.preventDefault();

    let id = this.id;
    products.splice(id, 1);
    addProducts();
  });

  /* Cargar cada producto seleccionado a la tabla */

  addProducts = () => {
    $('#tableProductsQuoteBody').empty();

    let tableProductsQuoteBody = document.getElementById(
      'tableProductsQuoteBody'
    );

    for (let i = 0; i < products.length; i++) {
      tableProductsQuoteBody.insertAdjacentHTML(
        'beforeend',
        `
        <tr>
            <td class="text-center">${products[i].ref}</td>              
            <td class="text-center">${products[i].nameProduct}</td>              
            <td class="text-center">${products[i].quantity}</td>              
            <td class="text-center">${products[i].price}</td>
            <td class="text-center">${products[i].discount} %</td>
            <td class="text-center">${products[i].totalPrice}</td>
            <td class="text-center">
            <a href="javascript:;" id="${i}" <i class="bx bx-edit updateProduct" data-toggle='tooltip' title='Actualizar Producto' style="font-size: 18px"></i></a>
              <a href="javascript:;" id="${i}" <i class="bx bx-trash deleteProduct" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 18px;color:red"></i></a>
            </td>
        </tr>`
      );
    }
  };
});
