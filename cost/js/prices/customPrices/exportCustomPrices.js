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

            for (let i = 1; i < cells.length - 1 ; i++) { 
                arr.push(cells[i].textContent);
            } 
            datos.push(arr);
        });

        let wb = XLSX.utils.book_new();
        let ws = XLSX.utils.aoa_to_sheet(datos);
        XLSX.utils.book_append_sheet(wb, ws, 'Precios Personalizados');

        XLSX.writeFile(wb, 'precios_personalizados.xlsx');
    });
});