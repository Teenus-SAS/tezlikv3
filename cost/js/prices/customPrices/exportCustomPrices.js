$(document).ready(function () {
    $('#btnExportCustomPrice').click(function (e) {
        e.preventDefault();
 
        let table = document.getElementById('tblCustomPrices');
        let rows = table.querySelectorAll('tr');
        let datos = [];

        rows.forEach(function (row) {
            let cells = row.querySelectorAll('td');

            cells.length == 0 ? cells = row.querySelectorAll('th') : cells;

            let arr = [];

            for (let i = 1; i < cells.length - 1; i++) {

                if (i != 3)
                    arr.push(cells[i].textContent);
            }
            datos.push(arr);
        });

        const productIndex = datos[0].indexOf("PRODUCTO");
        const result = [];

        for (let i = productIndex + 1; i < datos[0].length; i++) {
            const newArray = [["REFERENCIA", "PRODUCTO", datos[0][i]]];
            for (let j = 1; j < datos.length; j++) {

                if (datos[j][i] == '$ 0' || datos[j][i] == '')
                    1;
                else
                    newArray.push([datos[j][0], datos[j][1], datos[j][i]]);
            }
            if (newArray.length > 1)
                result.push(newArray);
        };
 
        let wb = XLSX.utils.book_new();
        for (let i = 0; i < result.length; i++) { 
            let ws = XLSX.utils.aoa_to_sheet(result[i]);
            XLSX.utils.book_append_sheet(wb, ws, `${result[i][0][2]}`); 
        }

        // let ws = XLSX.utils.aoa_to_sheet(datos);
        // XLSX.utils.book_append_sheet(wb, ws, 'Precios Personalizados');

        XLSX.writeFile(wb, 'precios_personalizados.xlsx');
    });
});