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
      .then(async (data) => {
        let arr = data.rowObject;

        if (arr.length == 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProductsProcess').val('');
          toastr.error('Archivo vacio. Verifique nuevamente');
          return false;
        }

        const expectedHeaders = ['referencia_producto', 'producto', 'proceso', 'maquina', 'tiempo_enlistamiento', 'tiempo_operacion', 'eficiencia', 'maquina_autonoma'];
        const actualHeaders = data.actualHeaders;

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProductsProcess').val('');

          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let resp = await validateDataFTP(arr);
        
        if (resp.importStatus == true)
          checkProductProcess(resp.productProcessToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileProductsProcess').val('');
        
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Validar data */ 
  const validateDataFTP = async (data) => {
    let productProcessToImport = [];
    let importStatus = true;

    const dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));
    let dataProcess = JSON.parse(sessionStorage.getItem('dataProcess'));
    let dataMachines = JSON.parse(sessionStorage.getItem('dataMachines'));

    if (!dataProcess) {
      await findSelectProcess();
      dataProcess = JSON.parse(sessionStorage.getItem('dataProcess'));
    }

    if (!dataMachines) {
      await getSelectMachine('/api/selectMachines');
      dataMachines = JSON.parse(sessionStorage.getItem('dataMachines'));
    }

    for (let i = 0; i < data.length; i++) {
      let arr = data[i];

      let enlistmentTime = arr.tiempo_enlistamiento > 0 ? arr.tiempo_enlistamiento.toString() : '0';
      let operationTime = arr.tiempo_operacion > 0 ? arr.tiempo_operacion.toString() : '0';
      let efficiency = arr.eficiencia > 0 ? arr.eficiencia.toString() : '0';

      if (
        !arr.referencia_producto || !arr.producto || !arr.proceso || !arr.maquina ||
        enlistmentTime.trim() === '' || operationTime.trim() === '' || efficiency.trim() === '' || !arr.maquina_autonoma ||
        !arr.referencia_producto.toString().trim() || !arr.producto.toString().trim() || !arr.proceso.toString().trim() || !arr.maquina.toString().trim() || !arr.maquina_autonoma.toString().trim()
      ) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileProductsProcess').val('');
        toastr.error(`Columna vacía en la fila: ${i + 2}`);
        importStatus = false;
        break;
      }

      let valOT = parseFloat(operationTime.replace(',', '.')) * 1;
      if (isNaN(valOT) || valOT <= 0) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileProductsProcess').val('');
        toastr.error(`El tiempo de operación debe ser mayor a cero (0). Fila: ${i + 2}`);
        importStatus = false;
        break;
      }

      let product = dataProducts.find(item =>
        item.reference == arr.referencia_producto.toString().trim() &&
        item.product == arr.producto.toString().toUpperCase().trim()
      );

      if (!product) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileProductsProcess').val('');
        toastr.error(`Producto no existe en la base de datos. Fila: ${i + 2}`);
        importStatus = false;
        break;
      }

      let process = dataProcess.find(item => item.process == arr.proceso.toString().toUpperCase().trim());

      if (!process) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileProductsProcess').val('');
        toastr.error(`Proceso no existe en la base de datos. Fila: ${i + 2}`);
        importStatus = false;
        break;
      }

      let idMachine = 0;
      if (arr.maquina.toString().toUpperCase().trim() !== 'PROCESO MANUAL') {
        let machine = dataMachines.find(item => item.machine == arr.maquina.toString().toUpperCase().trim());

        if (!machine) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProductsProcess').val('');
          toastr.error(`Máquina no existe en la base de datos. Fila: ${i + 2}`);
          importStatus = false;
          break;
        }

        idMachine = machine.id_machine;
      }

      productProcessToImport.push({
        idProduct: product.id_product,
        idProcess: process.id_process,
        idMachine: idMachine,
        referenceProduct: arr.referencia_producto,
        product: arr.producto,
        process: arr.proceso,
        machine: arr.maquina,
        enlistmentTime: enlistmentTime,
        operationTime: operationTime,
        efficiency: efficiency,
        employees: '',
        autoMachine: arr.maquina_autonoma
      });
    }

    return { importStatus, productProcessToImport };
  };
  /* Mensaje de advertencia */
  const checkProductProcess = (data) => {
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

  const saveProductProcessTable = (data) => {
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
    
    let url = 'assets/formatsXlsx/Productos_Procesos.xlsx';

    let link = document.createElement('a');
    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
