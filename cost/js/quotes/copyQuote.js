$(document).ready(function () {
  /* Copiar cotizacion */
  copyQuote = () => {
    if (tblQuotes == null || !tblQuotes)
      idQuote = sessionStorage.getItem('id_quote');
    else {
      let row = $(this).closest('tr')[0];
      let data = tblQuotes.fnGetData(row);

      idQuote = data.id_quote;
    }

    bootbox.confirm({
      title: 'Clonación',
      message:
        'Está seguro de copiar esta cotización? Esta acción no se puede reversar.',
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
          $.get(`/api/quotes/copyQuote/${idQuote}`, function (data, textStatus, jqXHR) {
            if (data.reload) {
              location.reload();
            }

            if (data.success == true) {
              toastr.success(data.message);
              if (tblQuotes == null || !tblQuotes) return false;
              $('#tblQuotes').DataTable().clear();
              $('#tblQuotes').DataTable().ajax.reload();
            } else if (data.error == true) toastr.error(data.message);
          }
          );
        }
      },
    });
  };
});
