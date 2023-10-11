$(document).ready(function () {
    $('#btnPrintPDF').click(function (e) {
        e.preventDefault();
        
        try {
            let invoice = document.getElementById('invoice');
            let copy_invoice = invoice.cloneNode(true);

            copy_invoice.style.width = '1500px';

            var opt = {
                margin: [5, 5, 5, 5],
                filename: `dashboard.pdf`,
                html2canvas: {
                    scale: 2,
                    bottom: 20,
                    width: 1501,
                },
                jsPDF: {
                    unit: 'pt',
                    format: 'letter',
                    orientation: 'portrait',
                },
            };
            html2pdf().from(copy_invoice).set(opt).toPdf().get('pdf').save();

        } catch (error) {
            console.log(error);
        } 
    });
});