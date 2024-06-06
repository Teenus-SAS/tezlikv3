$(document).ready(function () {
  let selectedFile;

  $('.cardImportProductsProcess').hide();

  $('#btnImportNewProductProcess').click(function (e) {
    e.preventDefault();
    $('.cardAddNewProduct').hide(800);
    $('.cardAddProcess').hide(800);
    $('.cardProducts').toggle(800);

    $('.cardImportProductsProcess').toggle(800);
  });

  $('#fileProductsProcess').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportProductsProcess').click(function (e) {
    e.preventDefault();

    let file = $('#fileProductsProcess').val();
    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formProductProcess');

    form.insertAdjacentHTML(
      'beforeend',
      `<div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
        <div class="spinner-border text-secondary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
      </div>`
    );

    importFile(selectedFile)
      .then((data) => {
        if (data.length == 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProductsProcess').val('');
          toastr.error('Archivo vacio. Verifique nuevamente');
          return false;
        }

        const expectedHeaders = ['referencia_producto', 'producto', 'proceso', 'maquina', 'tiempo_enlistamiento', 'tiempo_operacion', 'eficiencia', 'maquina_autonoma'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProductsProcess').val('');

          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        // let productProcessToImport = data.map((item) => {
        //   // let enlistmentTime = '';
        //   // let operationTime = '';

        //   // if (item.tiempo_enlistamiento)
        //   //   enlistmentTime = item.tiempo_enlistamiento.toString().replace('.', ',');
        //   // if (item.tiempo_operacion)
        //   //   operationTime = item.tiempo_operacion.toString().replace('.', ',');

        //   return {
        //     referenceProduct: item.referencia_producto,
        //     product: item.producto,
        //     process: item.proceso,
        //     machine: item.maquina,
        //     enlistmentTime: item.tiempo_enlistamiento,
        //     operationTime: item.tiempo_operacion,
        //     efficiency: item.eficiencia,
        //     autoMachine: item.maquina_autonoma
        //   };
        // });

        let productProcessToImport = [];
        let importStatus = true;

        for (let i = 0; i < data.length; i++) {
          let arr = data[i];

          let enlistmentTime = '0';
          let operationTime = '';
          let efficiency = '0';

          if (arr.tiempo_enlistamiento > 0) {
            enlistmentTime = arr.tiempo_enlistamiento.toString();
          }
          if (arr.tiempo_operacion > 0) {
            operationTime = arr.tiempo_operacion.toString();
          }
          if (arr.eficiencia > 0) {
            efficiency = arr.eficiencia.toString();
          }

          // Validación de campos vacíos o nulos
          if (!arr.referencia_producto || !arr.producto || !arr.proceso || !arr.maquina ||
            enlistmentTime.trim() == '' || operationTime.trim() == '' || efficiency.trim() == '' || !arr.maquina_autonoma) {
            $('.cardLoading').remove();
            $('.cardBottons').show(400);
            $('#fileProductsProcess').val('');
            importStatus = false;

            toastr.error(`Columna vacía en la fila: ${i + 2}`);
            break;
          }

          // Validación de campos que no están vacíos o nulos pero son solo espacios
          if (!arr.referencia_producto.toString().trim() || !arr.producto.toString().trim() || !arr.proceso.toString().trim() || !arr.maquina.toString().trim() || !arr.maquina_autonoma.toString().trim()) {
            $('.cardLoading').remove();
            $('.cardBottons').show(400);
            $('#fileProductsProcess').val('');
            importStatus = false;

            toastr.error(`Columna vacía en la fila: ${i + 2}`);
            break;
          }

          let valOT = parseFloat(operationTime.replace(',', '.')) * 1;
          if (isNaN(valOT) || valOT <= 0) {
            $('.cardLoading').remove();
            $('.cardBottons').show(400);
            $('#fileProductsProcess').val('');
            importStatus = false;

            toastr.error(`El tiempo de operacion debe ser mayor a cero (0). Fila: ${i + 2}`);
            break;
          }

          // Validar Producto
          let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));
          let product = dataProducts.find(item => item.reference == arr.referencia_producto.toString().trim() &&
            item.product == arr.producto.toString().toUpperCase().trim());

          if (!product) {
            $('.cardLoading').remove();
            $('.cardBottons').show(400);
            $('#fileProductsProcess').val('');
            importStatus = false;

            toastr.error(`Producto no existe en la base de datos. Fila: ${i + 2}`);
            break;
          }

          productProcessToImport.push({ idProduct: product.id_product });

          // Validar Proceso
          let dataProcess = JSON.parse(sessionStorage.getItem('dataProcess'));
          let process = dataProcess.find(item => item.process == arr.proceso.toString().toUpperCase().trim());

          if (!process) {
            $('.cardLoading').remove();
            $('.cardBottons').show(400);
            $('#fileProductsProcess').val('');
            importStatus = false;

            toastr.error(`Proceso no existe en la base de datos. Fila: ${i + 2}`);
            break;
          }
          productProcessToImport[i].idProcess = process.id_process;

          // Validar Maquina
          let dataMachines = JSON.parse(sessionStorage.getItem('dataMachines'));
          
          if (arr.maquina.toString().toUpperCase().trim() == 'PROCESO MANUAL') {
            productProcessToImport[i].idMachine = 0;
          } else {
            let machine = dataMachines.find(item => item.machine == arr.maquina.toString().toUpperCase().trim());

            if (!machine) {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#fileProductsProcess').val('');
              importStatus = false;

              toastr.error(`Maquina no existe en la base de datos. Fila: ${i + 2}`);
              break;
            }

            productProcessToImport[i].idMachine = machine.id_machine;
          }

          productProcessToImport[i].referenceProduct = arr.referencia_producto;
          productProcessToImport[i].product = arr.producto;
          productProcessToImport[i].process = arr.proceso;
          productProcessToImport[i].machine = arr.maquina;
          productProcessToImport[i].enlistmentTime = enlistmentTime;
          productProcessToImport[i].operationTime = operationTime;
          productProcessToImport[i].efficiency = efficiency;
          productProcessToImport[i].autoMachine = arr.maquina_autonoma;
        }
        
        if (importStatus == true)
          checkProductProcess(productProcessToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileProductsProcess').val('');
        
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkProductProcess = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/productsProcessDataValidation',
      data: { importProductsProcess: data },
      success: function (resp) {

        if (resp.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);

          $('#fileProductsProcess').val('');
          toastr.error(resp.message);
          return false;
        }

        bootbox.confirm({
          title: '¿Desea continuar con la importación?',
          message: `Se han encontrado los siguientes registros:<br><br>Datos a insertar: ${resp.insert} <br>Datos a actualizar: ${resp.update}`,
          buttons: {
            confirm: {
              label: 'Si',
              className: 'btn-success',
            },
            cancel: {
              label: 'No',
              className: 'btn-danger',
            },
          },
          callback: function (result) {
            if (result == true) {
              saveProductProcessTable(data);
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#fileProductsProcess').val('');
            }
          },
        });
      },
    });
  };

  saveProductProcessTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addProductsProcess',
      data: { importProductsProcess: data },
      success: function (r) {
        messageProcess(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsProductsProcess').click(function (e) {
    e.preventDefault();
    // let dataProductProcess = JSON.parse(sessionStorage.getItem('dataProductProcess'));

    // if (dataProductProcess.length > 0) {
    //   let wb = XLSX.utils.book_new();

    //   let data = [];

    //   namexlsx = 'Productos_Procesos.xlsx';
    //   for (i = 0; i < dataProductProcess.length; i++) {
    //     data.push({
    //       referencia_producto: dataProductProcess[i].reference,
    //       producto: dataProductProcess[i].product,
    //       proceso: dataProductProcess[i].process,
    //       maquina: dataProductProcess[i].machine,
    //       tiempo_enlistamiento: dataProductProcess[i].enlistment_time,
    //       tiempo_operacion: dataProductProcess[i].operation_time,
    //       eficiencia: dataProductProcess[i].efficiency,
    //       // mano_de_obra: dataProductProcess[i].workforce_cost,
    //       // costo_indirecto: dataProductProcess[i].indirect_cost,
    //       maquina_autonoma: dataProductProcess[i].auto_machine
    //     });
    //   }

    //   let ws = XLSX.utils.json_to_sheet(data);
    //   XLSX.utils.book_append_sheet(wb, ws, 'Productos Procesos');
    //   XLSX.writeFile(wb, namexlsx);
    // }
    // else {
    let url = 'assets/formatsXlsx/Productos_Procesos.xlsx';

    let link = document.createElement('a');
    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
    // }
  });
});
