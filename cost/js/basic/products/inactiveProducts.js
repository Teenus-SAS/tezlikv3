$(document).ready(function () {
  /* Inactivar productos */
  $(document).on("click", ".checkboxProduct", function () {
    let idProduct = this.id;

    bootbox.confirm({
      title: "Inactivar producto",
      message: "¿Esta seguro de inactivar este producto?",
      buttons: {
        confirm: {
          label: "Si",
          className: "btn-success",
        },
        cancel: {
          label: "No",
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result == true) {
          changeStatusProduct(idProduct, 0);
        } else {
          $(".checkboxProduct").prop("checked", true);
        }
      },
    });
  });

  changeStatusProduct = (id, op) => {
    $.ajax({
      url: `/api/changeActiveProduct/${id}/${op}`,
      success: function (data) {
        if (data.success == true) {
          toastr.success(data.message);
          loadAllData();
        } else if (data.error == true) toastr.error(data.message);
        else if (data.info == true) toastr.info(data.message);
      },
    });
  };

  /* Ocultar modal productos inactivos */
  $("#btnCloseInactivesProducts").click(function (e) {
    e.preventDefault();
    $("#createInactivesProducts").modal("hide");
    $("#tblInactiveProductsBody").empty();
  });

  /* Mostrar productos inactivos */
  $("#btnActiveProducts").click(function (e) {
    e.preventDefault();
    $("#tblInactiveProducts").empty();

    let tblInactiveProducts = document.getElementById("tblInactiveProducts");

    tblInactiveProducts.insertAdjacentHTML(
      "beforeend",
      `<thead>
        <tr>
        <th>No</th>
        <th>Referencia</th>
        <th>Producto</th>
        <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="tblInactiveProductsBody"></tbody>`
    );

    setTblInactivesProducts();
  });

  // Construir tabla con productos inactivos
  setTblInactivesProducts = async () => { 
    let data = dataInactiveProducts;

    let tblInactiveProductsBody = document.getElementById(
      "tblInactiveProductsBody"
    );

    for (i = 0; i < data.length; i++) {
      tblInactiveProductsBody.insertAdjacentHTML(
        "beforeend",
        `
        <tr>
            <td>${i + 1}</td>
            <td>${data[i].reference}</td>
            <td>${data[i].product}</td>
            <td>
                <a href="javascript:;" <span id="checkIn-${data[i].id_product}" class="badge badge-success checkInactiveProduct">Activar</span></a>
                <a href="javascript:;" <i id="${
                  data[i].id_product
                }" class="mdi mdi-delete-forever deleteProduct" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 30px;color:red"></i></a>
            </td>
        </tr>
      `
      );
    }

    $("#createInactivesProducts").modal("show");

    $("#tblInactiveProducts").DataTable({
      destroy: true,
      scrollY: "150px",
      scrollCollapse: true,
      // language: {
      //   url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      // },
      dom: '<"datatable-error-console">frtip',
      fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
        if (oSettings.json && oSettings.json.hasOwnProperty("error")) {
          console.error(oSettings.json.error);
        }
      },
    });

    let tables = document.getElementsByClassName("dataTables_scrollHeadInner");

    let attr = tables[0];
    attr.style.width = "100%";
    attr = tables[0].firstElementChild;
    attr.style.width = "100%";
  };

  /* Guardar productos a activar */
  $(document).on("click", ".checkInactiveProduct", async function () {
    let id = this.id;
    let idProduct = id.slice(8, id.length);
    
    await changeStatusProduct(idProduct, 1);
    
    // $(this).closest("tr").remove();
    var rowIndex = $(this).closest("tr").index();

    // Eliminar la fila de la DataTable
    $('#tblInactiveProducts').DataTable().row(rowIndex).remove().draw();
    // if (this.className.includes('badge-success')) {
    // let planeacion = {
    //     idProduct: idProduct,
    //   };

    //   inactiveProducts.push(planeacion);

    //   this.className = 'badge badge-warning checkInactiveProduct';
    //   this.text = 'Inactivar';
    // }
    // else {
    //   for (i = 0; i < inactiveProducts.length; i++) {
    //     if (inactiveProducts[i].idProduct == idProduct) {
    //       inactiveProducts.splice(i, 1);
    //     }
    //   }

    //   this.className = 'badge badge-success checkInactiveProduct';
    //   this.text = 'Activar';
    // }
  });

  /* Activar productos  
  $("#btnActivesProducts").click(function (e) {
    e.preventDefault();
    if (inactiveProducts.length == 0) {
      toastr.error("Seleccione un producto para activar");
      return false;
    }

    $.ajax({
      type: "POST",
      url: "/api/activeProducts",
      data: { data: inactiveProducts },
      success: function (data) {
        $("#createInactivesProducts").modal("hide");
        inactiveProducts = [];
        message(data);
      },
    });
  }); */
});
