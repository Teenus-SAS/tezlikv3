$(document).ready(function () {
  $('#btnLoadTrm').click(async function (e) {
    e.preventDefault();

    $('.cardBotton').hide(400);

    $('#title').attr('class', 'col-sm-11');

    let headerTrm = document.getElementById('headerTrm');

    headerTrm.insertAdjacentHTML(
      'beforeend',
      `<div class="col-sm-1 cardLoading">
        <div class="spinner-border text-secondary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
      </div>`
    );

    await $.get('/api/loadLastsTrm', function (data, textStatus, jqXHR) {
      message(data);
    });
  });

  message = (data) => {
    $('#title').attr('class', 'col-sm-5 col-xl-6');

    $('.cardLoading').remove();
    $('.cardBotton').show(400);
    if (data.success == true) {
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  function updateTable() {
    $('#tblHistoricalTrm').DataTable().clear();
    $('#tblHistoricalTrm').DataTable().ajax.reload();
  }
});
