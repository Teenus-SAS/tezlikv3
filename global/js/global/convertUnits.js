$(document).ready(function () {
  /* Conversion de unidades */
  convertUnitsMaterials = (data) => {
    try {
      let magnitude = data.magnitude;
      let unitProductMaterial = data.abbreviation_p_materials;
      let unitMaterial = data.abbreviation_material;
      let quantity = data.quantity;

      let arr = {};

      if (unitProductMaterial !== unitMaterial && magnitude !== 'UNIDAD') {
        switch (magnitude) {
          case 'LONGITUD':
            arr['M'] = {
              CM: { value: 0.01 },
              ML: { value: 0.001 },
              INCH: { value: 0.0254 },
              FT: { value: 0.3048 },
            };
            arr['CM'] = {
              M: { value: 100 },
              ML: { value: 0.1 },
              INCH: { value: 2.54 },
              FT: { value: 30.48 },
            };
            arr['ML'] = {
              M: { value: 1000 },
              CM: { value: 10 },
              INCH: { value: 25.4 },
              FT: { value: 304.8 },
            };
            arr['INCH'] = {
              M: { value: 39.37007874 },
              CM: { value: 0.3937007874 },
              ML: { value: 0.0393700787 },
              FT: { value: 12 },
            };
            arr['FT'] = {
              M: { value: 3.280839895 },
              CM: { value: 0.032808399 },
              ML: { value: 0.0032808399 },
              INCH: { value: 0.0833333333 },
            };
            break;
          case 'MASA':
            arr['TN'] = {
              KG: { value: 0.001 },
              GR: { value: 0.000001 },
              MG: { value: 0.000000001 },
              LB: { value: 0.0004535924 },
            };
            arr['KG'] = {
              TN: { value: 1000 },
              GR: { value: 0.001 },
              MG: { value: 0.000001 },
              LB: { value: 0.45359237 },
            };
            arr['GR'] = {
              TN: { value: 1000000 },
              KG: { value: 1000 },
              MG: { value: 0.001 },
              LB: { value: 453.59237 },
            };
            arr['MG'] = {
              TN: { value: 1000000000 },
              KG: { value: 1000000 },
              GR: { value: 1000 },
              LB: { value: 453592.37 },
            };
            arr['LB'] = {
              TN: { value: 2204.6226218 },
              KG: { value: 2.2046226218 },
              GR: { value: 0.0022046226 },
              MG: { value: 0.0000022046 },
            };
            break;
          case 'VOLUMEN':
            arr['CM3'] = {
              M3: { value: 1000000 },
              L: { value: 1000 },
              ML: { value: 1 },
              GL: { value: 3785.41 },
            };
            arr['M3'] = {
              CM3: { value: 0.000001 },
              L: { value: 0.001 },
              ML: { value: 0.000001 },
              GL: { value: 0.00378541 },
            };
            arr['L'] = {
              CM3: { value: 0.001 },
              M3: { value: 1000 },
              ML: { value: 0.001 },
              GL: { value: 3.78541 },
            };
            arr['ML'] = {
              CM3: { value: 1 },
              M3: { value: 1000000 },
              L: { value: 1000 },
              GL: { value: 3785.41 },
            };
            arr['GL'] = {
              CM3: { value: 0.000264172 },
              M3: { value: 264.172 },
              L: { value: 0.264172 },
              ML: { value: 0.000264172 },
            };
            break;
          case 'ÁREA':
            arr['DM2'] = {
              M2: { value: 100 },
              FT2: { value: 9.2903043597 },
              INCH2: { value: 0.0645160042 },
            };
            arr['M2'] = {
              DM2: { value: 0.01 },
              FT2: { value: 0.0929030436 },
              INCH2: { value: 0.00064516 },
            };
            arr['FT2'] = {
              DM2: { value: 0.1076391 },
              M2: { value: 10.76391 },
              INCH2: { value: 0.0069444446 },
            };
            arr['INCH2'] = {
              DM2: { value: 15.50003 },
              M2: { value: 1550.003 },
              FT2: { value: 143.99999628 },
            };
            break;
        }

        let unit = arr[unitMaterial][unitProductMaterial];

        quantity = quantity * unit.value;
        return quantity;
      } else {
        return quantity;
      }
    } catch (error) {
      console.log(error);
    }
  }; 
});
