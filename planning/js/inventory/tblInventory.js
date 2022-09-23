$(document).ready(function () {
  sessionStorage.removeItem('products');
  // Obtener Inventarios
  loadInventory = async () => {
    await fetch(`/api/inventory`)
      .then((response) => response.text())
      .then((data) => {
        data = JSON.parse(data);
        // Guardar productos
        dataProductsInProcess = JSON.stringify(data.productsInProcess);
        dataFinishProducts = JSON.stringify(data.finishProducts);
        sessionStorage.setItem('dataProductsInProcess', dataProductsInProcess);
        sessionStorage.setItem('dataFinishProducts', dataFinishProducts);

        // products = data.products;
        productsInProcess = data.productsInProcess;
        finishProducts = data.finishProducts;
        materials = data.rawMaterials;
        supplies = data.supplies;
      });
  };

  loadInventory();

  // Seleccionar Categoria
  $('#category').change(function (e) {
    e.preventDefault();

    // Ocultar card formulario Analisis Inventario ABC
    $('.cardAddMonths').hide(800);
    $('.cardBtnAddMonths').hide(800);

    value = this.value;
    if (value != 0) {
      // Productos
      if (value.includes('Producto terminado')) {
        $('.cardBtnAddMonths').show(800);
        data = getInventory(finishProducts);
        data['visible'] = true;
      } else if (value.includes('Producto en proceso')) {
        $('.cardBtnAddMonths').show(800);
        data = getInventory(productsInProcess);
        data['visible'] = true;
      }
      // Materias Prima
      else if (value.includes('Materiales')) {
        data = getInventory(materials);
        data['visible'] = false;
      }
      // Insumos
      else if (value.includes('Insumos')) {
        data = getInventory(supplies);
        data['visible'] = false;
      }
      // Todos
      else if (value.includes('Todos')) {
        dataProductsInProcess = getInventory(productsInProcess);
        dataFinishProducts = getInventory(finishProducts);
        dataMaterials = getInventory(materials);
        dataSupplies = getInventory(supplies);

        data = dataProductsInProcess.concat(
          dataFinishProducts,
          dataMaterials,
          dataSupplies
        );
        data['visible'] = false;
      }
      loadTable(data);
    }
  });

  getInventory = (data) => {
    let dataInventory = [];
    for (i = 0; i < data.length; i++) {
      data[i].classification
        ? (classification = data[i].classification)
        : (classification = '');

      dataInventory.push({
        reference: data[i].reference,
        description: data[i].descprit,
        category: data[i].category,
        unit: data[i].unit,
        quantity: data[i].quantity,
        classification,
      });
    }

    return dataInventory;
  };

  /* Cargar Tabla Inventarios */
  loadTable = (data) => {
    if ($.fn.dataTable.isDataTable('#tblInventories')) {
      $('#tblInventories').DataTable().destroy();
      $('#tblInventories').empty();
    }

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
