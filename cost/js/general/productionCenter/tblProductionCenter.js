$(document).ready(function () {
  loadTblPCenter = (data) => {
    tblPCenter = $('#tblPCenter').dataTable({
      destroy: true,
      pageLength: 50,
      data: data,
      dom: '<"datatable-error-console">frtip',
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      },
      fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
        if (oSettings.json && oSettings.json.hasOwnProperty('error')) {
          console.error(oSettings.json.error);
        }
      },
      columns: [
        {
          title: 'No.',
          data: null,
          className: 'uniqueClassName',
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
        },
        {
          title: 'Centro De Produccion',
          data: 'production_center',
          className: 'uniqueClassName',
        },
        {
          title: 'Acciones',
          data: null,
          className: 'uniqueClassName',
          render: function (data) {
            if (data.status == 0)
              icon = '/global/assets/images/trash_v.png';
            else
              icon = '/global/assets/images/trash_x.png';
          
            return `
                <a href="javascript:;" <i id="${data.id_production_center}" class="bx bx-edit-alt updatePCenter" data-toggle='tooltip' title='Actualizar Produccion' style="font-size: 30px;"></i></a>
                <a href="javascript:;"><img src="${icon}" alt="Eliminar Produccion" id="${data.id_production_center}" style="width:30px;height:30px;margin-top:-20px" onclick="deletePCenter()"></a>`;
          },
        },
      ],
    });
  }
});