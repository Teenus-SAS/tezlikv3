$(document).ready(function () {
  // Función para obtener encabezados de la hoja de cálculo
  const getHeaders = (worksheet) => {
    const headers = [];
    const range = XLSX.utils.decode_range(worksheet['!ref']);
    const firstRow = range.s.r; // obtener la primera fila

    for (let col = range.s.c; col <= range.e.c; ++col) {
      const cell = worksheet[XLSX.utils.encode_cell({ r: firstRow, c: col })];
      const hdr = cell && cell.t ? XLSX.utils.format_cell(cell) : `UNKNOWN ${col}`;

      if (!hdr.includes('UNKNOWN'))
        headers.push(hdr);
    }

    return headers;
  };
  
  importFile = (selectedFile) =>
    new Promise((resolve, reject) => {
      let fileReader = new FileReader();
      fileReader.readAsBinaryString(selectedFile);

      fileReader.onload = (event) => {
        let data = event.target.result;
        let workbook = XLSX.read(data, { type: 'binary' });

        workbook.SheetNames.forEach((sheet) => {
          const worksheet = workbook.Sheets[sheet];
          actualHeaders = getHeaders(worksheet);
          rowObject = XLSX.utils.sheet_to_json(worksheet, { header: 1, defval: null }).slice(1);
        });
        resolve({rowObject, actualHeaders});
      };
    });
});
