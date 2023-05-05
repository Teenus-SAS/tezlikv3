$(document).ready(function () {
  /* Productos */
  loadTblSimulatorProducts = (data) => {
    tblSimulator = $('#tblSimulator').DataTable({
      destroy: true,
      scrollY: '150px',
      scrollCollapse: true,
      paging: false,
      data: data,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      },
      columns: [
        {
          className: 'dt-control',
          orderable: false,
          data: null,
          defaultContent: '',
        },
        {
          title: 'Referencia',
          data: 'reference',
          className: 'uniqueClassName',
        },
        {
          title: 'Costo',
          data: 'price',
          className: 'uniqueClassName',
          render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
        },
      ],
    });

    setInterval(() => {
      let tables = document.getElementsByClassName(
        'dataTables_scrollHeadInner'
      );

      let attr = tables[0].firstElementChild;
      attr.style.width = '400px';
    }, 1000);
  };

  function formatProducts(d) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-10px;">
            <tr>
                <th>Producto:</td>
                <td>${d.product}</td>
            </tr>
            <tr>
                <th>Comision:</th>
                <td>${d.commission_sale}</td>
            </tr>
            <tr>
                <th>Rentabilidad:</th>
                <td>${d.profitability}</td>
            </tr>
        </table>`;
  }

  $(document).on('click', '.dt-control', function () {
    var tr = $(this).closest('tr');
    var row = tblSimulator.row(tr);

    if (row.child.isShown()) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    } else {
      // Open this row
      row.child(formatProducts(row.data())).show();
      tr.addClass('shown');
    }
  });

  /* Materia Prima 
  loadTblSimulatorMaterials = (data) => {
    tblSimulator = $('#tblSimulator').DataTable({
      destroy: true,
      scrollY: '150px',
      scrollCollapse: true,
      paging: false,
      data: data,
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
      },
      columns: [
        {
          className: 'dt-control-products',
          orderable: false,
          data: null,
          defaultContent: '',
        },
        {
          title: 'Referencia',
          data: 'reference',
          className: 'uniqueClassName',
        },
        {
          title: 'Costo',
          data: 'cost',
          className: 'uniqueClassName',
          render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
        },
      ],
    });

    setInterval(() => {
      let tables = document.getElementsByClassName(
        'dataTables_scrollHeadInner'
      );

      let attr = tables[0].firstElementChild;
      attr.style.width = '400px';
    }, 1000);
  };

  function formatProducts(d) {
    return `<table cellpadding="5" cellspacing="0" border="0" style="margin:-10px;">
            <tr>
                <th>Material:</td>
                <td>${d.material}</td>
            </tr>
            <tr>
                <th>Unidad:</th>
                <td></td>
            </tr>
        </table>`;
  }

  $(document).on('click', '.dt-control-products', function () {
    var tr = $(this).closest('tr');
    var row = tblSimulator.row(tr);

    if (row.child.isShown()) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    } else {
      // Open this row
      row.child(formatProducts(row.data())).show();
      tr.addClass('shown');
    }
  }); */
});
