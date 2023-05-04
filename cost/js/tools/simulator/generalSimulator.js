$(document).ready(function () {
  /* Cargar nombre de producto */
  $('#product').change(function (e) {
    e.preventDefault();

    let id = this.value;

    let data = sessionStorage.getItem('dataProducts');
    data = JSON.parse(data);
    let img = '';

    for (let i = 0; i < data.length; i++) {
      if (data[i].id_product == id) {
        img = data[i].img;
        break;
      }
    }

    let nameproduct = $('#product option:selected').text().trim();

    $('#nameProduct').html(nameproduct);

    if (img)
      $('.imageProduct').html(`
      <img src="${img}" class="mx-auto d-block" style="width:60px;border-radius:100px">
    `);
  });
});
