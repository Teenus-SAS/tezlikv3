$(document).ready(function () {
  const VISIBLE_COST = export_import !== "0" && flag_export_import !== "0";
  const API_ENDPOINTS = {
    CATEGORIES: "/api/categories",
    MATERIALS: "/api/materials",
    PRODUCTS_MATERIALS: "/api/productsMaterialsBasic",
  };

  const loadAllData = async (op) => {
    try {
      const promises = [
        op === 3 || op === 1 ? fetchData(API_ENDPOINTS.CATEGORIES) : null,
        fetchData(API_ENDPOINTS.MATERIALS),
        op === 1 ? fetchData(API_ENDPOINTS.PRODUCTS_MATERIALS) : null,
      ];

      const [categories, materials, productMaterials] = await Promise.all(
        promises
      );
      sessionStorage.setItem("dataMaterials", JSON.stringify(materials));

      if (op === 1 && categories) {
        updateCategoriesDropdown(categories);
        sessionStorage.setItem("dataCategory", JSON.stringify(categories));
        sessionStorage.setItem(
          "dataProductMaterials",
          JSON.stringify(productMaterials)
        );
      }

      const cachedCategories =
        op === 1
          ? categories
          : JSON.parse(sessionStorage.getItem("dataCategory"));

      toggleVisibility(
        ".categories",
        cachedCategories && cachedCategories.length > 0
      );

      const priceOption = determinePriceOption(
        flag_currency_usd,
        "#selectPriceUSD"
      );
      loadRawMaterialsTable(materials, cachedCategories, priceOption);
    } catch (error) {
      console.error("Error loading data:", error);
    }
  };

  const fetchData = async (url) => {
    const response = await fetch(url);
    if (!response.ok) throw new Error(`Error fetching ${url}`);
    return response.json();
  };

  const updateCategoriesDropdown = (categories) => {
    const $dropdown = $("#idCategory");
    $dropdown
      .empty()
      .append('<option disabled selected value="0">Seleccionar</option>');
    categories.forEach(({ id_category, category }) => {
      $dropdown.append(`<option value="${id_category}">${category}</option>`);
    });
    loadTblCategories(categories);
  };

  const toggleVisibility = (selector, condition) => {
    $(selector).toggle(condition);
  };

  const determinePriceOption = (flag, selector) => {
    return flag === "1" && $(selector).val() === "2" ? 2 : 1;
  };

  const formatCost = (data, op) => {
    const value = parseFloat(data);
    const options =
      value < 0.01
        ? { minimumFractionDigits: 2, maximumFractionDigits: 9 }
        : {
          minimumFractionDigits: op === 2 ? 2 : 0,
          maximumFractionDigits: op === 2 ? 2 : 0,
        };
    return `$ ${value.toLocaleString("es-CO", options)}`;
  };

  const loadRawMaterialsTable = (data, categories, priceOption) => {
    const processedData = data.map((item) => ({
      ...item,
      price: determinePrice(item, "cost", "cost_usd", priceOption),
      costImport: determinePrice(
        item,
        "cost_import",
        "cost_import_usd",
        priceOption
      ),
      costExport: determinePrice(
        item,
        "cost_export",
        "cost_export_usd",
        priceOption
      ),
      total: calculateTotal(item, priceOption),
    }));

    const columns = buildTableColumns(VISIBLE_COST, categories.length > 0);
    initializeDataTable("#tblRawMaterials", processedData, columns);
  };

  const determinePrice = (item, localKey, usdKey, priceOption) => {
    return flag_currency_usd === "1" && priceOption === 2
      ? parseFloat(item[usdKey])
      : item[localKey];
  };

  const calculateTotal = (item, priceOption) => {
    return flag_currency_usd === "1" && priceOption === 2
      ? parseFloat(item.cost_usd) +
      parseFloat(item.cost_import_usd) +
      parseFloat(item.cost_export_usd)
      : item.cost_total;
  };

  const buildTableColumns = (visibleCost, hasCategories) => {
    const baseColumns = [
      { title: "No.", data: null, render: (_, __, ___, meta) => meta.row + 1 },
      { title: "Referencia", data: "reference" },
      { title: "Materia Prima", data: "material" },
      { title: "Categoria", data: "category", visible: hasCategories },
      { title: "Unidad", data: "abbreviation" },
      { title: "Precio", data: "price", render: formatCost },
    ];

    if (visibleCost) {
      baseColumns.push(
        { title: "Costo Importación", data: "costImport", render: formatCost },
        {
          title: "Costo Nacionalización",
          data: "costExport",
          render: formatCost,
        },
        { title: "Total", data: "total", render: formatCost }
      );
    }

    baseColumns.push({
      title: "Acciones",
      data: null,
      render: renderActions,
    });

    return baseColumns;
  };

  const renderActions = (data) => {
    const statusIcon =
      data.status === 0
        ? "/public/assets/images/trash_v.png"
        : "/public/assets/images/trash_x.png";

    return `
        <a href="javascript:;" class="mdi mdi-playlist-check" title="Ver Detalle"></a>
        <a href="javascript:;" class="mdi mdi-paperclip" title="Observaciones"></a>
        <a href="javascript:;" class="bx bx-edit-alt" title="Actualizar"></a>
        <img src="${statusIcon}" alt="Eliminar" style="width:30px;height:30px;">
      `;
  };

  const initializeDataTable = (selector, data, columns) => {
    $(selector).dataTable({
      destroy: true,
      pageLength: 50,
      data,
      dom: '<"datatable-error-console">frtip',
      language: { url: "/assets/plugins/i18n/Spanish.json" },
      deferRender: true,
      columns,
    });
  };

  loadAllData(1);
});
