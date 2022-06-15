$(document).ready(function () {
  
  /*Ocualtar panel de actualización*/
  
  let id;
  $(".cardUpdLicense").hide();

  /*Cargar datos en panel actualización*/
  $(document).on("click", ".updateLicenses", function (e) {
    e.preventDefault();
    $(".cardUpdLicense").toggle(800);
    let row = $(this).parent().parent()[0];
    let data = tblCompaniesLic.fnGetData(row);

    id = data.id_company;

    $("#company").val(data.company);
    $("#license_start").val(data.license_start);
    $("#license_end").val(data.license_end);
    $("#quantityUsers").val(data.quantity_user);
  });

  /*Actualizar licencia*/

  $("#btnUpdLicense").click(function (e) {
    e.preventDefault();

    company = $("#company").val();
    license_start = $("#license_start").val();
    license_end = $("#license_end").val();
    quantityUsers = $("#quantityUsers").val();

    dataProduct = new FormData(document.getElementById("formUpdateLicense"));
    dataProduct.append("id_company", id);

    $.ajax({
      type: "POST",
      url: "/api/updateLicense",
      data: dataProduct,
      contentType: false,
      cache: false,
      processData: false,

      success: function (resp) {
        message(resp);
      },
    });
  });

  /* Cambiar Estado Licencia */

  $(document).on("click", ".licenseStatus", function (e) {
    e.preventDefault();
    id_company = this.id;

    $.ajax({
      type: "POST",
      url: `/api/changeStatusCompany/${id_company}`,
      success: function (resp) {
        if (resp.success == true) {
          updateTable();
          toastr.success(resp.message);
          return false;
        } else if (resp.error == true) toastr.error(resp.message);
        else if (resp.info == true) toastr.info(resp.message);
        // message(resp);       
      },
    });
  });


  /* Guardar id en sessionstorage */

  $(document).on("click", ".companyUsers", function (e) {
    e.preventDefault();
    id_company = this.id;
    sessionStorage.setItem('id_company',id_company)
  });


  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $(".cardUpdLicense").hide(800);
      $("#formUpdateLicense")[0].reset();
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
  

  /* Actualizar tabla */

  function updateTable() {
    $("#tblCompaniesLicense").DataTable().clear();
    $("#tblCompaniesLicense").DataTable().ajax.reload();
  }
});
