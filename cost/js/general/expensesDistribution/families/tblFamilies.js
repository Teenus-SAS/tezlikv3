$(document).ready(function () {
  loadTableFamilies = () => {
    if ($.fn.dataTable.isDataTable('#tblFamilies')) {
      $('#tblFamilies').DataTable().destroy();
      $('#tblFamilies').empty();
    } 

    tblFamilies = $('#tblFamilies').dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: '../../api/families',
        dataSrc: '',
      },
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
          title: 'Nombre',
          data: 'family',
        },
        {
          title: 'Acciones',
          data: 'id_family',
          className: 'uniqueClassName',
          render: function (data) {
            return `
          <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateFamily" data-toggle='tooltip' title='Actualizar Familia' style="font-size: 30px;"></i></a>    
          <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Familia' style="font-size: 30px;color:red" onclick="deleteFamily()"></i></a>`;
          },
        },
      ],
    });
  };

  loadTableProductsFamilies = () => {
    if ($.fn.dataTable.isDataTable('#tblFamilies')) {
      $('#tblFamilies').DataTable().destroy();
      $('#tblFamilies').empty();
    }

    tblFamilies = $('#tblFamilies').dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: '../../api/productsFamilies',
        dataSrc: '',
      },
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
          title: 'Familia',
          data: 'family',
          visible: false,
        },
        {
          title: 'Referencia',
          data: 'reference',
        },
        {
          title: 'Producto',
          data: 'product',
        },
        {
          title: 'Acciones',
          data: null,
          className: 'uniqueClassName',
          render: function () {
            return `
            <a href="javascript:;" <i class="bx bx-edit-alt updateProductFamily" data-toggle='tooltip' title='Actualizar Producto' style="font-size: 30px;"></i></a>    
            <a href="javascript:;" <i class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 30px;color:red" onclick="deleteProductFamily()"></i></a>`;
          },
        },
      ],
      rowGroup: {
        dataSrc: function (row) {
          return `<th class="text-center" colspan="4" style="font-weight: bold;"> ${row.family} </th>`;
        },
        startRender: function (rows, group) {
          return $('<tr/>').append(group);
        },
        className: 'odd',
      },
    });
  };
});
