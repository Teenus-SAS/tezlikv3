$(document).ready(function () {
  /* Seleccion producto */
  $('#refProduct').change(function (e) {
    e.preventDefault();
    id = this.value;
    $(`#selectNameProduct option[value=${id}]`).attr('selected', true);
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    id = this.value;
    $(`#refProduct option[value=${id}]`).attr('selected', true);
  });

  // Cargar tabla de Ventas
  tblSales = $('#tblSales').dataTable({
    pageLength: 50,
    ajax: {
      url: '../../api/unitSales',
      dataSrc: '',
    },
    language: {
      url: '//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json',
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
        title: 'Producto',
        data: 'product',
        className: 'uniqueClassName',
      },
      {
        title: 'Año',
        data: 'year',
        className: 'uniqueClassName',
      },
      {
        title: 'Enero',
        data: 'jan',
        className: 'uniqueClassName',
      },
      {
        title: 'Febrero',
        data: 'feb',
        className: 'uniqueClassName',
      },
      {
        title: 'Marzo',
        data: 'mar',
        className: 'uniqueClassName',
      },
      {
        title: 'Abril',
        data: 'apr',
        className: 'uniqueClassName',
      },
      {
        title: 'Mayo',
        data: 'may',
        className: 'uniqueClassName',
      },
      {
        title: 'Junio',
        data: 'jun',
        className: 'uniqueClassName',
      },
      {
        title: 'Julio',
        data: 'jul',
        className: 'uniqueClassName',
      },
      {
        title: 'Agosto',
        data: 'aug',
        className: 'uniqueClassName',
      },
      {
        title: 'Septiembre',
        data: 'sept',
        className: 'uniqueClassName',
      },
      {
        title: 'Octubre',
        data: 'oct',
        className: 'uniqueClassName',
      },
      {
        title: 'Noviembre',
        data: 'nov',
        className: 'uniqueClassName',
      },
      {
        title: 'Diciembre',
        data: 'dece',
        className: 'uniqueClassName',
      },
      {
        title: 'Acciones',
        data: 'id_unit_sales',
        className: 'uniqueClassName',
        render: function (data) {
          return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateSale" data-toggle='tooltip' title='Actualizar Venta' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever deleteSale" data-toggle='tooltip' title='Eliminar Venta' style="font-size: 30px;color:red"></i></a>`;
        },
      },
    ],
  });
});
