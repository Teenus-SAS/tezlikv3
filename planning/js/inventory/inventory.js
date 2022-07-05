$(document).ready(function () {
  // Cargar inventarios
  fetch(`/api/inventory`)
    .then((response) => response.text())
    .then((data) => {
      data = JSON.parse(data);
      products = data.products;
      materials = data.rawMaterials;
      supplies = data.supplies;
    });
  /*$.ajax({
    type: 'GET',
    url: '/api/inventory',
    success: function (r) {
      products = r.products;
      materials = r.rawMaterials;
      supplies = r.supplies;
    },
  });*/

  // Seleccionar categoria
  $('#category').change(function (e) {
    e.preventDefault();
    if ($.fn.dataTable.isDataTable('#tblInventories')) {
      $('#tblInventories').DataTable().destroy();
      $('#tblInventories').empty();
    }

    value = this.value;

    if (value > 0) {
      // Productos
      if (value == 1) {
        data = getProducts(products);
      }
      // Materias Prima
      if (value == 2) {
        data = getMaterials(materials);
      }
      // Insumos
      if (value == 3) {
        data = getSupplies(supplies);
      }
      // Todos
      if (value == 4) {
        dataProducts = getProducts(products);
        dataMaterials = getMaterials(materials);
        dataSupplies = getSupplies(supplies);

        data = dataProducts.concat(dataMaterials, dataSupplies);
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
});
