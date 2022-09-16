$(document).ready(function () {
  sessionStorage.removeItem('products');
  // Obtener Inventarios
  fetch(`/api/inventory`)
    .then((response) => response.text())
    .then((data) => {
      data = JSON.parse(data);
      // Guardar productos
      data_products = JSON.stringify(data.products);
      sessionStorage.setItem('products', data_products);

      products = data.products;
      materials = data.rawMaterials;
      supplies = data.supplies;

      $('#category').prop('disabled', false);
    });

  // Seleccionar Categoria
  $('#category').change(function (e) {
    e.preventDefault();

    // Ocultar card formulario Analisis Inventario ABC
    $('.cardAddMonths').hide(800);
    $('.cardBtnAddMonths').hide(800);

    if ($.fn.dataTable.isDataTable('#tblInventories')) {
      $('#tblInventories').DataTable().destroy();
      $('#tblInventories').empty();
    }
    value = this.value;

    if (value != 0) {
      // Productos
      if (value.includes('Producto')) {
        $('.cardBtnAddMonths').show(800);
        data = getProducts(products);
        data['visible'] = true;
      }
      // Materias Prima
      else if (value.includes('Materiales')) {
        data = getMaterials(materials);
        data['visible'] = false;
      }
      // Insumos
      else if (value.includes('Insumos')) {
        data = getSupplies(supplies);
        data['visible'] = false;
      }
      // Todos
      else if (value.includes('Todos')) {
        dataProducts = getProducts(products);
        dataMaterials = getMaterials(materials);
        dataSupplies = getSupplies(supplies);

        data = dataProducts.concat(dataMaterials, dataSupplies);
        data['visible'] = false;
      }

      loadTable(data);
    }
  });

  getProducts = (products) => {
    data = [];
    for (i = 0; i < products.length; i++) {
      data.push({
        reference: products[i].reference,
        description: products[i].product,
        category: 'Producto',
        unit: 'Unidad',
        quantity: products[i].quantity,
        classification: products[i].classification,
      });
    }

    return data;
  };
  getMaterials = (materials) => {
    data = [];
    for (i = 0; i < materials.length; i++) {
      data.push({
        reference: materials[i].reference,
        description: materials[i].material,
        category: 'Materia Prima',
        unit: materials[i].unit,
        quantity: materials[i].quantity,
      });
    }
    return data;
  };
  getSupplies = (supplies) => {
    data = [];
    for (i = 0; i < supplies.length; i++) {
      data.push({
        reference: supplies[i].reference,
        description: supplies[i].material,
        category: 'Insumo',
        unit: supplies[i].unit,
        quantity: supplies[i].quantity,
      });
    }
    return data;
  };

  /* Cargar Tabla Inventarios */
  loadTable = (data) => {
    tblInventories = $('#tblInventories').dataTable({
      pageLength: 50,
      data: data,
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
          className: 'uniqueClassName',
        },
        {
          title: 'Descripción',
          data: 'description',
          className: 'uniqueClassName',
        },
        {
          title: 'Categoria',
          data: 'category',
          className: 'classCenter',
        },
        {
          title: 'Unidad',
          data: 'unit',
          className: 'classCenter',
        },
        {
          title: 'Cantidad',
          data: 'quantity',
          className: 'uniqueClassName',
        },
        {
          title: 'Clasificación',
          data: null,
          className: 'uniqueClassName',
          visible: data['visible'],
          render: function (data) {
            return `<p>${data.classification}</p>`;
          },
        },
      ],
    });
  };
});
