$(document).ready(function () {
  $('#btnLoadTrm').click(function (e) {
    e.preventDefault();

    $.get('/api/loadLastsTrm', function (data, textStatus, jqXHR) {
      message(data);
    });
  });

  message = (data) => {
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
