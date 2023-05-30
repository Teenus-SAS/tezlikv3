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
              CM: { value: 100, op: '/' },
              ML: { value: 1000, op: '/' },
              INCH: { value: 39.37, op: '/' },
              FT: { value: 3.281, op: '/' },
            };
            arr['CM'] = {
              M: { value: 100, op: '*' },
              ML: { value: 10, op: '/' },
              INCH: { value: 2.54, op: '*' },
              FT: { value: 30.48, op: '*' },
            };
            arr['ML'] = {
              M: { value: 1000, op: '*' },
              CM: { value: 10, op: '*' },
              INCH: { value: 25.4, op: '*' },
              FT: { value: 304.8, op: '*' },
            };
            arr['INCH'] = {
              M: { value: 39.37, op: '*' },
              CM: { value: 2.54, op: '/' },
              ML: { value: 25.4, op: '/' },
              FT: { value: 12, op: '*' },
            };
            arr['FT'] = {
              M: { value: 3.281, op: '*' },
              CM: { value: 38.48, op: '/' },
              ML: { value: 304.8, op: '/' },
              INCH: { value: 12, op: '/' },
            };
            break;
          case 'MASA':
            arr['TN'] = {
              KG: { value: 1000, op: '/' },
              GR: { value: 1000000, op: '/' },
              MG: { value: 1000000000, op: '/' },
              LB: { value: 2205, op: '/' },
            };
            arr['KG'] = {
              TN: { value: 1000, op: '*' },
              GR: { value: 1000, op: '/' },
              MG: { value: 1000000, op: '/' },
              LB: { value: 2.205, op: '/' },
            };
            arr['GR'] = {
              TN: { value: 1000000, op: '*' },
              KG: { value: 1000, op: '*' },
              MG: { value: 1000, op: '/' },
              LB: { value: 453.6, op: '*' },
            };
            arr['MG'] = {
              TN: { value: 1000000000, op: '*' },
              KG: { value: 1000000, op: '*' },
              GR: { value: 1000, op: '*' },
              LB: { value: 453600, op: '*' },
            };
            arr['LB'] = {
              TN: { value: 2205, op: '*' },
              KG: { value: 2.205, op: '*' },
              GR: { value: 453.6, op: '/' },
              MG: { value: 1000, op: '*' },
            };
            break;
          case 'VOLUMEN':
            arr['CM3'] = {
              M3: { value: 1000000, op: '*' },
              L: { value: 1000, op: '*' },
              ML: { value: 1, op: '*' },
            };
            arr['M3'] = {
              CM3: { value: 1000000, op: '/' },
              L: { value: 1000, op: '/' },
              ML: { value: 1000000, op: '/' },
            };
            arr['L'] = {
              CM3: { value: 1000, op: '/' },
              M3: { value: 1000, op: '*' },
              ML: { value: 1000, op: '/' },
            };
            arr['ML'] = {
              CM3: { value: 1, op: '*' },
              M3: { value: 1000000, op: '*' },
              L: { value: 1000, op: '*' },
            };
            break;
          case 'ÁREA':
            arr['DM2'] = {
              M2: { value: 100, op: '*' },
              FT2: { value: 9.29, op: '*' },
              INCH2: { value: 15.5, op: '/' },
            };
            arr['M2'] = {
              DM2: { value: 100, op: '/' },
              FT2: { value: 10.764, op: '/' },
              INCH2: { value: 1550, op: '/' },
            };
            arr['FT2'] = {
              DM2: { value: 9.29, op: '/' },
              M2: { value: 10.764, op: '*' },
              INCH2: { value: 144, op: '/' },
            };
            arr['INCH2'] = {
              DM2: { value: 15.5, op: '*' },
              M2: { value: 1550, op: '*' },
              FT2: { value: 144, op: '*' },
            };
            break;
        }

        let unit = arr[unitMaterial][unitProductMaterial];

        quantity = calcQuantity(quantity, unit.op, unit.value);
        return quantity;
      } else {
        return quantity;
      }
    } catch (error) {
      console.log(error);
    }
  };

  calcQuantity = (num1, operator, num2) => {
    let value;
    if (operator == '/') {
      value = num1 / num2;
    } else if (operator == '*') {
      value = num1 * num2;
    }
    return value;
  };
});
