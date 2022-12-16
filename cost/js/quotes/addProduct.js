$(document).ready(function () {
  $('.addProd').hide();

  products = [];

  $('#btnAddNewProduct').click(function (e) {
    e.preventDefault();
    $('.addProd').toggle(800);

    $('#imgProduct').attr('src', '');
    $('#selectNameProduct option').removeAttr('selected');
    $(`#selectNameProduct option[value='0']`).prop('selected', true);
    $('#refProduct option').removeAttr('selected');
    $(`#refProduct option[value='0']`).prop('selected', true);
    $('#discount option[value=0]').prop('selected', true);
  });

  $('#refProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    $('#selectNameProduct option').removeAttr('selected');
    $(`#selectNameProduct option[value=${id}]`).prop('selected', true);
    loadDataProduct(id);
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    $('#refProduct option').removeAttr('selected');
    $(`#refProduct option[value=${id}]`).prop('selected', true);
    loadDataProduct(id);
  });

  loadDataProduct = async (id) => {
    let data = await searchData(`/api/productCost/${id}`);

    if (data.price == false) {
      price = 0;
    } else {
      price = parseInt(data.price).toLocaleString('es-CO');
    }

    sessionStorage.removeItem('price');
    sessionStorage.setItem('price', data.price);

    $('#price').val(price);

    if (data.img) $('#imgProduct').attr('src', data.img);

    $('#quantity').click();
  };

  /* Calcular precio total */
  $(document).on('click keyup', '.calcPrice', function (e) {
    let id = this.id;

    if (id.includes('price')) {
      let idProduct = $('#refProduct').val();
      if (idProduct > 0) {
        let oldPrice = sessionStorage.getItem('price');

        let price = replaceNumber(this.value);

        if (price < parseInt(oldPrice)) {
          oldPrice = parseInt(oldPrice).toLocaleString('es-CO');

          $('#price').val(oldPrice);
          toastr.error('Ingrese un precio mayor al original');
          return false;
        }
      }
    }

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

    $('#totalPrice').val(parseInt(val).toLocaleString('es-CO'));
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

    if (ref == 'Seleccionar' || price == '' || quantity == '') {
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

    price = replaceNumber(price);

    let product = {
      idProduct: idProduct,
      ref: ref.trim(),
      nameProduct: nameProduct.trim(),
      price: `$ ${parseInt(price).toLocaleString('es-CO')}`,
      quantity: quantity,
      discount: discount,
      totalPrice: `$ ${totalPrice}`,
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
              <a href="javascript:;" id="${i}" <i class="bx bx-trash deleteProduct" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 18px;color:red"></i></a>
            </td>
        </tr>`
      );
    }
  };
});
