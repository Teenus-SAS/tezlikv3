$(document).ready(function () {
  const collator = new Intl.Collator('en');

  // sortReference = (x, y) => {
  //   return collator.compare(x.reference, y.reference);
  // };

  // sortNameProduct = (x, y) => {
  //   return collator.compare(x.product, y.product);
  // };
  sortByKey = (key) => {
    return (x, y) => {
      return collator.compare(x[key], y[key]);
    };
  };

  sortFunction = (data, key) => data.sort(sortByKey(key));
  
  // Función para agrupar los elementos por la propiedad 'category'
  leaveUniqueKey = (array, key) => {
    const uniqueValues = new Set();
    return array.reduce((acc, obj) => {
        const newObj = { ...obj }; // Clonamos el objeto para no modificar el original
        if (!uniqueValues.has(obj[key])) {
            uniqueValues.add(obj[key]);
            acc.push(newObj);
        }
        return acc;
    }, []);
  }
});
