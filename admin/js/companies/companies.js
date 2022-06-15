$(document).ready(function() {

    /* Abrir modal crear empresa */

    let id;
    $("#btnNewCompany").click(function(e) {
        e.preventDefault();
        $("#createCompany").modal("show");
        $("#license").show();
        $("#btnCreateCompany").removeClass("updCompany");
        $("#btnCreateCompany").addClass("crtCompany");
        $("#btnCreateCompany").html("Crear");
        $("#formCreateCompany").trigger("reset");
    });

    /* Cerrar Modal*/

    $("#btnCloseCompany").click(function(e) {
        e.preventDefault();
        $("#createCompany").modal("hide");
    });

    /* Crear Empresa */

    $(document).on("click", ".crtCompany", function(e) {
        e.preventDefault();
        company = $("#company").val();
        companyNIT = $("#companyNIT").val();
        companyCreator = $("#companyCreator").val();
        companyCreatedAt = $("#companyCreated_at").val();
        companyLogo = $("#companyLogo").val();
        companyCity = $("#companyCity").val();
        companyState = $("#companyState").val();
        companyCountry = $("#companyCountry").val();
        companyAddress = $("#companyAddress").val();
        companyTel = $("#companyTel").val();
        /*Licencia*/
        companyLicStart = $("#companyLic_start").val();
        companyLicEnd = $("#companyLic_end").val();
        companyUsers = $("#companyUsers").val();
        companyStatus = $("#companyStatus").val();

        dataProduct = new FormData(document.getElementById("formCreateCompany"));
        dataProduct.append("companyStatus", companyStatus);

        if (
            company === "" ||
            companyNIT === "" ||
            companyCreator === "" ||
            companyCreatedAt === "" ||
            companyCity === "" ||
            companyState === "" ||
            companyCountry === "" ||
            companyAddress === "" ||
            companyTel === "" ||
            companyLicStart == "" ||
            companyLicEnd == "" ||
            companyUsers == ""
        ) {
            toastr.error("Ingrese todos los campos");
            return false;
        } else {
            $.ajax({
                type: "POST",
                url: "/api/addNewCompany",
                data: dataProduct,
                contentType: false,
                cache: false,
                processData: false,

                success: function(resp) {
                    // $('#createCompany').modal('hide');
                    // $('#formCreateCompany').val('');
                    message(resp);
                    // updateTable();
                },
            });
        }
    });

    /* Cargar datos en el modal Empresa */

    $(document).on("click", ".updateCompany", function(e) {
        e.preventDefault();
        $("#createCompany").modal("show");
        $("#license").hide();
        $("#btnCreateCompany").removeClass("crtCompany");
        $("#btnCreateCompany").addClass("updCompany");
        $("#btnCreateCompany").html("Actualizar");

        let row = $(this).parent().parent()[0];
        let data = tblCompanies.fnGetData(row);

        id = data.id_company;
        date = data.created_at.split(" ")[0];

        $("#company").val(data.company);
        $("#companyNIT").val(data.nit);
        $("#companyCreator").val(data.creador);
        $("#companyCreated_at").val(date);
        $("#companyLogo").val(data.logo);
        $("#companyCity").val(data.city);
        $("#companyState").val(data.state);
        $("#companyCountry").val(data.country);
        $("#companyAddress").val(data.address);
        $("#companyTel").val(data.telephone);
    });


    /* Actualizar Empresa */

    $(document).on("click", ".updCompany", function(e) {
        e.preventDefault();

        company = $("#company").val();
        companyNIT = $("#companyNIT").val();
        companyCreator = $("#companyCreator").val();
        companyCreatedAt = $("#companyCreated_at").val();
        companyLogo = $("#companyLogo").val();
        companyCity = $("#companyCity").val();
        companyState = $("#companyState").val();
        companyCountry = $("#companyCountry").val();
        companyAddress = $("#companyAddress").val();
        companyTel = $("#companyTel").val();

        dataProduct = new FormData(document.getElementById("formCreateCompany"));
        dataProduct.append("id_company", id);

        $.ajax({
            type: "POST",
            url: "/api/updateDataCompany",
            data: dataProduct,
            contentType: false,
            cache: false,
            processData: false,

            success: function(resp) {
                // $('#createCompany').modal('hide');
                // $('#formCreateCompany').val('');
                message(resp);
                // updateTable();
            },
        });
    });
    
    /* Mensaje de exito */

    const message = (data) => {
        if (data.success == true) {
            $("#createCompany").hide(800);
            $("#formCreateCompany")[0].reset();
            updateTable();
            toastr.success(data.message);
            return false;
        } else if (data.error == true) toastr.error(data.message);
        else if (data.info == true) toastr.info(data.message);
    };


    /* Actualizar tabla */

    function updateTable() {
        $("#tblCompanies").DataTable().clear();
        $("#tblCompanies").DataTable().ajax.reload();
    }
});