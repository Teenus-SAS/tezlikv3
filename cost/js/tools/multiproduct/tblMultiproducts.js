$(document).ready(function () {
  $('#btnShowTbl').click(function (e) {
    e.preventDefault();

    $('.cardGraphicMultiproducts').hide(800);
    $('.cardTblMultiproducts').show(800);
  });

  loadTblMultiproducts = async () => {
    let data = await searchData('/api/multiproducts');

    let tblMultiproductsBody = document.getElementById('tblMultiproductsBody');

    for (let i = 0; i < data.length; i++) {
      tblMultiproductsBody.insertAdjacentHTML(
        'beforeend',
        `<tr>
          <td>${data[i].product}</td>
          <td>
            <input class="form-control number text-center" id="soldUnit-${i}">
          </td>
          <td>$ ${data[i].price.toLocaleString('es-CO', {
            maximumFractionDigits: 0,
          })}</td>
          <td>$ ${data[i].variable_cost.toLocaleString('es-CO', {
            maximumFractionDigits: 0,
          })}</td>
          <td>$ ${data[i].cost_fixed.toLocaleString('es-CO', {
            maximumFractionDigits: 0,
          })}</td>
          <td id="part-${i}"></td>
          <td id="cont-${i}"></td>
          <td id="aver-${i}"></td>
          <td id="unitTo-${i}"></td>
        </tr>`
      );
    }

    $('#tblMultiproducts').dataTable({
      pageLength: 50,
      autoWidth: true,
    });
  };

  loadTblMultiproducts();
});
