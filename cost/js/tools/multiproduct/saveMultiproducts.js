$(document).ready(function () {
  /* Guardar Multiproductos */
  saveMultiproducts = async (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addMultiproduct',
      data: { data: data },
      success: function (resp) {
        if (resp.reload) {
          location.reload();
        }
        // console.log(resp);
      },
    });
  };
});
