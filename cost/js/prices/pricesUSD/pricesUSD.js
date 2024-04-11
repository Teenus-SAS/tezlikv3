$(document).ready(function () { 
  let op = 1;

  $(document).on('click', '#btnSimulation', function () {
    let USDHeader = document.getElementById('USDHeader');

    $('#USDHeader').empty();

    if (op == 1) {
      op = 2;

      USDHeader.insertAdjacentHTML('beforeend',
        `<div class="col-xs-2 mr-2 form-group floating-label enable-floating-label USDInputs">
          <label class="font-weight-bold text-dark">Dolar Hoy</label>
          <input type="text" class="form-control text-center" name="valueDollar" id="valueDollar" style="background-color: aliceblue;"
            value="$ ${currentDollar.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2, })}" readonly>
        </div>
        <div class="col-xs-2 form-group floating-label enable-floating-label mr-2 USDInputs">
          <label class="font-weight-bold text-dark">Dolar con Cobertura</label>
          <input type="number" class="form-control text-center calcInputs" name="valueCoverage" id="valueCoverage"
            value="${parseFloat(coverage1).toFixed(2)}">
        </div>
        <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label USDInputs">
          <label class="font-weight-bold text-dark">Cobertura Cambiaria</label>
          <input type="text" class="form-control text-center" name="exchangeCoverage" id="exchangeCoverage" style="background-color: aliceblue;" readonly>
        </div>
        <div class="col-xs-2 form-group floating-label enable-floating-label USDInputs">
          <label class="font-weight-bold text-dark">Correción TRM</label>
          <input type="number" class="form-control text-center calcInputs" name="deviation" id="deviation" value="${deviation}">
        </div>
        <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label USDInputs">
          <label class="font-weight-bold text-dark">Valor Dolar</label>
          <input type="number" class="form-control text-center" name="valueCoverage" id="valueCoverage" style="background-color: aliceblue;"
            value="${parseFloat(coverage).toFixed(2)}" readonly>
        </div>
        <div class="col-xs-2 form-group floating-label enable-floating-label USDInputs">
          <button class="btn btn-warning" id="btnSimulation">Simular</button>
        </div>`);
      
      if (flag_composite_product == '1') { 
        loadTblPrices(parents, 3);
      } else
        loadTblPrices(allPrices, 3);
    } else {
      op = 1;

      USDHeader.insertAdjacentHTML('beforeend',
        `<div class="col-xs-2 mr-2 USDInputs">
          <button class="btn btn-warning" id="btnSimulation">Simular</button>
        </div>
        <div class="col-xs-2 mr-2 USDInputs">
          <button class="btn btn-info btnPricesUSD" id="cop">Precios COP</button>
        </div>
        <div class="col-xs-2 mr-2 form-group floating-label enable-floating-label USDInputs" style="margin-bottom: 0px;">
          <label class="mb-1 font-weight-bold text-dark">Valor Dolar</label>
          <input type="number" class="form-control text-center calcInputs" name="valueCoverage" id="valueCoverage"
            value="${parseFloat(coverage).toFixed(2)}">
        </div>
        
        `);
      if (flag_composite_product == '1') {
        loadTblPrices(parents, 2);
      } else
        loadTblPrices(allPrices, 2);
    }
  });

  /* Calcular valor de cobertura ingresando numero de desviación */
  $(document).on('blur', '.calcInputs', function (e) {
    let num = $('#deviation').val();
    let valueCoverage = parseFloat($('#valueCoverage').val());

    if (isNaN(valueCoverage) || valueCoverage <= 0) {
      toastr.error('Ingrese valor covertura valido');
      return false;
    }

    if (num == '' || num <= 0) {
      toastr.error('Ingrese valor de TRM valido');
      return false;
    }
    else if (!num) {
      bootbox.confirm({
      title: 'Guardar',
      message:
        'Está seguro de modificar la cobertura? Esta acción no se puede reversar.',
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
          getUSDData(valueCoverage, null, 1);
        }
      },
    });
    
    }
    else
      getUSDData(valueCoverage, num, 2, this.id);
  });

  getUSDData = async (valueCoverage, deviation, op, id) => {
    $('.USDInputs').hide(400);

    let USDHeader = document.getElementById('USDHeader');

    USDHeader.insertAdjacentHTML(
      'beforeend',
      `<div class="spinner-border text-secondary" role="status">
        <span class="sr-only">Loading...</span>
      </div>`
    );

    if (op == 1) {
      let data = await searchData(`/api/priceUSD/${valueCoverage}`);

      if (data.success) {
        coverage = valueCoverage;

        $('.spinner-border').remove();
        $('.USDInputs').show(400);

        loadAllData();
      }
    } else {
      let data = {};
      data['deviation'] = deviation;
      data['coverage'] = valueCoverage;
      data['id'] = id;

      $.post('/api/simPriceUSD', data,
        function (resp, textStatus, jqXHR) {
          if (resp.success) {
            $('#exchangeCoverage').val(
              `$ ${resp.exchangeCoverage.toLocaleString('es-CO', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              })}`
            ); 

            coverage1 = resp.coverage;

            $('#valueCoverage').val(parseFloat(resp.coverage).toFixed(2));
       
            $('.spinner-border').remove();
            $('.USDInputs').show(400);

            if (flag_composite_product == '1') {
              loadTblPrices(parents, 3, resp.coverage);
            } else
              loadTblPrices(allPrices, 3, resp.coverage);
          }
        },
      ); 
    }
  }; 
});
