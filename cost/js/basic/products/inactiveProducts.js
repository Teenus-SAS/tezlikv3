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
  const setTblInactivesProducts = async () => {
    const data = dataInactiveProducts;
    const tblInactiveProductsBody = document.getElementById("tblInactiveProductsBody");
    let html = '';

    for (let i = 0; i < data.length; i++) {
      html += `
        <tr>
            <td>${i + 1}</td>
            <td>${data[i].reference}</td>
            <td>${data[i].product}</td>
            <td>
                <a href="javascript:;">
                    <span id="checkIn-${data[i].id_product}" class="badge badge-success checkInactiveProduct">Activar</span>
                </a>
                <a href="javascript:;">
                    <i id="${data[i].id_product}" class="mdi mdi-delete-forever deleteProduct" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 30px; color: red;"></i>
                </a>
            </td>
        </tr>`;
    }

    tblInactiveProductsBody.innerHTML = html;

    // Mostrar modal
    $("#createInactivesProducts").modal("show");

    // Inicializar DataTable
    $("#tblInactiveProducts").DataTable({
      destroy: true,
      scrollY: "150px",
      scrollCollapse: true,
      dom: '<"datatable-error-console">frtip',
      fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
        if (oSettings.json && oSettings.json.hasOwnProperty("error")) {
          console.error(oSettings.json.error);
        }
      }
    });

    // Ajustar el ancho de la tabla
    const tables = document.getElementsByClassName("dataTables_scrollHeadInner");
    if (tables.length > 0) {
      tables[0].style.width = "100%";
      if (tables[0].firstElementChild) {
        tables[0].firstElementChild.style.width = "100%";
      }
    }
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
  });
});
