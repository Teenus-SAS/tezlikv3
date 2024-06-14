$(document).ready(function () {
  findSelectProcess = async () => {
    await $.ajax({
      type: 'GET',
      url: '/api/process',
      success: function (r) {
        sessionStorage.setItem('dataProcess', JSON.stringify(r));
      
        let $select = $(`#idProcess`);
        $select.empty();

        $select.append(`<option disabled selected>Seleccionar</option>`);
        $.each(r, function (i, value) {
          $select.append(
            `<option value = ${value.id_process} class='${value.count_payroll}'> ${value.process} </option>`
          );
        });
      },
    });
  };
});
