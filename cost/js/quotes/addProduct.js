$(document).ready(function () {
  $('.addProd').hide();

  products = [];

  $('#btnAddNewProduct').click(function (e) {
    e.preventDefault();
    $('.addProd').toggle(800);
    $('#discount option[value=0]').prop('selected', true);
  });

  $('#refProduct').change(function (e) {
    e.preventDefault();
    id = this.value;
    $(`#selectNameProduct option[value=${id}]`).prop('selected', true);
    loadDataProduct(id);
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    id = this.value;
    $(`#refProduct option[value=${id}]`).prop('selected', true);
    loadDataProduct(id);
  });

  loadDataProduct = async (id) => {
    data = await searchData(`/api/productCost/${id}`);

    data == false ? (price = 0) : (price = data.price.toLocaleString());
    $('#price').val(price);
  };

  /* Calcular precio total */
  $(document).on('click keyup', '.calcPrice', function (e) {
    let quantity = $('#quantity').val();
    let price = $('#price').val();
    let discount = $('#discount').val();

    quantity == '' ? (quantity = '0') : quantity;
    quantity = replaceNumber(quantity);

    price == '' ? (price = '0') : price;
    price = replaceNumber(price);

    let val =
      parseFloat(quantity) *
      parseFloat(price) *
      (1 - parseFloat(discount) / 100);

    val.isInteger
      ? (val = val.toLocaleString())
      : (val = val.toLocaleString(undefined, {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        }));

    $('#totalPrice').val(val);
  });

  replaceNumber = (number) => {
    while (number.includes('.')) {
      if (number.includes('.')) number = number.replace('.', '');
    }
    if (number.includes(',')) number = number.replace(',', '.');
    return number;
  };

  /* Adicionar productos a la tabla */

  $('#btnAddProduct').on('click', function (e) {
    e.preventDefault();
    let ref = $('#refProduct :selected').text();
    let price = $('#price').val();
    let quantity = $('#quantity').val();

    if (ref == '' || price == '' || quantity == '') {
      toastr.error('Para cotizar, ingrese todos los datos de los productos');
      return false;
    }

    if (price <= 0) {
      toastr.error('El precio debe ser mayor a cero');
      return false;
    }

    $('.addProd').hide();

    let idProduct = $('#selectNameProduct').val();
    let nameProduct = $('#selectNameProduct :selected').text();
    let discount = $('#discount').val();
    let totalPrice = $('#totalPrice').val();

    product = {
      idProduct: idProduct,
      ref: ref.trim(),
      nameProduct: nameProduct.trim(),
      price: price,
      quantity: quantity,
      discount: discount,
      totalPrice: totalPrice,
    };
    products.push(product);

    addProducts();

    $('#refProduct').prop('selectedIndex', 0);
    $('#selectNameProduct').prop('selectedIndex', 0);
    $('#quantity').val('');
    $('#price').val('');
    $('#discount').val('');
    $('#totalPrice').val('');
  });

  /* Borrar productos seleccionados de la tabla */

  $(document).on('click', '.deleteProduct', function (e) {
    e.preventDefault();

    id = this.id;
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
            <td class="text-center">$ ${products[i].price}</td>
            <td class="text-center">${products[i].discount} %</td>
            <td class="text-center">$ ${products[i].totalPrice}</td>
            <td class="text-center"><a href="javascript:;" id="${i}" <i class="bx bx-trash deleteProduct" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 18px;color:red"></i></a></td>
        </tr>`
      );
    }
  };
});
