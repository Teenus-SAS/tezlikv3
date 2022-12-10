$(document).ready(function () {
  /* Seleccion producto */

  $('#refProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    $('#selectNameProduct option').removeAttr('selected');
    $(`#selectNameProduct option[value=${id}]`).attr('selected', true);
    loadtableExternalServices(id);
  });

  $('#selectNameProduct').change(function (e) {
    e.preventDefault();
    let id = this.value;
    $('#refProduct option').removeAttr('selected');
    $(`#refProduct option[value=${id}]`).attr('selected', true);
    loadtableExternalServices(id);
  });

  /* Cargue tabla de Proyectos */

  const loadtableExternalServices = (idProduct) => {
    tblExternalServices = $('#tblExternalServices').dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: `../../api/externalservices/${idProduct}`,
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
          title: 'Referencia',
          data: 'reference',
        },
        {
          title: 'Servicio',
          data: 'name_service',
        },
        {
          title: 'Costo',
          data: 'cost',
          className: 'classRight',
          render: $.fn.dataTable.render.number('.', ',', 0, '$ '),
        },
        {
          title: 'Acciones',
          data: 'id_service',
          className: 'uniqueClassName',
          render: function (data) {
            return `
                        <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateExternalService" data-toggle='tooltip' title='Actualizar Servicio' style="font-size: 30px;"></i></a>
                        <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Servicio' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
          },
        },
      ],
    });
  };
});
