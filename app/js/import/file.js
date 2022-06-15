let wb = XLSX.utils.book_new();

wb.Props = {
    Title: "Importar Datos",
    Subject: "Test",
    Author: "Teenus SAS",
    CreatedDate: new Date()
};

wb.SheetNames.push("Test Sheet");

let ws_data = [
    ['reference', 'product', 'profit', 'commission']
];

let ws = XLSX.utils.aoa_to_sheet(ws_data);

wb.Sheets["Test Sheet"] = ws;

let wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'binary' });

function s2ab(s) {
    var buf = new ArrayBuffer(s.length); //convert s to arrayBuffer
    var view = new Uint8Array(buf); //create uint8array as viewer
    for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF; //convert to octet
    return buf;
}

$("#btnDownloadFormatImportsProducts").click(function() {
    saveAs(new Blob([s2ab(wbout)], { type: "application/octet-stream" }), 'test.xlsx');
});