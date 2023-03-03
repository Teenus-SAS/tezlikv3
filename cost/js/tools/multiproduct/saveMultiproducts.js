$(document).ready(function () {
  /* Guardar Multiproductos */
  saveMultiproducts = async (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addMultiproduct',
      data: { data: data },
      success: function (resp) {
        console.log(resp);
      },
    });
  };
});
